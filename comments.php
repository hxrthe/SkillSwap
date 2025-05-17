<?php
require_once 'SP.php';
require_once 'SkillSwapDatabase.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$crud = new Crud();
$db = new Database();
$conn = $db->getConnection();

if (!isset($_GET['post_id'])) {
    header('Location: communitycomm.php');
    exit;
}

$postId = $_GET['post_id'];

// Get post details first
try {
    $post = $crud->getPost($postId);
    if (!$post) {
        header('Location: communitycomm.php');
        exit;
    }
} catch (Exception $e) {
    $error = "Failed to fetch post details: " . $e->getMessage();
}

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
    try {
        if (isset($_POST['parent_comment_id'])) {
            $crud->createReply($postId, $_SESSION['user_id'], $post['Community_ID'], $_POST['content'], $_POST['parent_comment_id']);
        } else {
            $crud->createComment($postId, $_SESSION['user_id'], $post['Community_ID'], $_POST['content']);
        }
        header('Location: ' . $_SERVER['PHP_SELF'] . '?post_id=' . $postId . '&community_id=' . urlencode($post['Community_ID']));
        exit;
    } catch (Exception $e) {
        $error = "Failed to post comment: " . $e->getMessage();
    }
}

// Get comments after handling submission
try {
    $comments = $crud->getPostComments($postId);
} catch (Exception $e) {
    $error = "Failed to fetch comments: " . $e->getMessage();
}

// For comment input (logged-in user)
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
            header('Location: communitycomm.php?community_id=' . urlencode($post['Community_ID']));
            exit;
        } else {
            $error = $result['message'];
        }
    } catch (PDOException $e) {
        $error = "Failed to delete post: " . $e->getMessage();
    }
}

