<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil data dari session
$nama = $_SESSION['nama'];
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Beranda</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            background: white;
            padding: 25px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        a.logout {
            display: inline-block;
            margin-top: 20px;
            color: red;
            text-decoration: none;
        }
        a.logout:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Selamat datang, <?php echo htmlspecialchars($nama); ?>!</h2>
        <p>Peran Anda: <strong><?php echo htmlspecialchars($role); ?></strong></p>

        <p>Ini adalah halaman beranda setelah login berhasil.</p>

        <a class="logout" href="logout.php">Logout</a>
    </div>
</body>
</html>
