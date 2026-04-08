<?php
require_once __DIR__ . '/../config/koneksi.php';

class KeranjangController
{

    public function index()
    {
        session_start();
        global $conn;

        $selected_menu = $_SESSION['selected_menu'] ?? null;
        $selected_fasilitas = $_SESSION['selected_fasilitas'] ?? null;

        $data_menu = [];
        $data_fasilitas = [];

        // ======================
        // AMBIL DATA MENU
        // ======================
        if (isset($_SESSION['keranjang'])) {
            foreach ($_SESSION['keranjang'] as $id_menu => $qty) {

                $stmt = $conn->prepare("SELECT * FROM tb_menu WHERE id_menu = ?");
                $stmt->execute([$id_menu]);
                $m = $stmt->fetch();

                if ($m) {
                    $m['qty'] = $qty;
                    $m['subtotal'] = $m['harga'] * $qty;
                    $data_menu[] = $m;
                }
            }
        }

        // AMBIL DATA FASILITAS
        if (isset($_SESSION['keranjang_fasilitas'])) {
            foreach ($_SESSION['keranjang_fasilitas'] as $id => $item) {

                $stmt = $conn->prepare("SELECT * FROM tb_fasilitas WHERE id_fasilitas = ?");
                $stmt->execute([$id]);
                $f = $stmt->fetch();

                if ($f) {
                    $f['pengali'] = $item['pengali'];
                    $f['subtotal'] = $f['harga'] * $item['pengali'];
                    $f['tgl_sewa'] = $item['tgl_sewa'];
                    $f['jam_mulai'] = $item['jam_mulai'];
                    $f['jam_selesai'] = isset($item['jam_selesai']) ? $item['jam_selesai'] : null;
                    $f['satuan'] = $item['satuan'];
                    $data_fasilitas[] = $f;
                }
            }
        }

        // tampilkan view
        require_once __DIR__ . '/../views/pelanggan/keranjang.php';
    }

    public function tambah()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id_menu = $_POST['id_menu'] ?? null;

        // PERBAIKAN: Tangkap jumlah dari form. Jika tidak ada, baru default ke 1
        $jumlah = isset($_POST['jumlah']) ? (int)$_POST['jumlah'] : 1;

        // Proteksi tambahan: Pastikan jumlah tidak minus atau nol
        if ($jumlah < 1) {
            $jumlah = 1;
        }

        if (!$id_menu) {
            header("Location: index.php?controller=menu");
            exit;
        }

        // Jika keranjang belum ada, buat array baru
        if (!isset($_SESSION['keranjang'])) {
            $_SESSION['keranjang'] = [];
        }

        // Jika menu sudah ada di keranjang, tambah jumlahnya sesuai inputan form
        if (isset($_SESSION['keranjang'][$id_menu])) {
            $_SESSION['keranjang'][$id_menu] += $jumlah;
        } else {
            // Jika belum ada, set jumlah sesuai inputan form
            $_SESSION['keranjang'][$id_menu] = $jumlah;
        }

        echo "<script>alert('Menu berhasil ditambahkan ke keranjang!'); window.location='index.php?controller=keranjang&action=index';</script>";
        exit;
    }

    public function update()
    {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?controller=keranjang&action=index");
            exit;
        }

        global $conn;

        $_SESSION['selected_menu'] = $_POST['selected_menu'] ?? [];
        $_SESSION['selected_fasilitas'] = $_POST['selected_fasilitas'] ?? [];

        // Update untuk Menu
        if (isset($_POST['id_menu'])) {
            $id_menu = $_POST['id_menu'];
            $qty = (int)($_POST['qty'] ?? 1);
            if ($qty < 1) {
                unset($_SESSION['keranjang'][$id_menu]);
            } else {
                $_SESSION['keranjang'][$id_menu] = $qty;
            }
        }

        // Update untuk Fasilitas
        if (isset($_POST['id_fasilitas'])) {
            $id_f = $_POST['id_fasilitas'];
            $new_pengali = (int)$_POST['pengali'];

            if (isset($_SESSION['keranjang_fasilitas'][$id_f])) {
                $f_name = $_SESSION['keranjang_fasilitas'][$id_f]['nama_fasilitas'];
                $is_pool = (stripos($f_name, 'pool') !== false || stripos($f_name, 'renang') !== false);

                // VALIDASI MAKSIMAL 5 ORANG SAAT MENGUBAH DI KERANJANG
                if ($_SESSION['keranjang_fasilitas'][$id_f]['satuan'] == 'Orang' && $is_pool && $new_pengali > 5) {
                    echo "<script>alert('Maksimal booking untuk Swimming Pool adalah 5 orang.'); window.history.back();</script>";
                    exit;
                }

                if ($new_pengali > 0) {

                    // CEK BENTROK REAL-TIME JIKA MENAMBAH DURASI JAM
                    if ($_SESSION['keranjang_fasilitas'][$id_f]['satuan'] == 'Jam') {
                        $jam_mulai = $_SESSION['keranjang_fasilitas'][$id_f]['jam_mulai'];
                        $tgl_sewa = $_SESSION['keranjang_fasilitas'][$id_f]['tgl_sewa'];
                        $jam_selesai = date('H:i:s', strtotime($jam_mulai) + ($new_pengali * 3600));

                        $query_cek = "
                            SELECT bf.jam_mulai, bf.jam_selesai
                            FROM tb_booking_fasilitas bf
                            JOIN tb_pesanan p ON bf.id_pesanan = p.id_pesanan
                            WHERE bf.id_fasilitas = ?
                            AND bf.tgl_sewa = ?
                            AND p.status != 'Dibatalkan'
                            AND (? < bf.jam_selesai AND ? > bf.jam_mulai)
                        ";
                        $stmt_cek = $conn->prepare($query_cek);
                        $stmt_cek->execute([$id_f, $tgl_sewa, $jam_mulai, $jam_selesai]);

                        if ($stmt_cek->rowCount() > 0) {
                            echo "<script>alert('Gagal menambah durasi! Jam tambahan tersebut sudah di-booking oleh pelanggan lain. Waktu bentrok!'); window.history.back();</script>";
                            exit;
                        }
                        $_SESSION['keranjang_fasilitas'][$id_f]['jam_selesai'] = $jam_selesai;
                    }

                    $_SESSION['keranjang_fasilitas'][$id_f]['pengali'] = $new_pengali;
                    $harga = $_SESSION['keranjang_fasilitas'][$id_f]['harga'];
                    $_SESSION['keranjang_fasilitas'][$id_f]['subtotal'] = $harga * $new_pengali;
                } else {
                    unset($_SESSION['keranjang_fasilitas'][$id_f]);
                }
            }
        }

        header("Location: index.php?controller=keranjang&action=index");
        exit;
    }

    public function hapusMenu()
    {
        session_start();

        $id = $_GET['id'] ?? null;

        if (!$id || !is_numeric($id)) {
            header("Location: index.php?controller=keranjang&action=index");
            exit;
        }

        if (isset($_SESSION['keranjang'][$id])) {
            unset($_SESSION['keranjang'][$id]);
        }

        header("Location: index.php?controller=keranjang&action=index");
        exit;
    }

    public function hapusFasilitas()
    {
        session_start();

        $id = $_GET['id'] ?? null;

        if ($id && isset($_SESSION['keranjang_fasilitas'][$id])) {
            unset($_SESSION['keranjang_fasilitas'][$id]);
        }

        header("Location: index.php?controller=keranjang&action=index");
        exit;
    }
}
