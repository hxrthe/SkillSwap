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
            <div class="card">
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
        </div>

        <div id="sent" class="tab-content">
            <div class="card">
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
        </div>

        <div id="ongoing" class="tab-content">
            <div class="card">
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
        </div>

        <div id="completed" class="tab-content">
            <div class="card">
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
        </div>
    </div>

    <script>
        function openTab(event, tabId) {
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => content.classList.remove('active'));

            // Remove active class from all tab links
            const tabLinks = document.querySelectorAll('.tab-link');
            tabLinks.forEach(link => link.classList.remove('active'));

            // Show the selected tab content and set the active class
            document.getElementById(tabId).classList.add('active');
            event.currentTarget.classList.add('active');
        }
    </script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>
