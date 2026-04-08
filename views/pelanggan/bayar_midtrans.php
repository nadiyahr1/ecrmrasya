<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selesaikan Pembayaran</title>
    <script type="text/javascript"
            src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="Mid-client-COG378gmzSZyuqn5"></script>
    <style>
        .pay-container { max-width: 500px; margin: 50px auto; text-align: center; font-family: sans-serif; padding: 30px; border: 1px solid #ddd; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .btn-pay { background-color: #6F4E37; color: white; padding: 12px 24px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; font-weight: bold; margin-top: 20px;}
        .btn-pay:hover { background-color: #5a3e2b; }
    </style>
</head>
<body>

<div class="pay-container">
    <h2>Selesaikan Pembayaran Anda</h2>
    <p>ID Pesanan: <strong><?= htmlspecialchars($pesanan['id_pesanan']) ?></strong></p>
    <h3 style="color: #d9534f;">Total: Rp <?= number_format($pesanan['total_transaksi'], 0, ',', '.') ?></h3>
    <p>Silakan klik tombol di bawah ini untuk memilih metode pembayaran (Transfer Bank, QRIS, E-Wallet, dll).</p>
    
    <button id="pay-button" class="btn-pay">Pilih Metode Pembayaran</button>
</div>

<script type="text/javascript">
    document.getElementById('pay-button').onclick = function(){
        // Memanggil fungsi snap.pay dengan token dari database
        snap.pay('<?= $pesanan['snap_token'] ?>', {
            // Callback jika pembayaran berhasil
            onSuccess: function(result){
                alert("Pembayaran berhasil!"); 
                window.location.href = 'index.php?controller=pelanggan&action=profil&tab=riwayat';
            },
            // Callback jika pelanggan menutup pop-up tanpa membayar
            onPending: function(result){
                alert("Menunggu pembayaran Anda!");
                window.location.href = 'index.php?controller=pelanggan&action=profil&tab=riwayat';
            },
            // Callback jika terjadi kesalahan
            onError: function(result){
                alert("Pembayaran gagal!");
            },
            onClose: function(){
                alert('Anda menutup popup sebelum menyelesaikan pembayaran');
            }
        });
    };
</script>

</body>
</html>