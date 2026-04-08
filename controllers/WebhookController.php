<?php
require_once __DIR__ . '/../config/koneksi.php';
// Pastikan path pemanggilan Midtrans benar sesuai foldermu
require_once dirname(__FILE__) . '/../midtrans/Midtrans.php';

// Masukkan Server Key milikmu
\Midtrans\Config::$serverKey = 'Mid-server-Dd4FZEE0vr-iZ4DiNLEmMVhM';
\Midtrans\Config::$isProduction = false;

class WebhookController
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    public function midtrans_handler()
    {
        try {
            $notif = new \Midtrans\Notification();
        } catch (\Exception $e) {
            exit($e->getMessage());
        }

        $transaction = $notif->transaction_status;
        $order_id = $notif->order_id;

        $stmt_cek = $this->conn->prepare("SELECT status FROM tb_pesanan WHERE id_pesanan = ?");
        $stmt_cek->execute([$order_id]);
        $pesanan_saat_ini = $stmt_cek->fetch();

        // Jika status di database sudah 'Dibatalkan', jangan biarkan Midtrans mengubahnya lagi
        if ($pesanan_saat_ini && $pesanan_saat_ini['status'] == 'Dibatalkan') {
            return; // Berhenti di sini, jangan lakukan UPDATE apapun
        }
        
        $status_pesanan = '';
        $bank = '';
        $va_number = '';

        // Tangkap nama bank dan nomor VA dari Midtrans
        if (isset($notif->va_numbers[0]->bank)) {
            $bank = strtoupper($notif->va_numbers[0]->bank);
            $va_number = $notif->va_numbers[0]->va_number;
        } elseif (isset($notif->bank)) {
            $bank = strtoupper($notif->bank);
        }

        // PASTIKAN KATA INI SAMA PERSIS DENGAN YANG ADA DI DATABASE
        if ($transaction == 'capture' || $transaction == 'settlement') {
            $status_pesanan = 'Menunggu Konfirmasi';
        } else if ($transaction == 'deny' || $transaction == 'expire' || $transaction == 'cancel') {
            $status_pesanan = 'Dibatalkan';
        }

        // Update database (Simpan Status, Bank, dan Nomor VA)
        if ($status_pesanan !== '') {
            if ($bank !== '') {
                // Contoh Hasil: "Transfer (BCA) - VA: 1234567890"
                $metode_baru = "Transfer (" . $bank . ") - VA: " . $va_number;

                $stmt = $this->conn->prepare("UPDATE tb_pesanan SET status = ?, metode_pembayaran = ? WHERE id_pesanan = ?");
                $stmt->execute([$status_pesanan, $metode_baru, $order_id]);
            } else {
                $stmt = $this->conn->prepare("UPDATE tb_pesanan SET status = ? WHERE id_pesanan = ?");
                $stmt->execute([$status_pesanan, $order_id]);
            }
        }

        // Balas Midtrans bahwa notifikasi berhasil diterima
        http_response_code(200);
        echo "OK";
    }
}
