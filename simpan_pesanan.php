<?php
session_start();
require_once 'config/koneksi.php';

// 1. Validasi Awal
if (!isset($_SESSION['id_member'])) {
    header("Location: login.php"); exit;
}

$id_member = $_SESSION['id_member'];
$id_pesanan = "RSY-" . date("YmdHis"); // Generate ID Unik
$tgl_pesanan = date("Y-m-d H:i:s");

// Ambil data dari POST checkout.php
$tipe_pesanan = $_POST['tipe_pesanan'];
$id_meja      = !empty($_POST['id_meja']) ? $_POST['id_meja'] : NULL;
$metode_bayar = $_POST['metode'];
$catatan      = $_POST['catatan'];
$id_meja      = !empty($_POST['id_meja']) ? $_POST['id_meja'] : NULL;
$id_voucher   = !empty($_POST['id_voucher']) ? $_POST['id_voucher'] : NULL;

try {
    // MULAI TRANSAKSI
    $conn->beginTransaction();

    // 2. Hitung Ulang Total & Potongan (Jangan percaya data dari sisi klien/browser)
    $subtotal = 0;
    
    // Hitung Menu
    if(!empty($_SESSION['keranjang'])) {
        foreach($_SESSION['keranjang'] as $id => $qty) {
            $m = $conn->query("SELECT harga FROM tb_menu WHERE id_menu = $id")->fetch();
            $subtotal += ($m['harga'] * $qty);
        }
    }
    // Hitung Fasilitas
    if(!empty($_SESSION['keranjang_fasilitas'])) {
        foreach($_SESSION['keranjang_fasilitas'] as $id => $b) {
            $f = $conn->query("SELECT harga_per_jam FROM tb_fasilitas WHERE id_fasilitas = $id")->fetch();
            $subtotal += ($f['harga_per_jam'] * $b['durasi']);
        }
    }

    // Hitung Diskon Level Member
    $u = $conn->query("SELECT l.diskon FROM tb_member m JOIN tb_level_member l ON m.id_level = l.id_level WHERE m.id_member = $id_member")->fetch();
    $disc_level = $subtotal * ($u['diskon']/100);
    
    // Hitung Potongan Voucher/Kupon
    $potongan_v = 0;
    if($id_voucher) {
        $pv = $conn->query("SELECT nominal_potongan, poin_dibutuhkan FROM tb_promo WHERE id_promo = $id_voucher")->fetch();
        $potongan_v = $pv['nominal_potongan'];
        
        // POTONG POIN MEMBER (Jika voucher tipe Loyalty)
        $conn->prepare("UPDATE tb_member SET poin = poin - ? WHERE id_member = ?")
             ->execute([$pv['poin_dibutuhkan'], $id_member]);
    }

    $total_final = ($subtotal + ($subtotal * 0.1)) - ($disc_level + $potongan_v);

    // 3. INSERT KE tb_pesanan
    $sql_p = "INSERT INTO tb_pesanan (id_pesanan, id_member, id_meja, id_promo, tgl_pesanan, total_transaksi, tipe_pemesanan, metode_pembayaran, catatan, status) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Menunggu Verifikasi')";
    $conn->prepare($sql_p)->execute([$id_pesanan, $id_member, $id_meja, $id_voucher, $tgl_pesanan, $total_final, $tipe_pesanan, $metode_bayar, $catatan]);

    // 4. INSERT DETAIL MENU & POTONG STOK
    if(!empty($_SESSION['keranjang'])) {
        foreach($_SESSION['keranjang'] as $id_m => $qty) {
            $m = $conn->query("SELECT harga FROM tb_menu WHERE id_menu = $id_m")->fetch();
            $sub = $m['harga'] * $qty;
            
            $conn->prepare("INSERT INTO tb_detail_pesanan (id_pesanan, id_menu, jumlah, subtotal) VALUES (?, ?, ?, ?)")
                 ->execute([$id_pesanan, $id_m, $qty, $sub]);
            
            $conn->prepare("UPDATE tb_menu SET stok = stok - ? WHERE id_menu = ?")
                 ->execute([$qty, $id_m]);
        }
    }

    // 5. INSERT DETAIL FASILITAS
    if(!empty($_SESSION['keranjang_fasilitas'])) {
        foreach($_SESSION['keranjang_fasilitas'] as $id_f => $b) {
            $f = $conn->query("SELECT harga_per_jam FROM tb_fasilitas WHERE id_fasilitas = $id_f")->fetch();
            $sub_f = $f['harga_per_jam'] * $b['durasi'];
            
            $sql_df = "INSERT INTO tb_detail_pesanan (id_pesanan, id_fasilitas, tgl_booking, jam_mulai, durasi_jam, subtotal) VALUES (?, ?, ?, ?, ?, ?)";
            $conn->prepare($sql_df)->execute([$id_pesanan, $id_f, $b['tgl_booking'], $b['jam_mulai'], $b['durasi'], $sub_f]);
        }
    }

    // JIKA SEMUA BERHASIL, SIMPAN PERMANEN
    $conn->commit();

    // Bersihkan Keranjang
    unset($_SESSION['keranjang']);
    unset($_SESSION['keranjang_fasilitas']);

    echo "<script>alert('Pesanan Berhasil Dibuat!'); window.location='riwayat_pesanan.php';</script>";

} catch (Exception $e) {
    // JIKA ADA SATU SAJA YANG GAGAL, BATALKAN SEMUA
    $conn->rollBack();
    echo "Gagal menyimpan pesanan: " . $e->getMessage();
}