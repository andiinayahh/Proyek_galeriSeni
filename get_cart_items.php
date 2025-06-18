<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

require_once 'config.php';

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT pi.artwork_id, k.judul, k.harga, pi.quantity
    FROM purchase_items pi
    JOIN karya_seni k ON pi.artwork_id = k.id
    JOIN purchases p ON pi.purchase_id = p.id
    WHERE p.user_id = ?
");
$stmt->execute([$user_id]);
$purchaseItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($purchaseItems);
exit;
?>