<?php include __DIR__ . '/../../layout/header.php'; ?>
<link rel="stylesheet" href="assets/css/pelanggan-loyalty.css">

<div class="tukar-container">
    
    <h2>🎁 Katalog Reward Member</h2>
    <p>Poin kamu: <strong><?= number_format($member['poin']) ?> poin</strong></p>
    <div class="tukar-grid">
        <?php foreach($promos as $p): ?>
            <?php
                $cukup = $member['poin'] >= $p['poin_dibutuhkan'];
            ?>
            <div class="tukar-card">
                <div class="tukar-icon">🎫</div>
                <h3><?= $p['nama_promo'] ?></h3>
                <p class="tukar-text-green">
                    Diskon Rp <?= number_format($p['potongan']) ?>
                </p>
                <div class="tukar-points-box">
                    <?= $p['poin_dibutuhkan'] ?> Poin
                </div>
                <p class="tukar-text-small">
                    <?= $p['keterangan'] ?>
                </p>

                <?php if($cukup): ?>                 
                    <form method="POST" action="index.php?controller=checkout&action=klaimPoin">
                        <input type="hidden" name="id_promo" value="<?= $p['id_promo'] ?>">
                        <button type="submit" class="btn-tukar-claim">
                            Klaim Reward
                        </button>
                    </form>
                <?php else: ?>
                    <button disabled class="btn-tukar-disabled">
                        Poin Tidak Cukup
                    </button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include __DIR__ . '/../../layout/footer.php'; ?>