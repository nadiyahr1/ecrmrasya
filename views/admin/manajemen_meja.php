<style>
    .grid-meja {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 20px;
        margin-top: 25px;
    }

    .card-meja {
        background: white;
        padding: 30px 20px;
        text-align: center;
        border-radius: 12px;
        border: 2px solid #eee;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s;
    }

    .card-meja:hover {
        transform: translateY(-3px);
    }

    /* Meja Kosong (Tersedia) */
    .card-meja.tersedia {
        border-color: #86efac;
        background: #f0fdf4;
    }

    /* Meja Penuh (Terisi) */
    .card-meja.terisi {
        border-color: #fca5a5;
        background: #fef2f2;
    }

    .no-meja {
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 10px;
        color: #333;
    }

    .status-badge {
        font-size: 12px;
        font-weight: bold;
        padding: 6px 15px;
        border-radius: 20px;
        display: inline-block;
        margin-bottom: 20px;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .status-badge.tersedia {
        background: #16a34a;
        color: white;
    }

    .status-badge.terisi {
        background: #dc2626;
        color: white;
    }

    .btn-aksi {
        padding: 10px 15px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        text-decoration: none;
        font-size: 13px;
        font-weight: bold;
        display: block;
        width: 100%;
        box-sizing: border-box;
        transition: 0.2s;
    }

    .btn-kosongkan {
        background: #3b82f6;
        color: white;
    }

    .btn-kosongkan:hover {
        background: #2563eb;
    }

    .btn-isi {
        background: #f59e0b;
        color: white;
    }

    .btn-isi:hover {
        background: #d97706;
    }
</style>

<div>
    <h2 style="margin: 0 0 5px 0; color: #333;">Manajemen Meja / Denah</h2>
    <p style="margin: 0; color: #666; font-size: 14px;">Pantau status meja dan kosongkan secara manual jika pelanggan sudah meninggalkan kafe.</p>
</div>

<div class="grid-meja">
    <?php foreach ($mejas as $m):
        // Mengubah status jadi huruf kecil agar cocok dengan class CSS
        $class_status = strtolower($m['status']);
    ?>
        <div class="card-meja <?= $class_status ?>">
            <div style="color: #888; font-size: 13px; font-weight: bold;">MEJA NOMOR</div>
            <div class="no-meja"><?= htmlspecialchars($m['no_meja']) ?></div>

            <div class="status-badge <?= $class_status ?>"><?= htmlspecialchars($m['status']) ?></div>

            <?php if ($m['status'] == 'Terisi'): ?>
                <a href="index.php?controller=admin&action=update_status_meja&id=<?= $m['id_meja'] ?>&status=Tersedia"
                    class="btn-aksi btn-kosongkan"
                    onclick="return confirm('Apakah pelanggan di Meja <?= $m['no_meja'] ?> sudah pergi? Meja akan dikosongkan.')">
                    Bebaskan Meja
                </a>
            <?php else: ?>
                <a href="index.php?controller=admin&action=update_status_meja&id=<?= $m['id_meja'] ?>&status=Terisi"
                    class="btn-aksi btn-isi"
                    onclick="return confirm('Tandai Meja <?= $m['no_meja'] ?> sebagai terisi?')">
                    Tandai Terisi
                </a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>