<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login - E-CRM Rasya.co</title>
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
                <p>Selamat Datang Kembali!</p>
            </div>
        </div>

        <div class="auth-side-form">
            <div class="auth-container-inner">
                <div class="auth-header">
                    <h2>Login</h2>
                    <p>Silakan masuk ke akun Rasya.co Anda.</p>
                </div>

                <form action="index.php?controller=auth&action=prosesLogin" method="POST">
                    <input type="hidden" name="action" value="login">

                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="auth-input" placeholder="Masukkan username" required>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="auth-input" placeholder="Masukkan password" required>
                    </div>

                    <button type="submit" class="btn-auth">Masuk Sekarang</button>
                </form>

                <div class="auth-footer">
                    Belum punya akun? <a href="index.php?controller=auth&action=register">Daftar Member</a>
                </div>
            </div>
        </div>
    </div>

</body>

</html>