<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap Admin</title>
    <!-- Import Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 40px;
            background-image: url('assets/images/ssbg5.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: #333;
            position: relative;
        }

        /* Add a semi-transparent overlay */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(90, 88, 88, 0.2); /* White overlay with reduced transparency */
            z-index: -1; /* Place it behind the content */
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
            border-radius: 0 0 20px 20px;
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

        .navbar .search-bar {
            flex: 1;
            margin: 0 20px;
            position: relative;
            display: flex;
            justify-content: center;
        }

        .navbar .search-bar input {
            width: 100%;
            max-width: 400px;
            padding: 8px 40px 8px 12px; /* Add padding for the search icon */
            border: 1px solid #ccc;
            border-radius: 20px;
        }

        .navbar .search-bar i {
            position: absolute;
            right: 330px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
        }

        .navbar .icons {
            display: flex;
            width: 100px;
            width: 100px;
            align-items: center;
            gap: 30px; /* Add gaps between icons */
            margin-right: 100px;
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

        /* Sidebar */
        .sidebar {
            width: 220px;
            height: 100vh;
            background-color: yellow; /* Yellow */
            color: #000;
            position: fixed;
            top: 70px; /* Start below the navbar */
            left: 0;
            padding: 20px;
            box-shadow: 2px 0 6px rgba(0, 0, 0, 0.2); /* Add shadow to the sidebar */
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin: 20px 0; /* Add gaps between list items */
            display: flex;
            align-items: center;
            gap: 15px; /* Add space between icons and text */
            padding: 10px; /* Add padding for better spacing */
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .sidebar ul li:hover {
            background-color: rgba(0, 0, 0, 0.1); /* Add hover effect */
        }

        .sidebar ul li a {
            text-decoration: none;
            color: #000;
            font-weight: bold;
            flex: 1;
        }

        .sidebar ul li i {
            font-size: 18px;
        }

        /* Main Content */
        .main-content {
            margin-left: 240px; /* Adjust for sidebar width */
            margin-top: 70px; /* Adjust for navbar height */
            padding: 20px;
        }

        .main-content h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .main-content .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .main-content .card {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .main-content .card i {
            font-size: 36px;
            color: yellow;
            margin-bottom: 10px;
        }

        .main-content .card h3 {
            margin: 0;
            font-size: 18px;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="logo">
            <img src="assets/images/sslogo.png" alt="SkillSwap Logo">
            SkillSwap
        </div>
        <div class="search-bar">
            <input type="text" placeholder="Search here...">
            <i class="fas fa-search"></i>
        </div>
        <div class="icons">
            <a href="#"><i class="fas fa-envelope"></i></a>
            <a href="#"><i class="fas fa-bell"></i></a>
            <a href="#"><i class="fas fa-cog"></i></a>
        </div>
        <div class="profile">
            <span>ADMIN</span>
            <i class="fas fa-user-circle"></i>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <ul>
            <li><i class="fas fa-home"></i><a href="#" onclick="showOverview()">Home</a></li>
            <li><i class="fas fa-chart-line"></i><a href="#" onclick="showDashboard()">Dashboard</a></li>
            <li><i class="fas fa-envelope"></i><a href="#" onclick="showMessages()">Messages</a></li>
            <li><i class="fas fa-cog"></i><a href="#">Settings</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1>Welcome to SkillSwap Admin</h1>
        <div class="grid">
            <div class="card">
                <i class="fas fa-users"></i>
                <h3>Users</h3>
            </div>
            <div class="card">
                <i class="fas fa-chart-bar"></i>
                <h3>Analytics</h3>
            </div>
            <div class="card">
                <i class="fas fa-envelope"></i>
                <h3>Messages</h3>
            </div>
            <div class="card">
                <i class="fas fa-cog"></i>
                <h3>Settings</h3>
            </div>
        </div>
    </div>

    <!-- Messaging Layout -->
    <div class="messages-container" style="display: none; margin-left: 240px; margin-top: 70px; height: calc(100vh - 70px);">
        <div class="messages-list" style="width: 30%; background-color: #fff; border-right: 1px solid #ddd; overflow-y: auto; padding: 20px;">
            <h2>Messages</h2>
            <div class="message-item" style="display: flex; align-items: center; justify-content: space-between; padding: 10px; border-bottom: 1px solid #f0f0f0; cursor: pointer;">
                <div class="details" style="display: flex; align-items: center; gap: 10px;">
                    <img src="https://via.placeholder.com/40" alt="User" style="width: 40px; height: 40px; border-radius: 50%;">
                    <div>
                        <div class="name" style="font-weight: bold;">Odama Studio</div>
                        <div class="preview" style="color: #888;">Mas Happy Typing...</div>
                    </div>
                </div>
                <div class="time" style="font-size: 12px; color: #888;">05:11 PM</div>
            </div>
            <!-- Add more message items here -->
            <div class="message-item" style="display: flex; align-items: center; justify-content: space-between; padding: 10px; border-bottom: 1px solid #f0f0f0; cursor: pointer;">
                <div class="details" style="display: flex; align-items: center; gap: 10px;">
                    <img src="https://via.placeholder.com/40" alt="User" style="width: 40px; height: 40px; border-radius: 50%;">
                    <div>
                        <div class="name" style="font-weight: bold;">Hatypo Studio</div>
                        <div class="preview" style="color: #888;">Momon: Lahh gas!</div>
                    </div>
                </div>
                <div class="time" style="font-size: 12px; color: #888;">16:01 PM</div>
            </div>
            <div class="message-item" style="display: flex; align-items: center; justify-content: space-between; padding: 10px; border-bottom: 1px solid #f0f0f0; cursor: pointer;">
                <div class="details" style="display: flex; align-items: center; gap: 10px;">
                    <img src="https://via.placeholder.com/40" alt="User" style="width: 40px; height: 40px; border-radius: 50%;">
                    <div>
                        <div class="name" style="font-weight: bold;">Nolaaa</div>
                        <div class="preview" style="color: #888;">Keren banget</div>
                    </div>
                </div>
                <div class="time" style="font-size: 12px; color: #888;">03:29 PM</div>
            </div>
            <div class="message-item" style="display: flex; align-items: center; justify-content: space-between; padding: 10px; border-bottom: 1px solid #f0f0f0; cursor: pointer;">
                <div class="details" style="display: flex; align-items: center; gap: 10px;">
                    <img src="https://via.placeholder.com/40" alt="User" style="width: 40px; height: 40px; border-radius: 50%;">
                    <div>
                        <div class="name" style="font-weight: bold;">Mas Happy</div>
                        <div class="preview" style="color: #888;">Typing...</div>
                    </div>
                </div>
                <div class="time" style="font-size: 12px; color: #888;">02:21 PM</div>
            </div>
            <div class="message-item" style="display: flex; align-items: center; justify-content: space-between; padding: 10px; border-bottom: 1px solid #f0f0f0; cursor: pointer;">
                <div class="details" style="display: flex; align-items: center; gap: 10px;">
                    <img src="https://via.placeholder.com/40" alt="User" style="width: 40px; height: 40px; border-radius: 50%;">
                    <div>
                        <div class="name" style="font-weight: bold;">Mas Rohmad</div>
                        <div class="preview" style="color: #888;">Zaa jo lali ngeshoot yaa</div>
                    </div>
                </div>
                <div class="time" style="font-size: 12px; color: #888;">01:12 PM</div>
            </div>
            <div class="message-item" style="display: flex; align-items: center; justify-content: space-between; padding: 10px; border-bottom: 1px solid #f0f0f0; cursor: pointer;">
                <div class="details" style="display: flex; align-items: center; gap: 10px;">
                    <img src="https://via.placeholder.com/40" alt="User" style="width: 40px; height: 40px; border-radius: 50%;">
                    <div>
                        <div class="name" style="font-weight: bold;">Mas Listian</div>
                        <div class="preview" style="color: #888;">Mantapp za</div>
                    </div>
                </div>
                <div class="time" style="font-size: 12px; color: #888;">12:10 AM</div>
            </div>
            <div class="message-item" style="display: flex; align-items: center; justify-content: space-between; padding: 10px; border-bottom: 1px solid #f0f0f0; cursor: pointer;">
                <div class="details" style="display: flex; align-items: center; gap: 10px;">
                    <img src="https://via.placeholder.com/40" alt="User" style="width: 40px; height: 40px; border-radius: 50%;">
                    <div>
                        <div class="name" style="font-weight: bold;">Rafi Rohmat</div>
                        <div class="preview" style="color: #888;">Voice message</div>
                    </div>
                </div>
                <div class="time" style="font-size: 12px; color: #888;">Yesterday</div>
            </div>
            <div class="message-item" style="display: flex; align-items: center; justify-content: space-between; padding: 10px; border-bottom: 1px solid #f0f0f0; cursor: pointer;">
                <div class="details" style="display: flex; align-items: center; gap: 10px;">
                    <img src="https://via.placeholder.com/40" alt="User" style="width: 40px; height: 40px; border-radius: 50%;">
                    <div>
                        <div class="name" style="font-weight: bold;">Caca</div>
                        <div class="preview" style="color: #888;">Oke suwun</div>
                    </div>
                </div>
                <div class="time" style="font-size: 12px; color: #888;">Yesterday</div>
            </div>
            <div class="message-item" style="display: flex; align-items: center; justify-content: space-between; padding: 10px; border-bottom: 1px solid #f0f0f0; cursor: pointer;">
                <div class="details" style="display: flex; align-items: center; gap: 10px;">
                    <img src="https://via.placeholder.com/40" alt="User" style="width: 40px; height: 40px; border-radius: 50%;">
                    <div>
                        <div class="name" style="font-weight: bold;">Farhan</div>
                        <div class="preview" style="color: #888;">Zaa udah tak update di figma</div>
                    </div>
                </div>
                <div class="time" style="font-size: 12px; color: #888;">Yesterday</div>
            </div>
        </div>
        <div class="chat-window" style="flex: 1; background-color: #fff; display: flex; flex-direction: column; padding: 20px;">
            <div class="chat-header" style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-bottom: 20px;">
                <div class="details" style="display: flex; align-items: center; gap: 10px;">
                    <img src="https://via.placeholder.com/40" alt="User" style="width: 40px; height: 40px; border-radius: 50%;">
                    <div>
                        <div class="name" style="font-weight: bold;">Odama Studio</div>
                        <div class="status" style="color: #888;">Mas Happy Typing...</div>
                    </div>
                </div>
            </div>
            <div class="chat-messages" style="flex: 1; overflow-y: auto; margin-bottom: 20px;">
                <div class="message received" style="margin-bottom: 15px;">
                    <div class="text" style="padding: 10px; border-radius: 10px; max-width: 70%; background-color: #f1f1f1;">Hello! How are you?</div>
                </div>
                <div class="message sent" style="margin-bottom: 15px;">
                    <div class="text" style="padding: 10px; border-radius: 10px; max-width: 70%; background-color: #e0f7fa; align-self: flex-end;">I'm good, thank you!</div>
                </div>
                <!-- Add more messages here -->
            </div>
            <div class="chat-input" style="display: flex; align-items: center; gap: 10px; position: sticky; bottom: 0; background-color: #fff; padding: 10px; border-top: 1px solid #ddd;">
                <input type="text" placeholder="Type a message..." style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 20px;">
                <button style="background-color: #ffeb3b; border: none; padding: 10px 20px; border-radius: 20px; cursor: pointer;">Send</button>
            </div>
        </div>
    </div>

    <!-- Dashboard Layout -->
    <div class="dashboard-container" style="display: none; margin-left: 240px; margin-top: 70px; padding: 20px; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        <div class="dashboard-card" style="background-color: rgb(255, 255, 255); border-radius: 10px; padding: 20px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            <h3 style="margin: 0 0 10px;">Users</h3>
            <canvas id="usersChart" style="height: 150px;"></canvas>
        </div>
        <div class="dashboard-card" style="background-color: rgb(255, 255, 255); border-radius: 10px; padding: 20px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            <h3 style="margin: 0 0 10px;">Engagement</h3>
            <canvas id="engagementChart" style="height: 150px;"></canvas>
        </div>
        <div class="dashboard-card" style="background-color: rgb(255, 255, 255); border-radius: 10px; padding: 20px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            <h3 style="margin: 0 0 10px;">Likes</h3>
            <canvas id="likesChart" style="height: 150px;"></canvas>
        </div>
        <div class="dashboard-card" style="background-color: rgb(255, 255, 255); border-radius: 10px; padding: 20px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            <h3 style="margin: 0 0 10px;">Shares</h3>
            <canvas id="sharesChart" style="height: 150px;"></canvas>
        </div>
        <div class="dashboard-card" style="background-color: rgb(255, 255, 255); border-radius: 10px; padding: 20px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            <h3 style="margin: 0 0 10px;">Posts</h3>
            <canvas id="postsChart" style="height: 150px;"></canvas>
        </div>
    </div>

    <!-- JavaScript Functions -->
    <script>
        function showMessages() {
            document.querySelector('.main-content').style.display = 'none'; // Hide main content
            document.querySelector('.dashboard-container').style.display = 'none'; // Hide dashboard layout
            document.querySelector('.messages-container').style.display = 'flex'; // Show messages layout
        }

        function showOverview() {
            document.querySelector('.main-content').style.display = 'block'; // Show main content
            document.querySelector('.messages-container').style.display = 'none'; // Hide messages layout
            document.querySelector('.dashboard-container').style.display = 'none'; // Hide dashboard layout
        }

        function renderCharts() {
            // Users Chart
            const usersCtx = document.getElementById('usersChart').getContext('2d');
            new Chart(usersCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Users',
                        data: [120, 150, 180, 200, 250, 300],
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    }
                }
            });

            // Engagement Chart
            const engagementCtx = document.getElementById('engagementChart').getContext('2d');
            new Chart(engagementCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Engagement',
                        data: [50, 70, 90, 110, 130, 150],
                        backgroundColor: 'rgba(255, 206, 86, 0.6)',
                        borderColor: 'rgba(255, 206, 86, 1)',
                        borderWidth: 1,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    }
                }
            });

            // Likes Chart
            const likesCtx = document.getElementById('likesChart').getContext('2d');
            new Chart(likesCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Facebook', 'Instagram', 'Twitter'],
                    datasets: [{
                        label: 'Likes',
                        data: [300, 200, 100],
                        backgroundColor: ['rgba(255, 99, 132, 0.6)', 'rgba(54, 162, 235, 0.6)', 'rgba(255, 206, 86, 0.6)']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });

            // Shares Chart
            const sharesCtx = document.getElementById('sharesChart').getContext('2d');
            new Chart(sharesCtx, {
                type: 'polarArea',
                data: {
                    labels: ['Facebook', 'Instagram', 'Twitter'],
                    datasets: [{
                        label: 'Shares',
                        data: [120, 90, 60],
                        backgroundColor: ['rgba(153, 102, 255, 0.6)', 'rgba(255, 159, 64, 0.6)', 'rgba(75, 192, 192, 0.6)']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });

            // Posts Chart
            const postsCtx = document.getElementById('postsChart').getContext('2d');
            new Chart(postsCtx, {
                type: 'radar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Posts',
                        data: [10, 20, 30, 40, 50, 60],
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }

        function showDashboard() {
            document.querySelector('.main-content').style.display = 'none'; // Hide main content
            document.querySelector('.messages-container').style.display = 'none'; // Hide messages layout
            document.querySelector('.dashboard-container').style.display = 'grid'; // Show dashboard layout
            renderCharts(); // Render the charts
        }
    </script>
</body>
</html>