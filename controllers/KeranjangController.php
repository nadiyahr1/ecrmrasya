<?php
require_once __DIR__ . '/../config/koneksi.php';

class KeranjangController
{

    public function index()
    {
        session_start();
        global $conn;

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

        // ======================
        // AMBIL DATA FASILITAS
        // ======================
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
                    $f['satuan'] = $item['satuan'];
                    $data_fasilitas[] = $f;
                }
            }
        }

        // tampilkan view
        require_once __DIR__ . '/../views/transaksi/keranjang.php';
    }

    public function tambah()
    {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?controller=home&action=index");
            exit;
        }

        $id_menu = $_POST['id_menu'] ?? null;

        // VALIDASI
        if (!$id_menu || !is_numeric($id_menu)) {
            header("Location: index.php?controller=home&action=index");
            exit;
        }

        if (!isset($_SESSION['keranjang'])) {
            $_SESSION['keranjang'] = [];
        }

        if (isset($_SESSION['keranjang'][$id_menu])) {
            $_SESSION['keranjang'][$id_menu] += 1;
        } else {
            $_SESSION['keranjang'][$id_menu] = 1;
        }

        header("Location: index.php?controller=keranjang&action=index");
        exit;
    }

    public function update() {
    session_start();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: index.php?controller=keranjang&action=index");
        exit;
    }

    $id_menu = $_POST['id_menu'] ?? null;
    $qty = $_POST['qty'] ?? 1;

    // VALIDASI
    if (!$id_menu || !is_numeric($id_menu)) {
        header("Location: index.php?controller=keranjang&action=index");
        exit;
    }

    $qty = (int)$qty;

    if ($qty < 1) {
        unset($_SESSION['keranjang'][$id_menu]);
    } else {
        $_SESSION['keranjang'][$id_menu] = $qty;
    }

    header("Location: index.php?controller=keranjang&action=index");
    exit;
}

    public function hapusMenu() {
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
