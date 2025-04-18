<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yellow Dashboard</title>
    <!-- Import Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: rgb(255, 230, 146); /* Light yellow background */
            color: #333;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
            background-color: #ffeb3b; /* Yellow */
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
        }

        .navbar .search-bar {
            flex: 1;
            margin: 0 20px;
        }

        .navbar .search-bar input {
            width: 100%;
            max-width: 400px;
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .navbar .icons {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .navbar .icons a {
            text-decoration: none;
            color: #000;
            font-size: 18px;
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

        .sidebar {
            width: 220px;
            height: calc(100vh - 70px); /* Adjust height to fit below the navbar */
            background-color: #ffeb3b; /* Yellow */
            color: #000;
            position: fixed;
            top: 70px; /* Start below the navbar */
            left: 0;
            padding: 20px;
            box-shadow: 2px 0 6px rgba(0, 0, 0, 0.2); /* Add shadow to the sidebar */
            z-index: 999; /* Ensure it stays below the navbar */
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin: 20px 0; /* Add gaps between list items */
            display: flex;
            align-items: center; /* Align icons and text */
            gap: 10px; /* Add space between icons and text */
        }

        .sidebar ul li a {
            text-decoration: none;
            color: #000;
            font-weight: bold;
            flex: 1; /* Ensure text aligns properly */
        }

        .main-content {
            margin-left: 270px; /* Add more space between the body and the sidebar */
            margin-top: 70px; /* Adjust for navbar height */
            padding: 20px;
            overflow-x: hidden; /* Prevent horizontal overflow */
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); /* Responsive grid */
            gap: 20px;
        }

        .card {
            background-color: #fff59d; /* Light yellow card */
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden; /* Prevent content overflow */
        }

        .card h3 {
            margin: 0 0 10px;
        }

        canvas {
            max-width: 100%; /* Ensure canvas fits within the card */
            height: auto;
        }

        /* Mini Sidebar */
        .mini-sidebar {
            width: 200px;
            height: calc(100vh - 100px); /* Reduce size at the bottom */
            background-color: rgba(231, 213, 53, 0.9); /* Light yellow */
            color: #000;
            position: fixed;
            right: -240px; /* Initially hidden */
            top: 70px;
            padding: 20px;
            box-shadow: -2px 0 6px rgba(0, 0, 0, 0.2); /* Add shadow */
            transition: right 0.3s ease; /* Smooth slide-in effect */
            z-index: 1001;
            border-radius: 40px 0 0 40px; /* Rounded corners */
        }

        .mini-sidebar.active {
            right: 0; /* Slide in when active */
        }

        .mini-sidebar h3 {
            margin-bottom: 20px;
        }

        .mini-sidebar ul {
            list-style: none;
            padding: 0;
        }

        .mini-sidebar ul li {
            margin: 15px 0;
        }

        .mini-sidebar ul li a {
            text-decoration: none;
            color: #000;
            font-weight: bold;
        }

        /* Overlay to close sidebar */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
            z-index: 1000;
            display: none; /* Hidden by default */
        }

        .overlay.active {
            display: block; /* Show when active */
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar .search-bar {
                display: none; /* Hide search bar on smaller screens */
            }

            .sidebar {
                width: 180px; /* Reduce sidebar width */
            }

            .main-content {
                margin-left: 220px; /* Adjust for smaller sidebar */
            }
        }

        @media (max-width: 480px) {
            .navbar .icons {
                gap: 10px; /* Reduce gap between icons */
            }

            .sidebar {
                width: 160px; /* Further reduce sidebar width */
            }

            .main-content {
                margin-left: 200px; /* Adjust for smaller sidebar */
            }

            .grid {
                grid-template-columns: 1fr; /* Single column layout */
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Toggle Mini Sidebar
        function toggleMiniSidebar() {
            const miniSidebar = document.querySelector('.mini-sidebar');
            const overlay = document.querySelector('.overlay');
            miniSidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        // Close Mini Sidebar when clicking outside
        function closeMiniSidebar() {
            const miniSidebar = document.querySelector('.mini-sidebar');
            const overlay = document.querySelector('.overlay');
            miniSidebar.classList.remove('active');
            overlay.classList.remove('active');
        }
    </script>
</head>
<body>
    <div class="bg">
        <img src="ssbg4.png" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: -1;">
    </div>
    <div class="navbar">
        <div class="logo">
            Dashboard
        </div>
        <div class="search-bar">
            <input type="text" placeholder="Search...">
        </div>
        <div class="icons">
            <a href="#"><i class="fas fa-envelope"></i></a> <!-- Inbox Icon -->
            <a href="#"><i class="fas fa-bell"></i></a> <!-- Notification Icon -->
            <a href="#"><i class="fas fa-cog"></i></a> <!-- Settings Icon -->
        </div>
        <div class="profile" onclick="toggleMiniSidebar()">
            <img src="https://via.placeholder.com/30" alt="Profile">
            <span>John Smith</span>
        </div>
    </div>
    <div class="sidebar">
        <h2>Dashboard</h2>
        <ul>
            <li><i class="fas fa-chart-line"></i><a href="#">Charts</a></li>
            <li><i class="fas fa-chart-pie"></i><a href="#">Statistics</a></li>
            <li><i class="fas fa-calendar-alt"></i><a href="#">Calendar</a></li>
            <li><i class="fas fa-envelope"></i><a href="#">Messages</a></li>
            <li><i class="fas fa-cog"></i><a href="#">Settings</a></li>
            <li><i class="fas fa-life-ring"></i><a href="#">Support</a></li>
        </ul>
    </div>
    <div class="mini-sidebar">
        <h3>User Menu</h3>
        <ul>
            <li><a href="#">Profile</a></li>
            <li><a href="#">Settings</a></li>
            <li><a href="#">Logout</a></li>
        </ul>
    </div>
    <div class="overlay" onclick="closeMiniSidebar()"></div>
    <div class="main-content">
        <h1>Dashboard</h1>
        <div class="grid">
            <div class="card">
                <h3>Engagement</h3>
                <canvas id="engagementChart"></canvas>
            </div>
            <div class="card">
                <h3>Likes</h3>
                <canvas id="likesChart"></canvas>
            </div>
            <div class="card">
                <h3>Shares</h3>
                <canvas id="sharesChart"></canvas>
            </div>
            <div class="card">
                <h3>Comments</h3>
                <canvas id="commentsChart"></canvas>
            </div>
        </div>
    </div>
    <script>
        // Engagement Chart
        const engagementCtx = document.getElementById('engagementChart').getContext('2d');
        new Chart(engagementCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Engagement',
                    data: [120, 150, 180, 200, 170, 220],
                    borderColor: '#ffeb3b',
                    backgroundColor: 'rgba(255, 235, 59, 0.2)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true }
                }
            }
        });

        // Likes Chart
        const likesCtx = document.getElementById('likesChart').getContext('2d');
        new Chart(likesCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Likes',
                    data: [80, 100, 120, 140, 110, 160],
                    backgroundColor: '#ffeb3b',
                    borderColor: '#fbc02d',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true }
                }
            }
        });

        // Shares Chart
        const sharesCtx = document.getElementById('sharesChart').getContext('2d');
        new Chart(sharesCtx, {
            type: 'pie',
            data: {
                labels: ['Facebook', 'Twitter', 'Instagram', 'LinkedIn'],
                datasets: [{
                    label: 'Shares',
                    data: [40, 30, 20, 10],
                    backgroundColor: ['#ffeb3b', '#fbc02d', '#fff59d', '#fdd835']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true }
                }
            }
        });

        // Comments Chart
        const commentsCtx = document.getElementById('commentsChart').getContext('2d');
        new Chart(commentsCtx, {
            type: 'doughnut',
            data: {
                labels: ['Positive', 'Neutral', 'Negative'],
                datasets: [{
                    label: 'Comments',
                    data: [60, 30, 10],
                    backgroundColor: ['#ffeb3b', '#fff59d', '#fbc02d']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true }
                }
            }
        });
    </script>
</body>
</html>