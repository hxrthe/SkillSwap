<?php include 'menuu.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .inbox-container {
            width: 100%;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .tabs {
            display: flex;
            justify-content: space-around;
            background: linear-gradient(to right, #f9f9f9, #fdfd96);
            padding: 10px 0;
            border-bottom: 2px solid #ddd;
        }

        .tabs button {
            background: none;
            border: none;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            color: #333;
            padding: 10px 20px;
        }

        .tabs button.active {
            border-bottom: 3px solid #333;
            color: #000;
        }

        .tab-content {
            flex: 1;
            padding: 20px;
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .card {
            display: flex;
            align-items: center;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-right: 15px;
        }

        .card .card-info {
            display: flex;
            flex-direction: column;
        }

        .card .card-info h3 {
            margin: 0;
            font-size: 18px;
        }

        .card .card-info p {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }

        .card .card-info .location {
            display: flex;
            align-items: center;
            font-size: 14px;
            color: #333;
        }

        .card .card-info .location ion-icon {
            margin-right: 5px;
        }

        .message {
            display: block; /* Ensure each message is on its own line */
            margin-bottom: 10px;
            padding: 10px 15px; /* Add padding for spacing */
            border-radius: 10px;
            max-width: 70%;
            word-wrap: break-word; /* Ensure long words break properly */
        }

        .message.user {
            background-color: #d1f7c4;
            text-align: right;
            margin-left: auto; /* Push the user's message to the right */
        }

        .message.other-person {
            background-color: #f1f1f1;
            text-align: left;
            margin-right: auto; /* Push the other person's message to the left */
        }
    </style>
</head>
<body>
    <div class="inbox-container">
        <!-- Tabs -->
        <div class="tabs">
            <button class="tab-link active" onclick="openTab(event, 'request')">Request</button>
            <button class="tab-link" onclick="openTab(event, 'sent')">Sent</button>
            <button class="tab-link" onclick="openTab(event, 'ongoing')">Ongoing</button>
            <button class="tab-link" onclick="openTab(event, 'completed')">Completed</button>
        </div>

        <!-- Tab Content -->
        <div id="request" class="tab-content active">
            <!-- Example Cards -->
            <div class="card" onclick="openMessagePage('Cooking', 'James Doe')">
                <img src="bob.jpg" alt="User Picture">
                <div class="card-info">
                    <h3>Cooking</h3>
                    <p>Offered by James Doe</p>
                    <div class="location">
                        <ion-icon name="location-outline"></ion-icon>
                        Rosario, Batangas
                    </div>
                </div>
            </div>
            <!-- Repeat similar cards for 10 examples -->
            <div class="card" onclick="openMessagePage('Baking', 'Anna Smith')">
                <img src="anna.jpg" alt="User Picture">
                <div class="card-info">
                    <h3>Baking</h3>
                    <p>Offered by Anna Smith</p>
                    <div class="location">
                        <ion-icon name="location-outline"></ion-icon>
                        Quezon City, Philippines
                    </div>
                </div>
            </div>
            <!-- Add 8 more cards here -->
        </div>

        <div id="sent" class="tab-content">
            <!-- Example Cards -->
            <div class="card" onclick="openMessagePage('Graphic Design', 'Jane Smith')">
                <img src="jane.jpg" alt="User Picture">
                <div class="card-info">
                    <h3>Graphic Design</h3>
                    <p>Requested by Jane Smith</p>
                    <div class="location">
                        <ion-icon name="location-outline"></ion-icon>
                        Los Angeles, USA
                    </div>
                </div>
            </div>
            <!-- Add 9 more cards here -->
        </div>

        <div id="ongoing" class="tab-content">
            <!-- Example Cards -->
            <div class="card" onclick="openMessagePage('Web Development', 'John Doe')">
                <img src="doe.jpg" alt="User Picture">
                <div class="card-info">
                    <h3>Web Development</h3>
                    <p>Ongoing with John Doe</p>
                    <div class="location">
                        <ion-icon name="location-outline"></ion-icon>
                        New York, USA
                    </div>
                </div>
            </div>
            <!-- Add 9 more cards here -->
        </div>

        <div id="completed" class="tab-content">
            <!-- Example Cards -->
            <div class="card" onclick="openMessagePage('Photography', 'Sarah Lee')">
                <img src="sarah.png" alt="User Picture">
                <div class="card-info">
                    <h3>Photography</h3>
                    <p>Completed with Sarah Lee</p>
                    <div class="location">
                        <ion-icon name="location-outline"></ion-icon>
                        Manila, Philippines
                    </div>
                </div>
            </div>
            <!-- Add 9 more cards here -->
        </div>
    </div>

    <div id="messagePage" style="display: none; padding: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <button onclick="goBackToInbox()" style="background-color: yellow; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">Back</button>
            <h3 id="messageTitle">Messaging</h3>
            <div>
                <button onclick="openSettings()" style="background-color: yellow; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">Settings</button>
            </div>
        </div>
        <div id="chatContainer" style="height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
            <!-- Example Messages -->
            <div class="message other-person">
                Hi! Are you available for a session tomorrow?
            </div>
            <div class="message user">
                Yes, I am available. What time works for you?
            </div>
            <div class="message other-person">
                How about 3 PM?
            </div>
            <div class="message user">
                Sounds good. Let me know if you need anything else.
            </div>
            <div class="message other-person">
                Great! See you tomorrow.
            </div>
        </div>
        <div style="display: flex; gap: 10px;">
            <input type="text" id="messageInput" placeholder="Type a message..." style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            <button onclick="sendMessage()" style="background-color: yellow; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">Send</button>
        </div>
    </div>

    <div id="scheduleModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 1001;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 400px; background-color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);">
            <h3>Schedule Appointment</h3>
            <p>Please schedule an appointment before starting the conversation.</p>
            <form>
                <label for="appointmentDate">Date:</label>
                <input type="date" id="appointmentDate" style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 5px;">
                <label for="appointmentTime">Time:</label>
                <input type="time" id="appointmentTime" style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 5px;">
                <button type="button" onclick="closeScheduleModal()" style="background-color: yellow; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">Confirm</button>
            </form>
        </div>
    </div>

    <script>
        let firstOpen = {};

        function openTab(event, tabId) {
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => content.classList.remove('active'));

            const tabLinks = document.querySelectorAll('.tab-link');
            tabLinks.forEach(link => link.classList.remove('active'));

            document.getElementById(tabId).classList.add('active');
            event.currentTarget.classList.add('active');
        }

        function openMessagePage(service, person) {
            const messagePage = document.getElementById('messagePage');
            const inboxContainer = document.querySelector('.inbox-container');
            const messageTitle = document.getElementById('messageTitle');

            inboxContainer.style.display = 'none';
            messagePage.style.display = 'block';
            messageTitle.textContent = `${service} with ${person}`;

            if (!firstOpen[person]) {
                firstOpen[person] = true;
                document.getElementById('scheduleModal').style.display = 'block';
            }
        }

        function goBackToInbox() {
            const messagePage = document.getElementById('messagePage');
            const inboxContainer = document.querySelector('.inbox-container');

            messagePage.style.display = 'none';
            inboxContainer.style.display = 'flex';
        }

        function closeScheduleModal() {
            document.getElementById('scheduleModal').style.display = 'none';
        }

        function sendMessage() {
            const messageInput = document.getElementById('messageInput');
            const chatContainer = document.getElementById('chatContainer');

            if (messageInput.value.trim() !== '') {
                const message = document.createElement('div');
                message.textContent = messageInput.value;
                message.className = 'message user';
                message.style.marginBottom = '10px';
                message.style.padding = '10px';
                message.style.backgroundColor = '#d1f7c4';
                message.style.borderRadius = '10px';
                message.style.maxWidth = '70%';
                message.style.textAlign = 'right';
                message.style.marginLeft = 'auto';
                chatContainer.appendChild(message);
                messageInput.value = '';
            }
        }
    </script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>
