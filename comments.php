<?php
require_once 'SP.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$crud = new Crud();

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

        .comment-section {
            margin-top: 20px;
        }

        .comment-input {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .comment-input img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
        }

        .comment-input input {
            flex: 1;
            border: none;
            background: #f0f2f5;
            border-radius: 20px;
            padding: 8px 12px;
            font-size: 15px;
        }

        .comment-input input:focus {
            outline: none;
        }

        .send-button {
            background-color: #1877f2;
            border: none;
            color: white;
            font-weight: 600;
            cursor: pointer;
            padding: 8px 16px;
            border-radius: 20px;
            transition: all 0.2s;
            font-size: 14px;
            min-width: 60px;
            text-align: center;
        }

        .send-button:hover {
            background-color: #166fe5;
            transform: translateY(-1px);
        }

        .send-button:active {
            background-color: #1565d8;
            transform: translateY(0);
        }

        .send-button:disabled {
            background-color: #e4e6eb;
            color: #bcc0c4;
            cursor: not-allowed;
            transform: none;
        }

        .comment {
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
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
        }

        .reply-form.active {
            display: flex;
            gap: 10px;
        }

        .reply-form img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
        }

        .reply-form input {
            flex: 1;
            border: none;
            background: #f0f2f5;
            border-radius: 20px;
            padding: 8px 12px;
            font-size: 15px;
        }

        .reply-form input:focus {
            outline: none;
        }

        .comment img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
        }

        .comment-content {
            background: #f0f2f5;
            padding: 8px 12px;
            border-radius: 18px;
            max-width: 100%;
            display: flex;
            flex-direction: column;
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
            font-size: 12px;
            color: #65676b;
        }

        .comment-action {
            cursor: pointer;
        }

        .comment-action:hover {
            text-decoration: underline;
        }

        .like-count {
            margin-left: 42px;
            font-size: 12px;
            color: #65676b;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="communitycomm.php?community_id=<?php echo urlencode($post['Community_ID']); ?>" class="back-button">&lt; Back to Posts</a>

        <div class="post-container">
            <div class="post-header">
                <img src="<?php echo $opPic; ?>" alt="Profile Picture">
                <div class="post-info">
                    <a href="#" class="post-author"><?php echo htmlspecialchars($post['First_Name'] . ' ' . $post['Last_Name']); ?></a>
                    <div class="post-time"><?php echo date('M j, Y g:i A', strtotime($post['Created_At'])); ?></div>
                </div>
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
                    <a href="#" class="comment-author"><?php echo htmlspecialchars($comment['First_Name'] . ' ' . $comment['Last_Name']); ?></a>
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

    <script>
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