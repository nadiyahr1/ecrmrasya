<?php
require_once __DIR__ . '/../models/CheckoutModel.php';

class CheckoutController
{

    private $model;

    public function __construct()
    {
        $this->model = new CheckoutModel();
    }

    private function startSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // HALAMAN CHECKOUT
    // HALAMAN CHECKOUT
    public function index()
    {
        $this->startSession();

        // 1. Proteksi Halaman
        if (!isset($_SESSION['id_member'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $id_member = $_SESSION['id_member'];

        // 2. Tangkap Data dari Keranjang
        $selected_menu = $_POST['selected_menu'] ?? [];
        $selected_fasilitas = $_POST['selected_fasilitas'] ?? [];

        if (empty($selected_menu) && empty($selected_fasilitas)) {
            echo "<script>alert('Pilih minimal 1 item untuk checkout!'); window.location='index.php?controller=keranjang&action=index';</script>";
            exit;
        }

        // 3. Ambil Semua Data yang Dibutuhkan View melalui Model
        $user = $this->model->getMember($id_member);
        $mejas = $this->model->getMejaTersedia();
        $promos = $this->model->getPromoLoyalty();

        // Siapkan detail menu yang dipilih
        $menus = [];
        foreach ($selected_menu as $id) {
            $m = $this->model->getMenuById($id);
            if ($m) {
                $menus[$id] = $m;
            }
        }

        // Siapkan detail fasilitas yang dipilih
        $fasilitas_data = [];
        foreach ($selected_fasilitas as $id) {
            $f = $this->model->getFasilitasById($id);
            if ($f) {
                $fasilitas_data[$id] = $f;
            }
        }

        $ada_fasilitas = !empty($selected_fasilitas);

        // 4. Panggil View (semua variabel di atas akan otomatis terbaca di dalam view)
        require_once __DIR__ . '/../views/transaksi/checkout.php';
    }
    public function simpanPesanan()
    {
        // 1. PERBAIKAN: Gunakan method internal agar tidak terjadi bentrok session
        $this->startSession();

        date_default_timezone_set('Asia/Jakarta');

        // VALIDASI LOGIN
        if (!isset($_SESSION['id_member'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        // 2. PERBAIKAN: Sesuaikan dengan nama variabel di View kamu (selected_menu)
        $selected_menu = $_POST['checkout_menu'] ?? [];
        $selected_fasilitas = $_POST['checkout_fasilitas'] ?? [];

        if (empty($selected_menu) && empty($selected_fasilitas)) {
            echo "<script>alert('Pilih minimal 1 item!'); window.location='index.php?controller=keranjang&action=index';</script>";
            exit;
        }

        $id_member   = $_SESSION['id_member'];
        $id_pesanan  = "RSY-" . date("YmdHis") . rand(100, 999);
        $tgl_pesanan = date("Y-m-d H:i:s");

        $tipe_pemesanan = $_POST['tipe_pemesanan'] ?? 'Dine-in';
        $metode_bayar   = $_POST['metode'] ?? '';
        $catatan        = $_POST['catatan'] ?? '';

        // 3. PERBAIKAN: Jika form kosong, pastikan nilainya NULL agar tidak error Foreign Key di MySQL
        $id_meja        = !empty($_POST['id_meja']) ? $_POST['id_meja'] : null;
        $id_voucher     = !empty($_POST['id_voucher']) ? $_POST['id_voucher'] : null;

        if (empty($metode_bayar)) {
            echo "<script>alert('Metode pembayaran wajib dipilih!'); window.history.back();</script>";
            exit;
        }

        try {
            $this->model->begin();

            $subtotal = 0;

            // MENU
            foreach ($selected_menu as $id) {
                if (isset($_SESSION['keranjang'][$id])) {
                    $qty = $_SESSION['keranjang'][$id];
                    $m = $this->model->getMenuById($id);
                    if ($m) {
                        $subtotal += $m['harga'] * $qty;
                    }
                }
            }

            // FASILITAS
            foreach ($selected_fasilitas as $id) {
                if (isset($_SESSION['keranjang_fasilitas'][$id])) {
                    $item = $_SESSION['keranjang_fasilitas'][$id];
                    $f = $this->model->getFasilitasById($id);
                    if ($f) {
                        $subtotal += $f['harga'] * $item['pengali'];
                    }
                }
            }

            // MEMBER (Diskon & Pajak)
            $u = $this->model->getMember($id_member);
            $disc_level = $subtotal * ($u['diskon'] / 100);
            $pajak = $subtotal * 0.1;
            $total_final = ($subtotal + $pajak) - $disc_level;

            // PROMO
            if ($id_voucher) {
                $promo = $this->model->getPromo($id_voucher);
                if ($promo) {
                    $diskon_promo = ($promo['tipe_potongan'] == 'Persen')
                        ? $total_final * ($promo['potongan'] / 100)
                        : $promo['potongan'];

                    $total_final -= $diskon_promo;

                    // Proteksi agar total tidak jadi minus
                    if ($total_final < 0) $total_final = 0;
                }
            }

            // INSERT PESANAN UTAMA (Urutan ini sudah SANGAT TEPAT dengan di Model)
            $this->model->insertPesanan([
                $id_pesanan,
                $id_member,
                $id_meja,
                $id_voucher,
                $tgl_pesanan,
                $total_final,
                $tipe_pemesanan,
                $metode_bayar,
                $catatan
            ]);

            // INSERT DETAIL MENU
            foreach ($selected_menu as $id_m) {
                if (isset($_SESSION['keranjang'][$id_m])) {
                    $qty = $_SESSION['keranjang'][$id_m];
                    $m = $this->model->getMenuById($id_m);

                    if ($m) {
                        $sub = $m['harga'] * $qty;

                        $this->model->insertDetailMenu([$id_pesanan, $id_m, $qty, $sub]);
                        $this->model->updateStokMenu($qty, $id_m);

                        unset($_SESSION['keranjang'][$id_m]); // Hapus item dari keranjang
                    }
                }
            }

            // INSERT DETAIL FASILITAS
            foreach ($selected_fasilitas as $id_f) {
                if (isset($_SESSION['keranjang_fasilitas'][$id_f])) {
                    $item = $_SESSION['keranjang_fasilitas'][$id_f];
                    $f = $this->model->getFasilitasById($id_f);

                    if ($f) {
                        $sub_f = $f['harga'] * $item['pengali'];

                        $this->model->insertFasilitas([
                            $id_pesanan,
                            $id_f,
                            $item['tgl_sewa'],
                            $item['jam_mulai'],
                            ($item['satuan'] == 'Jam') ? $item['pengali'] : null,
                            ($item['satuan'] == 'Orang') ? $item['pengali'] : null,
                            $sub_f
                        ]);

                        unset($_SESSION['keranjang_fasilitas'][$id_f]); // Hapus fasilitas dari keranjang
                    }
                }
            }

            // UPDATE POIN LOYALTY (Reward)
            $poin = floor($total_final / 10000); // Tiap kelipatan 10.000 dapat 1 poin
            if ($poin > 0) {
                $this->model->updatePoin($poin, $id_member);
            }

            // UPDATE MEJA
            if ($id_meja) {
                $this->model->updateMeja($id_meja);
            }

            $this->model->clearKeranjang($id_member);
            
            $this->model->commit();

            // 4. PERBAIKAN UX: Berikan alert sukses sebelum berpindah halaman
            echo "<script>alert('Berhasil! Pesanan Anda telah dibuat.'); window.location='index.php?controller=checkout&action=riwayat';</script>";
            exit;
        } catch (Exception $e) {
            $this->model->rollback();
            die("Error saat memproses pesanan: " . $e->getMessage());
        }
        
    }
    public function riwayat()
    {
        session_start();
        global $conn;

        if (!isset($_SESSION['id_member'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $id_m = $_SESSION['id_member'];

        $stmt = $conn->prepare("
        SELECT * FROM tb_pesanan 
        WHERE id_member = ? 
        ORDER BY tgl_pesanan DESC
    ");
        $stmt->execute([$id_m]);

        $riwayat = $stmt->fetchAll();

        require_once __DIR__ . '/../views/transaksi/riwayat_pesanan.php';
    }

    public function detail()
    {
        session_start();
        global $conn;

        if (!isset($_SESSION['id_member'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $id_p = $_GET['id'] ?? null;
        $id_m = $_SESSION['id_member'];

        if (!$id_p) {
            header("Location: index.php?controller=checkout&action=riwayat");
            exit;
        }

        // ==========================
        // DATA PESANAN
        // ==========================
        $stmt = $conn->prepare("
        SELECT p.*, m.nama_member 
        FROM tb_pesanan p 
        JOIN tb_member m ON p.id_member = m.id_member 
        WHERE p.id_pesanan = ? AND p.id_member = ? ");
        $stmt->execute([$id_p, $id_m]);
        $p = $stmt->fetch();

        if (!$p) {
            header("Location: index.php?controller=checkout&action=riwayat");
            exit;
        }

        // ==========================
        // DETAIL MENU
        // ==========================
        $stmt_m = $conn->prepare("
        SELECT d.*, m.nama_menu, m.foto 
        FROM tb_detail_pesanan d 
        JOIN tb_menu m ON d.id_menu = m.id_menu 
        WHERE d.id_pesanan = ? ");

        $stmt_m->execute([$id_p]);
        $detail_menu = $stmt_m->fetchAll();

        // DETAIL FASILITAS
        $stmt_f = $conn->prepare("
        SELECT b.*, f.nama_fasilitas, f.foto_fasilitas 
        FROM tb_booking_fasilitas b 
        JOIN tb_fasilitas f ON b.id_fasilitas = f.id_fasilitas 
        WHERE b.id_pesanan = ? ");

        $stmt_f->execute([$id_p]);
        $detail_fas = $stmt_f->fetchAll();

        // HITUNG TOTAL
        $subtotal_produk = 0;

        foreach ($detail_menu as $dm) {
            $subtotal_produk += $dm['subtotal'];
        }

        foreach ($detail_fas as $df) {
            $subtotal_produk += isset($df['subtotal']) ? $df['subtotal'] : 0;
        }

        $pajak_layanan = $subtotal_produk * 0.10;
        $total_kotor = $subtotal_produk + $pajak_layanan;
        $diskon = $total_kotor - $p['total_transaksi'];

        require_once __DIR__ . '/../views/transaksi/detail_pesanan.php';
    }

    public function formUlasan()
    {
        session_start();
        global $conn;

        if (!isset($_SESSION['id_member'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $id_p = $_GET['id'] ?? null;

        if (!$id_p) {
            header("Location: index.php?controller=checkout&action=riwayat");
            exit;
        }

        // PESANAN
        $stmt = $conn->prepare("SELECT id_pesanan, tgl_pesanan FROM tb_pesanan WHERE id_pesanan = ?");
        $stmt->execute([$id_p]);
        $p = $stmt->fetch();

        // MENU
        $stmt_m = $conn->prepare("
        SELECT d.*, m.nama_menu, m.foto 
        FROM tb_detail_pesanan d 
        JOIN tb_menu m ON d.id_menu = m.id_menu 
        WHERE d.id_pesanan = ?
    ");
        $stmt_m->execute([$id_p]);
        $detail_menu = $stmt_m->fetchAll();

        // FASILITAS
        $stmt_f = $conn->prepare("
        SELECT b.*, f.nama_fasilitas, f.foto_fasilitas, f.satuan 
        FROM tb_booking_fasilitas b 
        JOIN tb_fasilitas f ON b.id_fasilitas = f.id_fasilitas 
        WHERE b.id_pesanan = ?
    ");
        $stmt_f->execute([$id_p]);
        $detail_fas = $stmt_f->fetchAll();

        require_once __DIR__ . '/../views/transaksi/form_ulasan.php';
    }

    public function simpanUlasan()
    {
        session_start();
        global $conn;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $id_p = $_POST['id_pesanan'];
            $id_m = $_SESSION['id_member'];
            $komentar = $_POST['komentar'];

            try {
                $stmt = $conn->prepare("
                INSERT INTO tb_ulasan (id_pesanan, id_member, komentar) 
                VALUES (?, ?, ?)
            ");
                $stmt->execute([$id_p, $id_m, $komentar]);

                header("Location: index.php?controller=checkout&action=riwayat");
                exit;
            } catch (PDOException $e) {
                die("Error: " . $e->getMessage());
            }
        }
    }

    public function tukarPoin()
    {
        session_start();
        global $conn;

        if (!isset($_SESSION['id_member'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $id_m = $_SESSION['id_member'];

        // AMBIL DATA MEMBER
        $stmt = $conn->prepare("SELECT poin FROM tb_member WHERE id_member = ?");
        $stmt->execute([$id_m]);
        $member = $stmt->fetch();

        // AMBIL PROMO LOYALTY
        $promos = $conn->query("
        SELECT * FROM tb_promo 
        WHERE tipe_promo = 'Loyalty'
    ")->fetchAll();

        require_once __DIR__ . '/../views/transaksi/tukar_poin.php';
    }

    public function klaimPoin()
    {
        session_start();
        global $conn;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $id_m = $_SESSION['id_member'];
            $id_promo = $_POST['id_promo'];

            // ambil promo
            $stmt = $conn->prepare("SELECT * FROM tb_promo WHERE id_promo = ?");
            $stmt->execute([$id_promo]);
            $promo = $stmt->fetch();

            // ambil poin user
            $stmt = $conn->prepare("SELECT poin FROM tb_member WHERE id_member = ?");
            $stmt->execute([$id_m]);
            $member = $stmt->fetch();

            if ($member['poin'] < $promo['poin_dibutuhkan']) {
                die("Poin tidak cukup");
            }

            try {
                $conn->beginTransaction();

                // kurangi poin
                $conn->prepare("
                UPDATE tb_member SET poin = poin - ? WHERE id_member = ?
            ")->execute([$promo['poin_dibutuhkan'], $id_m]);

                // simpan history
                $conn->prepare("
                INSERT INTO tb_history_poin (id_member, poin, keterangan, tgl_perubahan)
                VALUES (?, ?, 'Tukar Reward', NOW())
            ")->execute([$id_m, -$promo['poin_dibutuhkan']]);

                $conn->commit();

                header("Location: index.php?controller=checkout&action=tukarPoin");
                exit;
            } catch (Exception $e) {
                $conn->rollBack();
                die($e->getMessage());
            }
        }
    }
}
