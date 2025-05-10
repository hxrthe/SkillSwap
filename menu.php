<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKILLSWAP</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');

        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 50px;
            background-color: yellow;
            color: black;
            font-family: 'Poppins', sans-serif;
            border: 2px solid black;
            box-shadow: 0 4px 8px rgba(87, 87, 87, 0.09);
            border-radius: 0 0 30px 30px;
        }
        .header .logo {
            font-size: 30px;
            font-weight: bold;
        }
        .header nav {
            display: flex;
            gap: 50px;
            padding-right: 70px;
        }
        .header nav a {
            color: black;
            text-decoration: none;
            font-size: 20px;
        }
        .header nav a:hover {
            text-decoration: underline;
        }
        .menu-icon {
            display: none;
            font-size: 20px;
            cursor: pointer;
        }
        @media (max-width: 768px) {
            .header nav {
                display: none;
                flex-direction: column;
                background-color: yellow;
                position: absolute;
                top: 50px;
                right: 20px;
                padding: 10px;
                border: 1px solid #444;
            }
            .header nav.active {
                display: flex;
            }
            .menu-icon {
                display: block;
            }
        }

        @media screen and (max-width: 650px) {
            .header {
                padding: 10px 20px;
            }
        }

        .sidebar {
            display: none;
            position: fixed;
            top: 0;
            right: 0;
            width: 300px;
            height: 50%; /* Extend the sidebar to full height */
            background-color: yellow;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            padding: 30px;
            box-sizing: border-box;
            border-radius: 20px 0 0 20px;
            overflow-y: auto;
        }
        .sidebar.active {
            display: block;
        }
        .sidebar-header {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .sidebar-header .user-image {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        .sidebar-header h3 {
            margin: 0;
            font-size: 30px;
            font-weight: 600;
        }
        .sidebar-menu {
            list-style: none;
            padding-top: 40px;
            margin: 0;
        }
        .sidebar-menu li {
            margin-bottom: 15px;
        }
        .sidebar-menu li a {
            text-decoration: none;
            color: #333;
            font-size: 30x;
        }
        .sidebar-menu li a:hover {
            text-decoration: underline;
        }
        ion-icon {
            margin-right: 0;
            font-size: 24px;
            color: black;
        }
        .logout-button {
            background-color:rgb(0, 0, 0);
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 115px;
            margin-left: 75px;
        }
        .logout-button:hover {
            background-color:rgb(82, 82, 82);
        }
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent black */
            z-index: 999; /* Ensure it appears below the sidebar */
        }

        .overlay.active {
            display: block;
        }

        .blurred {
            filter: blur(8px);
        }

        .site-logo {
            width: 40px; /* Adjust the size of the logo */
            height: 40px;
            margin-right: 10px; /* Add spacing between the logo and the text */
            vertical-align: middle; /* Align the logo with the text */
        }
    </style>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>
<body>
    <header class="header">
        <div class="logo">
            <img src="assets/images/sslogo.png" alt="Site Logo" class="site-logo">
            SkillSwap
        </div>
        <nav>
            <a href="menu.php">HOME</a>
            <a href="login.php">LOGIN</a>
            <a href="about.php">ABOUT</a>
            <a href="contact.php">CONTACT</a>
            <a href="javascript:void(0)" onclick="toggleSidebar()">
                <ion-icon name="person-outline"></ion-icon>
            </a>
        </nav>
        <div class="menu-icon" onclick="toggleMenu()">☰</div>
    </header>

    <div id="overlay" class="overlay" onclick="toggleSidebar()"></div>
    <div id="sidebar" class="sidebar">
        <div class="sidebar-header">
            <img src="mochi.png" alt="User Image" class="user-image">
            <h3>MOCHI</h3>
        </div>
        <ul class="sidebar-menu">
            <li>
                <ion-icon name="create-outline"></ion-icon>
                <a href="#edit-profile">Edit Your Profile</a>
            </li>
            <li>
                <ion-icon name="settings-outline"></ion-icon>
                <a href="#settings">Settings</a>
            </li>
        </ul>
        <button class="logout-button" onclick="logout()">Logout</button>
    </div>

    <div class="bg">
        <img src="ssbg5.png" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: -1;">
    </div>

    <script>
        function toggleMenu() {
            const nav = document.querySelector('.header nav');
            nav.classList.toggle('active');
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            const header = document.querySelector('.header'); // Select the header

            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            bg.classList.toggle('blurred');
            header.classList.toggle('blurred');
        }

        function logout() {
            // Add your logout logic here
            alert('Logged out');
        }
    </script>
</body>
</html>