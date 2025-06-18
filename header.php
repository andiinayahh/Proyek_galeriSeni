<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Galeri Seni - <?php echo ucfirst($page); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>">Galeri Seni</a>
            <div class="navbar-nav">
                <a class="nav-link" href="<?php echo BASE_URL; ?>?page=home">Beranda</a>
                <a class="nav-link" href="<?php echo BASE_URL; ?>?page=gallery">Galeri</a>
                <a class="nav-link" href="<?php echo BASE_URL; ?>?page=about">Tentang</a>
            </div>
        </div>
    </nav>
    <div class="container mt-4">