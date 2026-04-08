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

                <form action="index.php?controller=auth&action=prosesRegister" method="POST">
                    <input type="hidden" name="action" value="register">

                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" class="auth-input" placeholder="Nama sesuai KTP" required>
                    </div>

                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" id="username" class="auth-input" placeholder="Buat username unik" required>
                        <small id="error-username" class="error-text"></small>
                    </div>

                    <div class="form-group">
                        <label>Nomor WhatsApp</label>
                        <input type="number" name="no_telp" id="no_telp" class="auth-input" placeholder="0812..." required>
                        <small id="error-no_telp" class="error-text"></small>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="auth-input" placeholder="Minimal 6 karakter" required>
                    </div>

                    <button type="submit" class="btn-auth">Buat Akun Member</button>
                </form>

                <div class="auth-footer">
                    Sudah jadi member? <a href="index.php?controller=auth&action=login">Login di Sini</a>
                </div>
            </div>
        </div>
    </div>

</body>

</html>