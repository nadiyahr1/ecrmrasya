<?php
require_once __DIR__ . '/../models/CheckoutModel.php';

class CheckoutController
{

    private $model;
    private $conn;

    public function __construct()
    {
        $this->model = new CheckoutModel();
        global $conn;
        $this->conn = $conn;
    }

    private function startSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // HALAMAN CHECKOUT
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Cek apakah pengguna sudah login
        if (!isset($_SESSION['id_member'])) {
            echo "<script>alert('Silakan login terlebih dahulu untuk checkout!'); window.location='index.php?controller=auth&action=login';</script>";
            exit;
        }

        global $conn;
        $id_member = $_SESSION['id_member'];

        // --- PERBAIKAN LOGIKA ITEM TERPILIH ---
        // Jika halaman ini diakses via POST (tombol Checkout ditekan)
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_SESSION['selected_menu'] = $_POST['selected_menu'] ?? [];
            $_SESSION['selected_fasilitas'] = $_POST['selected_fasilitas'] ?? [];
        }

        $selected_menu = $_SESSION['selected_menu'] ?? [];
        $selected_fasilitas = $_SESSION['selected_fasilitas'] ?? [];

        // Jika tidak ada sama sekali item yang dibawa, kembalikan ke keranjang
        if (empty($selected_menu) && empty($selected_fasilitas)) {
            echo "<script>alert('Tidak ada item yang dipilih untuk checkout!'); window.location='index.php?controller=keranjang&action=index';</script>";
            exit;
        }
        // --------------------------------------

        // 2. Sinkronisasi Data Member
        $stmt_member = $conn->prepare("SELECT * FROM tb_member WHERE id_member = ?");
        $stmt_member->execute([$id_member]);
        $member = $stmt_member->fetch();

        if ($member) {
            $_SESSION['nama_member'] = $member['nama_member'];
            $_SESSION['no_telp'] = $member['no_telp'];
            $_SESSION['poin'] = $member['poin'];
        }

        // 3. Tarik Ulang Data Keranjang Menu (HANYA YANG DICENTANG)
        $data_menu = [];
        if (isset($_SESSION['keranjang'])) {
            foreach ($_SESSION['keranjang'] as $id_menu => $qty) {
                if (in_array($id_menu, $selected_menu)) { // Filter: Hanya masukkan jika id ada di array selected
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
        }

        // 4. Tarik Ulang Data Keranjang Fasilitas (HANYA YANG DICENTANG)
        $data_fasilitas = [];
        if (isset($_SESSION['keranjang_fasilitas'])) {
            foreach ($_SESSION['keranjang_fasilitas'] as $id => $item) {
                if (in_array($id, $selected_fasilitas)) { // Filter: Hanya masukkan jika id ada di array selected
                    $stmt = $conn->prepare("SELECT * FROM tb_fasilitas WHERE id_fasilitas = ?");
                    $stmt->execute([$id]);
                    $f = $stmt->fetch();
                    if ($f) {
                        $f['pengali'] = $item['pengali'];
                        $f['satuan'] = $item['satuan'];
                        $harga_item = $f['biaya'] ?? $f['harga'];
                        $f['subtotal'] = $harga_item * $item['pengali'];
                        $data_fasilitas[] = $f;
                    }
                }
            }
        }

        // 5. Tarik Data Meja Tersedia
        $stmt_meja = $conn->prepare("SELECT * FROM tb_meja WHERE status = 'Tersedia'");
        $stmt_meja->execute();
        $meja_tersedia = $stmt_meja->fetchAll();

        // 6. Tarik Data Promo/Voucher Aktif
        $stmt_promo = $conn->prepare("
            SELECT p.* FROM tb_promo p
            WHERE p.tgl_selesai >= CURDATE() 
            AND p.status_promo = 'Aktif'
            AND NOT EXISTS (
                SELECT 1 FROM tb_pesanan pes 
                WHERE pes.id_promo = p.id_promo 
                AND pes.id_member = ? 
                AND pes.status != 'Dibatalkan'
            )
        ");
        $stmt_promo->execute([$id_member]);
        $promo_tersedia = $stmt_promo->fetchAll();

        require 'views/pelanggan/checkout.php';
    }

    public function simpanPesanan()
    {
        $this->startSession();
        date_default_timezone_set('Asia/Jakarta');

        // VALIDASI LOGIN
        if (!isset($_SESSION['id_member'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $selected_menu = $_POST['selected_menu'] ?? [];
        $selected_fasilitas = $_POST['checkout_fasilitas'] ?? [];

        if (empty($selected_menu) && empty($selected_fasilitas)) {
            echo "<script>alert('Pilih minimal 1 item!'); window.location='index.php?controller=keranjang&action=index';</script>";
            exit;
        }

        $id_member   = $_SESSION['id_member'];
        $id_pesanan  = "RSY-" . date("YmdHis") . rand(10, 99);
        $tgl_pesanan = date("Y-m-d H:i:s");

        $tipe_pemesanan = $_POST['tipe_pemesanan'] ?? 'Makan di Tempat';
        $metode_bayar   = $_POST['metode'] ?? '';
        $catatan        = $_POST['catatan'] ?? '';

        // Jika form kosong, pastikan nilainya NULL agar tidak error Foreign Key di MySQL
        $id_meja        = !empty($_POST['id_meja']) ? $_POST['id_meja'] : null;
        $id_promo_dipakai = !empty($_POST['id_promo']) ? $_POST['id_promo'] : null;

        if (empty($metode_bayar)) {
            echo "<script>alert('Metode pembayaran wajib dipilih!'); window.history.back();</script>";
            exit;
        }

        try {
            $this->model->begin();

            $subtotal = 0;

            // HITUNG SUBTOTAL MENU
            foreach ($selected_menu as $id) {
                if (isset($_SESSION['keranjang'][$id])) {
                    $qty = $_SESSION['keranjang'][$id];
                    $m = $this->model->getMenuById($id);
                    if ($m) $subtotal += $m['harga'] * $qty;
                }
            }

            // HITUNG SUBTOTAL FASILITAS
            foreach ($selected_fasilitas as $id) {
                if (isset($_SESSION['keranjang_fasilitas'][$id])) {
                    $item = $_SESSION['keranjang_fasilitas'][$id];
                    $f = $this->model->getFasilitasById($id);
                    if ($f) $subtotal += $f['harga'] * $item['pengali'];
                }
            }

            $u = $this->model->getMember($id_member);
            $pajak = $subtotal * 0.1; // Pajak 10%
            $total_final = $subtotal + $pajak;

            // Deklarasi variabel bonus untuk diproses setelah id_pesanan dibuat
            $bonus_diberikan = false;
            $id_bonus_promo = null;

            // LOGIKA PROMO & POIN 
            // LOGIKA PROMO & POIN 
            if (!empty($id_promo_dipakai)) {
                $promo = $this->model->getPromo($id_promo_dipakai);
                if ($promo) {

                    // 1. Validasi Promo Level (Keamanan Backend)
                    if ($promo['tipe_promo'] == 'Level' && $u['id_level'] != $promo['target_level']) {
                        throw new Exception("Voucher ini tidak berlaku untuk level membership Anda.");
                    }

                    // Validasi 1x Pakai (Backend Protection)
                    $stmt_cek_pakai = $this->conn->prepare("SELECT id_pesanan FROM tb_pesanan WHERE id_member = ? AND id_promo = ? AND status != 'Dibatalkan'");
                    $stmt_cek_pakai->execute([$id_member, $promo['id_promo']]);
                    if ($stmt_cek_pakai->rowCount() > 0) {
                        throw new Exception("Anda sudah pernah menggunakan promo ini. Promo hanya berlaku 1x per pelanggan.");
                    }

                    // Validasi Minimal Belanja (Backend Protection)
                    if ($promo['min_belanja'] > 0 && $total_final < $promo['min_belanja']) {
                        throw new Exception("Minimal belanja Rp " . number_format($promo['min_belanja'], 0, ',', '.') . " belum terpenuhi.");
                    }

                    // 2. Validasi & Pengurangan Saldo Poin (Untuk Tukar Poin)
                    if ($promo['tipe_promo'] == 'Tukar_Poin' && $promo['min_poin'] > 0) {
                        $stmt_m = $this->conn->prepare("SELECT poin FROM tb_member WHERE id_member = ?");
                        $stmt_m->execute([$id_member]);
                        $m_data = $stmt_m->fetch();

                        if ($m_data['poin'] < $promo['min_poin']) {
                            throw new Exception("Poin Anda tidak mencukupi untuk menggunakan promo ini.");
                        }

                        // Potong Poin
                        $upd_p = $this->conn->prepare("UPDATE tb_member SET poin = poin - ? WHERE id_member = ?");
                        $upd_p->execute([$promo['min_poin'], $id_member]);

                        // Catat Riwayat Poin Keluar
                        $hist = $this->conn->prepare("INSERT INTO tb_history_poin (id_member, poin, tipe, keterangan, tgl_perubahan) VALUES (?, ?, 'Keluar', ?, NOW())");
                        $hist->execute([$id_member, $promo['min_poin'], "Tukar Promo: " . $promo['nama_promo']]);

                        $_SESSION['poin'] = $m_data['poin'] - $promo['min_poin'];
                    }

                    // 3. Kurangi Kuota Promo (Jika ada batasan kuota)
                    if (!empty($promo['kuota']) && $promo['kuota'] > 0) {
                        $upd_kuota = $this->conn->prepare("UPDATE tb_promo SET kuota = kuota - 1 WHERE id_promo = ?");
                        $upd_kuota->execute([$id_promo_dipakai]);
                    }

                    // 4. Perhitungan Potongan Harga & VALIDASI PRODUK KETAT
                    if ($promo['tipe_potongan'] == 'Persen') {
                        $diskon_promo = $total_final * ($promo['potongan'] / 100);
                        $total_final -= $diskon_promo;
                    } elseif ($promo['tipe_potongan'] == 'Nominal') {
                        $total_final -= $promo['potongan'];
                    } elseif ($promo['tipe_potongan'] == 'Produk') {

                        // PERBAIKAN UTAMA: Ambil id menu prasyarat langsung dari kolom 'id_menu' sesuai database Anda
                        $id_trigger = $promo['id_menu_trigger'] ?? null;

                        // Karena tabel Anda tidak memiliki kolom min_beli dan id_menu_bonus, kita amankan dengan nilai default
                        $min_beli   = $promo['min_beli'] ?? 1;
                        $id_bonus   = $promo['id_menu_bonus'] ?? $id_trigger;

                        // Pastikan admin memang men-setting id_menu di promo ini
                        if (empty($id_trigger)) {
                            throw new Exception("Sistem error: Promo ini tidak memiliki pengaturan menu prasyarat.");
                        }

                        // Cek apakah menu syarat (misal: Espresso) ADA di dalam list yang di-checkout (Nasi Goreng)
                        if (in_array($id_trigger, $selected_menu) && isset($_SESSION['keranjang'][$id_trigger])) {
                            $qty_dibeli = $_SESSION['keranjang'][$id_trigger];

                            // Cek apakah jumlah pembeliannya sesuai syarat
                            if ($qty_dibeli >= $min_beli) {
                                $bonus_diberikan = true;
                                $id_bonus_promo = $id_bonus;
                                $catatan .= "\n[PROMO APPLIED: Free " . $promo['nama_promo'] . "]";
                            } else {
                                throw new Exception("Syarat minimal beli " . $min_beli . " porsi belum terpenuhi untuk promo ini.");
                            }
                        } else {
                            // JIKA TIDAK ADA ESPRESSO DI KERANJANG, OTOMATIS DITOLAK DI SINI
                            throw new Exception("Promo Ditolak: Anda harus membeli menu prasyarat (Menu Khusus Promo) terlebih dahulu.");
                        }
                    }

                    if ($total_final < 0) $total_final = 0;
                }
            }
            // --- END LOGIKA PROMO ---
            // --- END LOGIKA PROMO ---

            // INSERT PESANAN UTAMA 
            $this->model->insertPesanan([
                $id_pesanan,
                $id_member,
                $id_meja,
                $id_promo_dipakai,
                $tgl_pesanan,
                $total_final,
                $tipe_pemesanan,
                $metode_bayar,
                $catatan
            ]);

            // INSERT DETAIL MENU & KURANGI STOK
            foreach ($selected_menu as $id_m) {
                if (isset($_SESSION['keranjang'][$id_m])) {
                    $qty = $_SESSION['keranjang'][$id_m];
                    $m = $this->model->getMenuById($id_m);

                    if ($m) {
                        $sub = $m['harga'] * $qty;
                        $this->model->insertDetailMenu([$id_pesanan, $id_m, $qty, $sub]);

                        // Kurangi stok menu
                        $this->conn->prepare("UPDATE tb_menu SET stok = stok - ? WHERE id_menu = ?")->execute([$qty, $id_m]);
                        // Update status jika habis
                        $this->conn->prepare("UPDATE tb_menu SET status_menu = 'Habis' WHERE id_menu = ? AND stok <= 0")->execute([$id_m]);

                        unset($_SESSION['keranjang'][$id_m]);
                    }
                }

                // EKSEKUSI PENAMBAHAN MENU BONUS (Harga Rp 0)
                if ($bonus_diberikan && $id_bonus_promo) {
                    // Insert ke tabel detail pesanan dengan harga subtotal 0
                    $this->model->insertDetailMenu([$id_pesanan, $id_bonus_promo, 1, 0]);

                    // Kurangi stok menu bonus
                    $this->conn->prepare("UPDATE tb_menu SET stok = stok - 1 WHERE id_menu = ?")->execute([$id_bonus_promo]);

                    // Update status jika menu bonus jadi habis
                    $this->conn->prepare("UPDATE tb_menu SET status_menu = 'Habis' WHERE id_menu = ? AND stok <= 0")->execute([$id_bonus_promo]);
                }
            }

            // INSERT DETAIL FASILITAS
            foreach ($selected_fasilitas as $id_f) {
                if (isset($_SESSION['keranjang_fasilitas'][$id_f])) {
                    $item = $_SESSION['keranjang_fasilitas'][$id_f];
                    $f = $this->model->getFasilitasById($id_f);

                    if ($f) {
                        $sub_f = $f['harga'] * $item['pengali'];
                        $jam_selesai_db = isset($item['jam_selesai']) ? $item['jam_selesai'] : null;

                        $this->model->insertFasilitas([
                            $id_pesanan,
                            $id_f,
                            $item['tgl_sewa'],
                            $item['jam_mulai'],
                            $jam_selesai_db,
                            ($item['satuan'] == 'Jam') ? $item['pengali'] : null,
                            ($item['satuan'] == 'Orang') ? $item['pengali'] : null,
                            $sub_f
                        ]);
                        unset($_SESSION['keranjang_fasilitas'][$id_f]);
                    }
                }
            }

            // (Kode penambahan poin yang salah letak sudah dihapus sepenuhnya dari sini)

            // UPDATE STATUS MEJA (Jika pilih meja)
            if (!empty($id_meja)) {
                $this->model->updateMeja($id_meja);
            }

            $this->model->clearKeranjang($id_member);
            $this->model->commit();

            if ($metode_bayar == 'Transfer') {
                $amount = (int)ceil($total_final);

                if ($amount <= 0) {
                    die("Error: Total belanja tidak valid (Rp 0). Silakan cek keranjang Anda.");
                }
                require_once dirname(__FILE__) . '/../midtrans/Midtrans.php';
                // Konfigurasi Kunci API
                \Midtrans\Config::$serverKey = 'Mid-server-Dd4FZEE0vr-iZ4DiNLEmMVhM';
                \Midtrans\Config::$isProduction = false; // Karena pakai Sandbox
                \Midtrans\Config::$isSanitized = true;
                \Midtrans\Config::$is3ds = true;
                // 1. Buat parameter untuk dikirim ke Midtrans
                $params = array(
                    'transaction_details' => array(
                        'order_id' => $id_pesanan,
                        'gross_amount' => $amount, // Midtrans butuh angka bulat
                    ),
                    'customer_details' => array(
                        'first_name' => $_SESSION['nama'],
                        'phone' => $u['no_telp'], // $u sudah dideklarasikan di atasnya
                    ),
                );

                try {
                    // 2. Dapatkan Snap Token dari Midtrans
                    $snapToken = \Midtrans\Snap::getSnapToken($params);

                    // 3. Simpan token ke database
                    $stmt_token = $this->conn->prepare("UPDATE tb_pesanan SET snap_token = ? WHERE id_pesanan = ?");
                    $stmt_token->execute([$snapToken, $id_pesanan]);

                    // 4. Arahkan ke halaman pembayaran Midtrans
                    echo "<script>window.location='index.php?controller=checkout&action=bayar_midtrans&id=$id_pesanan';</script>";
                    exit;
                } catch (Exception $e) {
                    die("Gagal mendapatkan token pembayaran: " . $e->getMessage());
                }
            } else {
                echo "<script>alert('Berhasil! Pesanan Anda telah dibuat.'); window.location='index.php?controller=pelanggan&action=profil&tab=riwayat';</script>";
            }
        } catch (Exception $e) {
            $this->model->rollback();
            die("Error saat memproses pesanan: " . $e->getMessage());
        }
    }

    public function bayar_midtrans()
    {
        $this->startSession();
        require_once dirname(__FILE__) . '/../midtrans/Midtrans.php';
        if (!isset($_SESSION['id_member'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $id_pesanan = $_GET['id'] ?? null;
        $id_member = $_SESSION['id_member'];

        // Ambil data pesanan dan snap_token
        $stmt = $this->conn->prepare("SELECT id_pesanan, total_transaksi, snap_token FROM tb_pesanan WHERE id_pesanan = ? AND id_member = ?");
        $stmt->execute([$id_pesanan, $id_member]);
        $pesanan = $stmt->fetch();

        if (!$pesanan || empty($pesanan['snap_token'])) {
            echo "<script>alert('Pesanan tidak valid atau token pembayaran tidak ditemukan.'); window.location='index.php?controller=pelanggan&action=profil';</script>";
            exit;
        }

        require_once __DIR__ . '/../views/pelanggan/bayar_midtrans.php';
    }

    // API AJAX UNTUK CEK KODE & KLIK PROMO DI HALAMAN CHECKOUT
    public function cekPromo()
    {
        $this->startSession();

        // 1. Mulai penjagaan output agar PHP Warning tidak bocor ke JSON
        ob_start();

        header('Content-Type: application/json');

        if (!isset($_SESSION['id_member'])) {
            ob_clean(); // Bersihkan memori output sebelum membuang JSON
            echo json_encode(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.']);
            exit;
        }

        $id_member = $_SESSION['id_member'];
        $kode_promo = trim($_POST['kode_promo'] ?? '');
        $id_promo_post = $_POST['id_promo'] ?? '';
        $total_belanja = $_POST['total_belanja'] ?? 0;

        // 2. Pencarian Promo 
        if (!empty($kode_promo)) {
            $stmt = $this->conn->prepare("SELECT * FROM tb_promo WHERE kode_promo = ? AND status_promo = 'Aktif'");
            $stmt->execute([$kode_promo]);
        } else if (!empty($id_promo_post)) {
            $stmt = $this->conn->prepare("SELECT * FROM tb_promo WHERE id_promo = ? AND status_promo = 'Aktif'");
            $stmt->execute([$id_promo_post]);
        } else {
            ob_clean();
            echo json_encode(['status' => 'error', 'message' => 'Kode atau ID Promo tidak ditemukan.']);
            exit;
        }

        $promo = $stmt->fetch();

        if (!$promo) {
            ob_clean();
            echo json_encode(['status' => 'error', 'message' => 'Promo tidak valid atau sudah kadaluarsa.']);
            exit;
        }

        // [VALIDASI PROMO]

        // 3. Cek Tanggal
        if (isset($promo['tgl_selesai']) && strtotime($promo['tgl_selesai']) < strtotime(date('Y-m-d'))) {
            ob_clean();
            echo json_encode(['status' => 'error', 'message' => 'Promo ini sudah berakhir.']);
            exit;
        }

        // 4. Cek Min Belanja (Pakai ?? 0 untuk mencegah error jika NULL)
        if ($total_belanja < ($promo['min_belanja'] ?? 0)) {
            ob_clean();
            echo json_encode(['status' => 'error', 'message' => 'Total belanja Anda belum mencapai minimum (Rp ' . number_format($promo['min_belanja'] ?? 0, 0, ',', '.') . ').']);
            exit;
        }

        // 5. Cek Saldo Poin (Jika Tipe = Tukar Poin)
        if (($promo['tipe_promo'] ?? '') == 'Tukar_Poin' && ($promo['min_poin'] ?? 0) > 0) {
            $stmt_m = $this->conn->prepare("SELECT poin FROM tb_member WHERE id_member = ?");
            $stmt_m->execute([$id_member]);
            $m_data = $stmt_m->fetch();

            if ($m_data['poin'] < $promo['min_poin']) {
                ob_clean();
                echo json_encode(['status' => 'error', 'message' => 'Poin Anda tidak mencukupi untuk promo ini. Butuh ' . $promo['min_poin'] . ' poin.']);
                exit;
            }
        }

        // 6. Cek History Penggunaan (1x pakai per user)
        $stmt_cek = $this->conn->prepare("SELECT id_pesanan FROM tb_pesanan WHERE id_member = ? AND id_promo = ? AND status != 'Dibatalkan'");
        $stmt_cek->execute([$id_member, $promo['id_promo']]);
        if ($stmt_cek->rowCount() > 0) {
            ob_clean();
            echo json_encode(['status' => 'error', 'message' => 'Anda sudah pernah menggunakan promo ini. Promo hanya berlaku 1x per pelanggan.']);
            exit;
        }

        // 7. VALIDASI PRODUK (Mencegah Undefined Array Key pada tipe Produk)
        if (($promo['tipe_potongan'] ?? '') == 'Produk') {
            
            $id_trigger = $promo['id_menu_trigger'] ?? null; // ID Produk Syarat dari Database
            $min_beli = $promo['min_beli'] ?? 1;      // Minimal jumlah yang harus dibeli

            // Ambil daftar menu yang dipilih dari POST (ini dikirim dari halaman checkout)
            // Isinya biasanya: [id_menu => qty, id_menu => qty] atau [id_menu, id_menu]
            $selected_menu = $_POST['selected_menu'] ?? [];

            // Jika id_trigger tidak ditentukan di admin, lewati validasi ini
            if ($id_trigger) {
                
                $ditemukan = false;
                $qty_beli = 0;

                // Cek apakah produk syarat ada di dalam daftar yang di-checkout
                // Kita periksa baik jika selected_menu berupa array biasa atau array asosiatif (ID => Qty)
                if (array_key_exists($id_trigger, $selected_menu)) {
                    $ditemukan = true;
                    $qty_beli = $selected_menu[$id_trigger];
                } elseif (in_array($id_trigger, $selected_menu)) {
                    // Jika data yang dikirim hanya ID menu saja tanpa Qty
                    $ditemukan = true;
                    // Ambil qty dari session keranjang untuk validasi jumlah
                    $qty_beli = $_SESSION['keranjang'][$id_trigger] ?? 0;
                }

                // VALIDASI 1: Apakah produknya ada di daftar checkout?
                if (!$ditemukan) {
                    ob_clean();
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Voucher gagal! Anda harus menyertakan menu prasyarat di checkout untuk menggunakan promo ini.'
                    ]);
                    exit;
                }

                // VALIDASI 2: Apakah jumlah (QTY) produk tersebut memenuhi syarat?
                if ($qty_beli < $min_beli) {
                    ob_clean();
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Voucher ini membutuhkan pembelian menu prasyarat minimal ' . $min_beli . ' porsi.'
                    ]);
                    exit;
                }
            }
        }
        
        $nama_bonus = "";
        if (($promo['tipe_potongan'] ?? '') == 'Produk') {
            $id_bonus = $promo['id_menu_bonus'] ?? null;
            if ($id_bonus) {
                $stmtMenu = $this->conn->prepare("SELECT nama_menu FROM tb_menu WHERE id_menu = ?");
                $stmtMenu->execute([$id_bonus]);
                $menu = $stmtMenu->fetch();
                if ($menu) {
                    $nama_bonus = $menu['nama_menu'];
                }
            }
        }

        // 8. DATA BERHASIL DIKLAIM
        $response = [
            'status' => 'success',
            'message' => 'Promo berhasil digunakan!',
            'data' => [
                'id_promo'        => $promo['id_promo'],
                'nama_promo'      => $promo['nama_promo'] ?? 'Promo',
                'tipe_potongan'   => $promo['tipe_potongan'] ?? 'Nominal',
                'potongan'        => $promo['potongan'] ?? 0,
                'min_poin'        => $promo['min_poin'] ?? 0,
                'tipe_penggunaan' => $promo['tipe_promo'] ?? 'Umum',
                // Trik aman mengambil deskripsi atau nama promo
                'info_produk'     => !empty($promo['deskripsi']) ? $promo['deskripsi'] : ($promo['nama_promo'] ?? 'Promo Spesial'),
                'nilai_asli_potongan' => $promo['potongan'] ?? 0,
                'nama_bonus'          => $nama_bonus
            ]
        ];

        // Buang teks kotor PHP (jika ada error warning sebelumnya) lalu kirim respon murni
        ob_clean();
        echo json_encode($response);
        exit;
    }

    // HALAMAN UPLOAD BUKTI TRANSFER
    public function pembayaran()
    {
        $this->startSession();
        if (!isset($_SESSION['id_member'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $id_p = $_GET['id'] ?? null;
        $id_m = $_SESSION['id_member'];

        // PERBAIKAN: Tambahkan id_pesanan di dalam query SELECT ini
        $stmt = $this->conn->prepare("SELECT id_pesanan, total_transaksi, metode_pembayaran FROM tb_pesanan WHERE id_pesanan = ? AND id_member = ?");
        $stmt->execute([$id_p, $id_m]);
        $pesanan = $stmt->fetch();

        if (!$pesanan || trim($pesanan['metode_pembayaran']) !== 'Transfer') {
            echo "<script>alert('Akses ditolak atau pesanan tidak ditemukan.'); window.location='index.php?controller=pelanggan&action=profil';</script>";
            exit;
        }

        require_once 'layout/header.php';
        require_once __DIR__ . '/../views/pelanggan/pembayaran.php';
    }

    // PROSES UPLOAD FILE BUKTI
    public function prosesPembayaran()
    {
        $this->startSession();
        if (!isset($_SESSION['id_member'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_pesanan = $_POST['id_pesanan'] ?? 'UNKNOWN';

            // Siapkan link untuk kembali ke form jika terjadi error
            $url_back = "index.php?controller=checkout&action=pembayaran&id=" . $id_pesanan;

            // 1. Validasi apakah file ada dan berhasil di-upload ke temporary server
            if (!isset($_FILES['bukti_bayar']) || $_FILES['bukti_bayar']['error'] !== UPLOAD_ERR_OK) {
                $err_code = $_FILES['bukti_bayar']['error'] ?? 'Unknown';
                $msg = 'Gagal upload. ';

                // Jika error karena ukuran file melampaui limit php.ini (biasanya 2MB)
                if ($err_code == UPLOAD_ERR_INI_SIZE || $err_code == UPLOAD_ERR_FORM_SIZE) {
                    $msg .= 'Ukuran file gambar terlalu besar (Maksimal 2MB). Silakan kompres foto Anda.';
                } else {
                    $msg .= 'Harap pilih file gambar bukti transfer yang valid. (Kode Error: ' . $err_code . ')';
                }
                echo "<script>alert('$msg'); window.location.href='$url_back';</script>";
                exit;
            }

            // 2. Validasi Ekstensi File
            $ext = strtolower(pathinfo($_FILES['bukti_bayar']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($ext, $allowed)) {
                echo "<script>alert('Format gambar tidak didukung! Hanya diperbolehkan JPG, JPEG, PNG, dan WEBP.'); window.location.href='$url_back';</script>";
                exit;
            }

            // 3. Proses Pemindahan File
            $ext = strtolower(pathinfo($_FILES['bukti_bayar']['name'], PATHINFO_EXTENSION));

            $clean_id = preg_replace("/[^a-zA-Z0-9]/", "", $id_pesanan);
            $nama_file = "TF-" . $clean_id . "-" . time() . "." . $ext;

            $tmp = $_FILES['bukti_bayar']['tmp_name'];
            $folder_tujuan = __DIR__ . '/../assets/gambar/bukti_bayar/';

            $folder_tujuan = __DIR__ . '/../assets/gambar/bukti_bayar/';
            if (!is_dir($folder_tujuan)) {
                mkdir($folder_tujuan, 0777, true);
            }

            $path = $folder_tujuan . $nama_file;

            // Pindahkan dari temporary ke folder aplikasi kita
            if (move_uploaded_file($tmp, $path)) {

                // 4. Update Database dengan Try-Catch agar tidak error fatal
                try {
                    $stmt = $this->conn->prepare("UPDATE tb_pesanan SET bukti_bayar = ? WHERE id_pesanan = ? AND id_member = ?");
                    $stmt->execute([$nama_file, $id_pesanan, $_SESSION['id_member']]);

                    echo "<script>alert('Bukti transfer berhasil diunggah! Pesanan Anda akan segera dikonfirmasi Admin.'); window.location='index.php?controller=pelanggan&action=profil&tab=riwayat';</script>";
                    exit;
                } catch (PDOException $e) {
                    // Jika database error, hapus gambar yang telanjur masuk folder agar tidak nyampah
                    if (file_exists($path)) unlink($path);

                    $err_db = addslashes($e->getMessage());
                    echo "<script>alert('Terjadi kesalahan pada Database: $err_db'); window.location.href='$url_back';</script>";
                    exit;
                }
            } else {
                // Tangkap pesan sistem bawaan PHP jika move_uploaded_file gagal (misal izin folder)
                $sys_err = error_get_last();
                $msg_err = $sys_err ? addslashes($sys_err['message']) : 'Tidak ada akses tulis ke folder server.';
                echo "<script>alert('Gagal memindahkan file ke server. Info: $msg_err'); window.location.href='$url_back';</script>";
                exit;
            }
        }
    }

    public function tukarPoin()
    {
        $this->startSession();
        if (!isset($_SESSION['id_member'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $id_m = $_SESSION['id_member'];

        // 1. Ambil data poin member terbaru
        $stmt = $this->conn->prepare("SELECT poin FROM tb_member WHERE id_member = ?");
        $stmt->execute([$id_m]);
        $member = $stmt->fetch();

        $query = "SELECT * FROM tb_promo 
              WHERE tipe_promo = 'Tukar Poin' 
              AND status_promo = 'Aktif' 
              AND (kuota IS NULL OR kuota > 0)
              ORDER BY min_poin ASC";
        $promos = $this->conn->query($query)->fetchAll();

        require_once __DIR__ . '/../views/pelanggan/tukar_poin.php';
    }

    public function klaimPoin()
    {
        $this->startSession();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_m = $_SESSION['id_member'];
            $id_promo = $_POST['id_promo'];

            // ambil promo
            $stmt = $this->conn->prepare("SELECT * FROM tb_promo WHERE id_promo = ?");
            $stmt->execute([$id_promo]);
            $promo = $stmt->fetch();

            // ambil poin user
            $stmt_m = $this->conn->prepare("SELECT poin FROM tb_member WHERE id_member = ?");
            $stmt_m->execute([$id_m]);
            $member = $stmt_m->fetch();

            if ($member['poin'] < $promo['min_poin']) {
                echo "<script>alert('Poin tidak cukup!'); window.history.back();</script>";
                exit;
            }

            try {
                $this->conn->beginTransaction();

                // kurangi poin
                $upd = $this->conn->prepare("UPDATE tb_member SET poin = poin - ? WHERE id_member = ?");
                $upd->execute([$promo['min_poin'], $id_m]);

                // simpan history
                $hist = $this->conn->prepare("INSERT INTO tb_history_poin (id_member, poin, tipe, keterangan, tgl_perubahan) VALUES (?, ?, 'Keluar', ?, NOW())");
                $hist->execute([$id_m, -$promo['min_poin'], "Tukar Reward: " . $promo['nama_promo']]);

                $this->conn->commit();

                header("Location: index.php?controller=checkout&action=tukarPoin");
                exit;
            } catch (Exception $e) {
                $this->conn->rollBack();
                die($e->getMessage());
            }
        }
    }

    public function riwayat_poin()
    {
        $this->startSession();
        if (!isset($_SESSION['id_member'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $id_member = $_SESSION['id_member'];

        // Ambil data poin dari tb_history_poin
        $stmt = $this->conn->prepare("SELECT * FROM tb_history_poin WHERE id_member = ? ORDER BY tgl_perubahan DESC");
        $stmt->execute([$id_member]);
        $history = $stmt->fetchAll();

        require_once __DIR__ . '/../views/pelanggan/riwayat_poin.php';
    }
}
