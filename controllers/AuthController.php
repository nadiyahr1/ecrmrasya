<?php
class AuthController
{
    private $conn;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        require_once __DIR__ . '/../config/koneksi.php';
        $this->conn = $conn;
    }

    // FORM LOGIN
    public function login()
    {
        require_once 'views/auth/login.php';
    }

    // PROSES LOGIN
    public function prosesLogin()
    {
        $user_input = $_POST['username'] ?? '';
        $pass_input = $_POST['password'] ?? '';

        // CEK ADMIN / OWNER
        $stmt = $this->conn->prepare("SELECT * FROM tb_user WHERE username = ?");
        $stmt->execute([$user_input]);
        $user = $stmt->fetch();

        if ($user && password_verify($pass_input, $user['password'])) {

            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['nama']    = $user['nama_user'];
            $_SESSION['role']    = $user['role'];

            if ($_SESSION['role'] === 'Admin') {
                header("Location: index.php?controller=admin&action=index");
                exit;
            } elseif ($_SESSION['role'] === 'Owner') {
                header("Location: index.php?controller=owner&action=index");
                exit;
            } else {
                header("Location: index.php?controller=home&action=index");
                exit;
            }
        }

        // CEK MEMBER
        $stmt_member = $this->conn->prepare("SELECT * FROM tb_member WHERE username = ?");
        $stmt_member->execute([$user_input]);
        $member = $stmt_member->fetch();

        if ($member && password_verify($pass_input, $member['password'])) {

            if ($member['status_akun'] == 'Pending') {
                echo "<script>alert('Akun Anda sedang menunggu verifikasi oleh Admin. Silakan coba beberapa saat lagi.'); window.location='index.php?controller=auth&action=login';</script>";
                exit;
            }

            // Set Session Login
            $_SESSION['id_member'] = $member['id_member'];
            $_SESSION['nama']      = $member['nama_member'];
            $_SESSION['role']      = 'Pelanggan';

            // KODE TAMBAHAN STEP 3: TARIK DATA KERANJANG
            $stmt_cart = $this->conn->prepare("SELECT data_keranjang FROM tb_member WHERE id_member = ?");
            $stmt_cart->execute([$member['id_member']]);
            $row_cart = $stmt_cart->fetch();

            if (!empty($row_cart['data_keranjang'])) {
                $cart_data = json_decode($row_cart['data_keranjang'], true);

                if (isset($cart_data['menu'])) {
                    $_SESSION['keranjang'] = $cart_data['menu'];
                }
                if (isset($cart_data['fasilitas'])) {
                    $_SESSION['keranjang_fasilitas'] = $cart_data['fasilitas'];
                }
            }

            header("Location: index.php?controller=home&action=index");
            exit;
        }

        // LOGIN GAGAL
        echo "<script>alert('Username atau Password salah!'); window.location='index.php?controller=auth&action=login';</script>";
    }

    // FORM REGISTER
    public function register()
    {
        require_once 'views/auth/registrasi.php';
    }

    // PROSES REGISTER
    public function prosesRegistrasi()
    {
        $nama = trim($_POST['nama'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $no_telp = trim($_POST['no_telp'] ?? '');
        $password = $_POST['password'] ?? '';

        // ==========================================
        // 1. BLOK VALIDASI ATURAN REGISTRASI
        // ==========================================
        
        // A. Validasi Username: Harus huruf kecil / angka, tanpa spasi, minimal 5 karakter
        if (!preg_match('/^[a-z0-9]{5,}$/', $username)) {
            echo "<script>alert('Gagal: Username harus huruf kecil dan/atau angka tanpa spasi (Minimal 5 karakter)!'); window.history.back();</script>";
            exit;
        }

        // B. Validasi Password: Minimal 8 char, wajib ada huruf Besar, huruf kecil, dan angka
        if (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/', $password)) {
            echo "<script>alert('Gagal: Password minimal 8 karakter, wajib mengandung huruf besar, huruf kecil, dan angka!'); window.history.back();</script>";
            exit;
        }

        // C. Validasi No. WhatsApp: Harus berawalan 08 atau +62, panjang 10-15 digit
        if (!preg_match('/^(08|\+62)[0-9]{8,13}$/', $no_telp)) {
            echo "<script>alert('Gagal: Format No. Telepon harus berawalan 08 atau +62 (10 hingga 15 digit angka)!'); window.history.back();</script>";
            exit;
        }

        // ==========================================
        // 2. LANJUTKAN PROSES SIMPAN KE DATABASE
        // ==========================================
        
        // Enkripsi password menggunakan bcrypt bawaan PHP
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $this->conn->prepare("INSERT INTO tb_member (nama_member, username, password, no_telp, tgl_daftar, poin, id_level) VALUES (?, ?, ?, ?, CURDATE(), 0, 1)");
            $stmt->execute([$nama, $username, $hashed_password, $no_telp]);

            echo "<script>alert('Registrasi berhasil! Akun Anda sedang menunggu verifikasi oleh Admin. Silakan cek secara berkala.'); window.location='index.php?controller=auth&action=login';</script>";
        } catch (PDOException $e) {
            // CEK DUPLIKAT USERNAME/NO TELP DARI DATABASE
            if ($e->getCode() == 23000) {
                echo "<script>alert('Pendaftaran Gagal: Username atau Nomor Telepon tersebut sudah terdaftar di sistem!'); window.history.back();</script>";
            } else {
                echo "Error: " . $e->getMessage();
            }
        }
    }

    public function cekKetersediaan()
    {
        header('Content-Type: application/json');

        $field = $_POST['field'] ?? ''; // 'username' atau 'no_telp'
        $value = $_POST['value'] ?? '';

        if (empty($field) || empty($value)) {
            echo json_encode(['exists' => false]);
            exit;
        }

        // Tentukan tabel dan kolom berdasarkan field
        $column = ($field === 'username') ? 'username' : 'no_telp';
        $label = ($field === 'username') ? 'Username' : 'Nomor Telepon';

        $stmt = $this->conn->prepare("SELECT id_member FROM tb_member WHERE $column = ?");
        $stmt->execute([$value]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['exists' => true, 'message' => $label . ' sudah digunakan!']);
        } else {
            echo json_encode(['exists' => false]);
        }
        exit;
    }
}
