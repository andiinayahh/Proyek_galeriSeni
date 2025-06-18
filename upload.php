<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config.php';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $kategori = $_POST['kategori'];
    $harga = $_POST['harga'];
    $gambar = $_FILES['gambar'];
    $id_seniman = $_SESSION['user_id'];
    $artworkId = $_POST['artworkId'];
    if ($artworkId) {
        $stmt = $pdo->prepare("SELECT judul, deskripsi, path_gambar, kategori, harga FROM karya_seni WHERE id = ?");
        $stmt->execute([$artworkId]);
        $existingArtwork = $stmt->fetch(PDO::FETCH_ASSOC);
        $judul = $judul ?: $existingArtwork['judul'];
        $deskripsi = $deskripsi ?: $existingArtwork['deskripsi'];
        $kategori = $kategori ?: $existingArtwork['kategori'];
        $harga = $harga ?: $existingArtwork['harga'];
        $path_gambar = $existingArtwork['path_gambar'];
    }
    if ($gambar['error'] === UPLOAD_ERR_OK) {
        $path_gambar = 'img/' . basename($gambar['name']);
        if (move_uploaded_file($gambar['tmp_name'], $path_gambar)) {
            if ($artworkId) {
                $stmt = $pdo->prepare("UPDATE karya_seni SET judul = ?, deskripsi = ?, path_gambar = ?, kategori = ?, harga = ? WHERE id = ?");
                $stmt->execute([$judul, $deskripsi, $path_gambar, $kategori, $harga, $artworkId]);
                header('Location: beranda.php?status=updated');
                exit;
            } else {
                $stmt = $pdo->prepare("INSERT INTO karya_seni (id_seniman, judul, deskripsi, path_gambar, kategori, harga) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$id_seniman, $judul, $deskripsi, $path_gambar, $kategori, $harga]);
                header('Location: beranda.php?status=success');
                exit;
            }
        } else {
            header('Location: beranda.php?status=failed');
            exit;
        }
    } else {
        if ($artworkId) {
            $stmt = $pdo->prepare("UPDATE karya_seni SET judul = ?, deskripsi = ?, kategori = ?, harga = ? WHERE id = ?");
            $stmt->execute([$judul, $deskripsi, $kategori, $harga, $artworkId]);
            header('Location: beranda.php?status=updated');
            exit;
        } else {
            header('Location: beranda.php?status=error');
            exit;
        }
    }
}
?>