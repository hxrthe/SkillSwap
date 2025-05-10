<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Chat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }

        .container {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .tabs {
            display: flex;
            background-color: yellow;
            padding: 10px;
            justify-content: space-around;
        }

        .tab-link {
            flex: 1;
            text-align: center;
            padding: 10px;
            cursor: pointer;
            font-weight: bold;
            color: #000;
            background-color: #fff;
            border: 1px solid #000;
            border-radius: 5px;
            margin: 0 5px;
        }

        .tab-link.active {
            background-color: #000;
            color: #fff;
        }

        .tab-content {
            display: none;
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }

        .tab-content.active {
            display: block;
        }

        .people-list {
            margin-bottom: 20px;
        }

        .person {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
            cursor: pointer;
            background-color: #fff;
        }

        .person:hover {
            background-color: yellow;
        }

        .chat-container {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .chat-header {
            background-color: yellow;
            padding: 10px;
            font-weight: bold;
            text-align: center;
            position: relative; /* Required for the Back button to position correctly */
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
            border: 1px solid #ddd;
        }

        .chat-input {
            display: flex;
            padding: 10px;
            border-top: 1px solid #ddd;
        }

        .chat-input input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .chat-input button {
            background-color: #000;
            color: #fff;
            border: none;
            padding: 10px 20px;
            margin-left: 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .chat-input button:hover {
            background-color: #333;
        }

        .message {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 10px;
            max-width: 70%;
        }

        .message.user {
            background-color: #000;
            color: #fff;
            align-self: flex-end;
        }

        .message.other {
            background-color: yellow;
            color: #000;
            align-self: flex-start;
        }

        .back-button {
            background-color: #000; /* Black background */
            color: #fff; /* White text */
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            position: absolute; /* Position the button relative to the .chat-header */
            top: 10px; /* Place it at the top */
            left: 10px; /* Place it on the left */
            z-index: 10; /* Ensure it appears above other elements */
        }

        .back-button:hover {
            background-color: #333; /* Darker black on hover */
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Tabs -->
        <div class="tabs">
            <div class="tab-link active" onclick="openTab(event, 'requests')">Requests</div>
            <div class="tab-link" onclick="openTab(event, 'sent')">Sent</div>
            <div class="tab-link" onclick="openTab(event, 'ongoing')">Ongoing</div>
            <div class="tab-link" onclick="openTab(event, 'completed')">Completed</div>
        </div>

        <!-- Tab Content -->
        <div id="requests" class="tab-content active">
            <div class="people-list" id="peopleListRequests">
                <!-- Example people -->
                <div class="person" onclick="openChat('requests', 1, 'Alice')">Alice</div>
                <div class="person" onclick="openChat('requests', 2, 'Bob')">Bob</div>
            </div>
        </div>

        <div id="sent" class="tab-content">
            <div class="people-list" id="peopleListSent">
                <!-- Example people -->
                <div class="person" onclick="openChat('sent', 3, 'Charlie')">Charlie</div>
                <div class="person" onclick="openChat('sent', 4, 'Diana')">Diana</div>
            </div>
        </div>

        <div id="ongoing" class="tab-content">
            <div class="people-list" id="peopleListOngoing">
                <!-- Example people -->
                <div class="person" onclick="openChat('ongoing', 5, 'Eve')">Eve</div>
                <div class="person" onclick="openChat('ongoing', 6, 'Frank')">Frank</div>
            </div>
            <div class="chat-container" id="chatContainerOngoing" style="display: none;">
                <div class="chat-header" id="chatHeaderOngoing">
                    <button onclick="goBackToList('ongoing')" class="back-button">Back</button>
                    <span style="margin-left: 60px;">Chat with Eve</span>
                </div>
                <div class="chat-messages" id="chatMessagesOngoing"></div>
                <div class="chat-input">
                    <input type="text" id="messageInputOngoing" placeholder="Type a message...">
                    <button onclick="sendMessage('ongoing')">Send</button>
                </div>
            </div>
        </div>

        <div id="completed" class="tab-content">
            <div class="people-list" id="peopleListCompleted">
                <!-- Example people -->
                <div class="person">Grace</div>
                <div class="person">Hank</div>
            </div>
        </div>
    </div>

    <script>
        function openTab(event, tabName) {
            const tabs = document.querySelectorAll('.tab-content');
            const tabLinks = document.querySelectorAll('.tab-link');

            tabs.forEach(tab => tab.classList.remove('active'));
            tabLinks.forEach(link => link.classList.remove('active'));

            document.getElementById(tabName).classList.add('active');
            event.currentTarget.classList.add('active');
        }

        function openChat(status, receiverId, personName) {
            const chatContainer = document.getElementById(`chatContainer${capitalize(status)}`);
            const chatHeader = document.getElementById(`chatHeader${capitalize(status)}`);
            const chatMessages = document.getElementById(`chatMessages${capitalize(status)}`);
            const peopleList = document.getElementById(`peopleList${capitalize(status)}`);

            // Hide the people list and show the chat container
            peopleList.style.display = 'none';
            chatContainer.style.display = 'flex';

            chatHeader.textContent = `Chat with ${personName}`;
            chatMessages.innerHTML = ''; // Clear previous messages

            loadMessages(status, receiverId);
        }

        function sendMessage(status) {
            const input = document.getElementById(`messageInput${capitalize(status)}`);
            const message = input.value.trim();

            if (message === '') {
                console.error('Message is empty'); // Debugging
                return;
            }

            console.log('Sending message:', message); // Debugging

            fetch('chat_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `receiver_id=1&message=${encodeURIComponent(message)}&status=${status}` // Replace 1 with the actual receiver ID
            })
                .then(response => {
                    console.log('Response received:', response); // Debugging
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data); // Debugging
                    if (data.status === 'success') {
                        input.value = ''; // Clear the input field
                        loadMessages(status, 1); // Reload messages after sending
                    } else {
                        console.error('Error:', data.error);
                    }
                })
                .catch(error => console.error('Error sending message:', error));
        }

        function loadMessages(status, receiverId) {
            console.log('Loading messages for status:', status, 'receiverId:', receiverId); // Debugging

            fetch(`chat_api.php?receiver_id=${receiverId}&status=${status}`)
                .then(response => {
                    console.log('Response received:', response); // Debugging
                    return response.json();
                })
                .then(messages => {
                    console.log('Messages received:', messages); // Debugging
                    const chatMessages = document.getElementById(`chatMessages${capitalize(status)}`);
                    chatMessages.innerHTML = ''; // Clear previous messages

                    messages.forEach(msg => {
                        const messageDiv = document.createElement('div');
                        messageDiv.textContent = msg.message;
                        messageDiv.className = `message ${msg.sender_id == <?php echo $_SESSION['user_id']; ?> ? 'user' : 'other'}`;
                        chatMessages.appendChild(messageDiv);
                    });

                    chatMessages.scrollTop = chatMessages.scrollHeight; // Scroll to the bottom
                })
                .catch(error => console.error('Error loading messages:', error));
        }

        function capitalize(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        function goBackToList(status) {
            const chatContainer = document.getElementById(`chatContainer${capitalize(status)}`);
            const peopleList = document.getElementById(`peopleList${capitalize(status)}`);

            // Hide the chat container and show the people list
            chatContainer.style.display = 'none';
            peopleList.style.display = 'block';
        }
    </script>
</body>
</html>