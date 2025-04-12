<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap - Dashboard</title>
    <link rel="stylesheet" href="menu.css">
    <style>
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
            color: #000;
            transition: color 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        .nav-links a:hover {
            color: #666;
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
</body>
</html>
