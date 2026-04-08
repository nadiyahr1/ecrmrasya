<style>
    /* Menggunakan Grid dengan 2 kolom sama besar */
    .stat-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        padding: 20px 20px;
        /* Padding besar agar card tinggi dan gagah */
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        border: 1px solid #f0f0f0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        /* transition: transform 0.2s ease; */
    }

    /* Garis warna di sebelah kiri */
    .stat-card.green {
        border-left: 6px solid #10b981;
    }

    .stat-card.blue {
        border-left: 6px solid #3b82f6;
    }

    .stat-card.orange {
        border-left: 6px solid #f59e0b;
    }

    .stat-card.purple {
        border-left: 6px solid #8b5cf6;
    }

    .stat-number {
        font-size: 20px;
        font-weight: bold;
        color: #333;
        margin: 12px 0 5px 0;
    }

    .stat-label {
        font-size: 12px;
        color: #888;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 1px;
    }

    /* Judul di atas setiap baris */
    .section-title {
        margin-top: 10px;
        margin-bottom: 15px;
        color: #555;
        font-size: 17px;
        border-bottom: 2px solid #eee;
        padding-bottom: 8px;
        display: inline-block;
    }

    /* Desain Panel Bawah */
    .dashboard-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
        margin-bottom: 40px;
    }

    .card-panel {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        border: 1px solid #f0f0f0;
    }

    .table-mini {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    .table-mini th {
        text-align: left;
        font-size: 13px;
        color: #888;
        padding: 12px 10px;
        border-bottom: 2px solid #eee;
    }

    .table-mini td {
        padding: 12px 10px;
        font-size: 14px;
        border-bottom: 1px solid #f9f9f9;
        color: #444;
    }

    .badge {
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: bold;
    }

    .badge-success {
        background: #dcfce7;
        color: #166534;
    }
</style>

<!-- <div>
    <h2 style="margin: 0 0 5px 0; color: #333;">Dashboard Pemilik</h2>
    <p style="margin: 0 0 25px 0; color: #777; font-size: 14px;">Ringkasan performa bisnis dan retensi pelanggan Rasya.co.</p>
</div> -->

<div class="section-title">Performa Bulan <?= $nama_bulan_ini ?></div>
<div class="stat-row">
    <div class="stat-card green">
        <div class="stat-label">Total Omzet Pendapatan</div>
        <div class="stat-number">Rp <?= number_format($omzet_bulan_ini ?? 0, 0, ',', '.') ?></div>
    </div>
    <div class="stat-card blue">
        <div class="stat-label">Total Pesanan Selesai</div>
        <div class="stat-number"><?= number_format($total_pesanan ?? 0) ?> Transaksi</div>
    </div>
</div>

<div class="section-title" style="margin-top: 15px;">Akumulasi Tahun <?= $tahun_ini ?></div>
<div class="stat-row">
    <div class="stat-card orange">
        <div class="stat-label">Total Member</div>
        <div class="stat-number"><?= number_format($total_member ?? 0) ?> Orang</div>
    </div>
    <div class="stat-card purple">
        <div class="stat-label">Total Poin Terpakai</div>
        <div class="stat-number"><?= number_format($poin_terpakai ?? 0) ?> Poin</div>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card-panel">
        <h3 style="margin: 0; font-size: 16px; color: #333;">Reservasi Fasilitas (Hari Ini)</h3>
        <table class="table-mini">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Nama Pelanggan</th>
                    <th>Fasilitas</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>14:00</td>
                    <td>Budi Santoso</td>
                    <td>Meja VIP 1</td>
                    <td><span class="badge badge-success">Dikonfirmasi</span></td>
                </tr>
                <tr>
                    <td>16:30</td>
                    <td>Siska Putri</td>
                    <td>Meeting Room</td>
                    <td><span class="badge badge-success">Dikonfirmasi</span></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="card-panel">
        <h3 style="margin: 0 0 15px 0; font-size: 16px; color: #333;">Transaksi Hari Ini</h3>
        <div style="display: flex; flex-direction: column; gap: 15px;">
            <div style="border-left: 3px solid #6F4E37; padding-left: 12px;">
                <div style="font-weight: bold; font-size: 14px; color: #333;">#PES-00124</div>
                <div style="font-size: 12px; color: #888; margin-top: 3px;">Rp 125.000 • 10 Menit lalu</div>
            </div>
            <div style="border-left: 3px solid #6F4E37; padding-left: 12px;">
                <div style="font-weight: bold; font-size: 14px; color: #333;">#PES-00123</div>
                <div style="font-size: 12px; color: #888; margin-top: 3px;">Rp 45.000 • 25 Menit lalu</div>
            </div>
        </div>
    </div>
</div>