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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['firstName'], $_POST['lastName'], $_POST['email'], $_POST['password'], $_POST['role'])) {
    try {
        // Get form data
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $_POST['role'];

        // Create admin using Crud class
        $crud->createAdmin($firstName, $lastName, $email, $password, $role, true);

        $_SESSION['success'] = "Admin added successfully!";
        header("Location: manage_admins.php");
        exit();
    } catch (Exception $e) {
        if ($e->getMessage() === 'email_exists') {
            $_SESSION['error'] = "Email already exists!";
        } else {
            $_SESSION['error'] = "Error adding admin: " . $e->getMessage();
        }
        header("Location: manage_admins.php");
        exit();
    }
}

// Handle activate/deactivate admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['admin_id'])) {
    header('Content-Type: application/json');
    $admin_id = (int)$_POST['admin_id'];
    $action = $_POST['action'];
    try {
        if ($action === 'deactivate') {
            $stmt = $conn->prepare("UPDATE admins SET Is_Active = 0 WHERE Admin_ID = ?");
            $stmt->execute([$admin_id]);
            echo json_encode(['success' => true, 'status' => 'deactivated']);
        } elseif ($action === 'activate') {
            $stmt = $conn->prepare("UPDATE admins SET Is_Active = 1 WHERE Admin_ID = ?");
            $stmt->execute([$admin_id]);
            echo json_encode(['success' => true, 'status' => 'activated']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit();
}

// Fetch all admins
$all_admins = $conn->query("SELECT Admin_ID, First_Name, Last_Name, Email, Role, Is_Active FROM admins")->fetchAll(PDO::FETCH_ASSOC);

// --- Pagination and Filtering Logic ---
$adminsPerPage = 8;

// Get tab and page from GET
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'all';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Filter admins by role
$admins_all = $all_admins;
$admins_admin = array_filter($all_admins, function($a) { return $a['Role'] === 'admin'; });
$admins_super = array_filter($all_admins, function($a) { return $a['Role'] === 'super_admin'; });

// Paginate each set
function paginate($data, $page, $perPage) {
    $total = count($data);
    $totalPages = max(1, ceil($total / $perPage));
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $perPage;
    return [
        'data' => array_slice(array_values($data), $offset, $perPage),
        'totalPages' => $totalPages,
        'page' => $page
    ];
}
$all = paginate($admins_all, $tab === 'all' ? $page : 1, $adminsPerPage);
$admin = paginate($admins_admin, $tab === 'admin' ? $page : 1, $adminsPerPage);
$super = paginate($admins_super, $tab === 'super_admin' ? $page : 1, $adminsPerPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap Admin - Manage Admins</title>
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

        .tab-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 24px;
        }
        .tab-btn {
            background: #f5f5f5;
            border: none;
            padding: 12px 32px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            color: #333;
            border-radius: 10px 10px 10px 10px;
            transition: background 0.2s, color 0.2s;
        }
        .tab-btn.active {
            background: #ffeb3b;
            color: #000;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(255,235,59,0.08);
        }
        .tab-btn:not(.active) {
            background: #f5f5f5;
            color: #888;
        }
        .tab-content { display: none; }
        .tab-content.active { display: block; }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        th, td {
            padding: 14px 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background: #f0f2f5;
            font-weight: 500;
            font-size: 17px;
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

        /* Pagination Styles */
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
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
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
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .form-group input:focus, .form-group select:focus {
            border-color: #ffeb3b;
            outline: none;
        }
        .add-admin-btn {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            margin-bottom: 20px;
        }
        .add-admin-btn:hover {
            background: #45a049;
        }
        .submit-btn {
            background: #ffeb3b;
            color: #000;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            width: 100%;
        }
        .submit-btn:hover {
            background: #ffd600;
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
            .tab-bar {
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
            .tab-bar {
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
                <a href="manage_admins.php" class="active">
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
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="card">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success" style="background: #4CAF50; color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                    <?php 
                        echo $_SESSION['success'];
                        unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error" style="background: #f44336; color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                    <?php 
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>Manage Admins</h2>
                <?php if ($admin_role === 'super_admin'): ?>
                    <button class="add-admin-btn" onclick="openModal()">
                        <i class="fas fa-plus"></i> Add Admin
                    </button>
                <?php endif; ?>
            </div>
            <div class="tab-bar" style="display:flex; gap:10px; margin-bottom:20px;">
                <button class="tab-btn<?php if($tab==='all')echo' active';?>" onclick="switchTab('all')">All Admins</button>
                <button class="tab-btn<?php if($tab==='admin')echo' active';?>" onclick="switchTab('admin')">Admin Role</button>
                <button class="tab-btn<?php if($tab==='super_admin')echo' active';?>" onclick="switchTab('super_admin')">Superadmin Role</button>
            </div>
            <?php
            $tabData = [];
            if ($tab === 'all') $tabData = $all;
            if ($tab === 'admin') $tabData = $admin;
            if ($tab === 'super_admin') $tabData = $super;
            ?>
            <div id="tab-<?= $tab ?>" class="tab-content active">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($tabData['data'])): ?>
                            <?php foreach ($tabData['data'] as $admin): ?>
                                <tr>
                                    <td><?= htmlspecialchars($admin['Admin_ID']) ?></td>
                                    <td><?= htmlspecialchars($admin['First_Name'] . ' ' . $admin['Last_Name']) ?></td>
                                    <td><?= htmlspecialchars($admin['Email']) ?></td>
                                    <td><?= htmlspecialchars($admin['Role']) ?></td>
                                    <td><?= $admin['Is_Active'] ? 'Active' : 'Inactive' ?></td>
                                    <td>
                                        <?php if ($admin_role === 'super_admin' && $admin['Admin_ID'] != $admin_id): ?>
                                            <?php if ($admin['Is_Active']): ?>
                                                <button class="action-btn restrict-btn" onclick="toggleAdminStatus(<?= $admin['Admin_ID'] ?>, 'deactivate')">
                                                    <i class="fas fa-ban"></i> Deactivate
                                                </button>
                                            <?php else: ?>
                                                <button class="action-btn view-btn" style="background:#4CAF50;color:#fff;" onclick="toggleAdminStatus(<?= $admin['Admin_ID'] ?>, 'activate')">
                                                    <i class="fas fa-check"></i> Activate
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align:center; color:#888;">No admins found.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php
                $totalPages = $tabData['totalPages'] ?? 1;
                $curPage = $tabData['page'] ?? 1;
                ?>
                <div style="text-align:center; margin-top:20px;">
                    <nav aria-label="Admins Page navigation">
                        <ul class="pagination">
                            <li class="page-item <?php if ($curPage <= 1) echo 'disabled'; ?>">
                                <a class="page-link" href="?tab=<?= $tab ?>&page=<?= $curPage-1 ?>">Previous</a>
                            </li>
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?php if ($i == $curPage) echo 'active'; ?>">
                                    <a class="page-link<?php if ($i == $curPage) echo ' active'; ?>" href="?tab=<?= $tab ?>&page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?php if ($curPage >= $totalPages) echo 'disabled'; ?>">
                                <a class="page-link" href="?tab=<?= $tab ?>&page=<?= $curPage+1 ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Admin Modal -->
    <?php if ($admin_role === 'super_admin'): ?>
        <div id="addAdminModal" class="modal">
            <div class="modal-content">
                <span class="close-modal" onclick="closeModal()">&times;</span>
                <h2 style="margin-bottom: 20px;">Add New Admin</h2>
                <form id="addAdminForm" method="POST">
                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <input type="text" id="firstName" name="firstName" required>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <input type="text" id="lastName" name="lastName" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select id="role" name="role" required>
                            <option value="admin">Admin</option>
                            <option value="super_admin">Super Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="submit-btn">Add Admin</button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <script>
        function openModal() {
            document.getElementById('addAdminModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('addAdminModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('addAdminModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        function viewAdmin(adminId) {
            // Implement view admin functionality
            console.log('View admin:', adminId);
        }

        function toggleAdminStatus(adminId, action) {
            let actionText = action === 'deactivate' ? 'deactivate' : 'activate';
            let confirmText = action === 'deactivate' ? 'This admin will not be able to log in.' : 'This admin will be able to log in again.';
            
            Swal.fire({
                title: `Are you sure you want to ${actionText} this admin?`,
                text: confirmText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: action === 'deactivate' ? '#d33' : '#4CAF50',
                cancelButtonColor: '#3085d6',
                confirmButtonText: `Yes, ${actionText} it!`
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create form data
                    const formData = new FormData();
                    formData.append('action', action);
                    formData.append('admin_id', adminId);

                    // Send AJAX request
                    fetch('manage_admins.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: `Admin has been ${data.status}.`,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.error || 'Failed to update admin status'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while updating admin status'
                        });
                    });
                }
            });
        }

        function switchTab(tab) {
            const url = new URL(window.location.href);
            url.searchParams.set('tab', tab);
            url.searchParams.set('page', 1);
            window.location.href = url.toString();
        }

        // Show only the active tab content
        const tab = '<?= $tab ?>';
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.tab-content').forEach(function(el) {
                el.style.display = 'none';
            });
            document.getElementById('tab-' + tab).style.display = 'block';
        });

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