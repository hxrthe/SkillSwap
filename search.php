<?php
session_start();
require_once 'SkillSwapDatabase.php';
require_once 'SP.php';

// Get current user's ID
$currentUserId = $_SESSION['user_id'];

// Get users that haven't been matched with current user
$db = new Database();
$conn = $db->getConnection();

$crud = new Crud();

try {
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
            SELECT DISTINCT receiver_id
            FROM match_requests
            WHERE sender_id = :current_user_id
        )
        ORDER BY u.User_ID DESC
    ");
    $stmt->execute([':current_user_id' => $currentUserId]);
    $unmatchedUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $unmatchedUsers = [];
}

$userPicData = $crud->getUserProfilePicture($_SESSION['user_id']);
$userProfilePic = !empty($userPicData) ? 'data:image/jpeg;base64,' . base64_encode($userPicData) : 'default-profile.png';

// For original poster (OP) profile picture
$opPic = 'default-profile.png';
if (!empty($post['User_ID'])) {
    $opPicData = $crud->getUserProfilePicture($post['User_ID']);
    if (!empty($opPicData)) {
        $opPic = 'data:image/jpeg;base64,' . base64_encode($opPicData);
    }
}

// Fetch logged-in user's profile picture
try {
    $query = "SELECT profile_picture FROM users WHERE user_id = :user_id";
    $stmt = $conn->prepare($query);
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $userPicData = $stmt->fetchColumn();
    
    $userProfilePic = !empty($userPicData) 
        ? 'data:image/jpeg;base64,' . base64_encode($userPicData)
        : 'default-profile.png';
} catch (PDOException $e) {
    $userProfilePic = 'default-profile.png';
}

$stmt = $conn->prepare("
    SELECT u.First_Name, u.Last_Name, u.Profile_Picture, mr.created_at
    FROM match_requests mr
    JOIN users u ON u.User_ID = mr.receiver_id
    WHERE mr.sender_id = :current_user_id
    AND mr.status = 'pending'
    ORDER BY mr.created_at DESC
");
$stmt->execute([':current_user_id' => $_SESSION['user_id']]);
$sentRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("
    SELECT u.First_Name, u.Last_Name, u.Profile_Picture, mr.created_at
    FROM match_requests mr
    JOIN users u ON u.User_ID = mr.sender_id
    WHERE mr.receiver_id = :current_user_id
    AND mr.status = 'pending'
    ORDER BY mr.created_at DESC
");
$stmt->execute([':current_user_id' => $_SESSION['user_id']]);
$receivedRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

        .user-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); /* 3 users per row */
            gap: 20px;
            margin: 20px auto;
            max-width: 1200px;
        }

        .user-item {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .profile-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
            margin-bottom: 10px;
        }

        .profile-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .info h3 {
            margin: 0;
            font-size: 20px;
            color: var(--primary-color);
        }

        .info p {
            margin: 5px 0;
            color: var(--text-color);
        }

        .skills {
            margin-top: 10px;
        }

        .skill-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .skill-tag {
            background-color: var(--primary-color);
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 14px;
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 10px;
        }

        .match-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .match-btn:hover {
            background-color: #45a049;
        }

        .no-btn {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .no-btn:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="user-grid">
            <?php if (!empty($unmatchedUsers)): ?>
                <?php foreach ($unmatchedUsers as $user): ?>
                    <div class="user-item" data-user-id="<?php echo htmlspecialchars($user['User_ID']); ?>">
                        <div class="profile-image">
                            <img src="<?php echo !empty($user['profile_picture']) 
                            ? ('data:image/jpeg;base64,' . base64_encode($user['profile_picture'])) 
                            : 'default-profile.png'; ?>" alt="Profile Picture">
                        </div>
                        <div class="info">
                            <h3><?php echo htmlspecialchars($user['First_Name'] . ' ' . $user['Last_Name']); ?></h3>
                            <p><?php
                        if (!empty($user['bio'])) {
                            echo nl2br(htmlspecialchars($user['bio']));
                        } else {
                            echo "No bio added yet.";
                        }
                    ?></p>
                            <div class="skills">
                                <h4>Skills I Can Share:</h4>
                                <div class="skill-list">
                                    <?php if (!empty($user['can_share_skills'])): ?>
                                        <?php foreach (explode(',', $user['can_share_skills']) as $skill): ?>
                                            <span class="skill-tag"><?php echo htmlspecialchars(trim($skill)); ?></span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="skill-tag">No skills shared</span>
                                    <?php endif; ?>
                                </div>
                                <h4>Skills I Want to Learn:</h4>
                                <div class="skill-list">
                                    <?php if (!empty($user['want_to_learn_skills'])): ?>
                                        <?php foreach (explode(',', $user['want_to_learn_skills']) as $skill): ?>
                                            <span class="skill-tag"><?php echo htmlspecialchars(trim($skill)); ?></span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="skill-tag">No skills to learn</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="actions">
                            <button class="match-btn">Match</button>
                            <button class="no-btn">No</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No users found.</p>
            <?php endif; ?>
        </div>
    </div>
    <script>
        document.addEventListener('click', function (e) {
            const userItem = e.target.closest('.user-item');
            const userId = userItem ? userItem.dataset.userId : null;

            if (e.target.classList.contains('match-btn')) {
                if (userId) {
                    // Send match request via AJAX
                    fetch('match_request.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `target_user_id=${userId}`,
                    })
                        .then((response) => response.json())
                        .then((data) => {
                            if (data.success) {
                                // Remove the user container from the UI
                                userItem.remove();
                                console.log(`Match request sent to user ID: ${userId}`);
                            } else {
                                console.error(data.error || 'Failed to send match request');
                            }
                        })
                        .catch((error) => console.error('Error:', error));
                }
            }

            if (e.target.classList.contains('no-btn')) {
                if (userItem) {
                    // Move the user container to the bottom of the list
                    const userGrid = document.querySelector('.user-grid');
                    userGrid.appendChild(userItem);
                    console.log(`User ID ${userId} moved to the bottom of the list`);
                }
            }
        });
    </script>
</body>
</html>