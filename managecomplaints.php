<?php
session_start();
require_once 'SkillSwapDatabase.php';

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: loginpagee.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'];
$admin_role = $_SESSION['admin_role'];

// Database connection using PDO
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "skillswap"; 

try {
  $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Connection failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Complaints</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    @media (max-width: 1600px) {
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
    }
    /* Base Styles (Mobile First) */
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
      padding: 10px 20px;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
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
      font-size: 20px;
      font-weight: bold;
      color: #333;
    }
    .logo img {
      height: 35px;
    }
    .admin-info {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-top: 10px;
    }
    .admin-name {
      font-weight: 500;
    }
    .admin-role {
      color: #666;
      font-size: 14px;
    }
    .sidebar {
      position: relative;
      width: 100%;
      background: #fff;
      padding: 20px;
      box-shadow: none;
      top: 0;
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
      margin-top: 130px;
      margin-left: 0;
      padding: 20px;
    }
    @media (min-width: 992px) {
      .navbar {
        flex-direction: row;
        padding: 15px 30px;
      }
      .sidebar {
        width: 250px;
        position: fixed;
        top: 70px;
        left: 0;
        height: calc(100vh - 70px);
        box-shadow: 2px 0 4px rgba(0,0,0,0.1);
      }
      .main-content {
        margin-left: 250px;
        margin-top: 70px;
        padding: 30px;
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
        <a href="managecomplaints.php" class="active">
          <i class="fas fa-exclamation-circle"></i>
          Manage Complaints
        </a>
      </li>
    </ul>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="w-full">
      <h1 class="text-2xl font-bold mb-6">Manage Complaints</h1>
      <table class="w-full bg-white border border-gray-300">
        <thead>
          <tr class="bg-gray-200 text-left">
            <th class="py-3 px-6">ID</th>
            <th class="py-3 px-6">Name</th>
            <th class="py-3 px-6">Email</th>
            <th class="py-3 px-6">Contact No.</th>
            <th class="py-3 px-6">Action</th>
            <th class="py-3 px-6">Created At</th>
            <th class="py-3 px-6">Status</th>
          </tr>
        </thead>
        <tbody id="messageTable">
          <?php
          $sql = "SELECT * FROM complaints ORDER BY created_at DESC";
          $stmt = $pdo->query($sql);

          if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              echo "<tr class='border-t border-gray-300'>";
              echo "<td class='py-3 px-6'>" . $row["id"] . "</td>";
              echo "<td class='py-3 px-6'>" . htmlspecialchars($row["name"]) . "</td>";
              echo "<td class='py-3 px-6'>" . htmlspecialchars($row["email"]) . "</td>";
              echo "<td class='py-3 px-6'>" . htmlspecialchars($row["contact_no"]) . "</td>";
              echo "<td class='py-3 px-6'>
                      <button onclick=\"showMessage('" . htmlspecialchars(addslashes($row["message"])) . "', " . $row["id"] . ", this)\" class='bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded'>
                        View Message
                      </button>
                    </td>";
              echo "<td class='py-3 px-6'>" . $row["created_at"] . "</td>";
              $statusClass = $row["status"] == 'Resolved' ? 'text-green-600' : 'text-yellow-600';
              echo "<td class='py-3 px-6 status-cell $statusClass font-semibold'>" . $row["status"] . "</td>";
              echo "</tr>";
            }
          } else {
            echo "<tr><td colspan='7' class='py-3 px-6 text-center'>No complaints found.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal -->
  <div id="messageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
      <p id="messageContent" class="mb-4 text-gray-800">Message goes here...</p>
      <div class="flex justify-end space-x-3">
        <button id="markSolvedBtn" onclick="markAsSolved()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
          Mark as Solved
        </button>
        <button onclick="closeMessage()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
          Close

        </button>
      </div>
    </div>
  </div>

  <script>
    let currentRow = null;
let currentId = null;
let currentStatus = null;

function showMessage(message, id, button) {
  currentRow = button.closest('tr');
  currentId = id;

  // Get status text from the status cell in the same row
  currentStatus = currentRow.querySelector('.status-cell').textContent.trim();

  // Show the message in the modal
  document.getElementById('messageContent').textContent = message;
  document.getElementById('messageModal').classList.remove('hidden');
  document.getElementById('messageModal').classList.add('flex');

  // Configure the Mark as Solved button
  const solveBtn = document.getElementById('markSolvedBtn');

  if (currentStatus === 'Resolved') {
    solveBtn.disabled = true;
    solveBtn.textContent = 'Already Resolved';
    solveBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
    solveBtn.classList.remove('bg-green-500', 'hover:bg-green-600');
  } else {
    solveBtn.disabled = false;
    solveBtn.textContent = 'Mark as Solved';
    solveBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
    solveBtn.classList.add('bg-green-500', 'hover:bg-green-600');
        }
}
      function closeMessage() {
      document.getElementById('messageModal').classList.add('hidden');
      document.getElementById('messageModal').classList.remove('flex');
    }

    function markAsSolved() {
    if (!currentId) return;

    fetch('mark_solved.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `id=${currentId}`
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Update status in the UI
        const statusCell = currentRow.querySelector('.status-cell');
        statusCell.textContent = 'Resolved';
        statusCell.classList.remove('text-yellow-600');
        statusCell.classList.add('text-green-600');

        closeMessage();
        Swal.fire('Success', 'Complaint marked as solved!', 'success');
      } else {
        Swal.fire('Error', data.message || 'Something went wrong.', 'error');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      Swal.fire('Error', 'Request failed.', 'error');
    })
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
