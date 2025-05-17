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
    <title>All Matches</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #fdfd96, #fff);
            box-sizing: border-box;
        }

        .container {
            display: flex;
            flex-wrap: wrap; /* Allow cards to wrap to the next row */
            gap: 20px; /* Add spacing between cards */
            justify-content: center; /* Center the cards horizontally */
            padding: 20px;
        }

        .back-button {
            background-color: #000; /* Black background */
            color: #fff; /* White text */
            border: none;
            height: 20px;   
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 0; /* Center the button horizontally */
            display: block; /* Make it a block element */
            text-align: center; /* Center the text inside the button */
            width: fit-content; /* Adjust the width to fit the text */
            text-decoration: none; /* Remove underline */
            font-size: 16px; /* Adjust font size */
        }

        .back-button:hover {
            background-color: #333; /* Darker black on hover */
        }

        .card {
            flex: 0 1 calc(30% - 20px); /* Each card takes 30% of the row width minus the gap */
            height: 400px;
            background-color: #fff;
            margin-top: 50px;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Ensure content inside the card is spaced properly */
        }

        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-header img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-right: 20px;
        }

        .card-header h3 {
            font-size: 24px;
            margin: 0;
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

        .container .back-bttn {
            position:absolute;
            top: 100px;
            left: 20px;
            z-index: 1000; /* Ensure the button is above other elements */
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="back-bttn">
        <!-- Back Button -->
        <a href="home.php" class="back-button">Back to Home</a>
        </div>

        <!-- Display all cards -->
        <?php foreach ($allUsers as $otherUser): ?>
            <div class="card">
                <div class="card-header">
                    <img src="default-profile.png" alt="User Picture"> <!-- Replace with actual user profile picture if available -->
                    <div>
                        <h3><?php echo htmlspecialchars($otherUser['First_Name']); ?></h3>
                        <!-- <p><?php echo htmlspecialchars($otherUser['Skill']); ?></p> Replace 'Skill' with the actual column name for the user's skill -->
                        <!-- <p><?php echo htmlspecialchars($otherUser['Location']); ?></p> Replace 'Location' with the actual column name for the user's location -->
                    </div>
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
    <script>
        // Function to fetch and update the user cards
        function fetchAndUpdateUsers() {
            fetch('fetch_users.php')
                .then(response => response.json())
                .then(users => {
                    if (users.error) {
                        console.error(users.error);
                        return;
                    }

                    const container = document.querySelector('.container');
                    const backButton = document.querySelector('.back-bttn');

                    // Clear all existing cards except the back button
                    container.innerHTML = '';
                    container.appendChild(backButton);

                    // Loop through the updated user list and create cards
                    users.forEach(user => {
                        const card = document.createElement('div');
                        card.className = 'card';

                        card.innerHTML = `
                            <div class="card-header">
                                <img src="default-profile.png" alt="User Picture"> <!-- Replace with actual user profile picture if available -->
                                <div>
                                    <h3>${user.First_Name}</h3>
                                    <!-- Add other user details dynamically if needed -->
                                </div>
                            </div>
                            <div class="card-content">
                                <!-- Add dynamic content here -->
                            </div>
                            <div class="card-actions">
                                <button>Contact</button>
                            </div>
                        `;

                        container.appendChild(card);
                    });
                })
                .catch(error => console.error('Error fetching users:', error));
        }

        // Fetch and update users every 10 seconds
        setInterval(fetchAndUpdateUsers, 10000);

        // Fetch users immediately on page load
        fetchAndUpdateUsers();
    </script>
</body>
</html>