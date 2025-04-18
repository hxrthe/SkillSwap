<?php include 'menuu.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community</title>
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

        .filter-tags {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .filter-tags .tag {
            background-color: #fdfd96;
            border-radius: 20px;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .filter-tags .tag ion-icon {
            font-size: 16px;
            cursor: pointer;
        }

        .community-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .community-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            height: 400px;
            position: relative;
        }

        .community-card img {
            width: 100%;
            height: 300 px;
            border-radius: 10px;
            object-fit: cover;
        }

        .community-card .actions {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .community-card .actions button {
            background-color: #fdfd96;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 14px;
        }

        .community-card .actions button:hover {
            background-color: #fce76c;
        }

        .community-card .info {
            margin-top: 10px;
        }

        .community-card .info h3 {
            margin: 0;
            font-size: 18px;
        }

        .community-card .info p {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }

        .community-card .info .interests {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .community-card .info .interests div {
            background-color: #fdfd96;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
        }

        .community-card .join {
            position: inherit;
            top: 10px;
            margin-left: 220px;
            width: 100%;
            right: 20px;
            font-size: 14px;
            font-weight: bold;
            color: #007bff;
            cursor: pointer;
            margin-top: 1px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Search Bar -->
        <div class="search-bar-container">
            <ion-icon name="menu-outline"></ion-icon>
            <input type="text" class="search-bar" placeholder="Search community">
            <ion-icon name="search-outline"></ion-icon>
        </div>

        <!-- Filter Tags -->
        <div class="filter-tags">
            <div class="tag">Arts <ion-icon name="close-outline"></ion-icon></div>
            <div class="tag">Computer <ion-icon name="close-outline"></ion-icon></div>
            <div class="tag">Programming <ion-icon name="close-outline"></ion-icon></div>
            <div class="tag">Salon <ion-icon name="close-outline"></ion-icon></div>
            <ion-icon name="filter-outline" style="font-size: 24px; cursor: pointer;"></ion-icon>
        </div>

        <!-- Community Grid -->
        <div class="community-grid">
            <!-- Community Card 1 -->
            <div class="community-card">
                <div class="actions">
                    <button>Report</button>
                    <button>Save</button>
                </div>
                <img src="comm.jpg" alt="Community Image">
                <div class="join">
                    <a href="communitycomm.php" style="text-decoration: none;">Join Community →</a>
                </div>
                <div class="info">
                    <h3>Community Name</h3>
                    <p>TOPIC</p>
                    <div class="interests">
                        <div>Interest 1</div>
                        <div>Interest 2</div>
                        <div>Interest 3</div>
                    </div>
                </div>
                </div>
                

                <div class="community-card">
                <div class="actions">
                    <button>Report</button>
                    <button>Save</button>
                </div>
                <img src="comm.jpg" alt="Community Image">
                <div class="join"><a href="communitycomm.php" style="text-decoration: none;">Join Community →</a></div>
                <div class="info">
                    <h3>Community Name</h3>
                    <p>TOPIC</p>
                    <div class="interests">
                        <div>Interest 1</div>
                        <div>Interest 2</div>
                        <div>Interest 3</div>
                    </div>
                </div>
                </div>

                <div class="community-card">
                <div class="actions">
                    <button>Report</button>
                    <button>Save</button>
                </div>
                <img src="comm.jpg" alt="Community Image">
                <div class="join"><a href="communitycomm.php" style="text-decoration: none;">Join Community →</a></div>
                <div class="info">
                    <h3>Community Name</h3>
                    <p>TOPIC</p>
                    <div class="interests">
                        <div>Interest 1</div>
                        <div>Interest 2</div>
                        <div>Interest 3</div>
                    </div>
                </div>
                </div>
                <div class="community-card">
                <div class="actions">
                    <button>Report</button>
                    <button>Save</button>
                </div>
                <img src="comm.jpg" alt="Community Image">
                <div class="join"><a href="communitycomm.php" style="text-decoration: none;">Join Community →</a></div>
                <div class="info">
                    <h3>Community Name</h3>
                    <p>TOPIC</p>
                    <div class="interests">
                        <div>Interest 1</div>
                        <div>Interest 2</div>
                        <div>Interest 3</div>
                    </div>
                </div>
                </div>

                <div class="community-card">
                <div class="actions">
                    <button>Report</button>
                    <button>Save</button>
                </div>
                <img src="comm.jpg" alt="Community Image">
                <div class="join"><a href="communitycomm.php" style="text-decoration: none;">Join Community →</a></div>
                <div class="info">
                    <h3>Community Name</h3>
                    <p>TOPIC</p>
                    <div class="interests">
                        <div>Interest 1</div>
                        <div>Interest 2</div>
                        <div>Interest 3</div>
                    </div>
                </div>
                </div>
                <div class="community-card">
                <div class="actions">
                    <button>Report</button>
                    <button>Save</button>
                </div>
                <img src="comm.jpg" alt="Community Image">
                <div class="join"><a href="communitycomm.php" style="text-decoration: none;">Join Community →</a></div>
                <div class="info">
                    <h3>Community Name</h3>
                    <p>TOPIC</p>
                    <div class="interests">
                        <div>Interest 1</div>
                        <div>Interest 2</div>
                        <div>Interest 3</div>
                    </div>
                </div>
                </div>
            <!-- Add more community cards as needed -->
        </div>
    </div>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>