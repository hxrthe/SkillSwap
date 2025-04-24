<?php
session_start();
require_once 'SkillSwapDatabase.php';

$db = new Database();
$conn = $db->getConnection();

// Get all users except the currently logged in user
$current_user = isset($_SESSION['username']) ? $_SESSION['username'] : '';

// Initialize or get shown users from session
if (!isset($_SESSION['shown_users'])) {
    $_SESSION['shown_users'] = [];
}

try {
    // Get all available users that haven't been shown yet
    $shown_users_str = implode("','", $_SESSION['shown_users']);
    $shown_users_str = $shown_users_str ? "'" . $shown_users_str . "'" : "''";
    
    $stmt = $conn->prepare("SELECT username FROM users 
                           WHERE username != :current_user 
                           AND username NOT IN ($shown_users_str)
                           ORDER BY RAND()");
    $stmt->bindParam(':current_user', $current_user);
    $stmt->execute();
    $all_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // If no new users found, reset shown users and get all users again
    if (empty($all_users)) {
        $_SESSION['shown_users'] = [];
        $stmt->execute();
        $all_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Take the first 5 users for the initial stack
    $users = array_slice($all_users, 0, 5);
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $users = [];
}

// Handle match/nope actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['username'])) {
        $action = $_POST['action'];
        $username = $_POST['username'];
        
        // Add user to shown users
        if (!in_array($username, $_SESSION['shown_users'])) {
            $_SESSION['shown_users'][] = $username;
        }
        
        // If it's a match, you can add additional logic here
        if ($action === 'match') {
            // Add match logic here (e.g., store in matches table)
        }
        
        // Return success response
        echo json_encode(['success' => true]);
        exit;
    }
}
?>

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
            margin-top: 100px; /* Add margin to account for the menu */
        }

        .card {
            position: absolute;
            width: 100%;
            height: 100%;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            transition: transform 0.5s ease, opacity 0.5s ease;
            cursor: pointer;
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
            cursor: pointer;
            z-index: 10;
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
            transform: rotate(0deg);
        }

        .card:nth-child(2) {
            transform: rotate(-5deg);
        }

        .card:nth-child(3) {
            transform: rotate(5deg);
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
    <?php include 'menuu.php'; ?>

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
        <div class="card-container">
            <?php foreach ($users as $index => $user): ?>
            <div class="card" data-username="<?php echo htmlspecialchars($user['username']); ?>" 
                 style="z-index: <?php echo count($users) - $index; ?>; 
                        transform: rotate(<?php echo $index * 5; ?>deg);">
                <img src="jane.jpg" alt="User Picture">
                <div class="info">
                    <h3><?php echo htmlspecialchars($user['username']); ?></h3>
                    <p>TOPIC</p>
                    <div class="nope" onclick="handleAction('nope', this)"><- Nope</div>
                    <div class="match" onclick="handleAction('match', this)">Match -></div>
                    <div class="offer">
                        <div>WILL OFFER YOU</div>
                        <div>IN EXCHANGE FOR</div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        const cards = document.querySelectorAll('.card');
        let currentCardIndex = 0;

        function handleAction(action, element) {
            const card = element.closest('.card');
            const username = card.dataset.username;
            
            fetch('search.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=${action}&username=${username}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    swipeCard(action === 'match' ? 'right' : 'left');
                }
            });
        }

        function swipeCard(direction) {
            const currentCard = cards[currentCardIndex];
            if (!currentCard) return;

            if (direction === 'right') {
                currentCard.style.transform = 'translateX(100%) rotate(30deg)';
            } else if (direction === 'left') {
                currentCard.style.transform = 'translateX(-100%) rotate(-30deg)';
            }

            currentCard.style.opacity = '0';
            currentCardIndex++;

            if (currentCardIndex < cards.length) {
                cards[currentCardIndex].style.transform = 'rotate(0deg)';
            } else {
                window.location.reload();
            }
        }
    </script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>