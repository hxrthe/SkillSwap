<!-- filepath: c:\xampp2\htdocs\SkillSwap\adminmess.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <!-- Import Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9; /* Light gray background */
            color: #333;
        }

        /* Navbar */
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

        /* Sidebar */
        .sidebar {
            width: 250px;
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

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin: 20px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: #000;
            font-weight: bold;
        }

        /* Mini Sidebar */
        .mini-sidebar {
            width: 200px;
            height: calc(100vh - 70px);
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

        /* Messages Layout */
        .messages-container {
            display: flex;
            margin-top: 70px; /* Adjust for navbar height */
            margin-left: 300px; /* Adjust for left sidebar width */
            height: calc(100vh - 70px);
        }

        .messages-list {
            width: 20%;
            background-color: #fff;
            border-right: 1px solid #ddd;
            overflow-y: auto;
            padding: 20px;
        }

        .messages-list h2 {
            margin-bottom: 20px;
        }

        .messages-list .message-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
        }

        .messages-list .message-item:hover {
            background-color: #f9f9f9;
        }

        .messages-list .message-item .details {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .messages-list .message-item img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .messages-list .message-item .name {
            font-weight: bold;
        }

        .messages-list .message-item .time {
            font-size: 12px;
            color: #888;
        }

        .chat-window {
            flex: 1;
            background-color: #fff;
            display: flex;
            flex-direction: column;
            padding: 20px;
        }

        .chat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .chat-header .details {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chat-header img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            margin-bottom: 20px;
        }

        .chat-messages .message {
            margin-bottom: 15px;
        }

        .chat-messages .message .text {
            padding: 10px;
            border-radius: 10px;
            max-width: 70%;
        }

        .chat-messages .message.sent .text {
            background-color: #e0f7fa;
            align-self: flex-end;
        }

        .chat-messages .message.received .text {
            background-color: #f1f1f1;
        }

        .chat-input {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chat-input input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 20px;
        }

        .chat-input button {
            background-color: #ffeb3b;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">Messages</div>
        <div class="search-bar">
            <input type="text" placeholder="Search...">
        </div>
        <div class="icons">
            <a href="#"><i class="fas fa-envelope"></i></a>
            <a href="#"><i class="fas fa-bell"></i></a>
            <a href="#"><i class="fas fa-cog"></i></a>
        </div>
        <div class="profile" onclick="toggleMiniSidebar()">
            <img src="https://via.placeholder.com/30" alt="Profile">
            <span>John Smith</span>
        </div>
    </div>
    <div class="sidebar">
        <ul>
            <li><i class="fas fa-home"></i><a href="#">Home</a></li>
            <li><i class="fas fa-envelope"></i><a href="#">Messages</a></li>
            <li><i class="fas fa-cog"></i><a href="#">Settings</a></li>
        </ul>
    </div>
    <div class="mini-sidebar">
        <ul>
            <li><a href="#">Profile</a></li>
            <li><a href="#">Settings</a></li>
            <li><a href="#">Logout</a></li>
        </ul>
    </div>
    <div class="messages-container">
        <div class="messages-list">
            <h2>Messages</h2>
            <div class="message-item">
                <div class="details">
                    <img src="https://via.placeholder.com/40" alt="User">
                    <div>
                        <div class="name">Odama Studio</div>
                        <div class="preview">Mas Happy Typing...</div>
                    </div>
                </div>
                <div class="time">05:11 PM</div>
            </div>
            <!-- Add more message items here -->
        </div>
        <div class="chat-window">
            <div class="chat-header">
                <div class="details">
                    <img src="https://via.placeholder.com/40" alt="User">
                    <div>
                        <div class="name">Odama Studio</div>
                        <div class="status">Mas Happy Typing...</div>
                    </div>
                </div>
            </div>
            <div class="chat-messages">
                <div class="message received">
                    <div class="text">Hello! How are you?</div>
                </div>
                <div class="message sent">
                    <div class="text">I'm good, thank you!</div>
                </div>
                <!-- Add more messages here -->
            </div>
            <div class="chat-input">
                <input type="text" placeholder="Type a message...">
                <button>Send</button>
            </div>
        </div>
    </div>
    <script>
        function toggleMiniSidebar() {
            const miniSidebar = document.querySelector('.mini-sidebar');
            miniSidebar.classList.toggle('active');
        }
    </script>
</body>
</html>