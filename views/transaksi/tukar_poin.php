<?php include __DIR__ . '/../../layout/header.php'; ?>

<div style="padding: 20px;">
    
    <h2>🎁 Katalog Reward Member</h2>
    <p>Poin kamu: <strong><?= number_format($member['poin']) ?> poin</strong></p>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
        <?php foreach($promos as $p): ?>
            <?php
                $cukup = $member['poin'] >= $p['poin_dibutuhkan'];
            ?>
            <div style="background: white; padding: 20px; border-radius: 10px; text-align: center;">
                <div style="font-size: 40px;">🎫</div>
                <h3><?= $p['nama_promo'] ?></h3>
                <p style="color: green;">
                    Diskon Rp <?= number_format($p['potongan']) ?>
                </p>
                <div style="background: #f8f9fa; padding: 10px;">
                    <?= $p['poin_dibutuhkan'] ?> Poin
                </div>
                <p style="font-size: 12px;">
                    <?= $p['keterangan'] ?>
                </p>

                <?php if($cukup): ?>                 
                    <form method="POST" action="index.php?controller=checkout&action=klaimPoin">
                        <input type="hidden" name="id_promo" value="<?= $p['id_promo'] ?>">
                        <button style="background: green; color: white; padding: 10px; width:100%;">
                            Klaim Reward
                        </button>
                    </form>
                <?php else: ?>
                    <button disabled style="width:100%; padding:10px;">
                        Poin Tidak Cukup
                    </button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include __DIR__ . '/../../layout/footer.php'; ?>