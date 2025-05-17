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
            background: url('./assets/images/finalbg2.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        .chat-container {
            margin: 20px auto;
            max-width: 90%; /* Adjust to fit smaller screens */
            width: 600px; /* Default width for larger screens */
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .chat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .chat-header h3 {
            margin: 0;
            font-size: 1.5rem;
            text-align: center;
            flex: 1;
        }

        .chat-messages {
            height: 300px;
            overflow-y: auto;
            padding: 20px;
            margin-bottom: 20px;
            background-color: var(--chat-bg);
        }

        .message-container {
            margin-bottom: 15px;
            width: 100%;
            clear: both;
        }

        .message {
            max-width: 80%;
            padding: 10px 15px;
            border-radius: 20px;
            display: block;
            word-wrap: break-word;
            position: relative;
            margin-bottom: 5px;
        }

        .message.received {
            background-color: var(--received-bg);
            float: left;
            margin-left: 10px;
        }

        .message.sent {
            background-color: var(--sent-bg);
            float: right;
            margin-right: 10px;
        }

        .message-content {
            display: flex;
            flex-direction: column;
        }

        .message-text {
            margin-bottom: 5px;
        }

        .message-time {
            font-size: 0.8em;
            color: var(--text-muted);
        }

        .message.received .message-time {
            align-self: flex-start;
            margin-left: 5px;
        }

        .message.sent .message-time {
            align-self: flex-end;
            margin-right: 5px;
        }

        .message-container {
            margin-bottom: 15px;
            width: 100%;
            clear: both;
            margin: 0 auto;
        }

        .message.received {
            background-color: var(--message-bg);
            color: var(--message-text);
            border-bottom-left-radius: 5px;
        }

        .message.sent {
            background-color: var(--message-sent-bg);
            color: var(--message-text);
            float: right;
            border-bottom-right-radius: 5px;
        }

        .message-content {
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .message-text {
            margin: 0;
            font-size: 14px;
            word-break: break-word;
            line-height: 1.4;
        }

        .message-time {
            font-size: 12px;
            color: rgba(51, 51, 51, 0.6);
            margin-top: 5px;
            text-align: right;
            padding-right: 5px;
        }

        .chat-input {
            display: flex;
            gap: 10px;
            flex-wrap: wrap; /* Allow wrapping on smaller screens */
        }

        .chat-input input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .chat-input button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
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
            font-size: 1rem;
        }

        .back-button:hover {
            background-color: #e53935;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .chat-container {
                width: 100%; /* Full width for smaller screens */
                padding: 15px;
            }

            .chat-header h3 {
                font-size: 1.2rem;
            }

            .chat-input input,
            .chat-input button,
            .back-button {
                font-size: 0.9rem;
            }

            .chat-messages {
                height: 250px; /* Adjust height for smaller screens */
            }
        }

        @media (max-width: 480px) {
            .chat-container {
                padding: 10px;
            }

            .chat-header h3 {
                font-size: 1rem;
            }

            .chat-messages {
                height: 200px; /* Further adjust height for very small screens */
            }

            .chat-input input,
            .chat-input button,
            .back-button {
                font-size: 0.8rem;
            }
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
            fetch(`fetch_messages.php?request_id=${requestId}`)
                .then(response => response.json())
                .then(messages => {
                    const chatMessages = document.getElementById('chat-messages');
                    chatMessages.innerHTML = ''; // Clear existing messages

                    messages.forEach(msg => {
                        const messageContainer = document.createElement('div');
                        messageContainer.className = 'message-container';
                        messageContainer.style.clear = 'both'; // Clear floats

                        const messageBubble = document.createElement('div');
                        messageBubble.className = `message ${msg.sender_id == <?php echo $_SESSION['user_id']; ?> ? 'sent' : 'received'}`;
                        
                        // Format the date using JavaScript's Date object
                        const formattedTime = new Date(msg.timestamp * 1000).toLocaleString();

                        messageBubble.innerHTML = `
                            <div class="message-content">
                                <div class="message-text">${msg.message}</div>
                                
                                <div class="message-time">${formattedTime}</div>
                            </div>
                        `;

                        messageContainer.appendChild(messageBubble);
                        chatMessages.appendChild(messageContainer);
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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            loadMessages(<?php echo $request_id; ?>);
        });
    </script>
</body>
</html>