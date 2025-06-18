<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'config.php';
$artworkId = $_POST['artworkId']; 
$artworkTitle = $_POST['artworkTitle']; 
$artworkPrice = $_POST['artworkPrice'];
$quantity = $_POST['quantity'];
$paymentMethod = $_POST['paymentMethod'];
$totalPrice = $artworkPrice * $quantity;
$user_id = $_SESSION['user_id']; 
$stmt_purchase = $pdo->prepare("
    INSERT INTO purchases (user_id, total_price, payment_method, status) 
    VALUES (?, ?, ?, 'pending')
");
$stmt_purchase->execute([$user_id, $totalPrice, $paymentMethod]);

$purchase_id = $pdo->lastInsertId();
$stmt_purchase_item = $pdo->prepare("
    INSERT INTO purchase_items (purchase_id, artwork_id, title, price, quantity) 
    VALUES (?, ?, ?, ?, ?)
");
$stmt_purchase_item->execute([$purchase_id, $artworkId, $artworkTitle, $artworkPrice, $quantity]);
unset($_SESSION['cart']);
header("Location: beranda.php?success=true");
exit;
?>
