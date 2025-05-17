<?php
session_start();
require_once 'SkillSwapDatabase.php';
require_once 'SP.php';

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: loginpagee.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();
$crud = new Crud();

// Handle AJAX delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    header('Content-Type: application/json');
    
    if (!isset($_POST['id'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit();
    }

    $announcement_id = (int)$_POST['id'];

    try {
        $stmt = $conn->prepare("
            UPDATE announcements 
            SET Is_Active = 0 
            WHERE Announcement_ID = :id
        ");

        $stmt->execute([':id' => $announcement_id]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to delete announcement']);
    }
    exit();
}

// Handle create/edit form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && in_array($_POST['action'], ['create', 'edit'])) {
    error_log("Processing announcement form submission...");
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $announcement_id = isset($_POST['announcement_id']) ? (int)$_POST['announcement_id'] : null;

    error_log("Form data - Title: " . $title . ", Content: " . $content . ", Admin ID: " . $_SESSION['admin_id']);

    if (empty($title) || empty($content)) {
        $_SESSION['error'] = "Title and content are required.";
        header("Location: announcement.php" . ($announcement_id ? "?id=" . $announcement_id : ""));
        exit();
    }

    try {
        if ($_POST['action'] === 'create') {
            error_log("Creating new announcement...");
            // First verify the admin exists
            $admin_check = $conn->prepare("SELECT Admin_ID FROM admins WHERE Admin_ID = ?");
            $admin_check->execute([$_SESSION['admin_id']]);
            if (!$admin_check->fetch()) {
                error_log("Error: Admin ID " . $_SESSION['admin_id'] . " not found in admins table");
                throw new Exception("Invalid admin ID");
            }

                $stmt = $conn->prepare("CALL createAnnouncement(:admin_id, :title, :content)");

            $stmt->execute([
                ':admin_id' => $_SESSION['admin_id'],
                ':title' => $title,
                ':content' => $content
            ]);

            $new_id = $conn->lastInsertId();
            error_log("Announcement created successfully with ID: " . $new_id);
            
            // Verify the announcement was created
            $verify = $conn->prepare("SELECT * FROM announcements WHERE Announcement_ID = ?");
            $verify->execute([$new_id]);
            $created = $verify->fetch(PDO::FETCH_ASSOC);
            error_log("Verification of created announcement: " . print_r($created, true));

            $_SESSION['success'] = "Announcement created successfully.";
        } else {
            error_log('Edit POST: ' . print_r($_POST, true));
            $crud->editAnnouncement($announcement_id, $title, $content, $is_active);
            $_SESSION['success'] = "Announcement updated successfully.";
        }
    } catch (Exception $e) {
        error_log("Error during announcement creation: " . $e->getMessage());
        $_SESSION['error'] = "Failed to " . ($_POST['action'] === 'create' ? 'create' : 'update') . " announcement: " . $e->getMessage();
    }

    header("Location: announcement.php");
    exit();
}

// Fetch announcement for editing if ID is provided
$announcement = null;
if (isset($_GET['id'])) {
    try {
        $stmt = $conn->prepare("
            SELECT * FROM announcements 
            WHERE Announcement_ID = :id
        ");
        $stmt->execute([':id' => (int)$_GET['id']]);
        $announcement = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$announcement) {
            $_SESSION['error'] = "Announcement not found.";
            header("Location: announcement.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Failed to fetch announcement.";
        header("Location: announcement.php");
        exit();
    }
}

// Pagination logic for announcements
$announcementsPerPage = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $announcementsPerPage;
// Get total announcement count
$totalAnnouncements = $conn->query("SELECT COUNT(*) FROM announcements")->fetchColumn();
$totalPages = ceil($totalAnnouncements / $announcementsPerPage);
// Fetch announcements for current page
$stmt = $conn->prepare("
    SELECT a.*, adm.First_Name, adm.Last_Name 
    FROM announcements a 
    LEFT JOIN admins adm ON a.Admin_ID = adm.Admin_ID 
    ORDER BY a.Created_At DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $announcementsPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'set_active') {
    header('Content-Type: application/json');
    $announcement_id = (int)$_POST['id'];
    try {
        $crud->setActiveAnnouncement($announcement_id);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to set active announcement']);
    }
    exit();
}

// --- AJAX: Fetch single announcement for editing ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['fetch_announcement'])) {
    header('Content-Type: application/json');
    if (!isset($_GET['id'])) {
        echo json_encode(['success' => false, 'message' => 'No ID provided']);
        exit();
    }
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM announcements WHERE Announcement_ID = ?");
    $stmt->execute([$id]);
    $announcement = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($announcement) {
        echo json_encode(['success' => true, 'announcement' => $announcement]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Announcement not found']);
    }
    exit();
}

// Handle AJAX delete request using stored procedure
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_announcement') {
    header('Content-Type: application/json');
    if (!isset($_POST['id'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit();
    }
    $announcement_id = (int)$_POST['id'];
    try {
        $crud->deleteAnnouncement($announcement_id);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to delete announcement']);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap Admin Dashboard</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
    margin-bottom: 18px; /* Consistent spacing */
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

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        textarea {
            height: 150px;
            resize: vertical;
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

        .alert {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .alert-error {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }

        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100vw;
            height: 100vh;
            overflow: auto;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background: #fff;
            margin: 5% auto;
            padding: 30px;
            border-radius: 15px;
            width: 100%;
            max-width: 600px;
            position: relative;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        #closeModalBtn {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 28px;
            font-weight: bold;
            color: #666;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        #closeModalBtn:hover {
            color: #333;
        }

        .modal-content h2 {
            color: #333;
            margin-bottom: 25px;
            font-size: 24px;
            border-bottom: 2px solid #ffeb3b;
            padding-bottom: 10px;
        }

        .modal-content .form-group {
            margin-bottom: 20px;
        }

        .modal-content label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }

        .modal-content input[type="text"],
        .modal-content textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #eee;
            border-radius: 8px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
            font-size: 16px;
        }

        .modal-content input[type="text"]:focus,
        .modal-content textarea:focus {
            outline: none;
            border-color: #ffeb3b;
        }

        .modal-content textarea {
            height: 150px;
            resize: vertical;
        }

        .modal-content button[type="submit"] {
            background: #ffeb3b;
            color: #000;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
            width: 100%;
        }

        .modal-content button[type="submit"]:hover {
            background: #ffd600;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Create Announcement Button */
        #openModalBtn {
            background: #ffeb3b;
            color: #000;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        #openModalBtn:hover {
            background: #ffd600;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        #openModalBtn::before {
            content: '+';
            font-size: 20px;
            font-weight: bold;
        }

        .create-announcement-btn {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background 0.3s, box-shadow 0.3s, transform 0.2s;
        }

        .create-announcement-btn:hover {
            background: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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
            border: 1.5px solid #eee;
            border-radius: 8px;
            background: #fff;
            color: #222;
            text-decoration: none;
            font-size: 20px;
            font-weight: normal;
            transition: background 0.2s, color 0.2s, border 0.2s;
            cursor: pointer;
        }
        .page-link.active, .page-item.active .page-link {
            background: #ffeb3b;
            color: #111;
            font-weight: bold;
            border: 1.5px solid #ffeb3b;
        }
        .page-item.disabled .page-link {
            color: #bbb;
            pointer-events: none;
            background: #fff;
            border: 1.5px solid #eee;
        }
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
            .create-announcement-btn {
                font-size: 14px;
                padding: 8px 15px;
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
            th, td {
                padding: 10px 6px;
                font-size: 13px;
                white-space: nowrap;
            }
            .action-btn {
                padding: 6px 12px;
                font-size: 12px;
            }
            .create-announcement-btn {
                font-size: 12px;
                padding: 6px 12px;
            }
        }
        @media screen and (max-width: 480px) {
            .main-content {
                padding: 15px;
            }
            .card {
                padding: 12px;
            }
            th, td {
                padding: 8px 4px;
                font-size: 12px;
            }
            .action-btn {
                padding: 4px 8px;
                font-size: 11px;
            }
            .create-announcement-btn {
                font-size: 11px;
                padding: 4px 8px;
            }
        }
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
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
                <div class="admin-name"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></div>
                <div class="admin-role"><?php echo ucfirst($_SESSION['admin_role']); ?></div>
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
            <li>
                <a href="announcement.php" class="active">
                    <i class="fas fa-bullhorn"></i>
                    Announcement
                </a>
            </li>
            <?php if ($_SESSION['admin_role'] === 'super_admin'): ?>
            <li>
                <a href="manage_admins.php">
                    <i class="fas fa-user-shield"></i>
                    Manage Admins
                </a>
            </li>
            <?php endif; ?>
            <li>
                <a href="ManageComments.php">
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
                <a href="Community.php">
                    <i class="fas fa-users-cog"></i>
                    Community
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>Announcements</h2>
                <button class="create-announcement-btn" onclick="openAnnouncementModal()">
                    <i class="fas fa-plus"></i> Create Announcement
                </button>
            </div>
            <div class="table-responsive">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f0f2f5;">
                            <th style="padding:12px;">Title</th>
                            <th style="padding:12px;">Content</th>
                            <th style="padding:12px;">Posted By</th>
                            <th style="padding:12px;">Date</th>
                            <th style="padding:12px;">Status</th>
                            <th style="padding:12px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $stmt = $conn->query("
                                SELECT a.*, adm.First_Name, adm.Last_Name 
                                FROM announcements a 
                                LEFT JOIN admins adm ON a.Admin_ID = adm.Admin_ID 
                                ORDER BY a.Created_At DESC
                            ");
                            $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            if (count($announcements) > 0):
                                foreach ($announcements as $announcement):
                        ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding:12px; vertical-align:top;"><?php echo htmlspecialchars($announcement['Title']); ?></td>
                                <td style="padding:12px; vertical-align:top; max-width: 400px; word-wrap: break-word;">
                                    <div style="max-height: 100px; overflow-y: auto;">
                                        <?php echo nl2br(htmlspecialchars($announcement['Content'])); ?>
                                    </div>
                                </td>
                                <td style="padding:12px; vertical-align:top;"><?php echo htmlspecialchars($announcement['First_Name'] . ' ' . $announcement['Last_Name']); ?></td>
                                <td style="padding:12px; vertical-align:top;"><?php echo date('M d, Y', strtotime($announcement['Created_At'])); ?></td>
                                <td style="padding:12px; vertical-align:top; text-align:center;">
                                    <?php echo $announcement['Is_Active'] ? 
                                        '<span style="color:green">Active</span>' : 
                                        '<span style="color:red">Inactive</span>'; ?>
                                </td>
                                <td style="padding:12px; vertical-align:top; text-align:center;">
                                    <div style="display: flex; gap: 8px; justify-content: center;">
                                        <a href="#" onclick="editAnnouncement(<?php echo $announcement['Announcement_ID']; ?>)" class="action-btn view-btn">Edit</a>
                                        <button onclick="deleteAnnouncement(<?php echo $announcement['Announcement_ID']; ?>)" class="action-btn restrict-btn">Delete</button>
                                        <button onclick="setActiveAnnouncement(<?php echo $announcement['Announcement_ID']; ?>)" class="action-btn view-btn">
                                            Set Active
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php 
                                endforeach;
                            else:
                        ?>
                            <tr>
                                <td colspan="6" style="padding:12px; text-align:center; color:#666;">No announcements found</td>
                            </tr>
                        <?php 
                            endif;
                        } catch (PDOException $e) {
                            error_log("Database error: " . $e->getMessage());
                            $_SESSION['error'] = "Failed to fetch announcements: " . $e->getMessage();
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div style="text-align:center; margin-top:20px;">
                <nav aria-label="Announcements Page navigation">
                    <ul class="pagination">
                        <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                            <a class="page-link" href="?page=<?= $page-1 ?>">Previous</a>
                        </li>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                                <a class="page-link<?php if ($i == $page) echo ' active'; ?>" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?php if ($page >= $totalPages) echo 'disabled'; ?>">
                            <a class="page-link" href="?page=<?= $page+1 ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Modal for Create Announcement -->
        <div id="announcementModal" class="modal" style="display:none; position:fixed; z-index:2000; left:0; top:0; width:100vw; height:100vh; overflow:auto; background:rgba(0,0,0,0.4);">
            <div class="modal-content" style="background:#fff; margin:5% auto; padding:30px; border-radius:10px; width:100%; max-width:500px; position:relative;">
                <span id="closeModalBtn" style="position:absolute; top:15px; right:20px; font-size:28px; font-weight:bold; color:#888; cursor:pointer;">&times;</span>
                <h2 style="margin-bottom:20px;">Create New Announcement</h2>
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-error">
                        <?php 
                        echo $_SESSION['error']; 
                        unset($_SESSION['error']); 
                        ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <?php 
                        echo $_SESSION['success']; 
                        unset($_SESSION['success']); 
                        ?>
                    </div>
                <?php endif; ?>
                <form method="POST">
                    <input type="hidden" name="action" value="create">
                    <div class="form-group">
                        <label for="title">Title:</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="content">Content:</label>
                        <textarea id="content" name="content" required></textarea>
                    </div>
                    <div style="margin-top: 20px;">
                        <button type="submit" class="action-btn view-btn">Create Announcement</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Modal -->
        <div id="editModal" class="modal" style="display:none;">
            <div class="modal-content">
                <span id="closeEditModalBtn" class="close-btn">&times;</span>
                <h2>Edit Announcement</h2>
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-error">
                        <?php 
                        echo $_SESSION['error']; 
                        unset($_SESSION['error']); 
                        ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <?php 
                        echo $_SESSION['success']; 
                        unset($_SESSION['success']); 
                        ?>
                    </div>
                <?php endif; ?>
                <form method="POST">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="announcement_id" id="edit_announcement_id">
                    <div class="form-group">
                        <label for="edit_title">Title:</label>
                        <input type="text" id="edit_title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_content">Content:</label>
                        <textarea id="edit_content" name="content" required></textarea>
                    </div>
                    <div style="margin-top: 20px;">
                        <button type="submit" class="action-btn view-btn">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function deleteAnnouncement(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This announcement will be permanently deleted!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('announcement.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `action=delete_announcement&id=${id}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Deleted!', 'The announcement has been deleted.', 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Error', data.message || 'Failed to delete announcement', 'error');
                        }
                    });
                }
            });
        }

        // Modal logic
        const closeModalBtn = document.getElementById('closeModalBtn');
        const modal = document.getElementById('announcementModal');
        closeModalBtn.onclick = function() {
            modal.style.display = 'none';
        }
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        // Edit functionality
        function editAnnouncement(id) {
            fetch(`announcement.php?fetch_announcement=1&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('edit_announcement_id').value = data.announcement.Announcement_ID;
                        document.getElementById('edit_title').value = data.announcement.Title;
                        document.getElementById('edit_content').value = data.announcement.Content;
                        document.getElementById('editModal').style.display = 'block';
                    } else {
                        alert('Failed to fetch announcement data');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to fetch announcement data');
                });
        }

        // Close edit modal
        document.getElementById('closeEditModalBtn').onclick = function() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Close edit modal when clicking outside
        window.onclick = function(event) {
            if (event.target == document.getElementById('editModal')) {
                document.getElementById('editModal').style.display = 'none';
            }
        }

        function openAnnouncementModal() {
            document.getElementById('announcementModal').style.display = 'block';
        }

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

        function setActiveAnnouncement(id) {
            Swal.fire({
                title: 'Set this announcement as active?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, set active'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('announcement.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `action=set_active&id=${id}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            Swal.fire('Error', data.message || 'Failed to set active announcement', 'error');
                        }
                    });
                }
            });
        }
    </script>
</body>
</html> 