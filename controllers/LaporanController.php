<?php
require_once __DIR__ . '/../config/koneksi.php';

class LaporanController
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Ambil role dan jadikan huruf kecil untuk amannya
        $role = strtolower($_SESSION['role'] ?? '');

        // Proteksi: Hanya admin dan owner yang bisa akses laporan ini (Gunakan huruf kecil!)
        if (!in_array($role, ['admin', 'owner'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
    }

    // 1. Tampilkan Halaman Filter Laporan
    public function index()
    {
        $page = 'laporan_penjualan';

        // 1. Ambil Parameter Filter
        $tgl_mulai = $_GET['mulai'] ?? date('Y-m-01');
        $tgl_selesai = $_GET['selesai'] ?? date('Y-m-d');

        // 2. Logika Pagination
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; // Jumlah baris per halaman
        $halaman_aktif = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
        $offset = ($halaman_aktif - 1) * $limit;

        // 3. Hitung Total Data (untuk tahu jumlah total halaman)
        $query_count = "SELECT COUNT(*) AS total FROM tb_pesanan 
                    WHERE DATE(tgl_pesanan) BETWEEN ? AND ? AND status = 'Selesai'";
        $stmt_count = $this->conn->prepare($query_count);
        $stmt_count->execute([$tgl_mulai, $tgl_selesai]);
        $total_data = $stmt_count->fetch()['total'];
        $total_halaman = ceil($total_data / $limit);

        // 4. Ambil Data dengan LIMIT & OFFSET
        $query = "SELECT p.*, m.nama_member 
              FROM tb_pesanan p 
              LEFT JOIN tb_member m ON p.id_member = m.id_member 
              WHERE DATE(p.tgl_pesanan) BETWEEN ? AND ? 
              AND p.status = 'Selesai' 
              ORDER BY p.tgl_pesanan ASC 
              LIMIT $limit OFFSET $offset";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$tgl_mulai, $tgl_selesai]);
        $laporan = $stmt->fetchAll();

        // 5. Hitung Total Omzet (Tetap dari seluruh periode yang difilter, bukan per halaman)
        $query_omzet = "SELECT SUM(total_transaksi) as total_omzet FROM tb_pesanan 
                    WHERE DATE(tgl_pesanan) BETWEEN ? AND ? AND status = 'Selesai'";
        $stmt_omzet = $this->conn->prepare($query_omzet);
        $stmt_omzet->execute([$tgl_mulai, $tgl_selesai]);
        $total_omzet = $stmt_omzet->fetch()['total_omzet'] ?? 0;


        $role = strtolower($_SESSION['role'] ?? '');

        if ($role == 'owner') {
            // Jika Owner, pakai bingkai Owner
            require_once 'views/owner/header.php';
            require_once 'views/admin/laporan_penjualan.php'; // Isi laporan tetap dari file Admin
            require_once 'views/owner/footer.php';
        } else {
            // Jika Admin, pakai bingkai Admin
            require_once 'views/admin/header.php';
            require_once 'views/admin/laporan_penjualan.php';
            require_once 'views/admin/footer.php';
        }
    }

    public function laporanMember()
    {
        $page = 'laporan_member';

        // 1. Ambil Parameter Filter
        $tgl_mulai = $_GET['mulai'] ?? date('Y-m-01');
        $tgl_selesai = $_GET['selesai'] ?? date('Y-m-d');

        // 2. Logika Pagination
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $halaman_aktif = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
        $offset = ($halaman_aktif - 1) * $limit;

        // 3. Hitung Total Data - UBAH tgl_update JADI tanggal
        $query_count = "SELECT COUNT(*) AS total FROM tb_history_poin 
                    WHERE DATE(tgl_perubahan) BETWEEN ? AND ?";
        $stmt_count = $this->conn->prepare($query_count);
        $stmt_count->execute([$tgl_mulai, $tgl_selesai]);
        $total_data = $stmt_count->fetch()['total'];
        $total_halaman = ceil($total_data / $limit);

        // 4. Ambil Data - UBAH tgl_update JADI tanggal
        $query = "SELECT hp.*, m.nama_member 
              FROM tb_history_poin hp
              JOIN tb_member m ON hp.id_member = m.id_member
              WHERE DATE(hp.tgl_perubahan) BETWEEN ? AND ?
              ORDER BY hp.tgl_perubahan DESC 
              LIMIT $limit OFFSET $offset";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$tgl_mulai, $tgl_selesai]);
        $laporan = $stmt->fetchAll();

        require_once 'views/admin/header.php';
        require_once 'views/admin/laporan_member.php';
        require_once 'views/admin/footer.php';
    }

    public function statistikPoin()
    {
        $page = 'laporan_statistik_poin';

        // 1. Ambil Parameter Filter
        $tgl_mulai = $_GET['mulai'] ?? date('Y-m-01');
        $tgl_selesai = $_GET['selesai'] ?? date('Y-m-d');

        // 2. Logika Pagination
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $halaman_aktif = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
        $offset = ($halaman_aktif - 1) * $limit;

        // 3. Hitung Total Data (untuk pagination)
        $query_count = "SELECT COUNT(*) AS total FROM tb_history_poin 
                        WHERE DATE(tgl_perubahan) BETWEEN ? AND ?";
        $stmt_count = $this->conn->prepare($query_count);
        $stmt_count->execute([$tgl_mulai, $tgl_selesai]);
        $total_data = $stmt_count->fetch()['total'];
        $total_halaman = ceil($total_data / $limit);

        // 4. Hitung Total Poin Masuk (Berdasarkan Filter Tanggal)
        $stmtMasuk = $this->conn->prepare("SELECT SUM(poin) as total FROM tb_history_poin WHERE tipe = 'Masuk' AND DATE(tgl_perubahan) BETWEEN ? AND ?");
        $stmtMasuk->execute([$tgl_mulai, $tgl_selesai]);
        $poin_masuk = $stmtMasuk->fetch()['total'] ?? 0;

        // 5. Hitung Total Poin Keluar (Berdasarkan Filter Tanggal)
        $stmtKeluar = $this->conn->prepare("SELECT SUM(poin) as total FROM tb_history_poin WHERE tipe = 'Keluar' AND DATE(tgl_perubahan) BETWEEN ? AND ?");
        $stmtKeluar->execute([$tgl_mulai, $tgl_selesai]);
        $poin_keluar = $stmtKeluar->fetch()['total'] ?? 0;

        // 6. Mengambil Data Riwayat Pergerakan Poin (dengan Limit & Offset)
        $sqlRiwayat = "SELECT h.*, m.nama_member 
                       FROM tb_history_poin h 
                       JOIN tb_member m ON h.id_member = m.id_member 
                       WHERE DATE(h.tgl_perubahan) BETWEEN ? AND ?
                       ORDER BY h.tgl_perubahan DESC
                       LIMIT $limit OFFSET $offset";
        $stmtRiwayat = $this->conn->prepare($sqlRiwayat);
        $stmtRiwayat->execute([$tgl_mulai, $tgl_selesai]);
        $riwayat_poin = $stmtRiwayat->fetchAll();

        // 7. Pengecekan Peran
        $role = strtolower($_SESSION['role'] ?? '');

        if ($role == 'owner') {
            require_once 'views/owner/header.php';
            require_once 'views/owner/statistik_poin.php';
            require_once 'views/owner/footer.php';
        } else {
            // Jika suatu saat Admin juga butuh akses
            require_once 'views/admin/header.php';
            require_once 'views/admin/statistik_poin.php';
            require_once 'views/admin/footer.php';
        }
    }

    public function laporanPromo()
    {
        $page = 'laporan_promo';

        // 1. Ambil Parameter Filter
        $tgl_mulai = $_GET['mulai'] ?? date('Y-m-01');
        $tgl_selesai = $_GET['selesai'] ?? date('Y-m-d');

        // 2. Logika Pagination
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $halaman_aktif = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
        $offset = ($halaman_aktif - 1) * $limit;

        // 3. Hitung Total Data (Berapa kali promo dipakai)
        $query_count = "SELECT COUNT(*) AS total FROM tb_pesanan 
                        WHERE id_promo IS NOT NULL 
                        AND id_promo != 0
                        AND status = 'Selesai' 
                        AND DATE(tgl_pesanan) BETWEEN ? AND ?";
        $stmt_count = $this->conn->prepare($query_count);
        $stmt_count->execute([$tgl_mulai, $tgl_selesai]);
        $total_data = $stmt_count->fetch()['total'];
        $total_halaman = ($total_data > 0) ? ceil($total_data / $limit) : 1;

        $total_pemakaian = $total_data;

        // 4. Hitung Total Nominal Diskon Secara Keseluruhan
        $query_diskon = "SELECT p.total_transaksi, pr.potongan, pr.tipe_potongan 
                         FROM tb_pesanan p 
                         JOIN tb_promo pr ON p.id_promo = pr.id_promo 
                         WHERE p.status = 'Selesai' 
                         AND DATE(p.tgl_pesanan) BETWEEN ? AND ?";
        $stmt_diskon = $this->conn->prepare($query_diskon);
        $stmt_diskon->execute([$tgl_mulai, $tgl_selesai]);

        $total_diskon = 0;
        foreach ($stmt_diskon->fetchAll() as $d) {
            if ($d['tipe_potongan'] == 'Nominal') {
                $total_diskon += $d['potongan'];
            } elseif ($d['tipe_potongan'] == 'Persen') {
                // Rumus mengembalikan harga awal: Harga Awal = Harga Akhir / (1 - persentase)
                $persen = $d['potongan'] / 100;
                if ($persen < 1) { // Hindari error pembagian 0
                    $harga_awal = $d['total_transaksi'] / (1 - $persen);
                    $total_diskon += ($harga_awal - $d['total_transaksi']);
                }
            }
        }

        // 5. Mengambil Data Riwayat Penggunaan Promo (dengan Limit & Offset)
        $sqlRiwayat = "SELECT p.tgl_pesanan, p.total_transaksi, 
                              m.nama_member, 
                              pr.nama_promo, pr.potongan, pr.tipe_potongan, pr.tipe_promo 
                       FROM tb_pesanan p 
                       JOIN tb_promo pr ON p.id_promo = pr.id_promo 
                       LEFT JOIN tb_member m ON p.id_member = m.id_member 
                       WHERE p.status = 'Selesai' 
                       AND DATE(p.tgl_pesanan) BETWEEN ? AND ?
                       ORDER BY p.tgl_pesanan DESC
                       LIMIT $limit OFFSET $offset";
        $stmtRiwayat = $this->conn->prepare($sqlRiwayat);
        $stmtRiwayat->execute([$tgl_mulai, $tgl_selesai]);

        // Memasukkan hasil perhitungan diskon ke dalam array agar bisa dibaca di View
        $riwayat_promo = [];
        foreach ($stmtRiwayat->fetchAll() as $row) {
            $nominal_diskon = 0;
            if ($row['tipe_potongan'] == 'Nominal') {
                $nominal_diskon = $row['potongan'];
            } elseif ($row['tipe_potongan'] == 'Persen') {
                $persen = $row['potongan'] / 100;
                if ($persen < 1) {
                    $harga_awal = $row['total_transaksi'] / (1 - $persen);
                    $nominal_diskon = $harga_awal - $row['total_transaksi'];
                }
            }
            $row['nominal_diskon'] = $nominal_diskon;
            $riwayat_promo[] = $row;
        }

        // 6. Pengecekan Peran
        $role = strtolower($_SESSION['role'] ?? '');

        if ($role == 'owner') {
            require_once 'views/owner/header.php';
            require_once 'views/owner/laporan_promo.php';
            require_once 'views/owner/footer.php';
        } else {
            require_once 'views/admin/header.php';
            // Asumsi file ini juga bisa dibuka oleh admin nanti
            require_once 'views/owner/laporan_promo.php';
            require_once 'views/admin/footer.php';
        }
    }

    // 2. Tampilkan Halaman Cetak (Tanpa Header/Footer web)
    public function cetak()
    {
        $type = $_GET['type'] ?? 'penjualan'; // Default ke penjualan
        $tgl_mulai = $_GET['mulai'] ?? date('Y-m-01');
        $tgl_selesai = $_GET['selesai'] ?? date('Y-m-d');

        $stmt_owner = $this->conn->prepare("SELECT nama_user FROM tb_user WHERE role = 'Owner' LIMIT 1");
        $stmt_owner->execute();
        $data_owner = $stmt_owner->fetch();
        $nama_owner = $data_owner ? $data_owner['nama_user'] : '....................';

        if ($type == 'member') {
            $judul = "LAPORAN AKTIVITAS MEMBER & POIN";
            $query = "SELECT hp.*, m.nama_member 
                  FROM tb_history_poin hp
                  JOIN tb_member m ON hp.id_member = m.id_member
                  WHERE DATE(hp.tgl_perubahan) BETWEEN ? AND ?
                  ORDER BY hp.tgl_perubahan ASC";
        } else {
            $judul = "LAPORAN PENJUALAN";
            $query = "SELECT p.*, m.nama_member 
                  FROM tb_pesanan p 
                  LEFT JOIN tb_member m ON p.id_member = m.id_member 
                  WHERE DATE(p.tgl_pesanan) BETWEEN ? AND ? 
                  AND p.status = 'Selesai' 
                  ORDER BY p.tgl_pesanan ASC";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$tgl_mulai, $tgl_selesai]);
        $laporan = $stmt->fetchAll();

        // Kirim data ke satu file view yang sama
        require_once 'views/admin/cetak_laporan.php';
    }
}
