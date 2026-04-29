# Sistem E-CRM Berbasis Web - Cafe Rasya.co

Sistem Informasi *Electronic Customer Relationship Management* (E-CRM) ini dikembangkan sebagai proyek Tugas Akhir untuk strategi retensi pelanggan di Café Rasya.co. Sistem ini fokus pada pengelolaan hubungan pelanggan melalui fitur keanggotaan, loyalitas poin, dan integrasi pembayaran digital.

## 🚀 Fitur Utama
* **Sistem Membership:** Pendaftaran pelanggan dengan tingkatan tier (Bronze, Silver, Gold).
* **Loyalty Points:** Akumulasi poin dari setiap transaksi yang dapat ditukarkan dengan promo/voucher.
* **Integrasi Midtrans:** Proses pembayaran transaksi transfer bank secara otomatis menggunakan Midtrans Payment Gateway (Sandbox Mode).
* **Manajemen Voucher:** Pembuatan dan klaim voucher promo untuk meningkatkan retensi pelanggan.
* **Multi-Aktor:**
    * **Pelanggan:** Memantau poin, riwayat transaksi, dan menukar voucher.
    * **Admin:** Mengelola data transaksi dan validasi operasional.
    * **Owner:** Melihat laporan perkembangan pelanggan dan efektivitas strategi E-CRM.

## 🛠️ Teknologi yang Digunakan
* **Bahasa Pemrograman:** PHP Native
* **Arsitektur:** MVC (Model-View-Controller)
* **Database:** MySQL
* **Web Server:** Apache (XAMPP)
* **Payment Gateway:** Midtrans API
* **Tools:** Git, GitHub, Ngrok (untuk testing webhook lokal)

## 📁 Struktur Folder
* `app/config`: Konfigurasi database dan base URL.
* `app/controllers`: Logika utama aplikasi.
* `app/models`: Pengaturan query database.
* `app/views`: Tampilan antarmuka (User Interface).
* `midtrans/`: Library integrasi API Midtrans.
* `assets/`: File CSS, JS, dan Gambar.

## ⚙️ Instalasi Lokal
1. Clone repositori ini:
   ```bash
   git clone [https://github.com/nadiyahr1/ecrmrasya.git](https://github.com/nadiyahr1/ecrmrasya.git)
