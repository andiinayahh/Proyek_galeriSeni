<?php
// includes/functions.php

// Fungsi untuk redirect
function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit();
}

// Fungsi untuk sanitize input
function clean($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

// Fungsi untuk cek login
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Fungsi untuk format tanggal
function formatDate($date) {
    return date('d/m/Y H:i', strtotime($date));
}

// Fungsi untuk upload gambar
function uploadImage($file, $folder = 'gallery') {
    $target_dir = "assets/uploads/" . $folder . "/";
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Cek apakah file adalah gambar
    $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
    if (!in_array($file_extension, $allowed_types)) {
        return false;
    }
    
    // Cek ukuran file (max 5MB)
    if ($file['size'] > 5000000) {
        return false;
    }
    
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return $new_filename;
    }
    
    return false;
}

// Fungsi untuk mendapatkan data gallery
function getGalleryItems($limit = null) {
    global $conn;
    $sql = "SELECT * FROM gallery ORDER BY created_at DESC";
    if ($limit) {
        $sql .= " LIMIT " . $limit;
    }
    return mysqli_query($conn, $sql);
}

// Fungsi untuk mendapatkan detail gallery
function getGalleryDetail($id) {
    global $conn;
    $id = clean($id);
    $sql = "SELECT * FROM gallery WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result);
}
?>