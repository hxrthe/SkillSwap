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

$usersPerPage = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $usersPerPage;

// Get total user count
$totalUsers = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalPages = ceil($totalUsers / $usersPerPage);

// Fetch users for current page
$stmt = $conn->prepare("SELECT * FROM viewallusers LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $usersPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

/// Fetch all users with their restriction status from the view
$all_users = $conn->query("SELECT * FROM viewallusers")->fetchAll(PDO::FETCH_ASSOC);

// Pagination for restricted users
$restrictedPerPage = 5;
$restrictedPage = isset($_GET['restricted_page']) ? (int)$_GET['restricted_page'] : 1;
if ($restrictedPage < 1) $restrictedPage = 1;
$restrictedOffset = ($restrictedPage - 1) * $restrictedPerPage;
$totalRestricted = $conn->query("SELECT COUNT(*) FROM view_restrictedusers")->fetchColumn();
$totalRestrictedPages = ceil($totalRestricted / $restrictedPerPage);
$restricted_stmt = $conn->prepare("SELECT * FROM view_restrictedusers LIMIT :limit OFFSET :offset");
$restricted_stmt->bindValue(':limit', $restrictedPerPage, PDO::PARAM_INT);
$restricted_stmt->bindValue(':offset', $restrictedOffset, PDO::PARAM_INT);
$restricted_stmt->execute();
$restricted_users = $restricted_stmt->fetchAll(PDO::FETCH_ASSOC);

// Pagination for banned users
$bannedPerPage = 5;
$bannedPage = isset($_GET['banned_page']) ? (int)$_GET['banned_page'] : 1;
if ($bannedPage < 1) $bannedPage = 1;
$bannedOffset = ($bannedPage - 1) * $bannedPerPage;
$totalBanned = $conn->query("SELECT COUNT(*) FROM view_bannedusers")->fetchColumn();
$totalBannedPages = ceil($totalBanned / $bannedPerPage);
$banned_stmt = $conn->prepare("SELECT * FROM view_bannedusers LIMIT :limit OFFSET :offset");
$banned_stmt->bindValue(':limit', $bannedPerPage, PDO::PARAM_INT);
$banned_stmt->bindValue(':offset', $bannedOffset, PDO::PARAM_INT);
$banned_stmt->execute();
$banned_users = $banned_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['community_id'])) {
    require_once 'sp.php';
    $crud = new Crud();
    $id = intval($_POST['community_id']);
    try {
        if ($_POST['action'] === 'approve') {
            $crud->approveCommunity($id);
            echo json_encode(['success' => true]);
        } elseif ($_POST['action'] === 'decline') {
            $crud->declineCommunity($id);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap Admin - Manage Users</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
       /* Base Styles */
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

.pagination {
    display: inline-flex;
    list-style: none;
    padding: 0;
    margin: 0 auto;
    justify-content: center;
}
.page-item {
    margin-right: 0;
}
.page-link {
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
    .tab-buttons {
        flex-direction: column;
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

    .tab-btn {
        padding: 8px 15px;
        font-size: 14px;
    }
}

.table-responsive {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
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
                <a href="manage_users.php" class="active">
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
                <a href="Community(Admin).php">
                    <i class="fas fa-users-cog"></i>
                    Community
                </a>
            </li>
            <li>
                <a href="managecomplaints.php">
                    <i class="fas fa-exclamation-circle"></i>
                    Manage Complaints
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
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Verified</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['User_ID']) ?></td>
                                <td><?= htmlspecialchars($user['First_Name'] . ' ' . $user['Last_Name']) ?></td>
                                <td><?= htmlspecialchars($user['Email']) ?></td>
                                <td>
                                    <?php echo $user['Is_Verified'] ? 
                                        '<span style="color:green">Yes</span>' : 
                                        '<span style="color:red">No</span>'; ?>
                                </td>
                                <td>
                                    <?php if (isset($user['Status']) && $user['Status']): ?>
                                        <span style="color: <?php echo $user['Status'] === 'banned' ? 'red' : 'orange'; ?>;">
                                            <?php echo ucfirst($user['Status']); ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color:green">Active</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                       
                                        <?php if (!isset($user['Status']) || !$user['Status']): ?>
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

            <!-- Restricted Users Tab -->
            <div id="restricted-users" class="tab-content">
                <h2>Restricted Users</h2>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
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
                                <td><?= htmlspecialchars($user['User_ID']) ?></td>
                                <td><?= htmlspecialchars($user['First_Name'] . ' ' . $user['Last_Name']) ?></td>
                                <td><?= htmlspecialchars($user['Email']) ?></td>
                                <td><?= htmlspecialchars($user['Status']) ?></td>
                                <td><?= htmlspecialchars($user['Reason']) ?></td>
                                <td><?php echo $user['Restricted_Until'] ? date('M d, Y', strtotime($user['Restricted_Until'])) : 'Permanent'; ?></td>
                                <td><?= htmlspecialchars($user['Admin_First_Name'] . ' ' . $user['Admin_Last_Name']) ?></td>
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
                <div style="text-align:center; margin-top:20px;">
                <nav aria-label="Restricted Users Page navigation">
                  <ul class="pagination">
                    <li class="page-item <?php if ($restrictedPage <= 1) echo 'disabled'; ?>">
                      <a class="page-link" href="?restricted_page=<?php echo $restrictedPage - 1; ?>#restricted-users">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $totalRestrictedPages; $i++): ?>
                      <li class="page-item <?php if ($i == $restrictedPage) echo 'active'; ?>">
                        <a class="page-link<?php if ($i == $restrictedPage) echo ' active'; ?>" href="?restricted_page=<?php echo $i; ?>#restricted-users"><?php echo $i; ?></a>
                      </li>
                    <?php endfor; ?>
                    <li class="page-item <?php if ($restrictedPage >= $totalRestrictedPages) echo 'disabled'; ?>">
                      <a class="page-link" href="?restricted_page=<?php echo $restrictedPage + 1; ?>#restricted-users">Next</a>
                    </li>
                  </ul>
                </nav>
                </div>
            </div>

            <!-- Banned Users Tab -->
            <div id="banned-users" class="tab-content">
                <h2>Banned Users</h2>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
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
                                <td><?= htmlspecialchars($user['User_ID']) ?></td>
                                <td><?= htmlspecialchars($user['First_Name'] . ' ' . $user['Last_Name']) ?></td>
                                <td><?= htmlspecialchars($user['Email']) ?></td>
                                <td><?= htmlspecialchars($user['Status']) ?></td>
                                <td><?= htmlspecialchars($user['Reason']) ?></td>
                                <td><?php echo $user['Restricted_Until'] ? date('M d, Y', strtotime($user['Restricted_Until'])) : 'Permanent'; ?></td>
                                <td><?= htmlspecialchars($user['Admin_First_Name'] . ' ' . $user['Admin_Last_Name']) ?></td>
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
                <div style="text-align:center; margin-top:20px;">
                <nav aria-label="Banned Users Page navigation">
                  <ul class="pagination">
                    <li class="page-item <?php if ($bannedPage <= 1) echo 'disabled'; ?>">
                      <a class="page-link" href="?banned_page=<?php echo $bannedPage - 1; ?>#banned-users">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $totalBannedPages; $i++): ?>
                      <li class="page-item <?php if ($i == $bannedPage) echo 'active'; ?>">
                        <a class="page-link<?php if ($i == $bannedPage) echo ' active'; ?>" href="?banned_page=<?php echo $i; ?>#banned-users"><?php echo $i; ?></a>
                      </li>
                    <?php endfor; ?>
                    <li class="page-item <?php if ($bannedPage >= $totalBannedPages) echo 'disabled'; ?>">
                      <a class="page-link" href="?banned_page=<?php echo $bannedPage + 1; ?>#banned-users">Next</a>
                    </li>
                  </ul>
                </nav>
                </div>
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
            Swal.fire({
                title: 'Are you sure?',
                text: 'This will remove the restriction/ban from the user.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4CAF50',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
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
                            Swal.fire('Removed!', 'The restriction/ban has been removed.', 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Error', data.message || 'Failed to remove restriction/ban', 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error', 'An error occurred while removing the restriction/ban', 'error');
                    });
                }
            });
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == document.getElementById('restrictModal')) {
                closeRestrictModal();
            }
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
    </script>
</body>
</html> 