<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}
require_once 'config.php';
$user_id = $_SESSION['user_id'];
$seniman_id = $_POST['seniman_id']; 
$artwork_id = $_POST['artwork_id'];
if (!is_numeric($seniman_id) || $seniman_id <= 0) {
    echo json_encode(['error' => 'Invalid Seniman ID']);
    exit;
}
$stmt_check_conversation = $pdo->prepare("
    SELECT id 
    FROM conversations 
    WHERE (user1_id = ? AND user2_id = ?) 
       OR (user1_id = ? AND user2_id = ?)
");
$stmt_check_conversation->execute([$user_id, $seniman_id, $seniman_id, $user_id]);
$existing_conversation = $stmt_check_conversation->fetch();

if ($existing_conversation) {
    echo json_encode(['conversation_id' => $existing_conversation['id']]);
    exit;
}
$stmt_create_conversation = $pdo->prepare("
    INSERT INTO conversations (user1_id, user2_id) 
    VALUES (?, ?)
");
$stmt_create_conversation->execute([$user_id, $seniman_id]);
$conversation_id = $pdo->lastInsertId();
echo json_encode(['conversation_id' => $conversation_id]);
exit;
?>
