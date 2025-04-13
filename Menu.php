<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="menu.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        :root {
            --bg-color: #ffffff;
            --text-color: #000000;
            --nav-bg: #ffffff;
            --nav-text: #000000;
            --hover-color: #666666;
            --circle-bg: rgba(255, 215, 0, 0.2);
        }

        [data-theme="dark"] {
            --bg-color: #1a1a1a;
            --text-color: #ffffff;
            --nav-bg: #2d2d2d;
            --nav-text: #ffffff;
            --hover-color: #cccccc;
            --circle-bg: rgba(255, 255, 255, 0.1);
            background: linear-gradient(135deg, #2d2d2d 0%, #4a3c00 100%);
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: background-color 0.3s ease, color 0.3s ease, background 0.3s ease;
            min-height: 100vh;
        }

        .nav-links a {
            color: var(--nav-text);
        }

        .nav-links a:hover {
            color: var(--hover-color);
        }

        .circle-decoration {
            background-color: var(--circle-bg);
        }

        .theme-toggle {
            background: none;
            border: none;
            cursor: pointer;
            padding: 10px;
            margin-left: 20px;
            color: var(--nav-text);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .theme-toggle:hover {
            color: var(--hover-color);
        }

        .material-icons {
            font-size: 24px;
        }

        [data-theme="dark"] .circle-decoration {
            background: linear-gradient(135deg, rgba(74, 60, 0, 0.3) 0%, rgba(45, 45, 45, 0.3) 100%);
        }

        [data-theme="dark"] .nav-links a {
            color:rgb(208, 255, 0);
        }

        [data-theme="dark"] .nav-links a:hover {
            color:rgb(208, 255, 0);
        }

        [data-theme="dark"] .site-name {
            color:rgb(208, 255, 0);
        }
    </style>
</head>
<body>
    <header class="menu-header">
        <div class="logo-container">
            <img src="assets/sslogo.png" class="logo" alt="SkillSwap Logo">
            <span class="site-name">SkillSwap</span>
        </div>
        <nav class="navbar">
            <ul class="nav-links">
                <li><a href="Home Page.php">Home</a></li>
                <li><a href="Inbox.php">Inbox</a></li>
                <li><a href="Search.php">Search</a></li>
                <li><a href="community.php">Community</a></li>
            </ul>
        </nav>
        <button class="theme-toggle" id="themeToggle">
            <i class="material-icons">dark_mode</i>
        </button>
        <div class="profile-logo">
            <img src="assets/profi.png" alt="Profile Logo">
        </div>
        <button class="menu-toggle" onclick="toggleMenu()">☰</button>
    </header>

    <div class="side-panel" id="sidePanel">
        <ul>
        <li><a href="Home Page.php">Home</a></li>
                <li><a href="Inbox.php">Inbox</a></li>
                <li><a href="Search.php">Search</a></li>
                <li><a href="community.php">Community</a></li>
            <li><a href="Profile.php">Profile</a></li>
        </ul>
    </div>
    <div class="circle-decoration circle-1"></div>
    <div class="circle-decoration circle-2"></div>
    <div class="circle-decoration circle-3"></div>

    <script>
        function toggleMenu() {
            const sidePanel = document.getElementById("sidePanel");
            sidePanel.classList.toggle("visible");
        }

        const themeToggle = document.getElementById('themeToggle');
        const body = document.body;
        
        // Check for saved theme preference
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            body.setAttribute('data-theme', savedTheme);
        }

        // Toggle theme
        themeToggle.addEventListener('click', () => {
            const currentTheme = body.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            body.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            // Update icon
            const icon = themeToggle.querySelector('.material-icons');
            icon.textContent = newTheme === 'dark' ? 'light_mode' : 'dark_mode';
        });

        // Set initial icon based on theme
        const icon = themeToggle.querySelector('.material-icons');
        icon.textContent = body.getAttribute('data-theme') === 'dark' ? 'light_mode' : 'dark_mode';
    </script>
    <script src="toggle.js"></script>
</body>
</html>
<?php include 'footer.php'; ?>
