<?php
session_start();
require_once 'SkillSwapDatabase.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$request_id = isset($_GET['request_id']) ? intval($_GET['request_id']) : null;
$last_message_id = isset($_GET['last_message_id']) ? intval($_GET['last_message_id']) : 0;

if (!$request_id) {
    echo json_encode(['error' => 'Invalid request ID']);
    exit();
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("
        SELECT m.*, u.First_Name AS sender_name
        FROM messages m
        JOIN users u ON m.sender_id = u.User_ID
        WHERE m.request_id = :request_id AND m.id > :last_message_id
        ORDER BY m.created_at ASC
    ");
    $stmt->execute([
        ':request_id' => $request_id,
        ':last_message_id' => $last_message_id
    ]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the messages as JSON
    header('Content-Type: application/json');
    echo json_encode($messages);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

?>

<style>
.chat-messages div {
    margin-bottom: 10px;
    padding: 5px 10px;
    border-radius: 5px;
}

.chat-messages div:nth-child(odd) {
    background-color: #e0f7fa; /* Light blue for the user's messages */
    text-align: right;
}

.chat-messages div:nth-child(even) {
    background-color: #f1f8e9; /* Light green for the other user's messages */
    text-align: left;
}
</style>

<script>
let lastMessageId = 0; // Track the last message ID to avoid reloading all messages

function loadMessages(requestId) {
    fetch(`fetch_messages.php?request_id=${requestId}&last_message_id=${lastMessageId}`)
        .then(response => response.json())
        .then(messages => {
            console.log('Fetched messages:', messages); // Debugging: Log fetched messages

            if (messages.error) {
                console.error(messages.error);
                return;
            }

            const chatMessages = document.getElementById('chat-messages');

            messages.forEach(msg => {
                // Check if the message already exists in the chat
                if (document.querySelector(`#message-${msg.id}`)) return;

                const messageElement = document.createElement('div');
                messageElement.id = `message-${msg.id}`; // Add a unique ID for each message
                messageElement.textContent = `${msg.sender_name}: ${msg.message}`;
                chatMessages.appendChild(messageElement);

                // Update the last message ID
                lastMessageId = Math.max(lastMessageId, msg.id);
            });

            // Scroll to the bottom of the chat
            chatMessages.scrollTop = chatMessages.scrollHeight;
        })
        .catch(error => console.error('Error fetching messages:', error));
}

// Example usage
const exampleMessages = [
    {
        "id": 1,
        "request_id": 1,
        "sender_id": 2,
        "message": "Hello!",
        "created_at": "2025-05-02 10:00:00",
        "sender_name": "John"
    },
    {
        "id": 2,
        "request_id": 1,
        "sender_id": 3,
        "message": "Hi there!",
        "created_at": "2025-05-02 10:01:00",
        "sender_name": "Jane"
    }
];

exampleMessages.forEach(msg => {
    const messageElement = document.createElement('div');
    messageElement.textContent = `${msg.sender_name}: ${msg.message}`;
    document.getElementById('chat-messages').appendChild(messageElement);
});
</script>