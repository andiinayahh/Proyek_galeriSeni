<?php
require_once 'config.php';

$page = $_GET['page'] ?? 'home';
$allowed_pages = ['home', 'gallery', 'about' , 'daftar'];

if (in_array($page, $allowed_pages)) {
    require "{$page}.php";
} else {
    require "404.php";
}

$allowed_pages = ['home', 'gallery', 'about', 'login'];

if (!in_array($page, $allowed_pages)) {
    $page = 'home';
}

require_once __DIR__ . '/header.php';
require_once __DIR__ . '/pages/' . $page . '.php';
require_once __DIR__ . '/footer.php';

echo __DIR__ . '/config.php';
die();

echo "Path yang dicari: " . __DIR__ . '/config.php';
echo "<br>File exists: " . (file_exists(__DIR__ . '/config.php') ? 'Ya' : 'Tidak');
die();
?>
