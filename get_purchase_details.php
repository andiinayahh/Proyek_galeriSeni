<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Pengguna belum login']);
    exit;
}
$purchaseId = $_GET['purchase_id'];
$stmt = $pdo->prepare("SELECT * FROM purchases WHERE id = ? AND user_id = ?");
$stmt->execute([$purchaseId, $_SESSION['user_id']]);
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);

if ($transaction) {
    $stmt_items = $pdo->prepare("SELECT * FROM purchase_items WHERE purchase_id = ?");
    $stmt_items->execute([$purchaseId]);
    $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
    $response = [
        'success' => true,
        'transaction' => [
            'transaction_number' => $transaction['transaction_number'],
            'transaction_date' => $transaction['created_at'],
            'payment_method' => $transaction['payment_method'],
            'status' => $transaction['status'],
            'total_amount' => $transaction['total_price'],
            'items' => $items
        ]
    ];

    echo json_encode($response);
} else {
    echo json_encode(['error' => 'Transaksi tidak ditemukan']);
}
?>
