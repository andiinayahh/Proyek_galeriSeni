<?php
require_once 'config.php';

if (!isset($_GET['conversation_id']) || !is_numeric($_GET['conversation_id'])) {
    echo json_encode(['error' => 'Invalid conversation ID']);
    exit;
}

$conversation_id = $_GET['conversation_id'];

$stmt = $pdo->prepare("
    SELECT m.message, m.sent_at, u.nama AS sender_name
    FROM messages m
    JOIN users u ON m.sender_id = u.id
    WHERE m.conversation_id = ?
    ORDER BY m.sent_at ASC
");
$stmt->execute([$conversation_id]);

$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['messages' => $messages]);
exit;
?>
