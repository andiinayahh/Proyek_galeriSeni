<?php
// includes/init.php
session_start();

// Load semua file yang diperlukan
require_once 'config/database.php';
require_once 'includes/functions.php';

// Set timezone
date_default_timezone_set('Asia/Makassar');

// Error reporting (untuk development)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>