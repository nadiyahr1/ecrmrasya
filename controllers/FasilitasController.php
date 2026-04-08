<?php
require_once __DIR__ . '/../config/koneksi.php';

class FasilitasController
{
    private $conn;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        global $conn;
        $this->conn = $conn;
    }

    // 1. Menampilkan Semua Fasilitas
    public function index()
    {
        $fasilitas = $this->conn->query("SELECT * FROM tb_fasilitas WHERE status_fasilitas = 'Tersedia'")->fetchAll();

        require_once 'layout/header.php';
        require_once 'views/fasilitas/index.php';
        require_once 'layout/footer.php';
    }

    // 2. Menampilkan Detail Fasilitas
    // 2. Menampilkan Detail Fasilitas
    public function detailFasilitas()
    {
        $id_f = $_GET['id'] ?? null;
        if (!$id_f) {
            echo "<script>alert('Pilih fasilitas terlebih dahulu!'); window.location='index.php?controller=fasilitas';</script>";
            exit;
        }

        $stmt = $this->conn->prepare("SELECT * FROM tb_fasilitas WHERE id_fasilitas = ?");
        $stmt->execute([$id_f]);
        $fasilitas = $stmt->fetch();

        if (!$fasilitas) {
            echo "<script>alert('Fasilitas tidak ditemukan!'); window.location='index.php?controller=fasilitas';</script>";
            exit;
        }

        // --- TAMBAHAN KODE ULASAN ---
        // Mengambil ulasan yang status_tampil = 'Y' khusus untuk fasilitas ini
        $stmt_ulasan = $this->conn->prepare("
            SELECT u.komentar, u.tgl_ulasan, mem.nama_member 
            FROM tb_ulasan u 
            JOIN tb_pesanan p ON u.id_pesanan = p.id_pesanan 
            JOIN tb_booking_fasilitas bf ON p.id_pesanan = bf.id_pesanan
            JOIN tb_member mem ON p.id_member = mem.id_member 
            WHERE bf.id_fasilitas = ? AND u.status_tampil = 'Y'
            ORDER BY u.tgl_ulasan DESC
        ");
        $stmt_ulasan->execute([$id_f]);
        $ulasan = $stmt_ulasan->fetchAll();
        // --- AKHIR TAMBAHAN ---

        require_once 'layout/header.php';
        require_once 'views/fasilitas/detail_fasilitas.php';
        require_once 'layout/footer.php';
    }

    // 3. Menampilkan Form Reservasi (Booking)
    public function booking()
    {
        // Harus login untuk booking
        if (!isset($_SESSION['id_member'])) {
            echo "<script>alert('Silakan login terlebih dahulu untuk melakukan reservasi!'); window.location='index.php?controller=auth&action=login';</script>";
            exit;
        }

        $id_f = $_GET['id'] ?? null;
        $stmt = $this->conn->prepare("SELECT * FROM tb_fasilitas WHERE id_fasilitas = ?");
        $stmt->execute([$id_f]);
        $f = $stmt->fetch();

        require_once 'layout/header.php';
        require_once 'views/fasilitas/booking.php';
        require_once 'layout/footer.php';
    }

    // 4. Memproses Validasi dan Memasukkan ke Session Booking
    public function prosesBooking()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $id_f = $_POST['id_fasilitas'];

        // PERBAIKAN: Gunakan ?? null agar tidak error jika form tidak mengirimkan data ini
        $tgl_sewa = $_POST['tgl_sewa'] ?? date('Y-m-d');
        $jam_mulai = $_POST['jam_mulai'] ?? null;

        // 1. AMBIL DATA DARI DATABASE
        $stmt_f = $this->conn->prepare("SELECT * FROM tb_fasilitas WHERE id_fasilitas = ?");
        $stmt_f->execute([$id_f]);
        $f_data = $stmt_f->fetch();

        if (!$f_data) {
            echo "<script>alert('Fasilitas tidak ditemukan!'); window.history.back();</script>";
            exit;
        }

        // Cek apakah form mengirimkan 'durasi' atau 'jumlah_orang'
        $pengali = isset($_POST['durasi']) ? (int)$_POST['durasi'] : (isset($_POST['jumlah_orang']) ? (int)$_POST['jumlah_orang'] : 1);
        $jam_selesai = null;

        // PROTEKSI MAKSIMAL 5 ORANG UNTUK SWIMMING POOL
        $is_pool = (stripos($f_data['nama_fasilitas'], 'pool') !== false || stripos($f_data['nama_fasilitas'], 'renang') !== false);

        if ($f_data['satuan'] == 'Orang' && $is_pool && $pengali > 5) {
            echo "<script>alert('Maksimal booking untuk fasilitas ini adalah 5 orang.'); window.history.back();</script>";
            exit;
        }

        // 2. VALIDASI BENTROK (HANYA dijalankan jika fasilitas per "Jam" dan jam_mulai ada isinya)
        if ($f_data['satuan'] == 'Jam' && $jam_mulai != null) {
            $jam_selesai = date('H:i:s', strtotime($jam_mulai) + ($pengali * 3600));

            $query_cek = "
                SELECT bf.jam_mulai, bf.jam_selesai
                FROM tb_booking_fasilitas bf
                JOIN tb_pesanan p ON bf.id_pesanan = p.id_pesanan
                WHERE bf.id_fasilitas = ?
                AND bf.tgl_sewa = ?
                AND p.status != 'Dibatalkan'
                AND (? < bf.jam_selesai AND ? > bf.jam_mulai)
            ";

            $stmt_cek = $this->conn->prepare($query_cek);
            $stmt_cek->execute([$id_f, $tgl_sewa, $jam_mulai, $jam_selesai]);

            if ($stmt_cek->rowCount() > 0) {
                echo "<script>
                        alert('Maaf, jam tersebut sudah dibooking orang lain. Silakan pilih jam lain.');
                        window.history.back();
                      </script>";
                exit;
            }
        }

        // 3. HITUNG SUBTOTAL
        $subtotal = $f_data['harga'] * $pengali;

        // 4. MASUKKAN KE SESSION KERANJANG
        if (!isset($_SESSION['keranjang_fasilitas'])) {
            $_SESSION['keranjang_fasilitas'] = [];
        }

        $_SESSION['keranjang_fasilitas'][$id_f] = [
            'id_fasilitas'   => $id_f,
            'nama_fasilitas' => $f_data['nama_fasilitas'],
            'tgl_sewa'       => $tgl_sewa,
            'jam_mulai'      => $jam_mulai,
            'jam_selesai'    => $jam_selesai,
            'pengali'        => $pengali,
            'satuan'         => $f_data['satuan'],
            'harga'          => $f_data['harga'],
            'subtotal'       => $subtotal
        ];

        echo "<script>
                alert('Fasilitas berhasil ditambahkan ke keranjang!');
                window.location.href = 'index.php?controller=keranjang&action=index';
              </script>";
    }
}
