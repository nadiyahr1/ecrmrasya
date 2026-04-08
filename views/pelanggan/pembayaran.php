<?php include 'layout/header.php'; ?>

<div style="max-width: 600px; margin: 20px auto 0; padding: 0 15px;">
    <a href="index.php?controller=pelanggan&action=detail_pesanan&id=<?= htmlspecialchars($pesanan['id_pesanan'] ?? $_GET['id'] ?? '') ?>" 
       style="text-decoration: none; color: #6F4E37; font-weight: bold; font-family: 'Segoe UI', sans-serif; display: inline-block; padding: 10px 15px; background: #fdfaf8; border-radius: 8px; border: 1px solid #f1e9e2; transition: 0.3s;">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Detail Pesanan
    </a>
</div>

<div style="max-width: 600px; margin: 80px auto 50px; padding: 35px; background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); font-family: 'Segoe UI', sans-serif;">
    <div style="text-align: center; margin-bottom: 25px;">
        <h2 style="color: #6F4E37; margin-top: 0; font-size: 28px;">Pembayaran Transfer</h2>
        <p style="color: #666; font-size: 15px;">Selesaikan pembayaran Anda agar pesanan dapat segera kami proses.</p>
    </div>

    <div style="background: #fdfaf8; border: 1px solid #f5ede6; padding: 25px; border-radius: 15px; margin-bottom: 30px; text-align: center;">
        <p style="margin: 0 0 10px 0; color: #888; font-size: 15px; font-weight: bold; text-transform: uppercase;">Total Tagihan</p>
        <h1 style="color: #6F4E37; margin: 0; font-size: 40px;">Rp <?= number_format($pesanan['total_transaksi']) ?></h1>
        <div style="margin-top: 15px; padding-top: 15px; border-top: 1px dashed #ccc;">
            <p style="margin: 0; font-weight: bold; color: #333; font-size: 16px;">Order ID: <span style="color: #6F4E37;"><?= $id_p ?></span></p>
        </div>
    </div>

    <div style="margin-bottom: 35px;">
        <h4 style="margin-bottom: 15px; color: #333; font-size: 16px;">Silakan Transfer ke Salah Satu Rekening Berikut:</h4>

        <div style="display: flex; justify-content: space-between; align-items: center; padding: 18px; border: 1px solid #ddd; border-radius: 12px; margin-bottom: 12px; background: #fafafa;">
            <div>
                <strong style="font-size: 18px; color: #0056b3;">BCA</strong><br>
                <span style="font-size: 18px; letter-spacing: 2px; font-weight: bold; color: #333;">1234 5678 90</span><br>
                <small style="color: #888; font-size: 13px;">a.n. Rasya Cafe</small>
            </div>
            <i class="fa-solid fa-building-columns" style="font-size: 30px; color: #ccc;"></i>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; padding: 18px; border: 1px solid #ddd; border-radius: 12px; background: #fafafa;">
            <div>
                <strong style="font-size: 18px; color: #004085;">Bank Mandiri</strong><br>
                <span style="font-size: 18px; letter-spacing: 2px; font-weight: bold; color: #333;">0987 6543 2112</span><br>
                <small style="color: #888; font-size: 13px;">a.n. Rasya Cafe</small>
            </div>
            <i class="fa-solid fa-building-columns" style="font-size: 30px; color: #ccc;"></i>
        </div>
    </div>

    <form action="index.php?controller=checkout&action=prosesPembayaran" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id_pesanan" value="<?= htmlspecialchars($pesanan['id_pesanan'] ?? $_GET['id'] ?? '') ?>">
        <div style="margin-bottom: 25px; background: #fffcf5; padding: 20px; border-radius: 12px; border: 1px solid #f9eed7;">
            <label style="display: block; font-weight: bold; margin-bottom: 10px; color: #6F4E37; font-size: 15px;">
                <i class="fa-solid fa-camera" style="margin-right: 5px;"></i> Upload Bukti Transfer <span style="color:red;">*</span>
            </label>
            <input type="file" name="bukti_bayar" accept="image/*" required style="width: 100%; padding: 12px; border: 2px dashed #dcbca3; border-radius: 10px; background: white; cursor: pointer; color: #555; box-sizing: border-box;">
            <small style="color: #888; display: block; margin-top: 10px; font-size: 12px;"><i class="fa-solid fa-circle-info"></i> Format didukung: JPG, PNG, JPEG. Pastikan foto terlihat jelas agar mudah diverifikasi.</small>
        </div>

        <button type="submit" style="width: 100%; padding: 18px; background: #28a745; color: white; border: none; border-radius: 12px; font-weight: bold; font-size: 18px; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);">
            <i class="fa-solid fa-cloud-arrow-up" style="margin-right: 8px;"></i> Konfirmasi Pembayaran
        </button>
    </form>
</div>