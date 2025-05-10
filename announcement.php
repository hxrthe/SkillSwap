<?php
session_start();
require_once 'SkillSwapDatabase.php';

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: loginpagee.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

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
            $stmt = $conn->prepare("
                UPDATE announcements 
                SET Title = :title, Content = :content, Is_Active = :is_active 
                WHERE Announcement_ID = :id
            ");

            $stmt->execute([
                ':title' => $title,
                ':content' => $content,
                ':is_active' => $is_active,
                ':id' => $announcement_id
            ]);

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

// Fetch all active announcements
try {
    error_log("Attempting to fetch announcements...");
    // First, let's check if there are any announcements at all
    $check_stmt = $conn->query("SELECT COUNT(*) FROM announcements");
    $total_announcements = $check_stmt->fetchColumn();
    error_log("Total announcements in database: " . $total_announcements);

    // Now fetch active announcements with admin info
    $stmt = $conn->query("
        SELECT a.*, adm.First_Name, adm.Last_Name 
        FROM announcements a 
        LEFT JOIN admins adm ON a.Admin_ID = adm.Admin_ID 
        WHERE a.Is_Active = 1 
        ORDER BY a.Created_At DESC
    ");
    $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Debug information
    error_log("Number of active announcements found: " . count($announcements));
    if (empty($announcements)) {
        error_log("No active announcements found in the database");
    } else {
        error_log("First announcement data: " . print_r($announcements[0], true));
    }
} catch (PDOException $e) {
    $announcements = [];
    error_log("Database error: " . $e->getMessage());
    $_SESSION['error'] = "Failed to fetch announcements: " . $e->getMessage();
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
        }

        .sidebar-menu li {
            margin-bottom: 10px;
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
            <a href="logout.php" style="color: #666; text-decoration: none;">
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
                <a href="Reports.php">
                    <i class="fas fa-flag"></i>
                    Review Reports
                </a>
            </li>
            <li>
                <a href="announcement.php" class="active">
                    <i class="fas fa-bullhorn"></i>
                    Announcement
                </a>
            </li>
            <li>
                    <a href="manage_admins.php">
                    <i class="fas fa-user-shield"></i>
                    Manage Admins
                </a>
            </li>
            <li>
                <a href="manageposts.php">
                    <i class="fas fa-user-shield"></i>
                    Manage Posts
                </a>
            </li>
            <li>
                <a href="Community.php">
                    <i class="fas fa-user-shield"></i>
                    Community
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <button id="openModalBtn" class="action-btn view-btn" style="font-size:16px;">Create Announcement</button>
        
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

        <div class="card" style="margin-top: 20px;">
            <h2>All Announcements</h2>
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
                                    <a href="announcement.php?id=<?php echo $announcement['Announcement_ID']; ?>" class="action-btn view-btn">Edit</a>
                                    <button onclick="deleteAnnouncement(<?php echo $announcement['Announcement_ID']; ?>)" class="action-btn restrict-btn">Delete</button>
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
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="is_active" id="edit_is_active">
                            Active
                        </label>
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
            if (confirm('Are you sure you want to delete this announcement?')) {
                fetch('announcement.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete&id=${id}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Failed to delete announcement');
                    }
                });
            }
        }

        // Modal logic
        const openModalBtn = document.getElementById('openModalBtn');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const modal = document.getElementById('announcementModal');
        openModalBtn.onclick = function() {
            modal.style.display = 'block';
        }
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
            fetch(`get_announcement.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('edit_announcement_id').value = data.announcement.Announcement_ID;
                        document.getElementById('edit_title').value = data.announcement.Title;
                        document.getElementById('edit_content').value = data.announcement.Content;
                        document.getElementById('edit_is_active').checked = data.announcement.Is_Active == 1;
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
    </script>
</body>
</html> 