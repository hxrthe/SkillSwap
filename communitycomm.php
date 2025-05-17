<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: loginpagee.php');
    exit;
}

$communityName = isset($_GET['community_name']) ? $_GET['community_name'] : 'Community';
$communityId = isset($_GET['community_id']) ? intval($_GET['community_id']) : 0;

require_once 'SkillSwapDatabase.php';
$db = new Database();
$conn = $db->getConnection();

// Handle post creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
    if (empty($_POST['community_id'])) {
        $error = 'Community ID is missing.';
    } else {
        try {
            $query = "INSERT INTO posts (User_ID, Community_ID, Content, Created_At) VALUES (:User_ID, :Community_ID, :content, NOW())";
            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':User_ID' => $_SESSION['user_id'],
                ':Community_ID' => $_POST['community_id'],
                ':content' => $_POST['content']
            ]);
            header('Location: ' . $_SERVER['PHP_SELF'] . '?community_id=' . urlencode($communityId) . '&community_name=' . urlencode($communityName));
            exit;
        } catch (PDOException $e) {
            $error = "Failed to create post: " . $e->getMessage();
        }
    }
}

// Handle like action
if (isset($_GET['like']) && isset($_GET['Post_ID'])) {
    try {
        // First get the post details
        $query = "SELECT * FROM posts WHERE Post_ID = :Post_ID";
        $stmt = $conn->prepare($query);
        $stmt->execute([':Post_ID' => $_GET['Post_ID']]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$post) {
            throw new Exception("Post not found");
        }

        // Check if user has already liked this post
        $query = "SELECT * FROM post_likes WHERE Post_ID = :Post_ID AND user_id = :user_id";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':Post_ID' => $_GET['Post_ID'],
            ':user_id' => $_SESSION['user_id']
        ]);
        $existingLike = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingLike) {
            // Unlike the post
            $query = "DELETE FROM post_likes WHERE Post_ID = :Post_ID AND user_id = :user_id";
            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':Post_ID' => $_GET['Post_ID'],
                ':user_id' => $_SESSION['user_id']
            ]);
        } else {
            // Like the post
            $query = "INSERT INTO post_likes (Post_ID, user_id, Community_ID) VALUES (:Post_ID, :user_id, :Community_ID)";
            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':Post_ID' => $_GET['Post_ID'],
                ':user_id' => $_SESSION['user_id'],
                ':Community_ID' => $post['Community_ID']
            ]);
        }
        header('Location: ' . $_SERVER['PHP_SELF'] . '?community_id=' . $post['Community_ID'] . '&community_name=' . urlencode($communityName));
        exit;
    } catch (Exception $e) {
        $error = "Failed to like/unlike post: " . $e->getMessage();
    }
}

// Handle post deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post'])) {
    try {
        // Call the stored procedure to delete the post
        $query = "CALL DeleteUserPost(:Post_ID, :User_ID, @success, @message)";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':Post_ID' => $_POST['delete_post'],
            ':User_ID' => $_SESSION['user_id']
        ]);
        
        // Get the result of the procedure
        $result = $conn->query("SELECT @success as success, @message as message")->fetch(PDO::FETCH_ASSOC);
        
        if ($result['success']) {
            header('Location: ' . $_SERVER['PHP_SELF'] . '?community_id=' . urlencode($communityId) . '&community_name=' . urlencode($communityName));
            exit;
        } else {
            $error = $result['message'];
        }
    } catch (PDOException $e) {
        $error = "Failed to delete post: " . $e->getMessage();
    }
}

