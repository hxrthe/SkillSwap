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
            width: 600px; /* Adjust as needed for your text */
            height: 120px;
            overflow: hidden;
        }
        .skillswap-title .skill,
        .skillswap-title .swap {
            position: absolute;
            top: 0;
            width: 50%;
            transition: none;
        }
        .skillswap-title .skill {
            left: 0;
            color: #222;
            z-index: 2;
            text-align: right;
            animation: skillSwapAnim 3s cubic-bezier(.68,-0.55,.27,1.55) infinite;
        }
        .skillswap-title .swap {
            right: 0;
            color: #FFD600;
            z-index: 2;
            text-align: left;
            animation: swapSwapAnim 3s cubic-bezier(.68,-0.55,.27,1.55) infinite;
        }
        @keyframes skillSwapAnim {
            0%   { transform: translateX(0); opacity: 1; }
            20%  { transform: translateX(0); opacity: 1; }
            40%  { transform: translateX(86%); opacity: 1; }
            60%  { transform: translateX(86%); opacity: 1; }
            80%  { transform: translateX(0); opacity: 1; }
            100% { transform: translateX(0); opacity: 1; }
        }
        @keyframes swapSwapAnim {
            0%   { transform: translateX(0); opacity: 1; }
            20%  { transform: translateX(0); opacity: 1; }
            40%  { transform: translateX(-86%); opacity: 1; }
            60%  { transform: translateX(-86%); opacity: 1; }
            80%  { transform: translateX(0); opacity: 1; }
            100% { transform: translateX(0); opacity: 1; }
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
            <span class="skill">SKILL</span>
            <span class="swap">SWAP</span>
        </span>
        <div class="skillswap-subtitle">
            Share Skills, Build Futures
        </div>
    </div>
</body>
</html>