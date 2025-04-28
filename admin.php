<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
        }

        /* Navbar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
            background-color: yellow; /* Yellow */
            padding: 10px 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .navbar .logo {
            font-size: 24px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar .logo img {
            width: 40px;
            height: 40px;
        }

        .navbar .profile {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .navbar .profile img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 70px; /* Below the navbar */
            left: 0;
            width: 240px;
            height: calc(100% - 70px);
            background-color: #333;
            color: #fff;
            padding: 20px;
            box-shadow: 2px 0 6px rgba(0, 0, 0, 0.1);
            z-index: 999;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin-bottom: 20px;
        }

        .sidebar ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar ul li a:hover {
            color: #ffc107;
        }

        /* Dashboard Container */
        .dashboard-container {
            margin-left: 260px; /* Adjust for sidebar width */
            margin-top: 90px; /* Adjust for navbar height */
            padding: 20px;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .dashboard-header h1 {
            font-size: 28px;
            color: #333;
        }

        .dashboard-header .filters {
            display: flex;
            gap: 10px;
        }

        .dashboard-header .filters select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        /* Dashboard Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .dashboard-card {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .dashboard-card h3 {
            margin-bottom: 10px;
            font-size: 18px;
            color: #555;
        }

        .dashboard-card canvas {
            height: 150px;
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
        <div class="profile">
            <span>Admin</span>
            <i class="fas fa-user-circle"></i>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <ul>
            <li><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="#"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="#"><i class="fas fa-file-alt"></i> Posts</a></li>
            <li><a href="#"><i class="fas fa-comments"></i> Announcements</a></li>
            <li><a href="#"><i class="fas fa-exclamation-triangle"></i> Violations</a></li>
            <li><a href="#"><i class="fas fa-cog"></i> Settings</a></li>
        </ul>
    </div>

    <!-- Dashboard -->
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Admin Dashboard</h1>
            <div class="filters">
                <select id="timeFilter" onchange="updateDashboardMetrics()">
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                    <option value="yearly">Yearly</option>
                </select>
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Number of Users -->
            <div class="dashboard-card">
                <h3>Number of Users</h3>
                <canvas id="usersChart"></canvas>
            </div>

            <!-- Number of Posts -->
            <div class="dashboard-card">
                <h3>Number of Posts</h3>
                <canvas id="postsChart"></canvas>
            </div>

            <!-- Comments on Announcements -->
            <div class="dashboard-card">
                <h3>Comments on Announcements</h3>
                <canvas id="commentsChart"></canvas>
            </div>

            <!-- Engagements in Announcements -->
            <div class="dashboard-card">
                <h3>Engagements in Announcements</h3>
                <canvas id="engagementsChart"></canvas>
            </div>

            <!-- Number of Violations -->
            <div class="dashboard-card">
                <h3>Number of Violations</h3>
                <canvas id="violationsChart"></canvas>
            </div>

            <!-- Active Users -->
            <div class="dashboard-card">
                <h3>Active Users</h3>
                <canvas id="activeUsersChart"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function updateDashboardMetrics() {
            // Update chart data dynamically (example data)
            usersChart.data.datasets[0].data = generateRandomData();
            postsChart.data.datasets[0].data = generateRandomData();
            commentsChart.data.datasets[0].data = generateRandomData();
            engagementsChart.data.datasets[0].data = generateRandomData();
            violationsChart.data.datasets[0].data = generateRandomData();
            activeUsersChart.data.datasets[0].data = generateRandomData();

            // Update all charts
            usersChart.update();
            postsChart.update();
            commentsChart.update();
            engagementsChart.update();
            violationsChart.update();
            activeUsersChart.update();
        }

        function generateRandomData() {
            return Array.from({ length: 7 }, () => Math.floor(Math.random() * 1000));
        }

        // Initialize Charts
        const ctxUsers = document.getElementById('usersChart').getContext('2d');
        const ctxPosts = document.getElementById('postsChart').getContext('2d');
        const ctxComments = document.getElementById('commentsChart').getContext('2d');
        const ctxEngagements = document.getElementById('engagementsChart').getContext('2d');
        const ctxViolations = document.getElementById('violationsChart').getContext('2d');
        const ctxActiveUsers = document.getElementById('activeUsersChart').getContext('2d');

        const usersChart = new Chart(ctxUsers, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Users',
                    data: generateRandomData(),
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    fill: true
                }]
            },
            options: { responsive: true }
        });

        const postsChart = new Chart(ctxPosts, {
            type: 'bar',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Posts',
                    data: generateRandomData(),
                    backgroundColor: 'rgba(255, 159, 64, 0.2)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1
                }]
            },
            options: { responsive: true }
        });

        const commentsChart = new Chart(ctxComments, {
            type: 'pie',
            data: {
                labels: ['Likes', 'Shares', 'Views'],
                datasets: [{
                    label: 'Comments',
                    data: generateRandomData().slice(0, 3),
                    backgroundColor: ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)'],
                    borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)'],
                    borderWidth: 1
                }]
            },
            options: { responsive: true }
        });

        const engagementsChart = new Chart(ctxEngagements, {
            type: 'doughnut',
            data: {
                labels: ['Likes', 'Shares', 'Views'],
                datasets: [{
                    label: 'Engagements',
                    data: generateRandomData().slice(0, 3),
                    backgroundColor: ['rgba(153, 102, 255, 0.2)', 'rgba(255, 159, 64, 0.2)', 'rgba(75, 192, 192, 0.2)'],
                    borderColor: ['rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)', 'rgba(75, 192, 192, 1)'],
                    borderWidth: 1
                }]
            },
            options: { responsive: true }
        });

        const violationsChart = new Chart(ctxViolations, {
            type: 'radar',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Violations',
                    data: generateRandomData(),
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: { responsive: true }
        });

        const activeUsersChart = new Chart(ctxActiveUsers, {
            type: 'polarArea',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Active Users',
                    data: generateRandomData(),
                    backgroundColor: ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)', 'rgba(75, 192, 192, 0.2)', 'rgba(153, 102, 255, 0.2)', 'rgba(255, 159, 64, 0.2)', 'rgba(201, 203, 207, 0.2)'],
                    borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)', 'rgba(201, 203, 207, 1)'],
                    borderWidth: 1
                }]
            },
            options: { responsive: true }
        });

        // Initialize metrics on page load
        updateDashboardMetrics();
    </script>
</body>
</html>