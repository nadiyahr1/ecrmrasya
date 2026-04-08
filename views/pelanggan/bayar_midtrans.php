<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selesaikan Pembayaran - Café Rasya.co</title>
    
    <link rel="stylesheet" href="assets/css/pelanggan-cart.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&family=Manrope:wght@200..800&family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <script type="text/javascript"
            src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="Mid-client-COG378gmzSZyuqn5"></script>
</head>
<body class="pay-page-body">

<div class="pay-container">
    <h2>Selesaikan Pembayaran</h2>
    <p>ID Pesanan: <strong><?= htmlspecialchars($pesanan['id_pesanan']) ?></strong></p>
    
    <div class="pay-total-amount">
        Total: Rp <?= number_format($pesanan['total_transaksi'], 0, ',', '.') ?>
    </div>
    
    <p class="pay-instruction">
        Silakan klik tombol di bawah ini untuk memilih metode pembayaran yang tersedia (Transfer Bank, QRIS, atau E-Wallet).
    </p>
    
    <button id="pay-button" class="btn-pay">Pilih Metode Pembayaran</button>
</div>

<script type="text/javascript">
    document.getElementById('pay-button').onclick = function(){
        snap.pay('<?= $pesanan['snap_token'] ?>', {
            onSuccess: function(result){
                alert("Pembayaran berhasil!"); 
                window.location.href = 'index.php?controller=pelanggan&action=profil&tab=riwayat';
            },
            onPending: function(result){
                alert("Menunggu pembayaran Anda!");
                window.location.href = 'index.php?controller=pelanggan&action=profil&tab=riwayat';
            },
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