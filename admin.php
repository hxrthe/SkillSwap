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

// Set default date range to today
$end_date = date('Y-m-d');
$start_date = date('Y-m-d'); // Same as end_date for today's data

// Get statistics
$user_stats = $crud->getUserStatistics(null, null); // Get total counts
$restriction_stats = $crud->getRestrictionStatistics(null, null); // Get total counts
$activity_stats = $crud->getUserActivityStatistics(null, null); // Get total counts
$system_stats = $crud->getSystemStatistics();
$daily_users = $crud->getDailyUserRegistrations($start_date, $end_date);
$daily_restrictions = $crud->getDailyRestrictions($start_date, $end_date);
$daily_posts = $crud->getDailyPosts($start_date, $end_date);
$daily_comments = $crud->getDailyComments($start_date, $end_date);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap Admin - Dashboard</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        .filter-section {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
        }

        .filter-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            background: #f0f2f5;
            color: #333;
        }

        .filter-btn.active {
            background: #ffeb3b;
            color: #000;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .stat-card h3 {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .stat-card .value {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .chart-container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .chart-container h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .chart-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 20px;
        }
    }/* Base Styles (Mobile First) */
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

.filter-section {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.filter-buttons {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.filter-btn {
    padding: 8px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: 500;
    background: #f0f2f5;
    color: #333;
}

.filter-btn.active {
    background: #ffeb3b;
    color: #000;
}

.stats-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.stat-card {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.stat-card h3 {
    color: #666;
    font-size: 14px;
    margin-bottom: 10px;
}

.stat-card .value {
    font-size: 24px;
    font-weight: bold;
    color: #333;
}

.chart-container {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.chart-container h2 {
    margin-bottom: 20px;
    color: #333;
}

.chart-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
}


/* Tablet (601px to 991px) */
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
    .filter-buttons {
        flex-direction: column;
    }
}

@media (min-width: 601px) and (max-width: 991px) {
    .navbar {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
    }
    .sidebar {
        width: 200px;
        position: fixed;
        top: 70px;
        left: 0;
        height: 100vh;
        box-shadow: 2px 0 4px rgba(0,0,0,0.1);
        margin-top: 0;
    }
    .main-content {
        margin-left: 200px;
        margin-top: 70px;
    }
    .filter-buttons {
        flex-direction: row;
        flex-wrap: wrap;
    }
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .chart-grid {
        grid-template-columns: repeat(1, 1fr);
    }
}


/* Laptops & Desktops (992px and up) */
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

    .filter-buttons {
        flex-direction: row;
    }

    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    }

    .chart-grid {
        grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
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
                <a href="admin.php" class="active">
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
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Users</h3>
                <div class="value"><?php echo $user_stats['total_users']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Admins</h3>
                <div class="value"><?php echo $system_stats['total_admins']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Verified Users</h3>
                <div class="value"><?php echo $user_stats['verified_users']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Active Users</h3>
                <div class="value"><?php echo $activity_stats['active_users']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Posts</h3>
                <div class="value"><?php echo $activity_stats['total_posts']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Comments</h3>
                <div class="value"><?php echo $activity_stats['total_comments']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Likes</h3>
                <div class="value"><?php echo $activity_stats['total_likes']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Communities</h3>
                <div class="value"><?php echo $system_stats['total_communities']; ?></div>
            </div>
            
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="filter-buttons">
                <button class="filter-btn active" onclick="updateDateRange('day')">Today</button>
                <button class="filter-btn" onclick="updateDateRange('week')">This Week</button>
                <button class="filter-btn" onclick="updateDateRange('month')">This Month</button>
                <button class="filter-btn" onclick="updateDateRange('year')">This Year</button>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="chart-grid">
            <!-- User Registrations Chart -->
            <div class="chart-container">
                <h2>User Registrations</h2>
                <canvas id="userRegistrationsChart"></canvas>
            </div>

            <!-- Restrictions Chart -->
            <div class="chart-container">
                <h2>User Restrictions</h2>
                <canvas id="restrictionsChart"></canvas>
            </div>

            <!-- Posts Chart -->
            <div class="chart-container">
                <h2>Posts</h2>
                <canvas id="postsChart"></canvas>
            </div>

            <!-- Comments Chart -->
            <div class="chart-container">
                <h2>Comments</h2>
                <canvas id="commentsChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Initialize charts
        const userRegistrationsCtx = document.getElementById('userRegistrationsChart').getContext('2d');
        const restrictionsCtx = document.getElementById('restrictionsChart').getContext('2d');
        const postsCtx = document.getElementById('postsChart').getContext('2d');
        const commentsCtx = document.getElementById('commentsChart').getContext('2d');

        // Store chart instances in global variables
        let userRegistrationsChart = new Chart(userRegistrationsCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($daily_users, 'date')); ?>,
                datasets: [{
                    label: 'New Users',
                    data: <?php echo json_encode(array_column($daily_users, 'new_users')); ?>,
                    borderColor: '#ffeb3b',
                    backgroundColor: 'rgba(255, 235, 59, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Restrictions Chart
        let restrictionsChart = new Chart(restrictionsCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($daily_restrictions, 'date')); ?>,
                datasets: [{
                    label: 'Restricted',
                    data: <?php echo json_encode(array_column($daily_restrictions, 'restricted_count')); ?>,
                    backgroundColor: '#ff9800'
                }, {
                    label: 'Banned',
                    data: <?php echo json_encode(array_column($daily_restrictions, 'banned_count')); ?>,
                    backgroundColor: '#f44336'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Posts Chart
        let postsChart = new Chart(postsCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($daily_posts, 'date')); ?>,
                datasets: [{
                    label: 'Posts',
                    data: <?php echo json_encode(array_column($daily_posts, 'post_count')); ?>,
                    borderColor: '#4CAF50',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Comments Chart
        let commentsChart = new Chart(commentsCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($daily_comments, 'date')); ?>,
                datasets: [{
                    label: 'Comments',
                    data: <?php echo json_encode(array_column($daily_comments, 'comment_count')); ?>,
                    borderColor: '#2196F3',
                    backgroundColor: 'rgba(33, 150, 243, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Date range filter
        function updateDateRange(period) {
            const buttons = document.querySelectorAll('.filter-btn');
            buttons.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');

            const end_date = new Date();
            let start_date = new Date();

            switch(period) {
                case 'day':
                    start_date.setDate(end_date.getDate() - 1);
                    break;
                case 'week':
                    start_date.setDate(end_date.getDate() - 7);
                    break;
                case 'month':
                    start_date.setMonth(end_date.getMonth() - 1);
                    break;
                case 'year':
                    start_date.setFullYear(end_date.getFullYear() - 1);
                    break;
            }

            // Format dates for API
            const formatDate = (date) => {
                return date.toISOString().split('T')[0];
            };

            // Fetch new data
            fetch(`get_dashboard_data.php?start_date=${formatDate(start_date)}&end_date=${formatDate(end_date)}`)
                .then(response => response.json())
                .then(data => {
                    // Update charts and stats
                    updateCharts(data);
                    updateStats(data);
                })
                .catch(error => console.error('Error:', error));
        }

        function updateCharts(data) {
            // Update user registrations chart
            userRegistrationsChart.data.labels = data.daily_users.map(u => u.date);
            userRegistrationsChart.data.datasets[0].data = data.daily_users.map(u => u.new_users);
            userRegistrationsChart.update();

            // Update restrictions chart
            restrictionsChart.data.labels = data.daily_restrictions.map(r => r.date);
            restrictionsChart.data.datasets[0].data = data.daily_restrictions.map(r => r.restricted_count);
            restrictionsChart.data.datasets[1].data = data.daily_restrictions.map(r => r.banned_count);
            restrictionsChart.update();

            // Update posts chart
            postsChart.data.labels = data.daily_posts.map(p => p.date);
            postsChart.data.datasets[0].data = data.daily_posts.map(p => p.post_count);
            postsChart.update();

            // Update comments chart
            commentsChart.data.labels = data.daily_comments.map(c => c.date);
            commentsChart.data.datasets[0].data = data.daily_comments.map(c => c.comment_count);
            commentsChart.update();
        }

        function updateStats(data) {
            // Remove the stat card updates since they should remain static
            // Only update the charts
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