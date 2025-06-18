<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'config.php';
$transactionNumber = $_POST['transactionNumber'];
$transactionDate = $_POST['transactionDate'];
$totalAmount = $_POST['totalAmount'];
$cartItems = json_decode($_POST['cartItems'], true);
$paymentMethod = $_POST['paymentMethod'];
$status = 'completed';
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['nama'];
try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("INSERT INTO transactions (transaction_number, user_id, transaction_date, total_amount, payment_method, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$transactionNumber, $user_id, $transactionDate, $totalAmount, $paymentMethod, $status]);
    $transactionId = $pdo->lastInsertId();
    foreach ($cartItems as $item) {
        $stmt_item = $pdo->prepare("INSERT INTO transaction_items (transaction_id, artwork_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt_item->execute([$transactionId, $item['artwork_id'], $item['quantity'], $item['harga']]);
    }
    foreach ($cartItems as $item) {
        $stmt_delete = $pdo->prepare("DELETE FROM purchase_items WHERE artwork_id = ?");
        $stmt_delete->execute([$item['artwork_id']]);
    }
    $pdo->commit();
    header("Location: beranda.php?status=success");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: beranda.php?status=failed");
    exit;
}
?>
