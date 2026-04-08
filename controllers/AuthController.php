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
    public function prosesRegister()
    {
        $nama     = $_POST['nama'];
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $no_telp  = $_POST['no_telp'];

        try {
            // 1. CEK DUPLIKAT NOMOR TELEPON (Tambahan Proteksi)
            $stmt_cek = $this->conn->prepare("SELECT id_member FROM tb_member WHERE no_telp = ?");
            $stmt_cek->execute([$no_telp]);
            if ($stmt_cek->rowCount() > 0) {
                echo "<script>alert('Nomor telepon sudah terdaftar! Gunakan nomor lain.'); window.history.back();</script>";
                exit;
            }

            $sql = "INSERT INTO tb_member 
                (id_level, poin, jml_transaksi, total_belanja, nama_member, username, password, no_telp, status_akun) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($sql);
            // Menggunakan 'Pending' sesuai enum database Anda
            $stmt->execute([1, 10, 0, 0, $nama, $username, $password, $no_telp, 'Pending']);

            // Pesan notifikasi yang lebih informatif
            echo "<script>alert('Registrasi berhasil! Akun Anda sedang menunggu verifikasi oleh Admin. Silakan cek secara berkala.'); window.location='index.php?controller=auth&action=login';</script>";
        } catch (PDOException $e) {
            // 2. CEK DUPLIKAT USERNAME (Melalui Error Code Database)
            if ($e->getCode() == 23000) {
                echo "<script>alert('Username sudah digunakan! Silakan pilih username lain.'); window.history.back();</script>";
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
