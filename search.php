<?php
session_start();
require_once 'SkillSwapDatabase.php';

// Get current user's ID
$currentUserId = $_SESSION['user_id'];

// Get users that haven't been matched with current user
$db = new Database();
$conn = $db->getConnection();

// Get all users except current user
try {
    // First try to create the user_skills table if it doesn't exist
    $conn->exec("CREATE TABLE IF NOT EXISTS user_skills (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        skill_name VARCHAR(255) NOT NULL,
        skill_type VARCHAR(20) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(User_ID)
    )");

    // Get all users except current user
    $stmt = $conn->prepare("
        SELECT u.*, 
               (SELECT GROUP_CONCAT(skill_name) 
                FROM user_skills 
                WHERE user_id = u.User_ID AND skill_type = 'can_share') as can_share_skills,
               (SELECT GROUP_CONCAT(skill_name) 
                FROM user_skills 
                WHERE user_id = u.User_ID AND skill_type = 'want_to_learn') as want_to_learn_skills
        FROM users u
        WHERE u.User_ID != :current_user_id
        AND u.User_ID NOT IN (
            SELECT DISTINCT CASE 
                WHEN sender_id = :current_user_id THEN request_id 
                ELSE sender_id 
            END as matched_user
            FROM messages 
            WHERE sender_id = :current_user_id OR request_id = :current_user_id
        )
        ORDER BY u.User_ID DESC
    ");
    $stmt->execute([':current_user_id' => $currentUserId]);
    $unmatchedUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debug: Log the number of users found
    error_log("Found " . count($unmatchedUsers) . " unmatched users");

    // Debug: Log the first user's data if any found
    if (!empty($unmatchedUsers)) {
        error_log("First user data: " . print_r($unmatchedUsers[0], true));
    }

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    // If there's any error, show empty skills
    $stmt = $conn->prepare("
        SELECT u.*, 
               '' as can_share_skills,
               '' as want_to_learn_skills
        FROM users u
        WHERE u.User_ID != :current_user_id
        AND u.User_ID NOT IN (
            SELECT DISTINCT CASE 
                WHEN sender_id = :current_user_id THEN request_id 
                ELSE sender_id 
            END as matched_user
            FROM messages 
            WHERE sender_id = :current_user_id OR request_id = :current_user_id
        )
        ORDER BY u.User_ID DESC
    ");
    $stmt->execute([':current_user_id' => $currentUserId]);
    $unmatchedUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$stmt = $conn->prepare("SELECT GROUP_CONCAT(skill_name) as current_skills FROM user_skills WHERE user_id = :user_id");
$stmt->execute([':user_id' => $currentUserId]);
$currentSkills = $stmt->fetch(PDO::FETCH_ASSOC)['current_skills'];

include 'menuu.php';

?>

<!DOCTYPE html>
<html lang="en" data-theme="<?php echo isset($_SESSION['theme']) ? $_SESSION['theme'] : 'light'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Matches</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        :root {
            --bg-color: #ffffff;
            --text-color: #333333;
            --card-bg: #f8f9fa;
            --border-color: #dee2e6;
            --primary-color: #4CAF50;
        }

        [data-theme="dark"] {
            --bg-color: #1a1a1a;
            --text-color: #ffffff;
            --card-bg: #2d2d2d;
            --border-color: #444444;
            --primary-color: #66BB6A;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            padding: 0;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .search-bar-container {
            display: flex;
            align-items: center;
            background-color: var(--card-bg);
            border-radius: 30px;
            padding: 10px 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .search-bar {
            flex: 1;
            border: none;
            outline: none;
            font-size: 16px;
            background-color: var(--bg-color);
            color: var(--text-color);
            padding: 8px;
            border-radius: 25px;
        }

        .toggle-buttons {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .toggle-buttons button {
            background-color: var(--card-bg);
            border: 2px solid var(--border-color);
            border-radius: 20px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            margin: 0 10px;
            transition: all 0.3s ease;
        }

        .toggle-buttons button.active {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .card-container {
            position: relative;
            width: 100%;
            max-width: 800px;
            height: auto;
            margin: 0 auto;
            overflow: visible;
            padding: 20px 0;
        }

        .card {
            display: block;
            visibility: visible;
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            width: 100%;
            min-height: 200px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
            position: relative;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        .card-content {
            display: flex;
            align-items: flex-start;
            gap: 30px;
            width: 100%;
        }

        .profile-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
            border: 3px solid var(--card-bg);
        }

        .profile-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .info {
            flex: 1;
            min-width: 0;
        }

        .info h3 {
            margin: 0;
            font-size: 24px;
            color: var(--primary-color);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .info p {
            margin: 5px 0;
            color: var(--text-color);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .skills {
            margin-top: 15px;
            width: 100%;
        }

        .skill-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            width: 100%;
        }

        .skill-tag {
            background-color: var(--primary-color);
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 14px;
            white-space: nowrap;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        @media (max-width: 768px) {
            .card-content {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .profile-image {
                width: 80px;
                height: 80px;
            }

            .info h3 {
                font-size: 20px;
                text-align: center;
            }

            .skill-list {
                justify-content: center;
            }

            .skill-tag {
                font-size: 13px;
                padding: 4px 8px;
            }
        }

        @media (max-width: 480px) {
            .card {
                padding: 15px;
            }

            .info h3 {
                font-size: 18px;
            }

            .skill-tag {
                font-size: 12px;
                padding: 3px 6px;
            }
        }

        .accept-button:hover, .decline-button:hover {
            transform: scale(1.1);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .tab-content {
            display: none;
            background-color: var(--card-bg);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .tab-content.active {
            display: block;
        }

        .match-request {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .match-request:last-child {
            border-bottom: none;
        }

        .match-request img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .match-request .info {
            flex: 1;
        }

        .match-request .info h4 {
            margin: 0;
            font-size: 16px;
            color: var(--text-color);
        }

        .match-request .info p {
            margin: 5px 0 0;
            font-size: 14px;
            color: var(--text-color);
        }

                .profile-image {
                    width: 80px;
                    height: 80px;
                }

                .info h3 {
                    font-size: 20px;
                    text-align: center;
                }

                .skill-list {
                    justify-content: center;
                }

                .skill-tag {
                    font-size: 13px;
                    padding: 4px 8px;
                }
            }

            @media (max-width: 480px) {
                .card {
                    padding: 15px;
                }

                .info h3 {
                    font-size: 18px;
                }

                .skill-tag {
                    font-size: 12px;
                    padding: 3px 6px;
                }
            }

            .accept-button:hover, .decline-button:hover {
                transform: scale(1.1);
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            }

            .tab-content {
                display: none;
                background-color: var(--card-bg);
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .tab-content.active {
                display: block;
            }

            .match-request {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 15px;
                border-bottom: 1px solid var(--border-color);
            }

            .match-request:last-child {
                border-bottom: none;
            }

            .match-request img {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                object-fit: cover;
            }

            .match-request .info {
                flex: 1;
            }

            .match-request .info h4 {
                margin: 0;
                font-size: 16px;
                color: var(--text-color);
            }

            .match-request .info p {
                margin: 5px 0 0;
                font-size: 14px;
                color: var(--text-color);
            }

            .match-request .actions {
                display: flex;
                gap: 10px;
            }

            .accept-button, .decline-button {
                position: absolute;
                bottom: -40px;
                width: 60px;
                height: 60px;
                border-radius: 50%;
                border: none;
                cursor: pointer;
                font-size: 30px;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            .accept-button {
                right: 20px;
                background-color: #4CAF50;
                color: white;
            }

            .decline-button {
                left: 20px;
                background-color: #f44336;
                color: white;
            }

            .accept-button:hover, .decline-button:hover {
                transform: scale(1.1);
                box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            }

            @media (max-width: 768px) {
                .accept-button, .decline-button {
                    width: 50px;
                    height: 50px;
                    font-size: 24px;
                    bottom: -30px;
                }
            }

            @media (max-width: 480px) {
                .accept-button, .decline-button {
                    width: 45px;
                    height: 45px;
                    font-size: 20px;
                    bottom: -25px;
                }
            }

            .search-bar-container {
                display: flex;
                align-items: center;
                background-color: #fff;
                border-radius: 30px;
                padding: 10px 20px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                margin-bottom: 20px;
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

            .card .action-icons {
                position: relative;
                width: 100%;
                display: flex;
                justify-content: space-between;
                padding: 0 40px; /* Increased padding from 20px to 40px */
                margin-top: 30px; /* Increased margin from 20px to 30px */
                gap: 30px; /* Increased gap from 20px to 30px */
            }

            .card .action-icons .icon {
                font-size: 32px; /* Increased font size from 24px to 32px */
                cursor: pointer;
                transition: transform 0.3s ease;
            }

            .card .action-icons .heart {
                color: #ff4444;
                margin-right: 60px;
            }

            .card .action-icons .heart:hover {
                transform: scale(1.2);
            }

            .card .action-icons .x {
                color: #444;
            }

            .card .action-icons .x:hover {
                transform: scale(1.2);
            }

            @media (max-width: 600px) {
                .container {
                    padding: 10px;
                }

                .search-bar-container {
                    flex-direction: column;
                    align-items: stretch;
                    padding: 15px;
                }

                .search-bar {
                    margin-bottom: 10px;
                }

                .toggle-buttons {
                    margin-bottom: 15px;
                }

                .card {
                    padding: 15px;
                }

                .card-content {
                    flex-direction: column;
                    align-items: center;
                    text-align: center;
                }

                .profile-image {
                    width: 80px;
                    height: 80px;
                }

                .info h3 {
                    font-size: 20px;
                }

                .skill-list {
                    justify-content: center;
                }
            }
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
            .search-bar {
                margin-bottom: 10px;
            }

            .toggle-buttons {
                margin-bottom: 15px;
            }

            .card {
                padding: 15px;
            }

            .card-content {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .profile-image {
                width: 80px;
                height: 80px;
            }

            .info h3 {
                font-size: 20px;
            }

            .skill-list {
                justify-content: center;
            }
        }
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

        .card .action-icons {
            position: relative;
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 30px;
            margin-top: 25px;
            gap: 25px;
        }

        .card .action-icons .icon {
            font-size: 28px;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .card .action-icons .heart {
            color: #ff4444;
            margin-right: 60px;
        }

        .card .action-icons .heart:hover {
            transform: scale(1.2);
        }

        .card .action-icons .x {
            color: #444;
        }

        .card .action-icons .x:hover {
            transform: scale(1.2);
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

        .accept-button, .decline-button {
            display: inline-block; /* Ensure buttons are displayed */
            background-color: #4CAF50; /* Accept button color */
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
            transition: background-color 0.3s ease;
        }

        .decline-button {
            background-color: #f44336; /* Decline button color */
        }

        .accept-button:hover {
            background-color: #45a049;
        }

        .decline-button:hover {
            background-color: #d32f2f;
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

        <div id="requests" class="tab-content">
            <!-- Requests will be dynamically added here -->
        </div>
    </div>

    <script>
        // Pass PHP data to JavaScript
        const usersData = <?php echo json_encode($unmatchedUsers); ?>;
        const currentUserId = <?php echo json_encode($currentUserId); ?>;

        // Initialize swipe functionality
        function initializeSwipe() {
            const cards = document.querySelectorAll('.card');
            
            if (cards.length === 0) {
                console.log('No cards to initialize swipe functionality.');
                return;
            }

            // Add swipe functionality
            cards.forEach((card, index) => {
                let startX;
                let moveX;
                let currentX;

                card.addEventListener('touchstart', (e) => {
                    startX = e.touches[0].clientX;
                });

                card.addEventListener('touchmove', (e) => {
                    moveX = e.touches[0].clientX;
                    currentX = moveX - startX;
                    card.style.transform = `translateX(${currentX}px)`;
                });

                card.addEventListener('touchend', () => {
                    if (Math.abs(currentX) > 100) {
                        if (currentX > 0) {
                            swipeCard('right', index);
                        } else {
                            swipeCard('left', index);
                        }
                    } else {
                        card.style.transform = 'translateX(0)';
                    }
                });
            });
        }

        const currentSkills = <?php echo json_encode($currentSkills); ?>;

        // Display users when the page loads
        window.onload = function() {
            displayUsers(usersData);
            initializeSwipe();
            
            // Debug: Log the number of users found
            console.log('Number of users found:', usersData.length);
            console.log('First user data:', usersData[0]);
        };

        function displayUsers(users) {
            const cardsContainer = document.querySelector('.card-container');
            cardsContainer.innerHTML = '';

            if (users.length === 0) {
                cardsContainer.innerHTML = '<p>No users found to match with.</p>';
                return;
            }

            users.forEach((user, index) => {
                const card = document.createElement('div');
                card.className = 'card';
                card.dataset.userId = user.User_ID; // Store user ID in card data
                card.innerHTML = `
                    <div class="card-content">
                        <div class="user-info">
                            <div class="profile-image">
                                <img src="${user.Profile_Picture || 'default-profile.png'}" alt="${user.First_Name} ${user.Last_Name}">
                            </div>
                            <div class="info">
                                <h3>${user.First_Name} ${user.Last_Name}</h3>
                                <p>${user.Bio || 'No bio added yet'}</p>
                                <div class="skills">
                                    <h4>Skills I Can Share:</h4>
                                    <div class="skill-list">
                                        ${user.can_share_skills ? user.can_share_skills.split(',').map(skill => `
                                            <span class="skill-tag">${skill.trim()}</span>
                                        `).join('') : '<span class="skill-tag">No skills shared yet</span>'}
                                    </div>
                                    <h4>Skills I Want to Learn:</h4>
                                    <div class="skill-list">
                                        ${user.want_to_learn_skills ? user.want_to_learn_skills.split(',').map(skill => `
                                            <span class="skill-tag">${skill.trim()}</span>
                                        `).join('') : '<span class="skill-tag">No skills to learn yet</span>'}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="action-icons">
                            <i class="bx bx-x x" onclick="swipeCard('left', ${index})"></i>
                            <i class="bx bx-heart heart" onclick="swipeCard('right', ${index})"></i>
                        </div>
                    </div>
                `;
                cardsContainer.appendChild(card);
            });
        }

        function swipeCard(direction, cardIndex) {
            const cards = document.querySelectorAll('.card');
            const currentCard = cards[cardIndex];
            if (!currentCard) return;

            // Get the user ID from the card data
            const userId = currentCard.dataset.userId; // Get user ID from card data
            console.log('Sending match request to user ID:', userId); // Debug log
            
            // Apply swipe animation
            if (direction === 'right') {
                currentCard.style.transform = 'translateX(100%)';
                // Send match request
                sendMatchRequest(userId);
            } else if (direction === 'left') {
                currentCard.style.transform = 'translateX(-100%)';
            }

            // Apply swipe animation
            currentCard.style.transform = direction === 'right' ? 'translateX(100%)' : 'translateX(-100%)';
            currentCard.style.opacity = '0';

            // Remove card after animation
            setTimeout(() => {
                if (direction === 'right') {
                    // Only remove after successful match request
                    sendMatchRequest(userId).then(() => {
                        currentCard.remove();
                        initializeSwipe();
                    }).catch(() => {
                        // If request fails, keep the card visible
                        currentCard.style.transform = 'translateX(0)';
                        currentCard.style.opacity = '1';
                    });
                } else {
                    // For left swipe, remove immediately
                    currentCard.remove();
                    initializeSwipe();
                }
            }, 500);
        }

        function sendMatchRequest(receiverId) {
            return new Promise((resolve, reject) => {
                const message = prompt("Enter a message for the match (optional): ");

                if (confirm("Are you sure you want to send this match request?")) {
                    fetch('match_user.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `sender_id=${currentUserId}&receiver_id=${receiverId}&message=${encodeURIComponent(message || '')}`
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Match request sent successfully!');
                                resolve();
                            } else {
                                alert('Failed to send match request: ' + (data.error || 'Unknown error'));
                                reject(new Error(data.error || 'Failed to send match request'));
                            }
                        })
                        .catch(error => {
                            console.error('Error sending match request:', error);
                            alert('An error occurred while sending the match request');
                            reject(error);
                        });
                } else {
                    reject(new Error('Match request cancelled'));
                }
            });
        }

        function handleMatch(receiverId) {
            const message = prompt("Enter a message for the match (optional):");
            const appointmentDate = prompt("Enter an appointment date and time (YYYY-MM-DD HH:MM:SS):");

            if (!appointmentDate) {
                alert("Appointment date is required.");
                return;
            }

            console.log('Sending match request to receiver ID:', receiverId);

            fetch('match_user.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `receiver_id=${receiverId}&message=${encodeURIComponent(message || '')}&appointment_date=${encodeURIComponent(appointmentDate)}`
            })
                .then(response => response.json())
                .then(data => {
                    console.log('Response from match_user.php:', data);

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

        function fetchIncomingRequests() {
            fetch('fetch_incoming_requests.php')
                .then(response => response.json())
                .then(incomingRequests => {
                    const requestsTab = document.getElementById('requests');
                    requestsTab.innerHTML = ''; // Clear existing content

                    if (incomingRequests.error) {
                        requestsTab.innerHTML = `<p>${incomingRequests.error}</p>`;
                        return;
                    }

                    if (incomingRequests.length === 0) {
                        requestsTab.innerHTML = '<p>No incoming requests found.</p>';
                        return;
                    }

                    incomingRequests.forEach(request => {
                        const requestElement = document.createElement('div');
                        requestElement.className = 'request';
                        requestElement.style.border = '1px solid #ddd'; // Add border for visibility
                        requestElement.style.padding = '10px'; // Add padding for better layout
                        requestElement.style.marginBottom = '10px'; // Add spacing between requests

                        requestElement.innerHTML = `
                            <h3>Request from ${request.sender_name}</h3>
                            <p><strong>Status:</strong> ${request.status}</p>
                            <p><strong>Appointment:</strong> ${request.appointment_date || 'Not set'}</p>
                            <button class="accept-button" onclick="updateRequestStatus(${request.id}, 'accepted')">Accept</button>
                            <button class="decline-button" onclick="updateRequestStatus(${request.id}, 'declined')">Decline</button>
                        `;
                        requestsTab.appendChild(requestElement);
                    });
                })
                .catch(error => console.error('Error fetching incoming requests:', error));
        }

        function updateRequestStatus(requestId, status) {
            fetch('update_request_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `request_id=${requestId}&status=${status}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`Request ${status} successfully!`);
                        fetchIncomingRequests(); // Refresh the requests tab
                        if (status === 'accepted') {
                            fetchOngoingRequests(); // Refresh the ongoing tab if accepted
                        }
                    } else {
                        alert('Error: ' + data.error);
                    }
                })
                .catch(error => console.error('Error updating request status:', error));
        }

        // Call fetchUsers when the page loads
        document.addEventListener('DOMContentLoaded', fetchUsers);
    </script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>