<?php
require_once 'config.php';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $konfirmasi = $_POST['confirm-password'];
    $role = $_POST['role'];
    if (empty($nama) || empty($email) || empty($password) || empty($konfirmasi) || empty($role)) {
        die("Semua kolom wajib diisi.");
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Email tidak valid.");
    }

    if ($password !== $konfirmasi) {
        die("Konfirmasi password tidak cocok.");
    }
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        die("Email sudah terdaftar.");
    }
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nama, $email, $password_hash, $role]);
    if ($stmt->rowCount() > 0) {
        header("Location: login.html?success=1");
        exit;
    } else {
        die("Gagal mendaftar.");
    }
} else {
    die("Akses tidak sah.");
}
?>
