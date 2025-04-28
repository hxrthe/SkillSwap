<?php include 'menuu.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Posts</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #fdfd96, #fff);
            box-sizing: border-box;
        }

        .container {
            padding: 20px;
        }

        .back-button {
            font-size: 18px;
            font-weight: bold;
            color: black;
            text-decoration: none;
            margin: 20px;
            display: inline-block;
        }

        .search-bar-container {
            display: flex;
            align-items: center;
            background-color: #fff;
            border-radius: 30px;
            padding: 10px 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            width: 80%;
            margin: 20px auto;
        }

        .search-bar-container ion-icon {
            font-size: 24px;
            color: #666;
            margin-right: 10px;
        }

        .search-bar {
            flex: 1;
            border: none;
            outline: none;
            font-size: 16px;
        }

        .post-container {
            width: 80%;
            margin: 0 auto;
            border: 2px solid #007bff;
            border-radius: 10px;
            padding: 20px;
            background-color: #fff;
        }

        .post {
            border-bottom: 1px solid #ddd;
            padding: 20px 0;
        }

        .post:last-child {
            border-bottom: none;
        }

        .post-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .post-header img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .post-header .user-info {
            display: flex;
            flex-direction: column;
        }

        .post-header .user-info h3 {
            margin: 0;
            font-size: 16px;
        }

        .post-header .user-info p {
            margin: 0;
            font-size: 12px;
            color: #666;
        }

        .post-content {
            font-size: 16px;
            margin-bottom: 10px;
        }

        .post-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            color: #666;
        }

        .post-actions div {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .post-actions div ion-icon {
            margin-right: 5px;
            font-size: 18px;
        }

        .post-actions div:hover {
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Back Button -->
        <a href="community.php" class="back-button">&lt; Back</a>

        <!-- Search Bar -->
        <div class="search-bar-container">
            <ion-icon name="menu-outline"></ion-icon>
            <input type="text" class="search-bar" placeholder="Search community">
            <ion-icon name="search-outline"></ion-icon>
        </div>

        <!-- Posts -->
        <div class="post-container">
            <!-- Post 1 -->
            <div class="post">
                <div class="post-header">
                    <img src="user1.jpg" alt="User Picture">
                    <div class="user-info">
                        <h3>Jane Doe</h3>
                        <p>14 mins ago</p>
                    </div>
                </div>
                <div class="post-content">
                    I like photography.
                </div>
                <div class="post-actions">
                    <div><ion-icon name="thumbs-up-outline"></ion-icon> Like</div>
                    <div><ion-icon name="chatbubble-outline"></ion-icon> Comment</div>
                    <div><ion-icon name="share-social-outline"></ion-icon> Share</div>
                </div>
            </div>

            <!-- Post 2 -->
            <div class="post">
                <div class="post-header">
                    <img src="user2.jpg" alt="User Picture">
                    <div class="user-info">
                        <h3>Bob Smith</h3>
                        <p>14 mins ago</p>
                    </div>
                </div>
                <div class="post-content">
                    Programming is life.
                </div>
                <div class="post-actions">
                    <div><ion-icon name="thumbs-up-outline"></ion-icon> Like</div>
                    <div><ion-icon name="chatbubble-outline"></ion-icon> Comment</div>
                    <div><ion-icon name="share-social-outline"></ion-icon> Share</div>
                </div>
            </div>

            <!-- Post 3 -->
            <div class="post">
                <div class="post-header">
                    <img src="user1.jpg" alt="User Picture">
                    <div class="user-info">
                        <h3>Jane Doe</h3>
                        <p>14 mins ago</p>
                    </div>
                </div>
                <div class="post-content">
                    I like photography.
                </div>
                <div class="post-actions">
                    <div><ion-icon name="thumbs-up-outline"></ion-icon> Like</div>
                    <div><ion-icon name="chatbubble-outline"></ion-icon> Comment</div>
                    <div><ion-icon name="share-social-outline"></ion-icon> Share</div>
                </div>
            </div>
        </div>
    </div>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>
