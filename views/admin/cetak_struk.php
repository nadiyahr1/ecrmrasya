<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Struk - <?= htmlspecialchars($p['id_pesanan']) ?></title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 300px;
            font-size: 13px;
            color: #000;
            margin: 0 auto;
            padding: 10px;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        hr {
            border-top: 1px dashed black;
            border-bottom: none;
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
            padding: 3px 0;
        }
    </style>
</head>

<body onload="window.print()">
    <div class="center">
        <strong>CAFE RASYA.CO</strong><br>
        <small>Jl. Contoh Alamat Cafe No. 123</small><br>
        <small>Telp: 0812-3456-7890</small>
        <hr>
        Struk: <?= htmlspecialchars($p['id_pesanan']) ?><br>
        <?= date('d M Y - H:i', strtotime($p['tgl_pesanan'])) ?>
    </div>
    <hr>
    Pelanggan : <?= htmlspecialchars($p['nama_member'] ?? 'Umum') ?><br>
    Tipe Order: <?= htmlspecialchars($p['tipe_pemesanan']) ?> <?= !empty($p['no_meja']) ? "(Meja: " . $p['no_meja'] . ")" : "" ?><br>
    Pembayaran:
    <?php
    $metode_struk = $p['metode_pembayaran'];
    if (strpos($metode_struk, ' - VA: ') !== false) {
        $parts_struk = explode(' - VA: ', $metode_struk);
        echo htmlspecialchars($parts_struk[0]); // Hanya mencetak "Transfer (BCA)"
    } else {
        echo htmlspecialchars($metode_struk);
    }
    ?>
    <hr>
    <table>
        <?php
        $total_item = 0;
        foreach ($daftar_menu as $m):
            $total_item += $m['subtotal'];
            $harga_satuan = $m['jumlah'] > 0 ? ($m['subtotal'] / $m['jumlah']) : 0;
        ?>
            <tr>
                <td colspan="2"><?= htmlspecialchars($m['nama_menu']) ?></td>
            </tr>
            <tr>
                <td><?= $m['jumlah'] ?> x <?= number_format($harga_satuan) ?></td>
                <td class="right"><?= number_format($m['subtotal']) ?></td>
            </tr>
        <?php endforeach; ?>

        <?php
        foreach ($daftar_fasilitas as $f):
            $total_item += $f['subtotal_sewa'];
        ?>
            <tr>
                <td colspan="2">[Fasilitas] <?= htmlspecialchars($f['nama_fasilitas']) ?></td>
            </tr>
            <tr>
                <td>1 x <?= number_format($f['subtotal_sewa']) ?></td>
                <td class="right"><?= number_format($f['subtotal_sewa']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <hr>
    <?php
    $pajak = $total_item * 0.1;
    $total_seharusnya = $total_item + $pajak;
    $diskon = $total_seharusnya - $p['total_transaksi'];
    ?>
    <table>
        <tr>
            <td>Subtotal</td>
            <td class="right"><?= number_format($total_item) ?></td>
        </tr>
        <tr>
            <td>Pajak (10%)</td>
            <td class="right"><?= number_format($pajak) ?></td>
        </tr>
        <?php if ($diskon > 0): ?>
            <tr>
                <td>Diskon Promo</td>
                <td class="right">-<?= number_format($diskon) ?></td>
            </tr>
        <?php endif; ?>
        <tr>
            <td style="padding-top: 5px;"><strong>TOTAL BAYAR</strong></td>
            <td class="right" style="padding-top: 5px;"><strong>Rp <?= number_format($p['total_transaksi']) ?></strong></td>
        </tr>
    </table>
    <hr>
    <div class="center">
        -- Terima Kasih --<br>
        <small>Silakan berkunjung kembali</small>
    </div>
</body>

</html>