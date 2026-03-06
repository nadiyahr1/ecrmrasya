<?php
require_once __DIR__ . '/../config/koneksi.php';

class CheckoutModel
{

    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    // MEMBER
    public function getMember($id_member)
    {
        $stmt = $this->conn->prepare("
            SELECT m.*, l.diskon, l.nama_level 
            FROM tb_member m
            JOIN tb_level_member l ON m.id_level = l.id_level
            WHERE m.id_member = ?
        ");
        $stmt->execute([$id_member]);
        return $stmt->fetch();
    }

    // MENU
    public function getMenuById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM tb_menu WHERE id_menu = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // FASILITAS
    public function getFasilitasById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM tb_fasilitas WHERE id_fasilitas = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // MEJA
    public function getMejaTersedia()
    {
        return $this->conn->query("SELECT * FROM tb_meja WHERE status='Tersedia'")->fetchAll();
    }

    // INSERT PESANAN
    public function insertPesanan($data)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO tb_pesanan 
            (id_pesanan, id_member, id_meja, id_promo, tgl_pesanan, total_transaksi, tipe_pemesanan, metode_pembayaran, catatan, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Menunggu Konfirmasi')
        ");
        return $stmt->execute($data);
    }

    public function insertDetailMenu($data)
    {
        return $this->conn->prepare("
            INSERT INTO tb_detail_pesanan 
            (id_pesanan, id_menu, jumlah, subtotal) 
            VALUES (?, ?, ?, ?)
        ")->execute($data);
    }

    public function updateStokMenu($qty, $id_menu)
    {
        return $this->conn->prepare("
            UPDATE tb_menu SET stok = stok - ? WHERE id_menu = ?
        ")->execute([$qty, $id_menu]);
    }

    public function insertFasilitas($data)
    {
        return $this->conn->prepare("
            INSERT INTO tb_booking_fasilitas 
            (id_pesanan, id_fasilitas, tgl_sewa, jam_mulai, durasi_jam, jumlah_orang, subtotal_sewa) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ")->execute($data);
    }

    public function updatePoin($poin, $id_member)
    {
        $this->conn->prepare("
            UPDATE tb_member SET poin = poin + ? WHERE id_member = ?
        ")->execute([$poin, $id_member]);

        $this->conn->prepare("
            INSERT INTO tb_history_poin (id_member, poin, keterangan, tgl_perubahan)
            VALUES (?, ?, 'Transaksi', NOW())
        ")->execute([$id_member, $poin]);
    }

    public function updateMeja($id_meja)
    {
        return $this->conn->prepare("
            UPDATE tb_meja SET status='Dipakai' WHERE id_meja=?
        ")->execute([$id_meja]);
    }

    // KOSONGKAN KERANJANG DI DATABASE
    public function clearKeranjang($id_member)
    {
        $this->conn->prepare("
            UPDATE tb_member SET data_keranjang = NULL WHERE id_member = ?
        ")->execute([$id_member]);
    }
    public function getPromo($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM tb_promo WHERE id_promo=?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // PROMO LOYALTY
    public function getPromoLoyalty()
    {
        $stmt = $this->conn->prepare("SELECT * FROM tb_promo WHERE tipe_promo = 'Loyalty' AND status_promo = 'Aktif'");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function begin()
    {
        $this->conn->beginTransaction();
    }

    public function commit()
    {
        $this->conn->commit();
    }

    public function rollback()
    {
        $this->conn->rollBack();
    }
}
