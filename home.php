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

try {
    // Fetch all active announcements
    $stmt = $conn->query("
        SELECT Title, Content 
        FROM announcements 
        WHERE Is_Active = 1
    ");
    $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Select one random announcement
    $currentAnnouncement = !empty($announcements) ? $announcements[array_rand($announcements)] : null;
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $currentAnnouncement = null;
}
?>

<?php include 'menuu.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Great+Vibes:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <style>
         @import url('https://fonts.googleapis.com/css2?family=Luckiest+Guy&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: url('./assets/images/finalbg2.jpg') no-repeat center center fixed;
            background-size: cover;
            box-sizing: border-box;
            overflow: hidden;
        }

        .main-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            gap: 20px;
            padding: 0;
            overflow: hidden;
        }

        .announcement-container {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 80%;
            max-width: 800px;
            overflow-y: auto;
            max-height: 50vh;
        }

        .announcement-title {
            font-size: 40px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .announcement-content {
            font-size: 20px;
            color: #333;
        }

        .user-greeting {
            text-align: center;
            margin-bottom: 20px;
            margin-top: -10vh;
        }

        .user-greeting h1 {
            font-size: 60px;
            white-space: nowrap;
            overflow: hidden;
            border-right: 2px solid #000;
            animation: typing 3s steps(30, end) infinite, blink 0.5s step-end infinite alternate;
        }

        .user-greeting p {
            font-size: 30px;
            color: #555;
        }

        .user-greeting .skillswap {
            font-family: 'Luckiest Guy', cursive, Arial, sans-serif;
            font-size: 100px;
            font-weight: bold;
            color: #000;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            margin-top: 10px;
        }

        /* Typing Animation */
        @keyframes typing {
            0% {
                width: 0;
            }
            50% {
                width: 100%;
            }
            100% {
                width: 0;
            }
        }

        /* Blinking Cursor Animation */
        @keyframes blink {
            from {
                border-color: transparent;
            }
            to {
                border-color: black;
            }
        }

        /* Floating Emojis */
        .emoji-container {
            position: fixed;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 10;
        }

        .emoji {
            position: absolute;
            bottom: -50px;
            font-size: 40px;
            animation: floatUp 5s linear infinite;
            z-index: 10;
            opacity: 1;
        }

        @keyframes floatUp {
            from {
                transform: translateY(0);
                opacity: 1;
            }
            to {
                transform: translateY(-100vh);
                opacity: 0;
            }
        }

        /* Notification Style */
        .notification-container {
            position: fixed;
            left: 20px;
            top: 100px;
            z-index: 1000;
        }

        .notification-bell {
            background: white;
            border-radius: 50%;
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            position: relative;
            transition: transform 0.2s;
        }

        .notification-bell:hover {
            transform: scale(1.1);
        }

        .notification-bell i {
            font-size: 24px;
            color: #333;
        }

        .notification-dropdown {
            position: absolute;
            left: 85px;
            top: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            width: 300px;
            max-height: 400px;
            overflow-y: auto;
            display: none;
            z-index: 1001;
        }

        .notification-dropdown.active {
            display: block;
        }

        .notification-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-title {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
            font-size: 16px;
        }

        .notification-content {
            color: #666;
            font-size: 14px;
            line-height: 1.4;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            display: none;
        }

        .notification-badge.active {
            display: flex;
        }

        /* Hide the original announcement container */
        .announcement-container {
            display: none;
        }

        /* Responsive Design */
        @media (max-width: 900px) {
            .user-greeting h1 {
                font-size: 40px;
            }
            .user-greeting p {
                font-size: 22px;
            }
            .user-greeting .skillswap {
                font-size: 60px;
            }
            .main-container {
                height: 80vh;
            }
        }

        @media (max-width: 600px) {
            .user-greeting h1 {
                font-size: 28px;
            }
            .user-greeting p {
                font-size: 16px;
            }
            .user-greeting .skillswap {
                font-size: 36px;
            }
            .main-container {
                height: 70vh;
                gap: 10px;
            }
            .notification-container {
                left: 10px;
                top: 70px;
            }
        }
    </style>
    <!-- Add Font Awesome for the bell icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <!-- Notification Bell -->
    <div class="notification-container">
        <div class="notification-bell" onclick="toggleNotifications()">
            <i class="fas fa-bell"></i>
            <div class="notification-badge" id="notificationBadge">1</div>
        </div>
        <div class="notification-dropdown" id="notificationDropdown">
            <?php if ($currentAnnouncement): ?>
                <div class="notification-item">
                    <div class="notification-title">
                        <?php echo htmlspecialchars($currentAnnouncement['Title']); ?>
                    </div>
                    <div class="notification-content">
                        <?php echo nl2br(htmlspecialchars($currentAnnouncement['Content'])); ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="notification-item">
                    <div class="notification-content">
                        No announcements available at the moment.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="main-container">
        <!-- User Greeting Section -->
        <div class="user-greeting">
            <h1 id="greeting-text">Hi <?php echo htmlspecialchars($user['First_Name']); ?>!</h1>
            <p>Let your skills shine through</p>
            <div class="skillswap">SkillSwap</div>
        </div>
    </div>

    <!-- Floating Emojis -->
    <div class="emoji-container">
        <!-- Emojis will be dynamically added here -->
    </div>

    <script>
        // Add floating emojis dynamically
        const emojiContainer = document.querySelector('.emoji-container');
        const emojis = ['🎉', '✨', '💡', '🔥', '🌟', '🎈', '💎', '🌈'];

        function createEmoji() {
            const emoji = document.createElement('div');
            emoji.classList.add('emoji');
            emoji.textContent = emojis[Math.floor(Math.random() * emojis.length)];
            emoji.style.left = Math.random() * 100 + 'vw'; // Random horizontal position
            emoji.style.animationDuration = Math.random() * 3 + 3 + 's'; // Random float duration
            emojiContainer.appendChild(emoji);

            console.log('Emoji added:', emoji.textContent); // Debug log

            // Remove emoji after animation
            emoji.addEventListener('animationend', () => {
                emoji.remove();
            });
        }

        // Generate emojis at intervals
        setInterval(createEmoji, 300);

        // Notification functionality
        function toggleNotifications() {
            const dropdown = document.getElementById('notificationDropdown');
            const badge = document.getElementById('notificationBadge');
            
            dropdown.classList.toggle('active');
            if (dropdown.classList.contains('active')) {
                badge.classList.remove('active');
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('notificationDropdown');
            const bell = document.querySelector('.notification-bell');
            
            if (!bell.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.remove('active');
            }
        });

        // Show badge if there are announcements
        document.addEventListener('DOMContentLoaded', function() {
            const hasAnnouncements = <?php echo $currentAnnouncement ? 'true' : 'false'; ?>;
            if (hasAnnouncements) {
                document.getElementById('notificationBadge').classList.add('active');
            }
        });
    </script>
</body>
</html>