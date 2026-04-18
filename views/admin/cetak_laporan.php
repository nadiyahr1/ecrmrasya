<!DOCTYPE html>
<html>

<head>
    <title>Cetak <?= $judul ?></title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            line-height: 1.6;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body onload="window.print()">
    <div class="header">
        <h2 style="margin:0;">RASYA.CO COFFEE EATERY</h2>
        <p style="margin:5px 0;"><strong><?= $judul ?></strong></p>
        <p style="margin:0;">Periode: <?= date('d/m/Y', strtotime($tgl_mulai)) ?> s/d <?= date('d/m/Y', strtotime($tgl_selesai)) ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Tanggal</th>
                <?php if ($type == 'member'): ?>
                    <th>Nama Member</th>
                    <th>Aktivitas</th>
                    <th>Poin</th>
                    <th>Keterangan</th>
                <?php else: ?>
                    <th>ID Pesanan</th>
                    <th>Nama Pelanggan</th>
                    <th>Metode Bayar</th>
                    <th class="text-right">Nominal (Rp)</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $total = 0;
            foreach ($laporan as $row):
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= ($type == 'member') ? date('d/m/y H:i', strtotime($row['tgl_perubahan'])) : date('d/m/y H:i', strtotime($row['tgl_pesanan'])) ?></td>

                    <?php if ($type == 'member'): ?>
                        <?php
                        // KODE PENYELAMAT: Sesuaikan 'jenis' dan 'poin' dengan nama kolom asli di database Anda
                        $jenis = $row['tipe'] ?? 'Tambah';
                        $poin = $row['poin'] ?? 0;
                        ?>
                        <td><?= $row['nama_member'] ?></td>
                        <td><?= $jenis ?></td>
                        <td><?= (strtolower($jenis) == 'tambah' || strtolower($jenis) == 'masuk' ? '+' : '-') . number_format($poin) ?></td>
                        <td><?= $row['keterangan'] ?? '-' ?></td>
                    <?php else: ?>
                        <td>#<?= $row['id_pesanan'] ?></td>
                        <td><?= $row['nama_member'] ?? 'Umum' ?></td>
                        <td>
                            <?php
                            $metode_cetak = $row['metode_pembayaran'];
                            if (strpos($metode_cetak, ' - VA: ') !== false) {
                                $parts_cetak = explode(' - VA: ', $metode_cetak);
                                echo htmlspecialchars($parts_cetak[0]);
                            } else {
                                echo htmlspecialchars($metode_cetak);
                            }
                            ?>
                        </td>
                        <td class="text-right"><?= number_format($row['total_transaksi']) ?></td>
                        <?php $total += $row['total_transaksi']; ?>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>

            <?php if ($type == 'penjualan'): ?>
                <tr>
                    <td colspan="5" class="text-right"><strong>TOTAL PENDAPATAN :</strong></td>
                    <td class="text-right"><strong>Rp <?= number_format($total) ?></strong></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top: 50px; text-align: right; margin-right: 30px;">
        <p>Kisaran, <?= date('d F Y') ?></p>
        <p style="margin-bottom: 80px;">Owner Rasya.co,</p>
        <p><strong>( <?= $nama_owner ?> )</strong></p>
    </div>
</body>

</html>