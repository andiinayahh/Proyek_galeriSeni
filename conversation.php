<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'config.php';

$conversation_id = $_GET['id'];
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT u1.nama AS user1, u2.nama AS user2, m.message, m.sent_at, m.sender_id 
    FROM conversations c
    JOIN users u1 ON c.user1_id = u1.id
    JOIN users u2 ON c.user2_id = u2.id
    JOIN messages m ON m.conversation_id = c.id
    WHERE c.id = ?
    ORDER BY m.sent_at ASC
");
$stmt->execute([$conversation_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
if ($messages):
    ?>
    <div class="conversation-container">
        <h3>Percakapan antara <?= htmlspecialchars($messages[0]['user1']); ?> dan
            <?= htmlspecialchars($messages[0]['user2']); ?></h3>
        <div class="messages">
            <?php foreach ($messages as $message): ?>
                <p><strong><?= ($message['sender_id'] == $user_id) ? 'Anda' : ($message['sender_id'] == $messages[0]['user1'] ? $messages[0]['user1'] : $messages[0]['user2']); ?>:</strong>
                    <?= htmlspecialchars($message['message']); ?> <em>(<?= $message['sent_at']; ?>)</em></p>
            <?php endforeach; ?>
        </div>
        <form action="send_message.php" method="POST">
            <textarea name="message" required></textarea><br>
            <button type="submit">Kirim Pesan</button>
        </form>
    </div>
<?php else: ?>
    <p>Percakapan tidak ditemukan.</p>
<?php endif; ?>