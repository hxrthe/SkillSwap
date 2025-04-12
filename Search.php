<?php include 'Menu.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap - Search</title>
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
            --card-bg: #ffffff;
            --card-text: #000000;
            --search-bg: transparent;
            --search-text: #000000;
            --toggle-bg: #f0f0f0;
            --toggle-text: #000000;
        }

        [data-theme="dark"] {
            --bg-color: #1a1a1a;
            --text-color: #ffffff;
            --nav-bg: #2d2d2d;
            --nav-text: #ffffff;
            --hover-color: #cccccc;
            --circle-bg: rgba(255, 255, 255, 0.1);
            --card-bg: #2d2d2d;
            --card-text: #ffffff;
            --search-bg: transparent;
            --search-text: #ffffff;
            --toggle-bg: #4a3c00;
            --toggle-text: #ffd700;
            background: linear-gradient(135deg, #2d2d2d 0%, #4a3c00 100%);
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: background-color 0.3s ease, color 0.3s ease, background 0.3s ease;
            min-height: 100vh;
        }
        
        .main-container {
            position: relative;
            z-index: 1;
        }

        .circle-decoration {
            position: fixed;
            border-radius: 50%;
            background-color: var(--circle-bg);
            z-index: -3;
            pointer-events: none;
        }

        [data-theme="dark"] .circle-decoration {
            background: linear-gradient(135deg, rgba(74, 60, 0, 0.3) 0%, rgba(45, 45, 45, 0.3) 100%);
        }

        .circle-1 {
            width: 400px;
            height: 250px;
            top: -20px;
            left: 1000px;
        }

        .circle-2 {
            width: 250px;
            height: 150px;
            top: 262px;
            left: 1130px;
        }

        .circle-3 {
            width: 400px;
            height: 300px;
            top: 450px;
            left: 1050px;
        }

        /* Search bar styles */
        .search-bar-container {
            color: var(--search-text);
        }

        .search input {
            background-color: var(--search-bg);
            color: var(--search-text);
            border: 1px solid var(--text-color);
        }

        /* Toggle styles */
        .toggle-container {
            background-color: var(--toggle-bg);
            color: var(--toggle-text);
        }

        /* Card styles */
        .card {
            background-color: var(--card-bg);
            color: var(--card-text);
            border: 1px solid var(--text-color);
        }

        .card-title, .card-topic {
            color: var(--card-text);
        }

        .offer-button {
            background-color: var(--card-bg);
            color: var(--card-text);
            border: 1px solid var(--text-color);
        }

        [data-theme="dark"] .card-title,
        [data-theme="dark"] .card-topic,
        [data-theme="dark"] .offer-label {
            color: #ffd700;
        }

        [data-theme="dark"] .slide-action {
            color: #ffd700;
        }

        [data-theme="dark"] .card-action-btn {
            color: #ffd700;
        }
    </style>
</head>
<body>
    <div class="site-container">
        <div class="background-logo"></div>
        
        
        <main>
            <div class="main-container">
                <div class="search-bar-container">
                    <div class="search">
                        <input type="text" name="query" placeholder="Search for skills or barters...">
                        <div class="filter-icon">&#9776;</div>
                    </div>
                    <button class="searchbtn">Search</button>
                </div>

                <div class="toggle-container">
                    <div class="toggle-slider" id="slider"></div>
                    <button class="toggle-button" onclick="toggleSwitch('online')">Online Barters</button>
                    <button class="toggle-button" onclick="toggleSwitch('person')">Barters in Person</button>
                </div>
            </div>

            <!-- Card Slider -->
            <div class="card-slider">
                <div class="card-stack">
                    <div class="card card-current">
                        <div class="card-content">
                            <div class="card-actions top-actions">
                                <button class="card-action-btn">
                                    <span>⚠️</span>
                                    Report
                                </button>
                                <button class="card-action-btn">
                                    <span>❤️</span>
                                    Save
                                </button>
                            </div>
                            <div class="card-image">
                                <img src="assets/SkillSwap.png">
                            </div>
                            <div class="card-actions bottom-actions">
                                <a class="slide-action" onclick="slideCard('left')">← Nope</a>
                                <a class="slide-action" onclick="slideCard('right')">Match →</a>
                            </div>
                            
                            <h2 class="card-title">Name</h2>
                            <div class="card-topic">TOPIC</div>
                            
                            <div class="offers-container">
                                <div class="offer-section">
                                    <div class="offer-label">WILL OFFER YOU</div>
                                    <button class="offer-button will-offer"></button>
                                </div>
                                
                                <div class="offer-section">
                                    <div class="offer-label">IN EXCHANGE FOR</div>
                                    <button class="offer-button in-exchange"></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-next">
                        <!-- Next card with same structure -->
                        <div class="card-content">
                            <div class="card-actions top-actions">
                                <button class="card-action-btn">
                                    <span>⚠️</span>
                                    Report
                                </button>
                                <button class="card-action-btn">
                                    <span>❤️</span>
                                    Save
                                </button>
                            </div>
                            <div class="card-image">
                                <img src="SkillSwap.png" alt="Skill Image">
                            </div>
                            <div class="card-actions bottom-actions">
                                <a class="slide-action" onclick="slideCard('left')">← Nope</a>
                                <a class="slide-action" onclick="slideCard('right')">Match →</a>
                            </div>
                            
                            <h2 class="card-title">Name</h2>
                            <div class="card-topic">TOPIC</div>
                            
                            <div class="offers-container">
                                <div class="offer-section">
                                    <div class="offer-label">WILL OFFER YOU</div>
                                    <button class="offer-button will-offer"></button>
                                </div>
                                
                                <div class="offer-section">
                                    <div class="offer-label">IN EXCHANGE FOR</div>
                                    <button class="offer-button in-exchange"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        function slideCard(direction) {
            const currentCard = document.querySelector('.card-current');
            const nextCard = document.querySelector('.card-next');
            
            if (direction === 'left') {
                currentCard.style.transform = 'translateX(-150%) rotate(-10deg)';
            } else {
                currentCard.style.transform = 'translateX(50%) rotate(10deg)';
            }
            
            currentCard.style.opacity = '0';
            
            setTimeout(() => {
                currentCard.remove();
                nextCard.classList.remove('card-next');
                nextCard.classList.add('card-current');
                // Add new next card here if needed
            }, 300);
        }

        function toggleSwitch(option) {
            const slider = document.getElementById("slider");
            if (option === "online") {
                slider.style.left = "0";
            } else if (option === "person") {
                slider.style.left = "50%";
            }
        }
    </script>
</body>
</html>
