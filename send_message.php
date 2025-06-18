<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}
require_once 'config.php';
$user_id = $_SESSION['user_id'];
$conversation_id = $_POST['conversation_id'];
$message = $_POST['message'];
$stmt_check_conversation = $pdo->prepare("SELECT id FROM conversations WHERE id = ?");
$stmt_check_conversation->execute([$conversation_id]);
$conversation = $stmt_check_conversation->fetch();
if (!$conversation) {
    echo json_encode(['error' => 'Conversation not found']);
    exit;
}
$stmt_send_message = $pdo->prepare("
    INSERT INTO messages (conversation_id, sender_id, message) 
    VALUES (?, ?, ?)
");
$stmt_send_message->execute([$conversation_id, $user_id, $message]);
$stmt_get_message = $pdo->prepare("
    SELECT m.message, m.sent_at, u.nama AS sender_name 
    FROM messages m
    JOIN users u ON m.sender_id = u.id
    WHERE m.conversation_id = ?
    ORDER BY m.sent_at DESC LIMIT 1
");
$stmt_get_message->execute([$conversation_id]);
$new_message = $stmt_get_message->fetch();
echo json_encode([
    'success' => true,
    'conversation_id' => $conversation_id,
    'message' => $new_message['message'],
    'sender_name' => $new_message['sender_name'],
    'sent_at' => $new_message['sent_at']
]);

exit;
?>
