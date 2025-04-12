<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="menu.css">
</head>
<body>
    <header class="menu-header">
        <div class="logo-container">
            <img src="sslogo.png" alt="Logo" class="logo">
            <span class="site-name">SkillSwap</span>
        </div>
        <nav class="navbar">
            <ul class="nav-links">
                <li><a href="#home">Home</a></li>
                <li><a href="#inbox">Inbox</a></li>
                <li><a href="Search.php">Search</a></li>
                <li><a href="#community">Community</a></li>
            </ul>
        </nav>
        <div class="profile-logo">
            <img src="profi.png" alt="Profile Logo">
        </div>
        <button class="menu-toggle" onclick="toggleMenu()">☰</button>
    </header>

    <div class="side-panel" id="sidePanel">
        <ul>
            <li><a href="#home">Home</a></li>
            <li><a href="#inbox">Inbox</a></li>
            <li><a href="#search">Search</a></li>
            <li><a href="#community">Community</a></li>
            <li><a href="#profile">Profile</a></li>
        </ul>
    </div>

    <script>
        function toggleMenu() {
            const sidePanel = document.getElementById("sidePanel");
            sidePanel.classList.toggle("visible");
        }
    </script>
    <script src="toggle.js"></script>
</body>
</html>
