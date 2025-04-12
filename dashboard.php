<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap - Dashboard</title>
    <link rel="stylesheet" href="menu.css">
    <style>
        :root {
            --bg-color: #ffffff;
            --text-color: #000000;
            --nav-bg: #ffffff;
            --nav-text: #000000;
            --hover-color: #666666;
            --welcome-bg: rgba(255, 255, 255, 0.8);
        }

        [data-theme="dark"] {
            --bg-color: #1a1a1a;
            --text-color: #ffffff;
            --nav-bg: #2d2d2d;
            --nav-text: #ffffff;
            --hover-color: #cccccc;
            --welcome-bg: rgba(45, 45, 45, 0.8);
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Additional styles specific to dashboard */
        @import url('https://fonts.googleapis.com/css2?family=Instrument+Serif&display=swap');

        .background-logo {
            left: 20%;
            opacity: 1;
            background-image: url('sslogo.png');
        }

        .background-skillswap {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background-image: url('SkillSwap.png');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            opacity: 0.3;
            z-index: -2;
            pointer-events: none;
        }

        .navbar {
            margin-left: 50px;
        }

        .nav-links {
            display: flex;
            gap: 40px;
        }

        .nav-links a {
            text-transform: uppercase;
            font-size: 18px;
            font-weight: 700;
            text-decoration: none;
            color: var(--nav-text);
            transition: color 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        .nav-links a:hover {
            color: var(--hover-color);
        }

        .welcome-message {
            position: absolute;
            right: 50px;
            top: 300px;
            transform: translateY(-50%);
            max-width: 600px;
            font-family: 'Instrument Serif', serif;
            font-size: 28px;
            letter-spacing: 5px;
            text-align: justify;
            line-height: 1.5;
            z-index: 1;
            background-color: var(--welcome-bg);
            padding: 20px;
            border-radius: 10px;
        }

        .theme-toggle {
            background: none;
            border: none;
            cursor: pointer;
            padding: 10px;
            margin-left: 20px;
        }

        .theme-toggle img {
            width: 24px;
            height: 24px;
            filter: var(--icon-filter);
        }

        [data-theme="dark"] .theme-toggle img {
            filter: invert(1);
        }
    </style>
</head>
<body>
    <div class="site-container">
        <!-- Background Elements -->
        <div class="background-logo"></div>
        <div class="background-skillswap"></div>

        <!-- Header -->
        <header class="menu-header">
            <div class="logo-container">
                <img src="sslogo.png" alt="SkillSwap Logo" class="logo">
                <span class="site-name">SkillSwap</span>
            </div>
            <nav class="navbar">
                <ul class="nav-links">
                    <li><a href="dashboard.php">HOME</a></li>
                    <li><a href="Search.php">LOGIN / REGISTER</a></li>
                    <li><a href="messages.php">ABOUT US</a></li>
                </ul>
            </nav>
            <button class="theme-toggle" id="themeToggle">
                <img src="sun.png" alt="Toggle Theme">
            </button>
            <div class="profile-logo">
                <img src="profi.png" alt="Profile" class="profile-img">
            </div>
        </header>

        <!-- Main Content -->
        <main>
            <div class="welcome-message">
            Have a talent or expertise you're proud of? Maybe you capture stunning photos, ride waves like a pro, or teach languages with ease. And perhaps there's something you've always wanted to pick up—dancing, surfing, or mastering a new language. SkillSwap empowers you to exchange your skills and learn from others, all without spending a dime.
            </div>
        </main>
    </div>

    <script>
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
        });
    </script>
</body>
</html>
