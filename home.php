<?php
session_start();

require_once 'SkillSwapDatabase.php';
require_once 'SP.php';

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: loginpagee.php");
    exit();
}

// Get user data
$db = new Database();
$conn = $db->getConnection();
$stmt = $conn->prepare("SELECT * FROM users WHERE User_ID = :user_id");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch all other users from the database
$stmt = $conn->prepare("SELECT * FROM users WHERE User_ID != :user_id");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'menuu.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: url('./assets/images/finalbg2.jpg') no-repeat center center fixed;
            background-size: cover;
            box-sizing: border-box;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            padding: 20px;
        }

        .left-section {
            flex: 2;
            padding-right: 20px;
        }

        .right-section {
            text-align: right;
            padding-right: 100px;
            width: 35%;
        }

        .search-bar-container {
            position: relative;
            width: 65%;
            margin-left: 30px;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .search-bar {
            width: 100%;
            padding: 15px 40px;
            border: 1px solid #ccc;
            font-size: 20px;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .search-bar-container ion-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            font-size: 24px;
            color: #666;
            cursor: pointer;
        }

        .search-bar-container .menu-icon {
            left: 10px;
        }

        .search-bar-container .search-icon {
            right: 10px;
        }

        .matches-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 50px;
            margin-left: 30px;
            margin-bottom: 20px;
        }

        .matches-header h2 {
            margin: 0;
            font-size: 50px;
        }

        .matches-header a {
            text-decoration: none;
            color: rgb(0, 0, 0);
            font-size: 16px;
            margin-right: 250px;
        }

        .card {
            display: none; /* Hide all cards by default */
            flex-direction: column;
            width: 60%;
            height: 400px;
            margin-left: 30px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card.active {
            display: flex; /* Show only the active card */
        }

        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 100px;
        }

        .card-header img {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            margin-top: 20px;
            margin-left: 20px;
        }

        .card-header h3 {
            margin-left: 20px;
            font-size: 30px;
        }

        .card-header p {
            margin-left: 20px;
            font-size: 14px;
            color: #666;
        }

        .card-content {
            margin-bottom: 10px;
        }

        .card-content p {
            margin: 5px 0;
            font-size: 16px;
        }

        .card-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-actions button {
            padding: 10px 20px;
            background-color: rgb(0, 0, 0);
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .card-actions button:hover {
            background-color: rgb(63, 63, 63);
        }

        .card-actions a {
            text-decoration: none;
            font-size: 20px;
            color: rgb(0, 0, 0);
        }

        .right-section h1 {
            font-size: 70px;
            width: 50%;
            padding-left: 1px;
            margin-right: 50px;
            margin-top: 80px;
        }

        .right-section p {
            font-size: 20px;
            color: black;
        }

        .right-section .skillswap {
            font-size: 100px;
            width: 50%;
            font-weight: bold;
            padding-left: 1px;
            margin-right: 100px;
            color: rgb(0, 0, 0);
        }

        /* Responsive Styles */
        @media screen and (max-width: 1024px) {
            .right-section h1 {
                font-size: 50px;
            }

            .right-section .skillswap {
                font-size: 80px;
            }
        }

        @media screen and (max-width: 768px) {
            .container {
                flex-direction: column;
                padding: 10px;
            }

            .left-section,
            .right-section {
                width: 100%;
                padding: 0;
                text-align: center;
            }

            .search-bar-container {
                width: 90%;
                margin: 10px auto;
            }

            .matches-header {
                flex-direction: column;
                align-items: flex-start;
                margin-left: 0;
                font-size: 30px;
            }

            .matches-header h2 {
                font-size: 30px;
            }

            .matches-header a {
                margin-right: 0;
                font-size: 14px;
            }

            .card {
                width: 90%;
                margin: 10px auto;
                height: auto;
            }

            .card-header img {
                width: 150px;
                height: 150px;
            }

            .card-header h3 {
                font-size: 24px;
            }

            .card-header p {
                font-size: 12px;
            }

            .card-content p {
                font-size: 14px;
            }

            .card-actions button {
                font-size: 14px;
                padding: 8px 16px;
            }
        }

        @media screen and (max-width: 400px) {
            .search-bar-container {
                width: 100%;
                margin: 10px auto;
            }

            .matches-header {
                font-size: 20px;
            }

            .matches-header h2 {
                font-size: 20px;
            }

            .matches-header a {
                font-size: 12px;
            }

            .card-header img {
                width: 120px;
                height: 120px;
            }

            .card-header h3 {
                font-size: 18px;
            }

            .card-header p {
                font-size: 10px;
            }

            .card-content p {
                font-size: 12px;
            }

            .card-actions button {
                font-size: 12px;
                padding: 6px 12px;
            }

            .right-section h1 {
                font-size: 40px;
            }

            .right-section p {
                font-size: 14px;
            }

            .right-section .skillswap {
                font-size: 50px;
            }
        }

        /* Add sliding animations */
        @keyframes slideOutLeft {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(-100%);
                opacity: 0;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        .card.slide-left {
            animation: slideOutLeft 0.5s forwards;
        }

        .card.slide-right {
            animation: slideOutRight 0.5s forwards;
        }
    </style>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>
<body>
    <div class="bg">
        <img src="ssbg4.png" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: -1;">
    </div>
    <div class="container">
        <!-- Left Section -->
        <div class="left-section">
            <div class="search-bar-container">
                <ion-icon name="menu-outline" class="menu-icon"></ion-icon>
                <input type="text" class="search-bar" placeholder="Search...">
                <ion-icon name="search-outline" class="search-icon"></ion-icon>
            </div>
            <div class="matches-header">
                <h2>Matches</h2>
                <a href="all_matches.php">See all matches ></a>
            </div>

            <!-- Loop through all users and display them as cards -->
            <?php foreach ($allUsers as $index => $otherUser): ?>
                <div class="card <?php echo $index === 0 ? 'active' : ''; ?>" id="card-<?php echo $index; ?>">
                    <div class="card-header">
                        <img src="default-profile.png" alt="User Picture"> <!-- Replace with actual user profile picture if available -->
                        <div>
                            <h3><?php echo htmlspecialchars($otherUser['First_Name']); ?></h3>
                            <!-- <p><?php echo htmlspecialchars($otherUser['Skill']); ?></p> Replace 'Skill' with the actual column name for the user's skill -->
                            <!-- <p><?php echo htmlspecialchars($otherUser['Location']); ?></p> Replace 'Location' with the actual column name for the user's location -->
                        </div>
                    </div>
                    <div>
                        <a href="#" onclick="showNextCard(<?php echo $index; ?>, 'left')">Negotiate later</a> | 
                        <a href="#" onclick="startNegotiation(<?php echo $otherUser['User_ID']; ?>)">Start negotiation</a>
                    </div>
                    <div class="card-content">
                        <!-- <p>Will offer you: <?php echo htmlspecialchars($otherUser['Offer']); ?></p> Replace 'Offer' with the actual column name -->
                        <!-- <p>In exchange for: <?php echo htmlspecialchars($otherUser['Exchange']); ?></p> Replace 'Exchange' with the actual column name -->
                    </div>
                    <div class="card-actions">
                        <button>Contact</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Right Section -->
        <div class="right-section">
            <h1>Hi <?php echo htmlspecialchars($user['First_Name']); ?>!</h1>
            <p>Let your skills shine through</p>
            <div class="skillswap">SKILLSWAP</div>
        </div>
    </div>

    <script>
        function showNextCard(currentIndex, direction) {
            const currentCard = document.getElementById(`card-${currentIndex}`);
            const nextCard = document.getElementById(`card-${currentIndex + 1}`);

            if (currentCard) {
                // Add sliding animation based on the direction
                if (direction === 'left') {
                    currentCard.classList.add('slide-left');
                } else if (direction === 'right') {
                    currentCard.classList.add('slide-right');
                }

                // Wait for the animation to complete before hiding the card
                setTimeout(() => {
                    currentCard.classList.remove('active', 'slide-left', 'slide-right');
                    if (nextCard) {
                        nextCard.classList.add('active'); // Show the next card
                    }
                }, 500); // Match the animation duration (0.5s)
            }
        }

        function startNegotiation(receiverId) {
            const message = prompt("Enter a message for the negotiation (optional):");

            fetch('start_negotiation.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `receiver_id=${receiverId}&message=${encodeURIComponent(message || '')}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Negotiation request sent successfully!');
                    } else {
                        alert('Error: ' + data.error);
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>