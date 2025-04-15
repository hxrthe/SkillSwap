<?php
include 'menuu.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            padding: 20px;
        }

        .left-section {
            flex: 2;
            padding-right: 20px;
        }

        .right-section {
            text-align: right;
            padding-right: 100px;
            width: 35%;
        }

        .search-bar-container {
            position: relative;
            width: 65%;
            margin-left: 80px;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .search-bar {
            width: 100%;
            padding: 15px 40px;
            border: 1px solid #ccc;
            font-size: 20px;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .search-bar-container ion-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            font-size: 24px;
            color: #666;
            cursor: pointer;
        }

        .search-bar-container .menu-icon {
            left: 10px;
        }

        .search-bar-container .search-icon {
            right: 10px;
        }

        .matches-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 50px;
            margin-left: 80px;
            margin-bottom: 20px;
        }

        .matches-header h2 {
            margin: 0;
            font-size: 50px;
        }

        .matches-header a {
            text-decoration: none;
            color: rgb(0, 0, 0);
            font-size: 16px;
            margin-right: 250px;
        }

        .card {
            display: flex;
            flex-direction: column;
            width: 60%;
            height: 500px;
            margin-left: 80px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 100px;
        }

        .card-header img {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            margin-top: 20px;
            margin-left: 20px;
        }

        .card-header h3 {
            margin-left: 20px;
            font-size: 30px;
        }

        .card-header p {
            margin-left: 20px;
            font-size: 14px;
            color: #666;
        }

        .card-content {
            margin-bottom: 10px;
        }

        .card-content p {
            margin: 5px 0;
            font-size: 16px;
        }

        .card-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-actions button {
            padding: 10px 20px;
            background-color: rgb(0, 0, 0);
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .card-actions button:hover {
            background-color: rgb(63, 63, 63);
        }

        .card-actions a {
            text-decoration: none;
            font-size: 20px;
            color: rgb(0, 0, 0);
        }

        .right-section h1 {
            font-size: 70px;
            width: 50%;
            margin-top: 80px;
        }

        .right-section p {
            font-size: 20px;
            color: black;
        }

        .right-section .skillswap {
            font-size: 100px;
            width: 50%;
            font-weight: bold;
            color: rgb(0, 0, 0);
        }

        /* Responsive Styles */
        @media screen and (max-width: 1024px) {
            .right-section h1 {
                font-size: 50px;
            }

            .right-section .skillswap {
                font-size: 80px;
            }
        }

        @media screen and (max-width: 768px) {
            .container {
                flex-direction: column;
                padding: 10px;
            }

            .left-section,
            .right-section {
                width: 100%;
                padding: 0;
                text-align: center;
            }

            .search-bar-container {
                width: 90%;
                margin: 10px auto;
            }

            .matches-header {
                flex-direction: column;
                align-items: flex-start;
                margin-left: 0;
                font-size: 30px;
            }

            .matches-header h2 {
                font-size: 30px;
            }

            .matches-header a {
                margin-right: 0;
                font-size: 14px;
            }

            .card {
                width: 90%;
                margin: 10px auto;
                height: auto;
            }

            .card-header img {
                width: 150px;
                height: 150px;
            }

            .card-header h3 {
                font-size: 24px;
            }

            .card-header p {
                font-size: 12px;
            }

            .card-content p {
                font-size: 14px;
            }

            .card-actions button {
                font-size: 14px;
                padding: 8px 16px;
            }
        }

        @media screen and (max-width: 400px) {
            .search-bar-container {
                width: 100%;
                margin: 10px auto;
            }

            .matches-header {
                font-size: 20px;
            }

            .matches-header h2 {
                font-size: 20px;
            }

            .matches-header a {
                font-size: 12px;
            }

            .card-header img {
                width: 120px;
                height: 120px;
            }

            .card-header h3 {
                font-size: 18px;
            }

            .card-header p {
                font-size: 10px;
            }

            .card-content p {
                font-size: 12px;
            }

            .card-actions button {
                font-size: 12px;
                padding: 6px 12px;
            }

            .right-section h1 {
                font-size: 40px;
            }

            .right-section p {
                font-size: 14px;
            }

            .right-section .skillswap {
                font-size: 50px;
            }
        }
    </style>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>
<body>
    <div class="bg">
        <img src="ssbg4.png" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: -1;">
    </div>
    <div class="container">
        <!-- Left Section -->
        <div class="left-section">
            <div class="search-bar-container">
                <ion-icon name="menu-outline" class="menu-icon"></ion-icon>
                <input type="text" class="search-bar" placeholder="Search...">
                <ion-icon name="search-outline" class="search-icon"></ion-icon>
            </div>
            <div class="matches-header">
                <h2>Matches</h2>
                <a href="#">See all matches ></a>
            </div>
            <!-- Card -->
            <div class="card">
                <div class="card-header">
                    <img src="mochi.png" alt="User Picture">
                    <div>
                        <h3>MOCHI</h3>
                        <p>Web Development</p>
                        <p>New York, USA</p>
                    </div>
                </div>
                <div>
                    <a href="#">Negotiate later</a> | <a href="#">Start negotiation</a>
                </div>
                <div class="card-content">
                    <p>Will offer you: Web Design</p>
                    <p>In exchange for: Graphic Design</p>
                </div>
                <div class="card-actions">
                    <button>Contact</button>
                </div>
            </div>
        </div>

        <!-- Right Section -->
        <div class="right-section">
            <h1>Hi USER!</h1>
            <p>Let your skills shine through</p>
            <div class="skillswap">SKILLSWAP</div>
        </div>
    </div>
</body>
</html>