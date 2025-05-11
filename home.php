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
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: url('./assets/images/finalbg2.jpg') no-repeat center center fixed;
            background-size: cover;
            box-sizing: border-box;
            overflow: hidden; /* Prevent scrollbars for floating emojis */
        }

        .main-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            gap: 20px;
        }

        .announcement-container {
            background-color: rgba(255, 255, 255, 0.8); /* Semi-transparent white background */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 80%;
            max-width: 800px;
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
            margin-top: 20px;
        }

        .user-greeting h1 {
            font-size: 60px;
            margin-bottom: 10px;
            white-space: nowrap; /* Prevent text wrapping */
            overflow: hidden;
            border-right: 2px solid #000; /* Cursor effect */
            animation: typing 3s steps(30, end) infinite, blink 0.5s step-end infinite alternate; /* Infinite typing animation */
        }

        .user-greeting p {
            font-size: 30px;
            color: #555;
        }

        .user-greeting .skillswap {
            font-size: 100px;
            font-weight: bold;
            color: #000;
        }

        /* Typing Animation */
        @keyframes typing {
            0% {
                width: 0;
            }
            50% {
                width: 100%; /* Full width of the text */
            }
            100% {
                width: 0; /* Reset to 0 for looping */
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
            pointer-events: none; /* Prevent interaction with emojis */
            z-index: 10; /* Ensure emojis are above other elements */
        }

        .emoji {
            position: absolute;
            bottom: -50px; /* Start below the screen */
            font-size: 40px; /* Ensure emojis are large enough */
            animation: floatUp 5s linear infinite; /* Infinite animation */
            z-index: 10; /* Ensure emojis are above other elements */
            opacity: 1; /* Ensure emojis are visible */
        }

        @keyframes floatUp {
            from {
                transform: translateY(0);
                opacity: 1;
            }
            to {
                transform: translateY(-100vh); /* Move up out of the screen */
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <div class="bg">
        <img src="ssbg4.png" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: -1;">
    </div>
    <div class="main-container">
        <!-- Announcement Section -->
        <div class="announcement-container">
            <?php if ($currentAnnouncement): ?>
                <div class="announcement">
                    <div class="announcement-title">
                        <?php echo htmlspecialchars($currentAnnouncement['Title']); ?>
                    </div>
                    <div class="announcement-content">
                        <?php echo nl2br(htmlspecialchars($currentAnnouncement['Content'])); ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="announcement-content">
                    No announcements available at the moment.
                </div>
            <?php endif; ?>
        </div>

        <!-- User Greeting Section -->
        <div class="user-greeting">
            <h1 id="greeting-text">Hi <?php echo htmlspecialchars($user['First_Name']); ?>!</h1>
            <p>Let your skills shine through</p>
            <div class="skillswap">SKILLSWAP</div>
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
    </script>
</body>
</html>