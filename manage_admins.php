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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

// Fetch all admins
$all_admins = $conn->query("SELECT Admin_ID, First_Name, Last_Name, Email, Role, Is_Active FROM admins")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap Admin - Manage Admins</title>
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
                <a href="announcement.php">
                    <i class="fas fa-bullhorn"></i>
                    Announcement
                </a>
            </li>
            <li>
                <a href="manage_admins.php" class="active">
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
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_admins as $admin): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($admin['Admin_ID']); ?></td>
                        <td><?php echo htmlspecialchars($admin['First_Name'] . ' ' . $admin['Last_Name']); ?></td>
                        <td><?php echo htmlspecialchars($admin['Email']); ?></td>
                        <td><?php echo htmlspecialchars($admin['Role']); ?></td>
                        <td><?php echo $admin['Is_Active'] ? 'Active' : 'Inactive'; ?></td>
                        <td>
                            <button class="action-btn view-btn" onclick="viewAdmin(<?php echo $admin['Admin_ID']; ?>)">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <button class="action-btn restrict-btn" onclick="toggleAdminStatus(<?php echo $admin['Admin_ID']; ?>)">
                                <i class="fas fa-ban"></i> <?php echo $admin['Is_Active'] ? 'Deactivate' : 'Activate'; ?>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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

        function toggleAdminStatus(adminId) {
            // Implement toggle admin status functionality
            console.log('Toggle admin status:', adminId);
        }
    </script>
</body>
</html> 