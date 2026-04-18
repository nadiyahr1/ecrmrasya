<?php 
// Menentukan judul halaman agar muncul di tab browser
$title = "Riwayat Poin - Café Rasya.co"; 
include __DIR__ . '/../../layout/header.php'; 
?>
<link rel="stylesheet" href="assets/css/pelanggan-loyalty.css">

<div class="loyalty-container">
    <a href="index.php?controller=pelanggan&action=profil" class="loyalty-back">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Profil
    </a>

    <h3 class="loyalty-h3">Riwayat Penggunaan Poin</h3>
    <p class="loyalty-p">Catatan perolehan dan penukaran koin loyalitas Anda.</p>

    <div class="loyalty-card-table">
        <table class="loyalty-table">
            <thead>
                <tr class="loyalty-thead-tr">
                    <th class="loyalty-th">Tanggal</th>
                    <th class="loyalty-th">Keterangan</th>
                    <th class="loyalty-th-right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($history)): ?>
                    <tr>
                        <td colspan="3" class="loyalty-td-empty">Belum ada riwayat poin.</td>
                    </tr>
                    <?php else:
                    foreach ($history as $h):
                        $is_plus = (isset($h['tipe']) && $h['tipe'] == 'Masuk');
                    ?>
                        <tr class="loyalty-tbody-tr">
                            <td class="loyalty-td-date">
                                <?= date('d/m/Y H:i', strtotime($h['tgl_perubahan'])) ?>
                            </td>
                            <td class="loyalty-td-desc">
                                <?= htmlspecialchars($h['keterangan']) ?>
                            </td>
                            <td class="loyalty-td-points" style="color: <?= $is_plus ? '#27ae60' : '#e74c3c' ?>;">
                                <?= $is_plus ? '+' : '-' ?> <?= abs($h['poin']) ?>
                            </td>
                        </tr>
                <?php endforeach;
                endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../../layout/footer.php'; ?>