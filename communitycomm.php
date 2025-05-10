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
            background: #f0f2f5;
            box-sizing: border-box;
        }

        .container {
            padding: 20px;
            max-width: 680px;
            margin: 0 auto;
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
    </style>
</head>
<body>
    <div class="container">
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

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>