// Handle comment deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_comment'])) {
    try {
        // Call the stored procedure to delete the comment
        $query = "CALL DeleteUserComment(:Comment_ID, :User_ID, @success, @message)";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':Comment_ID' => $_POST['delete_comment'],
            ':User_ID' => $_SESSION['user_id']
        ]);
        
        // Get the result of the procedure
        $result = $conn->query("SELECT @success as success, @message as message")->fetch(PDO::FETCH_ASSOC);
        
        if ($result['success']) {
            header('Location: ' . $_SERVER['PHP_SELF'] . '?post_id=' . $postId);
            exit;
        } else {
            $error = $result['message'];
        }
    } catch (PDOException $e) {
        $error = "Failed to delete comment: " . $e->getMessage();
    }
}
?>
<?php include 'menuu.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comments</title>
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
            max-width: 750px;
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

        .main-container {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .post-container {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.08);
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

        .comment-input {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            background: #fff;
            border-radius: 8px;
            padding: 12px 16px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .comment-input img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .comment-input input {
            flex: 1;
            border: none;
            background: #f0f2f5;
            border-radius: 20px;
            padding: 10px 15px;
            font-size: 15px;
        }

        .comment-input input:focus {
            outline: none;
        }

        .send-button {
            background-color: #1b74e4;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.2s;
        }

        .send-button:hover {
            background-color: #166fe5;
        }

        .comment {
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
            background: #fff;
            border-radius: 8px;
            padding: 12px 16px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .replies {
            margin-left: 42px;
            margin-top: 10px;
            border-left: 2px solid #e4e6eb;
            padding-left: 10px;
        }

        .reply-form {
            display: none;
            margin-left: 42px;
            margin-top: 10px;
            margin-bottom: 10px;
            background: #fff;
            border-radius: 8px;
            padding: 12px 16px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .reply-form.active {
            display: flex;
            gap: 10px;
        }

        .reply-form img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .reply-form input {
            flex: 1;
            border: none;
            background: #f0f2f5;
            border-radius: 20px;
            padding: 10px 15px;
            font-size: 15px;
        }

        .reply-form input:focus {
            outline: none;
        }

        .comment img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .comment-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .comment-content > div {
            position: relative;
            width: 100%;
        }

        .comment-author {
            font-weight: bold;
            color: #050505;
            text-decoration: none;
            margin-bottom: 4px;
        }

        .comment-text {
            color: #050505;
            word-break: break-word;
            margin-top: 4px;
        }

        .comment-actions {
            display: flex;
            gap: 15px;
            margin-top: 5px;
            margin-left: 42px;
            margin-bottom: 10px;
            font-size: 12px;
            color: #65676b;
        }

        .comment-action {
            cursor: pointer;
            color: #1877f2;
            font-weight: 500;
        }

        .comment-action:hover {
            text-decoration: underline;
        }

        .comment-time {
            color: #65676b;
        }

        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .reply-button {
            color: #1877f2;
            font-weight: 500;
            cursor: pointer;
        }

        .reply-button:hover {
            text-decoration: underline;
        }

        .meatball-menu {
            position: absolute;
            right: 0;
            top: 0;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: background-color 0.2s;
            z-index: 1000;
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

        .comment-section {
            background: none;
            box-shadow: none;
            border-radius: 0;
            padding: 0;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="communitycomm.php?community_id=<?php echo urlencode($post['Community_ID']); ?>" class="back-button">&lt; Back to Posts</a>

        <div class="main-container">
            <div class="post-container">
                <div class="post-header">
                    <img src="<?php echo $opPic; ?>" alt="Profile Picture">
                    <div class="post-info">
                        <a href="#" class="post-author"><?php echo htmlspecialchars($post['First_Name'] . ' ' . $post['Last_Name']); ?></a>
                        <div class="post-time"><?php echo date('M j, Y g:i A', strtotime($post['Created_At'])); ?></div>
                    </div>
                    <?php if ($post['User_ID'] === $_SESSION['user_id']): ?>
                    <div class="meatball-menu" onclick="toggleMenu(this)">
                        <ion-icon name="ellipsis-horizontal"></ion-icon>
                        <div class="menu-dropdown">
                            <div class="menu-item" onclick="showDeleteConfirm('post', <?php echo $post['Post_ID']; ?>)">
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
            </div>

            <div class="comment-section">
                <form method="POST" action="" class="comment-input">
                    <img src="<?php echo $userProfilePic; ?>" alt="Profile Picture">
                    <input type="text" name="content" placeholder="Write a comment..." required>
                    <button type="submit" class="send-button">Send</button>
                </form>

                <?php if (isset($error)): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php foreach ($comments as $comment): ?>
                <?php
                $commentPic = !empty($comment['profile_picture']) ? 'data:image/jpeg;base64,' . base64_encode($comment['profile_picture']) : 'default-profile.png';
                ?>
                <div class="comment">
                    <img src="<?php echo $commentPic; ?>" alt="Profile Picture">
                    <div class="comment-content">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <a href="#" class="comment-author"><?php echo htmlspecialchars($comment['First_Name'] . ' ' . $comment['Last_Name']); ?></a>
                            <?php if ($comment['User_ID'] === $_SESSION['user_id']): ?>
                            <div class="meatball-menu" onclick="toggleMenu(this)">
                                <ion-icon name="ellipsis-horizontal"></ion-icon>
                                <div class="menu-dropdown">
                                    <div class="menu-item" onclick="showDeleteConfirm('comment', <?php echo $comment['Comment_ID']; ?>)">
                                        <ion-icon name="trash-outline"></ion-icon>
                                        Delete Comment
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <span class="comment-text"><?php echo htmlspecialchars($comment['Comment_Text']); ?></span>
                    </div>
                </div>
                <div class="comment-actions">
                    <span class="comment-time"><?php echo date('M j, Y g:i A', strtotime($comment['Comment_Date'])); ?></span>
                    <span class="comment-action reply-button" data-comment-id="<?php echo $comment['Comment_ID']; ?>">Reply</span>
                </div>

                <div class="reply-form" id="reply-form-<?php echo $comment['Comment_ID']; ?>">
                    <form method="POST" action="">
                        <img src="<?php echo $userProfilePic; ?>" alt="Profile Picture">
                        <input type="text" name="content" placeholder="Write a reply..." required>
                        <input type="hidden" name="parent_comment_id" value="<?php echo $comment['Comment_ID']; ?>">
                        <button type="submit" class="send-button">Send</button>
                    </form>
                </div>

                <?php
                // Get and display replies
                $replies = $crud->getCommentReplies($comment['Comment_ID']);
                if (!empty($replies)):
                ?>
                <div class="replies">
                    <?php foreach ($replies as $reply): ?>
                    <?php
                    $replyPic = !empty($reply['profile_picture']) ? 'data:image/jpeg;base64,' . base64_encode($reply['profile_picture']) : 'default-profile.png';
                    ?>
                    <div class="comment">
                        <img src="<?php echo $replyPic; ?>" alt="Profile Picture">
                        <div class="comment-content">
                            <a href="#" class="comment-author"><?php echo htmlspecialchars($reply['First_Name'] . ' ' . $reply['Last_Name']); ?></a>
                            <span class="comment-text"><?php echo htmlspecialchars($reply['Comment_Text']); ?></span>
                        </div>
                    </div>
                    <div class="comment-actions">
                        <span class="comment-time"><?php echo date('M j, Y g:i A', strtotime($reply['Comment_Date'])); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="overlay" id="overlay"></div>
    <div class="delete-confirm-dialog" id="deleteConfirmDialog">
        <h3>Delete <span id="deleteType">Post</span></h3>
        <p>Are you sure you want to delete this <span id="deleteTypeLower">post</span>?</p>
        <div class="dialog-buttons">
            <button class="dialog-button cancel-button" onclick="hideDeleteConfirm()">Cancel</button>
            <form method="POST" style="display: inline;">
                <input type="hidden" name="delete_post" id="deletePostId" value="">
                <input type="hidden" name="delete_comment" id="deleteCommentId" value="">
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

        function showDeleteConfirm(type, id) {
            const dialog = document.getElementById('deleteConfirmDialog');
            const deleteType = document.getElementById('deleteType');
            const deleteTypeLower = document.getElementById('deleteTypeLower');
            const deletePostId = document.getElementById('deletePostId');
            const deleteCommentId = document.getElementById('deleteCommentId');
            
            deleteType.textContent = type.charAt(0).toUpperCase() + type.slice(1);
            deleteTypeLower.textContent = type.toLowerCase();
            
            if (type === 'post') {
                deletePostId.value = id;
                deleteCommentId.value = '';
            } else {
                deletePostId.value = '';
                deleteCommentId.value = id;
            }
            
            dialog.classList.add('show');
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

        // Existing reply button functionality
        document.querySelectorAll('.reply-button').forEach(button => {
            button.addEventListener('click', function() {
                const commentId = this.dataset.commentId;
                const replyForm = document.getElementById(`reply-form-${commentId}`);
                replyForm.classList.toggle('active');
            });
        });
    </script>
</body>
</html> 