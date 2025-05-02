<?php
session_start();

require_once 'SkillSwapDatabase.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: loginpagee.php");
    exit();
}

$request_id = isset($_GET['request_id']) ? intval($_GET['request_id']) : null;

if (!$request_id) {
    echo "Invalid request.";
    exit();
}

// Fetch chat details
$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("SELECT r.*, u1.First_Name AS sender_name, u2.First_Name AS receiver_name, u1.User_ID AS sender_id, u2.User_ID AS receiver_id
                        FROM requests r
                        JOIN users u1 ON r.sender_id = u1.User_ID
                        JOIN users u2 ON r.receiver_id = u2.User_ID
                        WHERE r.id = :request_id");
$stmt->execute([':request_id' => $request_id]);
$request = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$request) {
    echo "Chat not found.";
    exit();
}

// Determine the other user's name
$other_user_name = ($_SESSION['user_id'] == $request['sender_id']) ? $request['receiver_name'] : $request['sender_name'];
?>

<?php include 'menuu.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with <?php echo htmlspecialchars($other_user_name); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(to right, #fdfd96, #fff);
        }

        .chat-container {
            margin-top: 50px;
            max-width: 600px;
            margin-left: 400px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
        }

        .chat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .chat-header h3 {
            margin: 0;
        }

        .chat-messages {
            height: 300px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #fdfd96;
        }

        .chat-input {
            display: flex;
            gap: 10px;
        }

        .chat-input input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .chat-input button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .chat-input button:hover {
            background-color: #45a049;
        }

        .back-button {
            padding: 10px 15px;
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
        }

        .back-button:hover {
            background-color: #e53935;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <button class="back-button" onclick="history.back()">Back</button>
        <div class="chat-header">
            <h3>Chat with <?php echo htmlspecialchars($other_user_name); ?></h3>
        </div>
        <div class="chat-messages" id="chat-messages">
            <!-- Chat messages will be dynamically loaded here -->
        </div>
        <div class="chat-input">
            <input type="text" id="chat-input" placeholder="Type a message...">
            <button onclick="sendMessage(<?php echo $request_id; ?>)">Send</button>
        </div>
    </div>

    <script>
        let lastMessageId = 0; // Track the last message ID to avoid reloading all messages

        function sendMessage(requestId) {
            const message = document.getElementById('chat-input').value;
            if (!message.trim()) {
                alert('Please enter a message.');
                return;
            }

            fetch('send_message.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `request_id=${requestId}&message=${encodeURIComponent(message)}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('chat-input').value = ''; // Clear input
                        loadMessages(requestId); // Reload messages
                    } else {
                        alert('Error: ' + data.error);
                    }
                })
                .catch(error => console.error('Error sending message:', error));
        }

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

        // Load messages when the page loads
        document.addEventListener('DOMContentLoaded', () => {
            const requestId = <?php echo $request_id; ?>;

            // Load initial messages
            loadMessages(requestId);

            // Poll for new messages every 2 seconds
            setInterval(() => {
                loadMessages(requestId);
            }, 2000);
        });
    </script>
</body>
</html>