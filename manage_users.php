<?php
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
$crud = new Crud();

// Get admin info
$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'];
$admin_role = $_SESSION['admin_role'];

// Handle restriction form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'restrict') {
        try {
            $user_id = $_POST['user_id'];
            $status = $_POST['status'];
            $reason = $_POST['reason'];
            $restricted_until = !empty($_POST['restricted_until']) ? $_POST['restricted_until'] : null;
            
            if ($status === 'banned') {
                if ($crud->banUser($user_id, $reason, $restricted_until, $admin_id)) {
                    header("Location: manage_users.php?success=1");
                } else {
                    header("Location: manage_users.php?error=1");
                }
            } else {
                if ($crud->restrictUser($user_id, $status, $reason, $restricted_until, $admin_id)) {
                    header("Location: manage_users.php?success=1");
                } else {
                    header("Location: manage_users.php?error=1");
                }
            }
        } catch (Exception $e) {
            header("Location: manage_users.php?error=" . urlencode($e->getMessage()));
        }
        exit();
    } elseif ($_POST['action'] === 'remove_restriction') {
        header('Content-Type: application/json');
        try {
            if (!isset($_POST['user_id'])) {
                throw new Exception('User ID is required');
            }
            
            $user_id = $_POST['user_id'];
            
            // First check if the user is actually restricted
            $check_stmt = $conn->prepare("
                SELECT COUNT(*) FROM user_restrictions 
                WHERE User_ID = ? AND (Restricted_Until IS NULL OR Restricted_Until > CURRENT_TIMESTAMP)
            ");
            $check_stmt->execute([$user_id]);
            $is_restricted = $check_stmt->fetchColumn() > 0;
            
            if (!$is_restricted) {
                throw new Exception('User is not currently restricted');
            }
            
            // Try to remove the restriction
            if ($crud->removeRestriction($user_id)) {
                echo json_encode(['success' => true]);
            } else {
                throw new Exception('Failed to remove restriction');
            }
        } catch (Exception $e) {
            error_log("Error removing restriction: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit();
    }
}

// Fetch all users with their restriction status
$all_users = $crud->getAllUsersWithRestrictions();

// Fetch restricted users
$restricted_users = $crud->getRestrictedUsers();

// Fetch banned users
$banned_users = $crud->getBannedUsers();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap Admin - Manage Users</title>
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

        .ban-btn {
            background: #dc3545;
            color: #fff;
        }

        .ban-btn:hover {
            background: #c82333;
        }

        .action-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .modal-content {
            position: relative;
            background: #fff;
            width: 90%;
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .close-modal {
            position: absolute;
            right: 20px;
            top: 20px;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-group textarea {
            height: 100px;
            resize: vertical;
        }

        /* Tab Styles */
        .tab-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .tab-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            background: #f0f2f5;
            color: #333;
        }

        .tab-btn.active {
            background: #ffeb3b;
            color: #000;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
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
                <a href="manage_users.php" class="active">
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
                <a href="announcement.php">
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
        <div class="card">
            <div class="tab-buttons">
                <button class="tab-btn active" onclick="switchTab('all-users')">All Users</button>
                <button class="tab-btn" onclick="switchTab('restricted-users')">Restricted Users</button>
                <button class="tab-btn" onclick="switchTab('banned-users')">Banned Users</button>
            </div>

            <!-- All Users Tab -->
            <div id="all-users" class="tab-content active">
                <h2>All Users</h2>
                <table>
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Verified</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_users as $user): ?>
                        <tr>
                            <td>#<?php echo $user['User_ID']; ?></td>
                            <td><?php echo htmlspecialchars($user['First_Name'] . ' ' . $user['Last_Name']); ?></td>
                            <td><?php echo htmlspecialchars($user['Email']); ?></td>
                            <td>
                                <?php echo $user['Is_Verified'] ? 
                                    '<span style="color:green">Yes</span>' : 
                                    '<span style="color:red">No</span>'; ?>
                            </td>
                            <td>
                                <?php if ($user['Status']): ?>
                                    <span style="color: <?php echo $user['Status'] === 'banned' ? 'red' : 'orange'; ?>">
                                        <?php echo ucfirst($user['Status']); ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color:green">Active</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <button class="action-btn view-btn">View</button>
                                    <?php if (!$user['Status']): ?>
                                        <button class="action-btn restrict-btn" onclick="openRestrictModal(<?php echo $user['User_ID']; ?>, '<?php echo htmlspecialchars($user['First_Name'] . ' ' . $user['Last_Name']); ?>', 'restricted')">Restrict</button>
                                        <button class="action-btn ban-btn" onclick="openRestrictModal(<?php echo $user['User_ID']; ?>, '<?php echo htmlspecialchars($user['First_Name'] . ' ' . $user['Last_Name']); ?>', 'banned')">Ban</button>
                                    <?php else: ?>
                                        <button class="action-btn restrict-btn" style="background: #999; cursor: not-allowed;" disabled>
                                            <?php echo ucfirst($user['Status']); ?>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Restricted Users Tab -->
            <div id="restricted-users" class="tab-content">
                <h2>Restricted Users</h2>
                <table>
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Reason</th>
                            <th>Restricted Until</th>
                            <th>Restricted By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($restricted_users as $user): ?>
                        <tr>
                            <td>#<?php echo $user['User_ID']; ?></td>
                            <td><?php echo htmlspecialchars($user['First_Name'] . ' ' . $user['Last_Name']); ?></td>
                            <td><?php echo htmlspecialchars($user['Email']); ?></td>
                            <td><?php echo ucfirst($user['Status']); ?></td>
                            <td><?php echo htmlspecialchars($user['Reason']); ?></td>
                            <td><?php echo $user['Restricted_Until'] ? date('M d, Y', strtotime($user['Restricted_Until'])) : 'Permanent'; ?></td>
                            <td><?php echo htmlspecialchars($user['Admin_First_Name'] . ' ' . $user['Admin_Last_Name']); ?></td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <button class="action-btn view-btn">View</button>
                                    <button class="action-btn restrict-btn" onclick="removeRestriction(<?php echo $user['User_ID']; ?>)">Remove Restriction</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Banned Users Tab -->
            <div id="banned-users" class="tab-content">
                <h2>Banned Users</h2>
                <table>
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Reason</th>
                            <th>Banned Until</th>
                            <th>Banned By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($banned_users as $user): ?>
                        <tr>
                            <td>#<?php echo $user['User_ID']; ?></td>
                            <td><?php echo htmlspecialchars($user['First_Name'] . ' ' . $user['Last_Name']); ?></td>
                            <td><?php echo htmlspecialchars($user['Email']); ?></td>
                            <td><?php echo ucfirst($user['Status']); ?></td>
                            <td><?php echo htmlspecialchars($user['Reason']); ?></td>
                            <td><?php echo $user['Restricted_Until'] ? date('M d, Y', strtotime($user['Restricted_Until'])) : 'Permanent'; ?></td>
                            <td><?php echo htmlspecialchars($user['Admin_First_Name'] . ' ' . $user['Admin_Last_Name']); ?></td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <button class="action-btn view-btn">View</button>
                                    <button class="action-btn restrict-btn" onclick="removeRestriction(<?php echo $user['User_ID']; ?>)">Remove Ban</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Restriction Modal -->
    <div id="restrictModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeRestrictModal()">&times;</span>
            <h2>Restrict User</h2>
            <form id="restrictForm" action="manage_users.php" method="POST">
                <input type="hidden" name="action" value="restrict">
                <input type="hidden" id="userId" name="user_id">
                <input type="hidden" id="restrictionType" name="status">
                <div class="form-group">
                    <label for="userName">User Name:</label>
                    <input type="text" id="userName" readonly>
                </div>
                <div class="form-group">
                    <label for="reason">Reason:</label>
                    <textarea id="reason" name="reason" required></textarea>
                </div>
                <div class="form-group">
                    <label for="restricted_until">Restricted Until (Optional):</label>
                    <input type="datetime-local" id="restricted_until" name="restricted_until">
                </div>
                <button type="submit" class="action-btn restrict-btn">Apply Restriction</button>
            </form>
        </div>
    </div>

    <script>
        function switchTab(tabId) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Remove active class from all tab buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Show selected tab content
            document.getElementById(tabId).classList.add('active');
            
            // Add active class to clicked button
            event.target.classList.add('active');
        }

        function openRestrictModal(userId, userName, type) {
            document.getElementById('userId').value = userId;
            document.getElementById('userName').value = userName;
            document.getElementById('restrictionType').value = type;
            document.getElementById('restrictModal').style.display = 'block';
            
            // Update modal title and button text based on type
            const modalTitle = document.querySelector('#restrictModal h2');
            const submitBtn = document.querySelector('#restrictForm button[type="submit"]');
            
            if (type === 'banned') {
                modalTitle.textContent = 'Ban User';
                submitBtn.textContent = 'Ban User';
                submitBtn.className = 'action-btn ban-btn';
            } else {
                modalTitle.textContent = 'Restrict User';
                submitBtn.textContent = 'Apply Restriction';
                submitBtn.className = 'action-btn restrict-btn';
            }
        }

        function closeRestrictModal() {
            document.getElementById('restrictModal').style.display = 'none';
        }

        function removeRestriction(userId) {
            if (confirm('Are you sure you want to remove this restriction?')) {
                fetch('manage_users.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=remove_restriction&user_id=${userId}`
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to remove restriction');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while removing the restriction');
                });
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == document.getElementById('restrictModal')) {
                closeRestrictModal();
            }
        }
    </script>
</body>
</html> 