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

            header("Location: index.php?controller=home&action=index");
            exit;
        }

        // CEK MEMBER
        $stmt_member = $this->conn->prepare("SELECT * FROM tb_member WHERE username = ?");
        $stmt_member->execute([$user_input]);
        $member = $stmt_member->fetch();

        if ($member && password_verify($pass_input, $member['password'])) {

            if ($member['status_akun'] == 'Nonaktif') {
                echo "<script>alert('Akun belum aktif'); window.location='index.php?controller=auth&action=login';</script>";
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

            $sql = "INSERT INTO tb_member 
                    (id_level, poin, nama_member, username, password, no_telp, total_poin, status_akun) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([1, 0, $nama, $username, $password, $no_telp, 0, 'Aktif']);

            echo "<script>alert('Registrasi berhasil!'); window.location='index.php?controller=auth&action=login';</script>";
        } catch (PDOException $e) {

            if ($e->getCode() == 23000) {
                echo "<script>alert('Username sudah digunakan!'); window.history.back();</script>";
            } else {
                echo "Error: " . $e->getMessage();
            }
        }
    }
}
