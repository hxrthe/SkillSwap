<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'SkillSwapDatabase.php';
require_once 'sp.php';

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: loginpagee.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

// Get admin info
$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'];
$admin_role = $_SESSION['admin_role'];

// Pagination logic
$commentsPerPage = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $commentsPerPage;

// Get total comment count
$totalComments = $conn->query("SELECT COUNT(*) FROM post_comments")->fetchColumn();
$totalPages = ceil($totalComments / $commentsPerPage);

// Fetch comments for current page
$stmt = $conn->prepare("SELECT * FROM view_all_comments ORDER BY Comment_Date DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $commentsPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$all_comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$crud = new Crud();

// Handle delete comment
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['action']) && $_POST['action'] === 'delete_comment' &&
    isset($_POST['comment_id'])
) {
    header('Content-Type: application/json');
    $comment_id = (int)$_POST['comment_id'];
    try {
        $result = $crud->deleteComment($comment_id);
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to delete comment']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Failed to delete comment']);
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap Admin - Manage Comments</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f0f2f5;
        }

        .navbar {
            background: #fff;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .logo img {
            height: 40px;
        }

        .admin-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .admin-name {
            font-weight: 500;
        }

        .admin-role {
            color: #666;
            font-size: 14px;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 70px;
            bottom: 0;
            width: 250px;
            background: #fff;
            padding: 20px;
            box-shadow: 2px 0 4px rgba(0,0,0,0.1);
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-menu li {
            margin-bottom: 18px;
        }
        .sidebar-menu li:last-child {
            margin-bottom: 0;
        }
        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 15px;
            color: #333;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 18px;
        }
        .sidebar-menu a:hover {
            background: #f0f2f5;
        }
        .sidebar-menu a.active {
            background: #ffeb3b;
            color: #000;
        }
        .sidebar-menu i {
            font-size: 20px;
        }
        .main-content {
            margin-left: 250px;
            margin-top: 70px;
            padding: 30px;
        }

        .card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .card h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background: #f0f2f5;
            font-weight: 500;
        }

        tr:hover {
            background: #f9f9f9;
        }

        .action-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .view-btn {
            background: #ffeb3b;
            color: #000;
        }

        .view-btn:hover {
            background: #ffd600;
        }

        .restrict-btn {
            background: #ff4444;
            color: #fff;
        }

        .restrict-btn:hover {
            background: #cc0000;
        }

        .pagination {
            display: inline-flex;
            list-style: none;
            padding: 0;
            margin: 0 auto;
            justify-content: center;
            align-items: center;
            gap: 8px;
            background: none;
            border: none;
        }
        .page-item {
            margin: 0;
        }
        .page-link {
            display: inline-block;
            padding: 6px 18px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background: #fff;
            color: #333;
            text-decoration: none;
            font-size: 18px;
            font-weight: normal;
            margin-right: 5px;
            transition: background 0.2s, color 0.2s;
            display: inline-block;
        }
        .page-link.active, .page-item.active .page-link {
            background: #ffeb3b;
            color: #000;
            font-weight: bold;
            border: 1.5px solid #ffeb3b;
        }
        .page-item.disabled .page-link {
            color: #bbb;
            pointer-events: none;
            background: #fff;
            border: 1px solid #ddd;
        }
        /* Responsive Breakpoints */
        @media (max-width: 991px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
                top: auto;
                box-shadow: none;
                margin-top: 70px;
            }
            .main-content {
                margin-left: 0;
                margin-top: 150px;
                padding: 15px;
            }
            .navbar {
                flex-direction: column;
                align-items: flex-start;
                padding: 10px 20px;
                min-height: 70px;
            }
            .admin-info {
                margin-top: 10px;
            }
            .pagination {
                flex-wrap: wrap;
            }
        }
        @media (max-width: 600px) {
            .navbar {
                padding: 8px 15px;
            }
            .logo {
                font-size: 18px;
            }
            .sidebar-menu a {
                font-size: 16px;
            }
            .card h2,
            th,
            td {
                font-size: 14px;
            }
            .action-btn,
            .page-link {
                font-size: 14px;
                padding: 6px 12px;
            }
        }
        @media screen and (max-width: 1024px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
            .card {
                padding: 16px;
            }
            .tab-btn {
                padding: 10px 20px;
                font-size: 14px;
            }
            th, td {
                padding: 12px 8px;
                font-size: 14px;
            }
        }
        @media screen and (max-width: 768px) {
            .navbar {
                padding: 10px 15px;
            }
            .logo {
                font-size: 20px;
            }
            .logo img {
                height: 32px;
            }
            .admin-info {
                gap: 10px;
            }
            .admin-name {
                font-size: 14px;
            }
            .admin-role {
                font-size: 12px;
            }
            .tab-bar, .tab-buttons {
                flex-wrap: wrap;
                gap: 8px;
            }
            .tab-btn {
                padding: 8px 16px;
                font-size: 13px;
                flex: 1;
                min-width: 120px;
                text-align: center;
            }
            th, td {
                padding: 10px 6px;
                font-size: 13px;
                white-space: nowrap;
            }
            .action-btn {
                padding: 6px 12px;
                font-size: 12px;
            }
        }
        @media screen and (max-width: 480px) {
            .main-content {
                padding: 15px;
            }
            .card {
                padding: 12px;
            }
            .tab-btn {
                padding: 6px 12px;
                font-size: 12px;
                min-width: 100px;
            }
            th, td {
                padding: 8px 4px;
                font-size: 12px;
            }
            .action-btn {
                padding: 4px 8px;
                font-size: 11px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="logo">
            <img src="assets/images/sslogo.png" alt="SkillSwap Logo">
            SkillSwap Admin
        </div>
        <div class="admin-info">
            <div>
                <div class="admin-name"><?php echo htmlspecialchars($admin_name); ?></div>
                <div class="admin-role"><?php echo ucfirst($admin_role); ?></div>
            </div>
            <a href="#" onclick="confirmLogout()" style="color: #666; text-decoration: none;">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>
    <!-- Sidebar -->
    <div class="sidebar">
        <ul class="sidebar-menu">
            <li>
                <a href="admin.php">
                    <i class="fas fa-home"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="manage_users.php">
                    <i class="fas fa-users"></i>
                    Manage Users
                </a>
            </li>
            <!-- <li>
                <a href="announcement.php">
                    <i class="fas fa-bullhorn"></i>
                    Announcement
                </a>
            </li> -->
            <?php if ($admin_role === 'super_admin'): ?>
            <li>
                <a href="manage_admins.php">
                    <i class="fas fa-user-shield"></i>
                    Manage Admins
                </a>
            </li>
            <?php endif; ?>
            <li>
                <a href="ManageComments.php" class="active">
                    <i class="fas fa-comments"></i>
                    Manage Comments
                </a>
            </li>
            <li>
                <a href="manageposts.php">
                    <i class="fas fa-thumbtack"></i>
                    Manage Posts
                </a>
            </li>
            <li>
                <a href="Community(Admin).php">
                    <i class="fas fa-users-cog"></i>
                    Community
                </a>
            </li>
        </ul>
    </div>
    <!-- Main Content -->
    <div class="main-content">
        <div class="card">
            <h2>All Comments</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Comment ID</th>
                            <th>Post ID</th>
                            <th>User ID</th>
                            <th>Community ID</th>
                            <th>Comment Text</th>
                            <th>Comment Date</th>
                            <th>Parent Comment ID</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_comments as $comment): ?>
                        <tr>
                            <td><?= htmlspecialchars($comment['Comment_ID'] ?? '') ?></td>
                            <td><?= htmlspecialchars($comment['Post_ID'] ?? '') ?></td>
                            <td><?= htmlspecialchars($comment['User_ID'] ?? '') ?></td>
                            <td><?= htmlspecialchars($comment['Community_ID'] ?? '') ?></td>
                            <td><?= htmlspecialchars($comment['Comment_Text'] ?? '') ?></td>
                            <td><?= htmlspecialchars($comment['Comment_Date'] ?? '') ?></td>
                            <td><?= htmlspecialchars($comment['Parent_Comment_ID'] ?? '') ?></td>
                            <td>
                                <button class="action-btn restrict-btn" onclick="deleteComment(<?= (int)$comment['Comment_ID'] ?>)">Delete</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div style="text-align:center; margin-top:20px;">
                <nav aria-label="Page navigation example">
                  <ul class="pagination">
                    <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                      <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                      <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                        <a class="page-link<?php if ($i == $page) echo ' active'; ?>" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endfor; ?>
                    <li class="page-item <?php if ($page >= $totalPages) echo 'disabled'; ?>">
                      <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                    </li>
                  </ul>
                </nav>
            </div>
        </div>
    </div>
    <script>
        function confirmLogout() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You will be logged out of your account!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, logout!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'logout.php';
                }
            });
        }

        function deleteComment(commentId) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This comment will be permanently deleted!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('ManageComments.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'action=delete_comment&comment_id=' + encodeURIComponent(commentId)
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Deleted!', 'The comment has been deleted.', 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Error', data.error || 'Failed to delete comment', 'error');
                        }
                    });
                }
            });
        }
    </script>
</body>
</html>