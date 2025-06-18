<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

require_once 'config.php';
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT k.id AS artwork_id, k.judul AS title, k.harga AS price, p.quantity 
                       FROM purchases p
                       JOIN karya_seni k ON p.artwork_id = k.id
                       WHERE p.user_id = ?");
$stmt->execute([$user_id]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($cartItems)) {
    echo json_encode(['error' => 'No items in cart']);
    exit;
}
echo json_encode(['cartItems' => $cartItems]);
exit;
?>
