<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi Member - Rasya.co</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f4f4f4; margin:0; }
        .regis-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0px 0px 10px rgba(0,0,0,0.1); width: 350px; }
        input, select { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #0275d8; color: white; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>

<div class="regis-container">
    <h2>Daftar Member Baru</h2>
    <form action="proses_registrasi.php" method="POST">
        <input type="text" name="nama" placeholder="Nama Lengkap" required>
        <input type="text" name="username" placeholder="Username untuk Login" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="text" name="no_telp" placeholder="Nomor Telepon/WA" required>
        <button type="submit">Daftar Sekarang</button>
    </form>
    <p style="text-align:center;"><a href="index.php">Sudah punya akun? Login</a></p>
</div>

</body>
</html>