// Get all posts for the community with user details and like/comment counts
try {
    $query = "SELECT p.*, u.First_Name, u.Last_Name, u.profile_picture,
              (SELECT COUNT(*) FROM post_likes WHERE Post_ID = p.Post_ID) as like_count,
              (SELECT COUNT(*) FROM post_comments WHERE Post_ID = p.Post_ID) as comment_count
              FROM posts p
              JOIN users u ON p.user_id = u.user_id
              WHERE p.Community_ID = :Community_ID
              ORDER BY p.Created_At DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute([':Community_ID' => $communityId]);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to fetch posts: " . $e->getMessage();
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

include 'menuu.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Posts</title>
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
            padding: 20px;
            max-width: 1000px;
            margin: 0 auto;
        }

        .posts-background {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .back-button {
            font-size: 18px;
            font-weight: bold;
            color: black;
            text-decoration: none;
            margin: 20px;
            display: inline-block;
        }

        .create-post {
            background-color: #fff;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 20px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            border: 1px solid #e4e6eb;
        }

        .post-input {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .post-input img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .post-input input {
            flex: 1;
            border: none;
            background: #f0f2f5;
            border-radius: 20px;
            padding: 10px 15px;
            font-size: 15px;
        }

        .post-input input:focus {
            outline: none;
        }

        .post-container {
            background-color: #fff;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 20px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .post-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            position: relative;
        }

        .post-header img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .post-info {
            flex: 1;
        }

        .post-author {
            font-weight: bold;
            color: #050505;
            text-decoration: none;
        }

        .post-time {
            font-size: 12px;
            color: #65676b;
        }

        .post-content {
            margin-bottom: 12px;
            color: #050505;
            line-height: 1.4;
        }

        .post-actions {
            display: flex;
            border-top: 1px solid #e4e6eb;
            padding-top: 8px;
        }

        .action-button {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 8px;
            border-radius: 4px;
            color: #65676b;
            text-decoration: none;
            font-weight: 500;
        }

        .action-button:hover {
            background-color: #f0f2f5;
        }

        .action-button ion-icon {
            font-size: 18px;
        }

        .write-post-button {
            background-color: #1b74e4;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
        }

        .write-post-button:hover {
            background-color: #166fe5;
        }

        .action-button.liked {
            color: #1877f2;
        }
        .action-button.liked ion-icon {
            color: #1877f2;
        }

        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .meatball-menu {
            position: absolute;
            right: 0;
            top: 0;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: background-color 0.2s;
        }

        .meatball-menu:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .meatball-menu ion-icon {
            font-size: 20px;
            color: #65676b;
        }

        .menu-dropdown {
            position: absolute;
            right: 0;
            top: 100%;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            display: none;
            z-index: 1000;
            min-width: 180px;
            margin-top: 5px;
            border: 1px solid #e4e6eb;
            animation: fadeIn 0.2s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .menu-dropdown.show {
            display: block;
        }

        .menu-item {
            padding: 12px 16px;
            color: #dc3545;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.2s;
            font-weight: 500;
        }

        .menu-item ion-icon {
            font-size: 18px;
        }

        .menu-item:hover {
            background-color: #fff5f5;
        }

        .delete-confirm-dialog {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 24px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            z-index: 1001;
            width: 90%;
            max-width: 400px;
            animation: scaleIn 0.2s ease;
        }

        @keyframes scaleIn {
            from { opacity: 0; transform: translate(-50%, -50%) scale(0.95); }
            to { opacity: 1; transform: translate(-50%, -50%) scale(1); }
        }

        .delete-confirm-dialog.show {
            display: block;
        }

        .delete-confirm-dialog h3 {
            margin: 0 0 12px 0;
            color: #1a1a1a;
            font-size: 20px;
            font-weight: 600;
        }

        .delete-confirm-dialog p {
            margin: 0 0 20px 0;
            color: #65676b;
            line-height: 1.5;
        }

        .dialog-buttons {
            display: flex;
            gap: 12px;
            margin-top: 20px;
            justify-content: flex-end;
        }

        .dialog-button {
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            border: none;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s;
        }

        .cancel-button {
            background: #e4e6eb;
            color: #050505;
        }

        .cancel-button:hover {
            background: #d8dadf;
        }

        .delete-button {
            background: #dc3545;
            color: white;
        }

        .delete-button:hover {
            background: #c82333;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            animation: fadeIn 0.2s ease;
        }

        .overlay.show {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        
        <div class="posts-background">
            <a href="community.php" class="back-button">&lt; Back</a>

            <div class="create-post">
                <form method="POST" action="">
                    <div class="post-input">
                        <img src="<?php echo $userProfilePic; ?>" alt="Profile Picture">
                        <input type="text" name="content" placeholder="What's on your mind?" required>
                        <input type="hidden" name="community_id" value="<?php echo htmlspecialchars($communityId); ?>">
                        <button type="submit" class="write-post-button">Post</button>
                    </div>
                </form>
            </div>

            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if (isset($posts) && !empty($posts)): ?>
                <?php foreach ($posts as $post): ?>
                    <?php
                    // Check if user has liked this post
                    $query = "SELECT * FROM post_likes WHERE Post_ID = :Post_ID AND user_id = :user_id";
                    $stmt = $conn->prepare($query);
                    $stmt->execute([
                        ':Post_ID' => $post['Post_ID'],
                        ':user_id' => $_SESSION['user_id']
                    ]);
                    $hasLiked = $stmt->fetch(PDO::FETCH_ASSOC);
                    ?>
                    <div class="post-container">
                        <div class="post-header">
                            <img src="<?php echo !empty($post['profile_picture']) 
                                ? ('data:image/jpeg;base64,' . base64_encode($post['profile_picture'])) 
                                : 'default-profile.png'; ?>" alt="Profile Picture">
                            <div class="post-info">
                                <a href="#" class="post-author"><?php echo htmlspecialchars($post['First_Name'] . ' ' . $post['Last_Name']); ?></a>
                                <div class="post-time"><?php echo date('M j, Y g:i A', strtotime($post['Created_At'])); ?></div>
                            </div>
                            <?php if ($post['User_ID'] === $_SESSION['user_id']): ?>
                            <div class="meatball-menu" onclick="toggleMenu(this)">
                                <ion-icon name="ellipsis-horizontal"></ion-icon>
                                <div class="menu-dropdown">
                                    <div class="menu-item" onclick="showDeleteConfirm(<?php echo $post['Post_ID']; ?>)">
                                        <ion-icon name="trash-outline"></ion-icon>
                                        Delete Post
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="post-content">
                            <?php echo htmlspecialchars($post['Content']); ?>
                        </div>
                        <div class="post-actions">
                            <a href="?like=1&Post_ID=<?php echo $post['Post_ID']; ?>&community_id=<?php echo $communityId; ?>&community_name=<?php echo urlencode($communityName); ?>" 
                               class="action-button <?php echo $hasLiked ? 'liked' : ''; ?>">
                                <ion-icon name="<?php echo $hasLiked ? 'thumbs-up' : 'thumbs-up-outline'; ?>"></ion-icon>
                                Like (<?php echo $post['like_count']; ?>)
                            </a>
                            <a href="comments.php?post_id=<?php echo $post['Post_ID']; ?>&community_id=<?php echo urlencode($communityId); ?>&community_name=<?php echo urlencode($communityName); ?>" 
                               class="action-button">
                                <ion-icon name="chatbubble-outline"></ion-icon>
                                Comment (<?php echo $post['comment_count']; ?>)
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="post-container">
                    <p>No posts found in this community.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="overlay" id="overlay"></div>
    <div class="delete-confirm-dialog" id="deleteConfirmDialog">
        <h3>Delete Post</h3>
        <p>Are you sure you want to delete this post?</p>
        <div class="dialog-buttons">
            <button class="dialog-button cancel-button" onclick="hideDeleteConfirm()">Cancel</button>
            <form method="POST" style="display: inline;">
                <input type="hidden" name="delete_post" id="deletePostId" value="">
                <button type="submit" class="dialog-button delete-button">
                    <ion-icon name="trash-outline" style="vertical-align: middle; margin-right: 4px;"></ion-icon>
                    Delete
                </button>
            </form>
        </div>
    </div>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script>
        function toggleMenu(element) {
            const menu = element.querySelector('.menu-dropdown');
            const allMenus = document.querySelectorAll('.menu-dropdown');
            
            // Close all other menus
            allMenus.forEach(m => {
                if (m !== menu) m.classList.remove('show');
            });
            
            menu.classList.toggle('show');
        }

        function showDeleteConfirm(postId) {
            document.getElementById('deletePostId').value = postId;
            document.getElementById('deleteConfirmDialog').classList.add('show');
            document.getElementById('overlay').classList.add('show');
        }

        function hideDeleteConfirm() {
            document.getElementById('deleteConfirmDialog').classList.remove('show');
            document.getElementById('overlay').classList.remove('show');
        }

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            const menus = document.querySelectorAll('.menu-dropdown');
            const meatballMenus = document.querySelectorAll('.meatball-menu');
            
            let clickedOnMenu = false;
            meatballMenus.forEach(menu => {
                if (menu.contains(event.target)) {
                    clickedOnMenu = true;
                }
            });
            
            if (!clickedOnMenu) {
                menus.forEach(menu => menu.classList.remove('show'));
            }
        });

        // Close delete dialog when clicking overlay
        document.getElementById('overlay').addEventListener('click', hideDeleteConfirm);
    </script>
</body>
</html>