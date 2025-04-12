<?php include 'Menu.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap - Search</title>
    <link rel="stylesheet" href="menu.css">
    <style>
        
        .main-container {
            position: relative;
            z-index: 1;
        }
        .circle-decoration {
            position: fixed;
            border-radius: 50%;
            background-color: rgba(255, 255, 0, 0.7);
            z-index: -3;
            pointer-events: none;
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
