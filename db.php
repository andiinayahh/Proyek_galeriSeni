<?php
$host = "localhost";               // default: localhost
$user = "root";                   // default user XAMPP
$pass = "";                       // default password kosong
$dbname = "galeri_digital";

// Membuat koneksi
$conn = new mysqli($host, $user, $pass, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>

