<?php include 'menuu.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #fdfd96, #fff);
            box-sizing: border-box;
        }

        .container {
            padding: 20px;
        }

        .search-bar-container {
            display: flex;
            align-items: center;
            background-color: #fff;
            border-radius: 30px;
            padding: 10px 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            width: 30%;
            margin: 20px auto;
        }

        .search-bar-container ion-icon {
            font-size: 24px;
            color: #666;
            margin-right: 10px;
        }

        .search-bar {
            flex: 1;
            border: none;
            outline: none;
            font-size: 16px;
        }

        .toggle-buttons {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .toggle-buttons button {
            background-color: #fff;
            border: 2px solid #ddd;
            border-radius: 20px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            margin: 0 10px;
            transition: all 0.3s ease;
        }

        .toggle-buttons button.active {
            background-color: #fdfd96;
            border-color: #fdfd96;
            color: #000;
        }

        .card-container {
            position: relative;
            width: 600px;
            height: 550px;
            margin: 0 auto;
        }

        .card {
            display: block !important; /* Ensure the cards are displayed */
            visibility: visible !important; /* Ensure the cards are visible */
            background-color: #fff; /* Add a background color for visibility */
            border: 1px solid #ddd; /* Add a border for visibility */
            padding: 20px;
            margin-bottom: 20px;
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.5s ease, opacity 0.5s ease;
        }

        .card img {
            width: 100%;
            height: 300px;
            border-radius: 10px;
            object-fit: cover;
        }

        .card .actions {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .card .actions button {
            background-color: #fdfd96;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 14px;
        }

        .card .actions button:hover {
            background-color: #fce76c;
        }

        .card .info {
            margin-top: 10%;
        }

        .card .info h3 {
            margin: 0;
            font-size: 18px;
        }

        .card .info p {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }

        .card .info .offer {
            display: flex;
            justify-content: space-between;
            margin-top: 10%;
        }

        .card .info .offer div {
            background-color: #fdfd96;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
        }

        .card .nope,
        .card .match {
            position: absolute;
            top: 20px;
            font-size: 18px;
            font-weight: bold;
        }

        .card .nope {
            left: 20px;
            color: red;
        }

        .card .match {
            right: 20px;
            color: green;
        }

        .card:nth-child(1) {
            transform: rotate(0deg); /* Ensure the first card is not rotated */
        }

        .card:nth-child(2) {
            transform: rotate(-5deg); /* Rotate the second card slightly to the left */
        }

        .card:nth-child(3) {
            transform: rotate(5deg); /* Rotate the third card slightly to the right */
        }

        /* Responsive Styles */
        @media screen and (max-width: 1024px) {
            .card-container {
                width: 90%;
                height: auto;
            }

            .card img {
                height: 250px;
            }

            .card .info h3 {
                font-size: 16px;
            }

            .card .info p {
                font-size: 12px;
            }

            .card .info .offer div {
                font-size: 12px;
            }
        }

        @media screen and (max-width: 768px) {
            .search-bar-container {
                width: 80%;
            }

            .card-container {
                width: 100%;
                height: auto;
            }

            .card img {
                height: 200px;
            }

            .card .info h3 {
                font-size: 14px;
            }

            .card .info p {
                font-size: 12px;
            }

            .card .info .offer div {
                font-size: 12px;
            }

            .card .nope,
            .card .match {
                font-size: 14px;
            }
        }

        @media screen and (max-width: 400px) {
            .search-bar-container {
                width: 100%;
                padding: 5px 10px;
            }

            .card-container {
                width: 100%;
                height: auto;
            }

            .card img {
                height: 150px;
            }

            .card .info h3 {
                font-size: 12px;
            }

            .card .info p {
                font-size: 10px;
            }

            .card .info .offer div {
                font-size: 10px;
            }

            .card .nope,
            .card .match {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Search Bar -->
        <div class="search-bar-container">
            <ion-icon name="menu-outline"></ion-icon>
            <input type="text" class="search-bar" placeholder="Search barters">
            <ion-icon name="search-outline"></ion-icon>
        </div>

        <!-- Toggle Buttons -->
        <div class="toggle-buttons">
            <button class="active" onclick="filterBarters('online')">Online Barters</button>
            <button onclick="filterBarters('in-person')">Barters in Person</button>
        </div>

        <!-- Card Container -->
        <div class="card-container" id="card-container">
            <!-- Cards will be dynamically added here -->
        </div>
    </div>

    <script>
        let lastMessageId = 0; // Track the last message ID to avoid reloading all messages

        function fetchUsers() {
            fetch('fetch_users_for_search.php') // Use the PHP file to fetch users
                .then(response => response.json())
                .then(users => {
                    console.log('Fetched users:', users); // Debugging: Log the fetched users

                    if (users.error) {
                        console.error(users.error);
                        return;
                    }

                    const cardContainer = document.getElementById('card-container');
                    cardContainer.innerHTML = ''; // Clear existing cards

                    if (users.length === 0) {
                        cardContainer.innerHTML = '<p>No users found.</p>'; // Handle empty results
                        return;
                    }

                    users.forEach(user => {
                        const card = document.createElement('div');
                        card.className = 'card';

                        card.innerHTML = `
                            <img src="default-profile.png" alt="User Picture"> <!-- Replace with actual user profile picture if available -->
                            <div class="info">
                                <h3>${user.First_Name}</h3>
                                <p>${user.Skill}</p>
                                <div class="nope" onclick="handleNope(${user.User_ID})"><- Nope</div>
                                <div class="match" onclick="handleMatch(${user.User_ID})">Match -></div>
                                <div class="offer">
                                    <div>${user.Offer}</div>
                                    <div>${user.Exchange}</div>
                                </div>
                            </div>
                        `;

                        cardContainer.appendChild(card);
                    });

                    // Reinitialize swipe functionality
                    initializeSwipe();
                })
                .catch(error => console.error('Error fetching users:', error));
        }

        function initializeSwipe() {
            const cards = document.querySelectorAll('.card');

            if (cards.length === 0) {
                console.log('No cards to initialize swipe functionality.'); // Debugging
                return;
            }

            // Attach swipe functionality to the "Nope" and "Match" buttons
            cards.forEach((card, index) => {
                const nopeButton = card.querySelector('.nope');
                const matchButton = card.querySelector('.match');

                if (nopeButton) {
                    nopeButton.onclick = () => swipeCard('left', index);
                }

                if (matchButton) {
                    matchButton.onclick = () => swipeCard('right', index);
                }
            });
        }

        function swipeCard(direction, cardIndex) {
            const cards = document.querySelectorAll('.card');
            const currentCard = cards[cardIndex]; // Target the specific card
            if (!currentCard) return;

            // Apply swipe animation
            if (direction === 'right') {
                currentCard.style.transform = 'translateX(100%)';
            } else if (direction === 'left') {
                currentCard.style.transform = 'translateX(-100%)';
            }

            currentCard.style.opacity = '0';

            // Remove the swiped card from the DOM after the animation
            setTimeout(() => {
                currentCard.remove();

                // Reinitialize swipe functionality for the remaining cards
                initializeSwipe();
            }, 500); // Match the animation duration
        }

        function handleMatch(receiverId) {
            const message = prompt("Enter a message for the match (optional):");

            fetch('match_user.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `receiver_id=${receiverId}&message=${encodeURIComponent(message || '')}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Match request sent successfully!');
                        fetchUsers(); // Refresh the cards
                    } else {
                        alert('Error: ' + data.error);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function handleNope(receiverId) {
            // Perform any additional logic for "Nope" if needed
            fetchUsers(); // Refresh the cards
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
                        const messageElement = document.createElement('div');
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

        // Call fetchUsers when the page loads
        document.addEventListener('DOMContentLoaded', fetchUsers);
    </script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>