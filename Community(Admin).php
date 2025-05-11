<?php
session_start();
require_once 'SkillSwapDatabase.php';
require_once 'sp.php';

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: loginpagee.php");
    exit();
}

// Handle approve/decline AJAX actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['community_id'])) {
    $crud = new Crud();
    $id = intval($_POST['community_id']);
    try {
        if ($_POST['action'] === 'approve') {
            $crud->approveCommunity($id);
            echo json_encode(['success' => true]);
        } elseif ($_POST['action'] === 'decline') {
            $crud->declineCommunity($id);
            echo json_encode(['success' => true]);
        } elseif ($_POST['action'] === 'delete') {
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

$db = new Database();
$conn = $db->getConnection();
$crud = new Crud();

// Get admin info
$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'];
$admin_role = $_SESSION['admin_role'];

// Fetch all admins
$all_communities = $conn->query("SELECT * FROM communities")->fetchAll(PDO::FETCH_ASSOC);
$approved_communities = $conn->query("SELECT * FROM view_approvedcommunities")->fetchAll(PDO::FETCH_ASSOC);
$pending_communities = $conn->query("SELECT * FROM communities WHERE status = 'Pending'")->fetchAll(PDO::FETCH_ASSOC);

// Pagination for approved communities
$approvedPerPage = 8;
$approvedPage = isset($_GET['approved_page']) ? (int)$_GET['approved_page'] : 1;
if ($approvedPage < 1) $approvedPage = 1;
$approvedOffset = ($approvedPage - 1) * $approvedPerPage;
$totalApproved = $conn->query("SELECT COUNT(*) FROM view_approvedcommunities")->fetchColumn();
$totalApprovedPages = ceil($totalApproved / $approvedPerPage);
$approved_communities = $conn->prepare("SELECT * FROM view_approvedcommunities LIMIT :limit OFFSET :offset");
$approved_communities->bindValue(':limit', $approvedPerPage, PDO::PARAM_INT);
$approved_communities->bindValue(':offset', $approvedOffset, PDO::PARAM_INT);
$approved_communities->execute();
$approved_communities = $approved_communities->fetchAll(PDO::FETCH_ASSOC);

// Pagination for pending communities
$pendingPerPage = 8;
$pendingPage = isset($_GET['pending_page']) ? (int)$_GET['pending_page'] : 1;
if ($pendingPage < 1) $pendingPage = 1;
$pendingOffset = ($pendingPage - 1) * $pendingPerPage;
$totalPending = $conn->query("SELECT COUNT(*) FROM communities WHERE status = 'Pending'")->fetchColumn();
$totalPendingPages = ceil($totalPending / $pendingPerPage);
$pending_communities = $conn->prepare("SELECT * FROM communities WHERE status = 'Pending' LIMIT :limit OFFSET :offset");
$pending_communities->bindValue(':limit', $pendingPerPage, PDO::PARAM_INT);
$pending_communities->bindValue(':offset', $pendingOffset, PDO::PARAM_INT);
$pending_communities->execute();
$pending_communities = $pending_communities->fetchAll(PDO::FETCH_ASSOC);
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

        .approve-btn {
            background: #4CAF50;
            color: #fff;
        }

        .approve-btn:hover {
            background: #388E3C;
        }

        .decline-btn {
            background: #f44336;
            color: #fff;
        }

        .decline-btn:hover {
            background: #b71c1c;
        }

        .tab-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .tab-btn {
            background: #f5f5f5;
            border: none;
            padding: 10px 30px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            color: #333;
            border-radius: 0;
            transition: background 0.2s, color 0.2s;
        }
        .tab-btn.active {
            background: #ffeb3b;
            color: #000;
        }
        .tab-btn:not(.active) {
            background: #e0e0e0;
            color: #888;
        }
        .tab-content { display: none; }
        .tab-content.active { display: block; }

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
            <?php if ($admin_role === 'super_admin'): ?>
            <li>
                <a href="manage_admins.php">
                    <i class="fas fa-user-shield"></i>
                    Manage Admins
                </a>
            </li>
            <?php endif; ?>
            <li>
                <a href="manageposts.php">
                    <i class="fas fa-thumbtack"></i>
                    Manage Posts
                </a>
            </li>
            <li>
                <a href="Community.php"  class="active">
                    <i class="fas fa-users-cog"></i>
                    Community
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="card">
            <div class="tab-bar">
                <button id="approved-tab" class="tab-btn active" onclick="showTab('approved')">Approved Communities</button>
                <button id="pending-tab" class="tab-btn" onclick="showTab('pending')">Pending Communities</button>
            </div>
            <h2 id="tab-heading">Approved Communities</h2>
            <div id="approved-content" class="tab-content active">
                <table>
                    <thead>
                        <tr>
                            <th>Community ID</th>
                            <th>Name</th>
                            <th>Topic</th>
                            <th>Interest1</th>
                            <th>Interest2</th>
                            <th>Interest3</th>
                            <th>Created At</th>
                            <th>Image</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($approved_communities as $communities): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($communities['Community_ID']); ?></td>
                            <td><?php echo htmlspecialchars($communities['name']); ?></td>
                            <td><?php echo htmlspecialchars($communities['topic']); ?></td>
                            <td><?php echo htmlspecialchars($communities['interest1']); ?></td>
                            <td><?php echo htmlspecialchars($communities['interest2']); ?></td>
                            <td><?php echo htmlspecialchars($communities['interest3']); ?></td>
                            <td><?php echo htmlspecialchars($communities['created_at']); ?></td>
                            <td><?php echo htmlspecialchars($communities['image_url']); ?></td>
                            <td><?php echo htmlspecialchars($communities['status']); ?></td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <button class="action-btn view-btn">View</button>
                                    <button class="action-btn restrict-btn" onclick="deleteCommunity(<?php echo $communities['Community_ID']; ?>)">Delete</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div style="text-align:center; margin-top:20px;">
                <nav aria-label="Approved Communities Page navigation">
                  <ul class="pagination">
                    <li class="page-item <?php if ($approvedPage <= 1) echo 'disabled'; ?>">
                      <a class="page-link" href="?approved_page=<?php echo $approvedPage - 1; ?>">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $totalApprovedPages; $i++): ?>
                      <li class="page-item <?php if ($i == $approvedPage) echo 'active'; ?>">
                        <a class="page-link<?php if ($i == $approvedPage) echo ' active'; ?>" href="?approved_page=<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endfor; ?>
                    <li class="page-item <?php if ($approvedPage >= $totalApprovedPages) echo 'disabled'; ?>">
                      <a class="page-link" href="?approved_page=<?php echo $approvedPage + 1; ?>">Next</a>
                    </li>
                  </ul>
                </nav>
                </div>
            </div>
            <div id="pending-content" class="tab-content">
                <table>
                    <thead>
                        <tr>
                            <th>Community ID</th>
                            <th>Name</th>
                            <th>Topic</th>
                            <th>Interest1</th>
                            <th>Interest2</th>
                            <th>Interest3</th>
                            <th>Created At</th>
                            <th>Image</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pending_communities as $communities): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($communities['Community_ID']); ?></td>
                            <td><?php echo htmlspecialchars($communities['name']); ?></td>
                            <td><?php echo htmlspecialchars($communities['topic']); ?></td>
                            <td><?php echo htmlspecialchars($communities['interest1']); ?></td>
                            <td><?php echo htmlspecialchars($communities['interest2']); ?></td>
                            <td><?php echo htmlspecialchars($communities['interest3']); ?></td>
                            <td><?php echo htmlspecialchars($communities['created_at']); ?></td>
                            <td><?php echo htmlspecialchars($communities['image_url']); ?></td>
                            <td><?php echo htmlspecialchars($communities['status']); ?></td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                <button class="action-btn approve-btn" onclick="approveCommunity(<?php echo $communities['Community_ID']; ?>)">Approve</button>
                                <button class="action-btn decline-btn" onclick="declineCommunity(<?php echo $communities['Community_ID']; ?>)">Decline</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div style="text-align:center; margin-top:20px;">
                <nav aria-label="Pending Communities Page navigation">
                  <ul class="pagination">
                    <li class="page-item <?php if ($pendingPage <= 1) echo 'disabled'; ?>">
                      <a class="page-link" href="?pending_page=<?php echo $pendingPage - 1; ?>#pending-content">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPendingPages; $i++): ?>
                      <li class="page-item <?php if ($i == $pendingPage) echo 'active'; ?>">
                        <a class="page-link<?php if ($i == $pendingPage) echo ' active'; ?>" href="?pending_page=<?php echo $i; ?>#pending-content"><?php echo $i; ?></a>
                      </li>
                    <?php endfor; ?>
                    <li class="page-item <?php if ($pendingPage >= $totalPendingPages) echo 'disabled'; ?>">
                      <a class="page-link" href="?pending_page=<?php echo $pendingPage + 1; ?>#pending-content">Next</a>
                    </li>
                  </ul>
                </nav>
                </div>
            </div>
        </div>
    </div>

    <script>
    function showTab(tab) {
        document.getElementById('approved-tab').classList.remove('active');
        document.getElementById('pending-tab').classList.remove('active');
        document.getElementById('approved-content').classList.remove('active');
        document.getElementById('pending-content').classList.remove('active');
        document.getElementById(tab+'-tab').classList.add('active');
        document.getElementById(tab+'-content').classList.add('active');
        document.getElementById('tab-heading').textContent = tab === 'approved' ? 'Approved Communities' : 'Pending Communities';
    }

    function approveCommunity(communityId) {
        if (confirm('Are you sure you want to approve this community?')) {
            fetch('community.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=approve&community_id=' + encodeURIComponent(communityId)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) location.reload();
                else alert('Error: ' + data.error);
            });
        }
    }

    function declineCommunity(communityId) {
        if (confirm('Are you sure you want to decline (delete) this community?')) {
            fetch('Community.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=decline&community_id=' + encodeURIComponent(communityId)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) location.reload();
                else alert('Error: ' + data.error);
            });
        }
    }

    function deleteCommunity(communityId) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This community will be permanently deleted!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('community.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=delete&community_id=' + encodeURIComponent(communityId)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Deleted!', 'The community has been deleted.', 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.error || 'Failed to delete community', 'error');
                    }
                });
            }
        });
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
