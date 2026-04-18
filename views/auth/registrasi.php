<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Daftar Member - Rasya.co</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/auth.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&family=Manrope:wght@200..800&family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <div class="auth-page">
        <div class="auth-side-image" style="background-image: url('assets/gambar/coffeeshop.jpg');">
            <div class="image-overlay">
                <h1>Rasya.co Coffee & Eatery</h1>
                <p>Gabung Jadi Member & Raih Keuntungan!</p>
            </div>
        </div>

        <div class="auth-side-form">
            <div class="auth-container-inner">
                <div class="auth-header">
                    <h2>Daftar Akun</h2>
                    <p>Gabung jadi member, kumpulkan poin, dan nikmati hadiah eksklusif!</p>
                </div>

                <form action="index.php?controller=auth&action=prosesRegistrasi" method="POST">
                    <input type="hidden" name="action" value="register">

                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" name="nama" class="auth-input" placeholder="Masukkan Nama Anda" required>
                    </div>

                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" id="username" class="auth-input" placeholder="Min. 5 karakter, huruf kecil tanpa spasi" pattern="^[a-z0-9]{5,}$" required>
                        <small id="error-username" class="error-text" style="display:block; margin-top:5px; font-weight:bold; font-size:11px;"></small>
                    </div>

                    <div class="form-group">
                        <label>Nomor Telepon</label>
                        <input type="tel" name="no_telp" id="no_telp" class="auth-input" placeholder="Cth: 0812... atau +62812..." pattern="^(08|\+62)[0-9]{8,13}$" required>
                        <small id="error-no_telp" class="error-text" style="display:block; margin-top:5px; font-weight:bold; font-size:11px;"></small>
                    </div>

                    <div class="form-group" style="position: relative;">
                        <label>Password</label>
                        <input type="password" name="password" id="password" class="auth-input"
                            placeholder="Min. 8 karakter (Huruf Besar, Kecil, Angka)"
                            pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                            required>

                        <i class="fa-solid fa-eye" id="togglePassword"
                            style="position: absolute; right: 15px; top: 43px; cursor: pointer; color: #6F4E37;"></i>
                    </div>

                    <small id="error-password" class="error-text"
                        style="display:block; margin-top: -5px; margin-bottom: 15px; font-weight:bold; font-size:11px;"></small>

                    <button type="submit" class="btn-auth">Buat Akun Member</button>
                </form>

                <div class="auth-footer">
                    Sudah jadi member? <a href="index.php?controller=auth&action=login">Login di Sini</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function(e) {
            // Ubah tipe input
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            // Ubah ikon mata (buka/tutup)
            this.classList.toggle('fa-eye-slash');
        });
        // Validasi Real-time Username
        document.getElementById('username').addEventListener('input', function() {
            let val = this.value;
            let regex = /^[a-z0-9]{5,}$/;
            let err = document.getElementById('error-username');
            if (!regex.test(val) && val.length > 0) {
                err.innerText = "❌ Harus huruf kecil, tanpa spasi, minimal 5 karakter.";
                err.style.color = "#ef4444";
            } else {
                err.innerText = "";
            }
        });

        // Validasi Real-time Nomor Telepon
        document.getElementById('no_telp').addEventListener('input', function() {
            let val = this.value;
            let regex = /^(08|\+62)[0-9]{8,13}$/;
            let err = document.getElementById('error-no_telp');
            if (!regex.test(val) && val.length > 0) {
                err.innerText = "Wajib diawali 08 atau +62 (10-15 digit angka).";
                err.style.color = "#ef4444";
            } else {
                err.innerText = "";
            }
        });

        // Validasi Real-time Password
        document.getElementById('password').addEventListener('input', function() {
            let val = this.value;
            let regex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/;
            let err = document.getElementById('error-password');
            if (!regex.test(val) && val.length > 0) {
                err.innerText = "Min. 8 karakter, wajib ada huruf besar, kecil, & angka.";
                err.style.color = "#ef4444";
            } else if (val.length > 0) {
                err.innerText = "Password kuat!";
                err.style.color = "#10b981";
            } else {
                err.innerText = "";
            }
        });
    </script>

</body>

</html>