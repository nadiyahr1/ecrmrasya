<?php
require_once __DIR__ . '/../config/koneksi.php';

class AdminController
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Proteksi login admin
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
    }

    // DASHBOARD ADMIN
    public function index()
    {
        $hari_ini = date('Y-m-d');

        // 1. Ambil Data Statistik (Gunakan $this->conn)
        $pesanan_hari_ini = $this->conn->query("SELECT COUNT(*) FROM tb_pesanan WHERE DATE(tgl_pesanan) = '$hari_ini'")->fetchColumn();
        $member_baru      = $this->conn->query("SELECT COUNT(*) FROM tb_member WHERE DATE(tgl_daftar) = '$hari_ini'")->fetchColumn();
        $omzet_harian     = $this->conn->query("SELECT SUM(total_transaksi) FROM tb_pesanan WHERE DATE(tgl_pesanan) = '$hari_ini' AND status = 'Selesai'")->fetchColumn() ?: 0;

        $stok_menipis   = $this->conn->query("SELECT * FROM tb_menu WHERE stok < 10 ORDER BY stok ASC")->fetchAll();
        $jml_stok_tipis = count($stok_menipis);

        // 2. Ambil Data Pesanan Menunggu
        $pesanan_menunggu = $this->conn->query("SELECT p.*, m.nama_member 
                                          FROM tb_pesanan p 
                                          LEFT JOIN tb_member m ON p.id_member = m.id_member 
                                          WHERE p.status = 'Menunggu Konfirmasi' OR p.status = '' OR p.status IS NULL
                                          ORDER BY p.tgl_pesanan ASC LIMIT 5")->fetchAll();

        // 3. Ambil Notifikasi untuk Header
        $notif_pesanan = $this->conn->query("SELECT COUNT(*) FROM tb_pesanan WHERE status = 'Menunggu Konfirmasi'")->fetchColumn();

        // Menghitung member yang perlu diverifikasi
        // Asumsi: di tb_member ada kolom 'status' (Aktif/Pending)
        $member_pending = $this->conn->query("SELECT COUNT(*) FROM tb_member WHERE status_akun = 'Pending'")->fetchColumn();
        
        // Ambil daftar 5 member terbaru yang pending untuk ditampilkan di info
        $list_pending = $this->conn->query("SELECT * FROM tb_member WHERE status_akun = 'Pending' ORDER BY tgl_daftar DESC LIMIT 5")->fetchAll();
        
        $page = 'dashboard';

        // 4. Panggil View (Urutan: Header -> Isi -> Footer)
        require_once 'views/admin/header.php';
        require_once 'views/admin/dashboard.php';
        require_once 'views/admin/footer.php';
    }

    public function profil()
    {
        $id_user = $_SESSION['id_user'];
        $stmt = $this->conn->prepare("SELECT * FROM tb_user WHERE id_user = ?");
        $stmt->execute([$id_user]);
        $user = $stmt->fetch();
        $page = 'profil';
        require_once 'views/admin/header.php';
        require_once 'views/admin/profil.php';
        require_once 'views/admin/footer.php';
    }

    public function proses_edit_profil()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_user = $_SESSION['id_user'];
            $username = htmlspecialchars(trim($_POST['username']));
            $nama = htmlspecialchars(trim($_POST['nama']));
            $password_baru = trim($_POST['password_baru']);

            try {
                // Validasi username agar tidak kembar dengan admin lain
                $cek = $this->conn->prepare("SELECT id_user FROM tb_user WHERE username = ? AND id_user != ?");
                $cek->execute([$username, $id_user]);
                if ($cek->rowCount() > 0) {
                    throw new Exception("Username sudah digunakan oleh admin lain.");
                }

                if (!empty($password_baru)) {
                    // Sesuaikan enkripsi (md5 atau password_hash) dengan sistem loginmu
                    $pass_db = password_hash($password_baru, PASSWORD_DEFAULT);
                    $stmt = $this->conn->prepare("UPDATE tb_user SET username = ?, nama_user = ?, password = ? WHERE id_user = ?");
                    $stmt->execute([$username, $nama, $pass_db, $id_user]);
                } else {
                    $stmt = $this->conn->prepare("UPDATE tb_user SET username = ?, nama_user = ? WHERE id_user = ?");
                    $stmt->execute([$username, $nama, $id_user]);
                }

                $_SESSION['nama'] = $nama;
                echo "<script>alert('Profil Admin berhasil diperbarui!'); window.location='index.php?controller=admin&action=profil';</script>";
            } catch (Exception $e) {
                $err = addslashes($e->getMessage());
                echo "<script>alert('Gagal: $err'); window.history.back();</script>";
            }
        }
    }

    public function verifikasiPelanggan()
    {
        // Pastikan session sudah berjalan
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Proteksi akses hanya untuk Admin
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $id_member = $_GET['id'] ?? null;

        if ($id_member) {
            try {
                // Update status akun menjadi Aktif
                $stmt = $this->conn->prepare("UPDATE tb_member SET status_akun = 'Aktif' WHERE id_member = ?");
                $stmt->execute([$id_member]);

                echo "<script>alert('Akun pelanggan berhasil diverifikasi dan diaktifkan!'); window.location='index.php?controller=admin&action=data_pelanggan';</script>";
            } catch (PDOException $e) {
                echo "<script>alert('Terjadi kesalahan saat memverifikasi akun.'); window.location='index.php?controller=admin&action=data_pelanggan';</script>";
            }
        } else {
            echo "<script>alert('ID Pelanggan tidak valid!'); window.location='index.php?controller=admin&action=data_pelanggan';</script>";
        }
    }

    // MANAJEMEN PESANAN (Integrasi Bukti Transfer & Poin)
    // MANAJEMEN PESANAN (Tampil, Filter, Pagination, Update Status)
    public function data_pesanan()
    {
        // 1. LOGIKA UPDATE STATUS PESANAN
        if (isset($_POST['update_status'])) {
            $id_p = $_POST['id_pesanan'];
            $st_baru = $_POST['status_baru'];

            if ($st_baru == 'Selesai') {
                // Lempar ke method selesaikan_pesanan untuk hitung poin
                header("Location: index.php?controller=admin&action=selesaikan_pesanan&id=$id_p");
                exit;
            } else {
                $stmt = $this->conn->prepare("UPDATE tb_pesanan SET status = ? WHERE id_pesanan = ?");
                if ($stmt->execute([$st_baru, $id_p])) {
                    $tab = $_GET['tab'] ?? 'Semua';
                    $hal = $_GET['halaman'] ?? 1;
                    echo "<script>alert('Status Berhasil Diperbarui!'); window.location.href='index.php?controller=admin&action=data_pesanan&tab=$tab&halaman=$hal';</script>";
                    exit;
                }
            }
        }

        // 2. TANGKAP INPUTAN FILTER & PAGINATION
        $status_filter = $_GET['tab'] ?? 'Semua';
        $search        = trim($_GET['search'] ?? '');
        $limit         = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $halaman_aktif = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
        $offset        = ($halaman_aktif - 1) * $limit;

        // 3. SUSUN QUERY DINAMIS
        $where_sql = "1=1";
        $params = [];

        if ($status_filter == 'Menunggu Konfirmasi') {
            $where_sql .= " AND (p.status = 'Menunggu Konfirmasi' OR p.status = '' OR p.status IS NULL)";
        } elseif ($status_filter == 'Sedang Diproses') {
            $where_sql .= " AND p.status IN ('Konfirmasi', 'Sedang Diproses', 'Pesanan Siap')";
        } elseif ($status_filter == 'Selesai') {
            $where_sql .= " AND p.status = 'Selesai'";
        } elseif ($status_filter == 'Dibatalkan') {
            $where_sql .= " AND p.status = 'Dibatalkan'";
        }

        if (!empty($search)) {
            $where_sql .= " AND (p.id_pesanan LIKE ? OR m.nama_member LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        // 4. HITUNG TOTAL DATA UNTUK PAGINATION
        $stmt_count = $this->conn->prepare("SELECT COUNT(*) FROM tb_pesanan p LEFT JOIN tb_member m ON p.id_member = m.id_member WHERE $where_sql");
        $stmt_count->execute($params);
        $total_data = $stmt_count->fetchColumn();
        $total_halaman = ceil($total_data / $limit);

        // 5. AMBIL DATA PESANAN
        $query = "SELECT p.*, m.nama_member, mj.no_meja 
                  FROM tb_pesanan p 
                  LEFT JOIN tb_member m ON p.id_member = m.id_member 
                  LEFT JOIN tb_meja mj ON p.id_meja = mj.id_meja
                  WHERE $where_sql 
                  ORDER BY p.tgl_pesanan DESC 
                  LIMIT $limit OFFSET $offset";
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $pesanan = $stmt->fetchAll();

        $page = 'data_pesanan';

        // 6. PANGGIL VIEW
        require_once 'views/admin/header.php';
        require_once 'views/admin/data_pesanan.php';
        require_once 'views/admin/footer.php';
    }

    public function selesaikan_pesanan()
    {
        $id_p = $_GET['id'] ?? null;
        if (!$id_p) {
            header("Location: index.php?controller=admin&action=data_pesanan");
            exit;
        }

        try {
            $this->conn->beginTransaction();

            // 1. Ambil data pesanan
            $stmt = $this->conn->prepare("SELECT id_member, total_transaksi, status FROM tb_pesanan WHERE id_pesanan = ?");
            $stmt->execute([$id_p]);
            $p = $stmt->fetch();

            // Jalankan hanya jika pesanan ditemukan dan statusnya belum Selesai
            if ($p && $p['status'] !== 'Selesai') {

                // 2. Update Status Pesanan jadi Selesai
                $upd = $this->conn->prepare("UPDATE tb_pesanan SET status = 'Selesai' WHERE id_pesanan = ?");
                $upd->execute([$id_p]);

                // 3. LOGIKA POIN & LEVEL (E-CRM)
                if (!empty($p['id_member'])) {
                    $id_m = $p['id_member'];
                    $total_final = $p['total_transaksi'];

                    // A. Update Progress Belanja Member (jml_transaksi & total_belanja tetap perlu untuk level)
                    $sql_up_member = "UPDATE tb_member SET jml_transaksi = jml_transaksi + 1, total_belanja = total_belanja + ? WHERE id_member = ?";
                    $this->conn->prepare($sql_up_member)->execute([$total_final, $id_m]);

                    // B. Hitung Poin Berdasarkan Bonus Level
                    $stmt_bonus = $this->conn->prepare("SELECT l.bonus_poin FROM tb_member m JOIN tb_level_member l ON m.id_level = l.id_level WHERE m.id_member = ?");
                    $stmt_bonus->execute([$id_m]);
                    $res_bonus = $stmt_bonus->fetch();
                    $multiplier = $res_bonus['bonus_poin'] ?? 1;

                    // Rumus: Kelipatan 1000 dikali multiplier level
                    $poin_baru = floor($total_final / 1000) * $multiplier;

                    if ($poin_baru > 0) {
                        // UPDATE: Hanya menambah ke kolom 'poin'
                        $upd_poin = $this->conn->prepare("UPDATE tb_member SET poin = poin + ? WHERE id_member = ?");
                        $upd_poin->execute([$poin_baru, $id_m]);

                        // Catat di History Poin
                        $ket = "Poin belanja pesanan #$id_p (Bonus x$multiplier)";
                        $hist = $this->conn->prepare("INSERT INTO tb_history_poin (id_member, poin, tipe, keterangan, tgl_perubahan) VALUES (?, ?, 'Masuk', ?, NOW())");
                        $hist->execute([$id_m, $poin_baru, $ket]);
                    }

                    // Logika Kenaikan Level Otomatis berdasarkan total_belanja / jml_transaksi
                    $stmt_refresh = $this->conn->prepare("SELECT jml_transaksi, total_belanja FROM tb_member WHERE id_member = ?");
                    $stmt_refresh->execute([$id_m]);
                    $m_baru = $stmt_refresh->fetch();

                    $stmt_lvl = $this->conn->prepare("SELECT id_level FROM tb_level_member WHERE min_transaksi <= ? OR min_belanja <= ? ORDER BY id_level DESC LIMIT 1");
                    $stmt_lvl->execute([$m_baru['jml_transaksi'], $m_baru['total_belanja']]);
                    $lvl_data = $stmt_lvl->fetch();

                    if ($lvl_data) {
                        $this->conn->prepare("UPDATE tb_member SET id_level = ? WHERE id_member = ?")->execute([$lvl_data['id_level'], $id_m]);
                    }
                }
            }

            $this->conn->commit();
            echo "<script>alert('Pesanan Selesai. Poin telah ditambahkan ke saldo member.'); window.location='index.php?controller=admin&action=data_pesanan&tab=Selesai';</script>";
        } catch (Exception $e) {
            $this->conn->rollBack();
            die("Gagal memproses poin: " . $e->getMessage());
        }
    }

    public function detail_pesanan()
    {
        $id_p = $_GET['id'] ?? null;
        if (!$id_p) die("ID Pesanan tidak ditemukan.");

        // 1. AMBIL DATA PESANAN UTAMA
        $query = "SELECT p.*, m.nama_member, m.no_telp, l.nama_level, 
                         pr.nama_promo, pr.potongan as nilai_potongan, mj.no_meja 
                  FROM tb_pesanan p 
                  LEFT JOIN tb_member m ON p.id_member = m.id_member 
                  LEFT JOIN tb_level_member l ON m.id_level = l.id_level
                  LEFT JOIN tb_promo pr ON p.id_promo = pr.id_promo
                  LEFT JOIN tb_meja mj ON p.id_meja = mj.id_meja
                  WHERE p.id_pesanan = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id_p]);
        $p = $stmt->fetch();

        if (!$p) die("Data pesanan tidak ditemukan.");

        // 2. AMBIL RINCIAN MENU
        $menus = $this->conn->prepare("SELECT dp.*, mn.nama_menu, mn.harga 
                                       FROM tb_detail_pesanan dp 
                                       JOIN tb_menu mn ON dp.id_menu = mn.id_menu 
                                       WHERE dp.id_pesanan = ?");
        $menus->execute([$id_p]);
        $daftar_menu = $menus->fetchAll();

        // 3. AMBIL RINCIAN FASILITAS (SUDAH DISESUAIKAN KE KOLOM: subtotal_sewa)
        $fasilitas = $this->conn->prepare("SELECT bf.*, f.nama_fasilitas, f.satuan, f.harga as harga_f 
                                         FROM tb_booking_fasilitas bf 
                                         JOIN tb_fasilitas f ON bf.id_fasilitas = f.id_fasilitas 
                                         WHERE bf.id_pesanan = ?");
        $fasilitas->execute([$id_p]);
        $daftar_fasilitas = $fasilitas->fetchAll();

        require_once 'views/admin/detail_pesanan.php';
    }

    public function cetak_struk()
    {
        $id_p = $_GET['id'] ?? null;
        if (!$id_p) die("ID Pesanan tidak ditemukan.");

        // 1. AMBIL DATA PESANAN UTAMA
        $query = "SELECT p.*, m.nama_member, m.no_telp, l.nama_level, 
                         pr.nama_promo, mj.no_meja 
                  FROM tb_pesanan p 
                  LEFT JOIN tb_member m ON p.id_member = m.id_member 
                  LEFT JOIN tb_level_member l ON m.id_level = l.id_level
                  LEFT JOIN tb_promo pr ON p.id_promo = pr.id_promo
                  LEFT JOIN tb_meja mj ON p.id_meja = mj.id_meja
                  WHERE p.id_pesanan = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id_p]);
        $p = $stmt->fetch();

        if (!$p) die("Data pesanan tidak ditemukan.");

        // 2. AMBIL RINCIAN MENU
        $menus = $this->conn->prepare("SELECT dp.*, mn.nama_menu 
                                       FROM tb_detail_pesanan dp 
                                       JOIN tb_menu mn ON dp.id_menu = mn.id_menu 
                                       WHERE dp.id_pesanan = ?");
        $menus->execute([$id_p]);
        $daftar_menu = $menus->fetchAll();

        // 3. AMBIL RINCIAN FASILITAS
        $fasilitas = $this->conn->prepare("SELECT bf.*, f.nama_fasilitas 
                                         FROM tb_booking_fasilitas bf 
                                         JOIN tb_fasilitas f ON bf.id_fasilitas = f.id_fasilitas 
                                         WHERE bf.id_pesanan = ?");
        $fasilitas->execute([$id_p]);
        $daftar_fasilitas = $fasilitas->fetchAll();

        // 4. Panggil View Cetak Struk
        require_once 'views/admin/cetak_struk.php';
    }

    public function riwayat_poin_pelanggan()
    {
        $id_member = $_GET['id'] ?? null;
        if (!$id_member) die("ID Member tidak ditemukan.");

        // 1. AMBIL INFORMASI RINGKAS MEMBER (Hanya kolom 'poin')
        $stmt_m = $this->conn->prepare("SELECT m.nama_member, m.poin, l.nama_level 
                                  FROM tb_member m 
                                  JOIN tb_level_member l ON m.id_level = l.id_level 
                                  WHERE m.id_member = ?");
        $stmt_m->execute([$id_member]);
        $member = $stmt_m->fetch();

        if (!$member) {
            die("<center>Data member tidak ditemukan.</center>");
        }

        // 2. AMBIL RIWAYAT POIN
        $query = "SELECT * FROM tb_history_poin WHERE id_member = ? ORDER BY tgl_perubahan DESC";
        $stmt_h = $this->conn->prepare($query);
        $stmt_h->execute([$id_member]);
        $history = $stmt_h->fetchAll();

        // 3. Panggil View untuk Pop-up Modal
        require_once 'views/admin/riwayat_poin_pelanggan.php';
    }

    public function data_pelanggan()
    {
        // 1. TANGKAP INPUTAN PENCARIAN & PAGINATION
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $limit  = 10;
        $halaman_aktif = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
        $offset = ($halaman_aktif - 1) * $limit;

        // 2. QUERY HITUNG TOTAL MEMBER (Untuk Statistik & Pagination)
        $where_sql = "1=1";
        $params = [];
        if (!empty($search)) {
            $where_sql .= " AND (m.nama_member LIKE ? OR m.email LIKE ? OR m.no_telp LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $stmt_total = $this->conn->prepare("SELECT COUNT(*) FROM tb_member m WHERE $where_sql");
        $stmt_total->execute($params);
        $total_member = $stmt_total->fetchColumn();
        $total_halaman = ceil($total_member / $limit);

        // 3. STATISTIK TOTAL POIN BEREDAR (Menggunakan kolom 'poin')
        $tp = $this->conn->query("SELECT SUM(poin) FROM tb_member")->fetchColumn();
        $total_poin_beredar = $tp ?: 0;

        // 4. AMBIL DATA MEMBER & LEVEL (Tanpa l.diskon agar aman)
        $query = "SELECT m.*, l.nama_level 
                  FROM tb_member m 
                  LEFT JOIN tb_level_member l ON m.id_level = l.id_level 
                  WHERE $where_sql 
                  ORDER BY m.poin DESC, m.nama_member ASC 
                  LIMIT $limit OFFSET $offset";
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $members = $stmt->fetchAll();

        $page = 'data_pelanggan';

        // 5. PANGGIL VIEW
        require_once 'views/admin/header.php';
        require_once 'views/admin/data_pelanggan.php';
        require_once 'views/admin/footer.php';
    }

    // MANAJEMEN KATEGORI MENU 
    public function kategori_menu()
    {
        $kategori = $this->conn->query("SELECT * FROM tb_kategori ORDER BY id_kategori ASC")->fetchAll();

        $page = 'kategori_menu';
        require_once 'views/admin/header.php';
        require_once 'views/admin/kategori_menu.php';
        require_once 'views/admin/footer.php';
    }

    // CRUD KATEGORI MENU
    public function tambah_kategori()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama = $_POST['nama_kategori'];
            $stmt = $this->conn->prepare("INSERT INTO tb_kategori (nama_kategori) VALUES (?)");
            if ($stmt->execute([$nama])) {
                echo "<script>alert('Kategori berhasil ditambahkan!'); window.location.href='index.php?controller=admin&action=kategori_menu';</script>";
            } else {
                echo "<script>alert('Gagal menambahkan kategori!'); window.history.back();</script>";
            }
        }
    }

    public function edit_kategori()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id_kategori'];
            $nama = $_POST['nama_kategori'];
            $stmt = $this->conn->prepare("UPDATE tb_kategori SET nama_kategori = ? WHERE id_kategori = ?");
            if ($stmt->execute([$nama, $id])) {
                echo "<script>alert('Kategori berhasil diupdate!'); window.location.href='index.php?controller=admin&action=kategori_menu';</script>";
            } else {
                echo "<script>alert('Gagal mengupdate kategori!'); window.history.back();</script>";
            }
        }
    }

    public function hapus_kategori()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $cek = $this->conn->prepare("SELECT COUNT(*) FROM tb_menu WHERE id_kategori = ?");
            $cek->execute([$id]);
            if ($cek->fetchColumn() > 0) {
                echo "<script>alert('Gagal! Kategori ini masih digunakan oleh menu.'); window.location.href='index.php?controller=admin&action=kategori_menu';</script>";
            } else {
                $this->conn->prepare("DELETE FROM tb_kategori WHERE id_kategori = ?")->execute([$id]);
                echo "<script>alert('Kategori berhasil dihapus!'); window.location.href='index.php?controller=admin&action=kategori_menu';</script>";
            }
        }
    }

    // --- MANAJEMEN DAFTAR MENU ---
    public function menu()
    {
        // PENCARIAN & PAGINATION
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $limit  = 10;
        $halaman_aktif = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
        $offset = ($halaman_aktif - 1) * $limit;

        $where_sql = "1=1";
        $params = [];
        if (!empty($search)) {
            $where_sql .= " AND (m.nama_menu LIKE ? OR k.nama_kategori LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        // Hitung total untuk pagination
        $stmt_total = $this->conn->prepare("SELECT COUNT(*) FROM tb_menu m JOIN tb_kategori k ON m.id_kategori = k.id_kategori WHERE $where_sql");
        $stmt_total->execute($params);
        $total_data = $stmt_total->fetchColumn();
        $total_halaman = ceil($total_data / $limit);

        // AMBIL DATA MENU
        $query = "SELECT m.*, k.nama_kategori 
                  FROM tb_menu m 
                  JOIN tb_kategori k ON m.id_kategori = k.id_kategori 
                  WHERE $where_sql 
                  ORDER BY m.id_menu ASC LIMIT $limit OFFSET $offset";
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $menus = $stmt->fetchAll();

        // AMBIL KATEGORI UNTUK DROPDOWN MODAL
        $categories = $this->conn->query("SELECT * FROM tb_kategori ORDER BY nama_kategori ASC")->fetchAll();

        $page = 'menu';
        require_once 'views/admin/header.php';
        require_once 'views/admin/menu.php';
        require_once 'views/admin/footer.php';
    }

    // CRUD DAFTAR MENU
    public function tambah_menu()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama      = $_POST['nama_menu'];
            $id_kat    = $_POST['id_kategori'];
            $harga     = $_POST['harga'];
            $stok      = $_POST['stok'];

            // Logika Otomatisasi Status (Sesuai kesepakatan)
            $status_menu = ($stok > 0) ? 'Tersedia' : 'Habis';

            $foto_nama = $_FILES['foto']['name'];
            $tmp_name  = $_FILES['foto']['tmp_name'];
            $size      = $_FILES['foto']['size'];
            $ekstensi  = strtolower(pathinfo($foto_nama, PATHINFO_EXTENSION));

            $ekstensi_diizinkan = ['jpg', 'jpeg', 'png', 'webp'];
            $max_size = 2 * 1024 * 1024; // 2MB

            if (!in_array($ekstensi, $ekstensi_diizinkan)) {
                die("<script>alert('Format file tidak didukung!'); window.history.back();</script>");
            }
            if ($size > $max_size) {
                die("<script>alert('Ukuran file maksimal 2MB!'); window.history.back();</script>");
            }

            $nama_baru = "MNU-" . date('YmdHis') . "." . $ekstensi;
            if (move_uploaded_file($tmp_name, "assets/gambar/menu/" . $nama_baru)) {
                $stmt = $this->conn->prepare("INSERT INTO tb_menu (id_kategori, nama_menu, harga, stok, status_menu, foto) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$id_kat, $nama, $harga, $stok, $status_menu, $nama_baru]);
                echo "<script>alert('Menu berhasil ditambahkan!'); window.location.href='index.php?controller=admin&action=menu';</script>";
            } else {
                echo "<script>alert('Gagal upload foto!'); window.history.back();</script>";
            }
        }
    }

    public function edit_menu()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id        = $_POST['id_menu'];
            $nama      = $_POST['nama_menu'];
            $id_kat    = $_POST['id_kategori'];
            $harga     = $_POST['harga'];
            $stok      = $_POST['stok'];
            $foto_lama = $_POST['foto_lama'];

            $status_menu = ($stok > 0) ? 'Tersedia' : 'Habis';

            if (!empty($_FILES['foto']['name'])) {
                $foto_nama = $_FILES['foto']['name'];
                $tmp_name  = $_FILES['foto']['tmp_name'];
                $size      = $_FILES['foto']['size'];
                $ekstensi  = strtolower(pathinfo($foto_nama, PATHINFO_EXTENSION));

                if (!in_array($ekstensi, ['jpg', 'jpeg', 'png', 'webp']) || $size > (2 * 1024 * 1024)) {
                    die("<script>alert('Format salah atau ukuran lebih dari 2MB!'); window.history.back();</script>");
                }

                $nama_baru = "MNU-" . date('YmdHis') . "." . $ekstensi;
                if (move_uploaded_file($tmp_name, "assets/gambar/menu/" . $nama_baru)) {
                    if (!empty($foto_lama) && file_exists("assets/gambar/menu/" . $foto_lama)) {
                        unlink("assets/gambar/menu/" . $foto_lama);
                    }
                    $stmt = $this->conn->prepare("UPDATE tb_menu SET id_kategori=?, nama_menu=?, harga=?, stok=?, status_menu=?, foto=? WHERE id_menu=?");
                    $stmt->execute([$id_kat, $nama, $harga, $stok, $status_menu, $nama_baru, $id]);
                }
            } else {
                $stmt = $this->conn->prepare("UPDATE tb_menu SET id_kategori=?, nama_menu=?, harga=?, stok=?, status_menu=? WHERE id_menu=?");
                $stmt->execute([$id_kat, $nama, $harga, $stok, $status_menu, $id]);
            }
            echo "<script>alert('Menu berhasil diupdate!'); window.location.href='index.php?controller=admin&action=menu';</script>";
        }
    }

    public function hapus_menu()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $this->conn->prepare("SELECT foto FROM tb_menu WHERE id_menu = ?");
            $stmt->execute([$id]);
            $f = $stmt->fetch();

            if ($f && !empty($f['foto']) && file_exists("assets/gambar/menu/" . $f['foto'])) {
                unlink("assets/gambar/menu/" . $f['foto']);
            }
            $this->conn->prepare("DELETE FROM tb_menu WHERE id_menu = ?")->execute([$id]);
            echo "<script>alert('Menu dihapus!'); window.location.href='index.php?controller=admin&action=menu';</script>";
        }
    }

    // CRUD FASILITAS KAFE
    // Tampil Data
    public function fasilitas()
    {
        $query = "SELECT * FROM tb_fasilitas ORDER BY id_fasilitas ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $fasilitas = $stmt->fetchAll();

        $page = 'fasilitas';
        require_once 'views/admin/header.php';
        require_once 'views/admin/fasilitas.php';
        require_once 'views/admin/footer.php';
    }

    // Tambah Data
    public function tambah_fasilitas()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama   = $_POST['nama_fasilitas'];
            $harga  = $_POST['harga'];
            $satuan = $_POST['satuan'];
            $desc   = $_POST['deskripsi'];
            $status = isset($_POST['status_fasilitas']) ? $_POST['status_fasilitas'] : 'Tersedia';

            $foto_name = $_FILES['foto']['name'];
            $tmp_name  = $_FILES['foto']['tmp_name'];

            if ($foto_name != '') {
                $ekstensi  = pathinfo($foto_name, PATHINFO_EXTENSION);
                $nama_baru = "FAC-" . date('YmdHis') . "." . $ekstensi;

                // Path upload disesuaikan dengan root MVC
                if (move_uploaded_file($tmp_name, "assets/gambar/fasilitas/" . $nama_baru)) {
                    $stmt = $this->conn->prepare("INSERT INTO tb_fasilitas (nama_fasilitas, harga, satuan, deskripsi, foto_fasilitas, status_fasilitas) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$nama, $harga, $satuan, $desc, $nama_baru, $status]);

                    echo "<script>alert('Fasilitas berhasil ditambah!'); window.location.href='index.php?controller=admin&action=fasilitas';</script>";
                } else {
                    echo "<script>alert('Gagal mengunggah gambar!'); window.history.back();</script>";
                }
            } else {
                echo "<script>alert('Mohon pilih foto fasilitas!'); window.history.back();</script>";
            }
        }
    }

    // Edit Data
    public function edit_fasilitas()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id     = $_POST['id_fasilitas'];
            $nama   = $_POST['nama_fasilitas'];
            $harga  = $_POST['harga'];
            $satuan = $_POST['satuan'];
            $desc   = $_POST['deskripsi'];
            $lama   = $_POST['foto_lama'];
            $status = isset($_POST['status_fasilitas']) ? $_POST['status_fasilitas'] : 'Tersedia';

            if ($_FILES['foto']['name'] != '') {
                $foto_name = $_FILES['foto']['name'];
                $ekstensi  = pathinfo($foto_name, PATHINFO_EXTENSION);
                $nama_baru = "FAC-" . date('YmdHis') . "." . $ekstensi;

                if (move_uploaded_file($_FILES['foto']['tmp_name'], "assets/gambar/fasilitas/" . $nama_baru)) {
                    // Hapus foto lama
                    if (!empty($lama) && file_exists("assets/gambar/fasilitas/" . $lama)) {
                        unlink("assets/gambar/fasilitas/" . $lama);
                    }

                    $stmt = $this->conn->prepare("UPDATE tb_fasilitas SET nama_fasilitas=?, harga=?, satuan=?, deskripsi=?, foto_fasilitas=?, status_fasilitas=? WHERE id_fasilitas=?");
                    $stmt->execute([$nama, $harga, $satuan, $desc, $nama_baru, $status, $id]);
                }
            } else {
                $stmt = $this->conn->prepare("UPDATE tb_fasilitas SET nama_fasilitas=?, harga=?, satuan=?, deskripsi=?, status_fasilitas=? WHERE id_fasilitas=?");
                $stmt->execute([$nama, $harga, $satuan, $desc, $status, $id]);
            }

            echo "<script>alert('Data fasilitas diperbarui!'); window.location.href='index.php?controller=admin&action=fasilitas';</script>";
        }
    }

    // Hapus Data
    public function hapus_fasilitas()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $res = $this->conn->prepare("SELECT foto_fasilitas FROM tb_fasilitas WHERE id_fasilitas = ?");
            $res->execute([$id]);
            $f = $res->fetch();

            if ($f) {
                // Hapus file fisik gambar
                if (file_exists("assets/gambar/fasilitas/" . $f['foto_fasilitas']) && !empty($f['foto_fasilitas'])) {
                    unlink("assets/gambar/fasilitas/" . $f['foto_fasilitas']);
                }
                $this->conn->prepare("DELETE FROM tb_fasilitas WHERE id_fasilitas = ?")->execute([$id]);
            }
            echo "<script>alert('Fasilitas dihapus!'); window.location.href='index.php?controller=admin&action=fasilitas';</script>";
        }
    }

    // CRUD PROMO & VOUCHER
    // Tampil Data Promo
    public function promo()
    {
        $this->conn->query("UPDATE tb_promo SET status_promo = 'Nonaktif' WHERE tgl_selesai < CURDATE() AND status_promo = 'Aktif'");

        $query = "SELECT p.*, l.nama_level 
                  FROM tb_promo p 
                  LEFT JOIN tb_level_member l ON p.target_level = l.id_level 
                  ORDER BY p.id_promo DESC";
        $promos = $this->conn->query($query)->fetchAll();

        $levels = $this->conn->query("SELECT * FROM tb_level_member ORDER BY id_level ASC")->fetchAll();

        // AMBIL DATA MENU UNTUK DROPDOWN PROMO PRODUK
        $menus = $this->conn->query("SELECT id_menu, nama_menu FROM tb_menu WHERE status_menu = 'Tersedia' ORDER BY nama_menu ASC")->fetchAll();

        $page = 'promo';
        require_once 'views/admin/header.php';
        require_once 'views/admin/promo.php';
        require_once 'views/admin/footer.php';
    }

    // Tambah Promo
    // Tambah Promo
    public function tambah_promo()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama      = $_POST['nama_promo'];
            $kode = empty($_POST['kode_promo']) ? "" : $_POST['kode_promo'];
            $desc      = $_POST['deskripsi'];
            $syarat    = $_POST['syarat_ketentuan']; // VARIABEL BARU
            $tipe      = $_POST['tipe_promo'];
            $potongan  = $_POST['potongan'];
            $t_pot     = $_POST['tipe_potongan'];
            $tgl_m     = $_POST['tgl_mulai'];
            $tgl_s     = $_POST['tgl_selesai'];
            $status    = $_POST['status_promo'];

            $min_poin     = ($tipe == 'Tukar_Poin') ? (int)$_POST['min_poin'] : 0;
            $min_belanja = !empty($_POST['min_belanja']) ? (int)$_POST['min_belanja'] : 0;
            $target_level = ($tipe == 'Level' && !empty($_POST['target_level'])) ? (int)$_POST['target_level'] : null;
            $kuota        = !empty($_POST['kuota']) ? (int)$_POST['kuota'] : null;

            $id_trigger = ($t_pot == 'Produk' && !empty($_POST['id_menu_trigger'])) ? $_POST['id_menu_trigger'] : null;
            $id_bonus   = ($t_pot == 'Produk' && !empty($_POST['id_menu_bonus'])) ? $_POST['id_menu_bonus'] : null;
            $min_beli   = ($t_pot == 'Produk' && !empty($_POST['min_beli'])) ? (int)$_POST['min_beli'] : 1;

            $foto_name = $_FILES['foto']['name'];
            $tmp_name  = $_FILES['foto']['tmp_name'];
            $ekstensi  = strtolower(pathinfo($foto_name, PATHINFO_EXTENSION));
            $nama_baru = "PRM-" . date('YmdHis') . "." . $ekstensi;

            if (in_array($ekstensi, ['jpg', 'jpeg', 'png', 'webp'])) {
                if (move_uploaded_file($tmp_name, "assets/gambar/promo/" . $nama_baru)) {
                    // Query Insert dengan syarat_ketentuan
                    $sql = "INSERT INTO tb_promo (nama_promo, kode_promo, deskripsi, syarat_ketentuan, min_poin, min_belanja, potongan, tipe_potongan, foto_promo, tgl_mulai, tgl_selesai, tipe_promo, target_level, kuota, status_promo, id_menu_trigger, id_menu_bonus, min_beli) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->execute([$nama, $kode, $desc, $syarat, $min_poin, $min_belanja, $potongan, $t_pot, $nama_baru, $tgl_m, $tgl_s, $tipe, $target_level, $kuota, $status, $id_trigger, $id_bonus, $min_beli]);

                    echo "<script>alert('Promo berhasil ditambahkan!'); window.location.href='index.php?controller=admin&action=promo';</script>";
                }
            } else {
                echo "<script>alert('Format gambar tidak didukung!'); window.history.back();</script>";
            }
        }
    }

    // Edit Promo
    public function edit_promo()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id        = $_POST['id_promo'];
            $nama      = $_POST['nama_promo'];
            $kode      = empty($_POST['kode_promo']) ? null : $_POST['kode_promo'];
            $desc      = $_POST['deskripsi'];
            $syarat    = $_POST['syarat_ketentuan']; // VARIABEL BARU
            $tipe      = $_POST['tipe_promo'];
            $potongan  = $_POST['potongan'];
            $t_pot     = $_POST['tipe_potongan'];
            $tgl_m     = $_POST['tgl_mulai'];
            $tgl_s     = $_POST['tgl_selesai'];
            $status    = $_POST['status_promo'];
            $foto_lama = $_POST['foto_lama'];

            $min_poin     = ($tipe == 'Tukar_Poin') ? (int)$_POST['min_poin'] : 0;
            $min_belanja = !empty($_POST['min_belanja']) ? (int)$_POST['min_belanja'] : 0;
            $target_level = ($tipe == 'Level' && !empty($_POST['target_level'])) ? (int)$_POST['target_level'] : null;
            $kuota        = !empty($_POST['kuota']) ? (int)$_POST['kuota'] : null;

            $id_trigger = ($t_pot == 'Produk' && !empty($_POST['id_menu_trigger'])) ? $_POST['id_menu_trigger'] : null;
            $id_bonus   = ($t_pot == 'Produk' && !empty($_POST['id_menu_bonus'])) ? $_POST['id_menu_bonus'] : null;
            $min_beli   = ($t_pot == 'Produk' && !empty($_POST['min_beli'])) ? (int)$_POST['min_beli'] : 1;

            // Query Update dengan syarat_ketentuan
            if (!empty($_FILES['foto']['name'])) {
                $foto_name = $_FILES['foto']['name'];
                $tmp_name  = $_FILES['foto']['tmp_name'];
                $ekstensi  = strtolower(pathinfo($foto_name, PATHINFO_EXTENSION));
                $nama_baru = "PRM-" . date('YmdHis') . "." . $ekstensi;

                if (in_array($ekstensi, ['jpg', 'jpeg', 'png', 'webp']) && move_uploaded_file($tmp_name, "assets/gambar/promo/" . $nama_baru)) {
                    if (!empty($foto_lama) && file_exists("assets/gambar/promo/" . $foto_lama)) unlink("assets/gambar/promo/" . $foto_lama);

                    $sql = "UPDATE tb_promo SET nama_promo=?, kode_promo=?, deskripsi=?, syarat_ketentuan=?, min_poin=?, min_belanja=?, potongan=?, tipe_potongan=?, foto_promo=?, tgl_mulai=?, tgl_selesai=?, tipe_promo=?, target_level=?, kuota=?, status_promo=?, id_menu_trigger=?, id_menu_bonus=?, min_beli=? WHERE id_promo=?";
                    $this->conn->prepare($sql)->execute([$nama, $kode, $desc, $syarat, $min_poin, $min_belanja, $potongan, $t_pot, $nama_baru, $tgl_m, $tgl_s, $tipe, $target_level, $kuota, $status, $id_trigger, $id_bonus, $min_beli, $id]);
                }
            } else {
                $sql = "UPDATE tb_promo SET nama_promo=?, kode_promo=?, deskripsi=?, syarat_ketentuan=?, min_poin=?, min_belanja=?, potongan=?, tipe_potongan=?, tgl_mulai=?, tgl_selesai=?, tipe_promo=?, target_level=?, kuota=?, status_promo=?, id_menu_trigger=?, id_menu_bonus=?, min_beli=? WHERE id_promo=?";
                $this->conn->prepare($sql)->execute([$nama, $kode, $desc, $syarat, $min_poin, $min_belanja, $potongan, $t_pot, $tgl_m, $tgl_s, $tipe, $target_level, $kuota, $status, $id_trigger, $id_bonus, $min_beli, $id]);
            }
            echo "<script>alert('Promo diupdate!'); window.location.href='index.php?controller=admin&action=promo';</script>";
        }
    }

    // Hapus Promo
    public function hapus_promo()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $res = $this->conn->prepare("SELECT foto_promo FROM tb_promo WHERE id_promo = ?");
            $res->execute([$id]);
            $p = $res->fetch();

            if ($p && !empty($p['foto_promo'])) {
                if (file_exists("assets/gambar/promo/" . $p['foto_promo'])) {
                    unlink("assets/gambar/promo/" . $p['foto_promo']);
                }
            }

            $this->conn->prepare("DELETE FROM tb_promo WHERE id_promo = ?")->execute([$id]);
            echo "<script>alert('Promo berhasil dihapus!'); window.location.href='index.php?controller=admin&action=promo';</script>";
        }
    }

    // MANAJEMEN ULASAN PELANGGAN

    // 1. Tampil Halaman Ulasan
    public function ulasan()
    {
        // Mengambil data ulasan sekaligus nama member
        $query = "SELECT u.*, m.nama_member 
                  FROM tb_ulasan u 
                  LEFT JOIN tb_member m ON u.id_member = m.id_member 
                  ORDER BY u.tgl_ulasan DESC";
        $ulasan = $this->conn->query($query)->fetchAll();

        $page = 'ulasan';
        require_once 'views/admin/header.php';
        require_once 'views/admin/ulasan.php';
        require_once 'views/admin/footer.php';
    }

    // 2. Balas Ulasan
    public function balas_ulasan()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_ulasan = $_POST['id_ulasan'];
            $balasan = trim($_POST['balasan_admin']);

            $stmt = $this->conn->prepare("UPDATE tb_ulasan SET balasan_admin = ? WHERE id_ulasan = ?");
            if ($stmt->execute([$balasan, $id_ulasan])) {
                echo "<script>alert('Balasan berhasil disimpan!'); window.location.href='index.php?controller=admin&action=ulasan';</script>";
            } else {
                echo "<script>alert('Gagal menyimpan balasan.'); window.history.back();</script>";
            }
        }
    }

    // 3. Toggle Tampil/Sembunyi Ulasan (Public)
    public function toggle_ulasan()
    {
        $id = $_GET['id'] ?? null;
        $status = $_GET['status'] ?? null;

        if ($id && $status) {
            $status_baru = ($status == 'Y') ? 'Y' : 'N';

            $stmt = $this->conn->prepare("UPDATE tb_ulasan SET status_tampil = ? WHERE id_ulasan = ?");
            if ($stmt->execute([$status_baru, $id])) {
                echo "<script>window.location.href='index.php?controller=admin&action=ulasan';</script>";
            } else {
                echo "<script>alert('Gagal mengubah status visibilitas.'); window.location.href='index.php?controller=admin&action=ulasan';</script>";
            }
        }
    }

    // 4. Hapus Ulasan Permanen
    public function hapus_ulasan()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->conn->prepare("DELETE FROM tb_ulasan WHERE id_ulasan = ?")->execute([$id]);
            echo "<script>alert('Ulasan berhasil dihapus permanen!'); window.location.href='index.php?controller=admin&action=ulasan';</script>";
        }
    }

    // MANAJEMEN MEJA (MANUAL RESET OLEH ADMIN)
    public function manajemen_meja()
    {
        // Ambil semua data meja
        $mejas = $this->conn->query("SELECT * FROM tb_meja ORDER BY id_meja ASC")->fetchAll();

        $page = 'manajemen_meja';
        require_once 'views/admin/header.php';
        require_once 'views/admin/manajemen_meja.php';
        require_once 'views/admin/footer.php';
    }

    public function update_status_meja()
    {
        $id = $_GET['id'] ?? null;
        $status_baru = $_GET['status'] ?? 'Tersedia';

        if ($id) {
            $stmt = $this->conn->prepare("UPDATE tb_meja SET status = ? WHERE id_meja = ?");
            if ($stmt->execute([$status_baru, $id])) {
                echo "<script>window.location.href='index.php?controller=admin&action=manajemen_meja';</script>";
            } else {
                echo "<script>alert('Gagal mengubah status meja!'); window.history.back();</script>";
            }
        }
    }
}
