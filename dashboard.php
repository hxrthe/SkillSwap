<?php include 'menu.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SkillSwap Dashboard</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Luckiest+Guy&display=swap');
        body {
            min-height: 100vh;
            margin: 0;
            background: url('./assets/images/finalbg2.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .skillswap-container {
            text-align: center;
            margin-top: 270px;
        }
        .skillswap-title {
            font-size: 100px;
            font-family: 'Luckiest Guy', cursive, Arial, sans-serif;
            font-weight: bold;
            color: #222;
            letter-spacing: 2px;
            position: relative;
            display: inline-block;
             text-shadow: 2px 4px 16px rgba(0,0,0,0.25), 0 2px 8px rgba(0,0,0,0.18);
        }
        .skillswap-title .swap {
            display: inline-block;
            color: #FFD600;
            position: relative;
            animation: dropIn 3s cubic-bezier(.68,-0.55,.27,1.55) infinite;
             text-shadow: 2px 4px 16px rgba(0,0,0,0.25), 0 2px 8px rgba(0,0,0,0.18);
        }
        @keyframes dropIn {
            0% {
                opacity: 0;
                transform: translateY(-120px) scaleY(2);
            }
            60% {
                opacity: 1;
                transform: translateY(20px) scaleY(1.1);
            }
            80% {
                transform: translateY(-5px) scaleY(0.95);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scaleY(1);
            }
        }
        .skillswap-subtitle {
            margin-top: 24px;
            font-size: 50px;
            color: #fff;
            font-family: 'Luckiest Guy', cursive, Arial, sans-serif;
            text-shadow: 0 2px 8px rgba(0,0,0,0.25);
            letter-spacing: 1px;
             text-shadow: 2px 4px 16px rgba(0,0,0,0.25), 0 2px 8px rgba(0,0,0,0.18);
        }
    </style>
</head>
<body>
    <div class="skillswap-container">
        <span class="skillswap-title">
            SKILL<span class="swap">SWAP</span>
        </span>
        <div class="skillswap-subtitle">
            Share Skills, Build Futures
        </div>
    </div>
</body>
</html>