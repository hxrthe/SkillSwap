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

        .create-community-container {
            display: flex;
            justify-content: flex-end;
            padding: 20px;
        }

        #create-community-btn {
            background-color: yellow;
            color: black;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
        }

        #create-community-btn:hover {
            background-color:rgb(255, 245, 61);
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.31);
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            width: 50%;
            box-shadow: 0 4px 8px rgb(255, 245, 61);
        }

        .modal-content h2 {
            margin-top: 0;
        }

        .modal-content label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        .modal-content input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .modal-content button {
            background-color: black;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
        }

        .modal-content button:hover {
            background-color:rgb(75, 76, 76);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Create Community Button -->
        <div class="create-community-container">
            <button id="create-community-btn" onclick="openCreateCommunityModal()">Create Community</button>
        </div>

        <!-- Search Bar -->
        <div class="search-bar-container">
            <ion-icon name="menu-outline"></ion-icon>
            <input type="text" class="search-bar" placeholder="Search community">
            <ion-icon name="search-outline"></ion-icon>
        </div>

        <!-- Filter Tags -->
        <!-- <div class="filter-tags">
            <div class="tag">Arts <ion-icon name="close-outline"></ion-icon></div>
            <div class="tag">Computer <ion-icon name="close-outline"></ion-icon></div>
            <div class="tag">Programming <ion-icon name="close-outline"></ion-icon></div>
            <div class="tag">Salon <ion-icon name="close-outline"></ion-icon></div>
            <ion-icon name="filter-outline" style="font-size: 24px; cursor: pointer;"></ion-icon>
        </div> -->

        <!-- Community Grid -->
        <div class="community-grid">
            <!-- Community Cards -->
            <div class="community-card">
                <div class="actions">
                    <button>Report</button>
                    <button>Save</button>
                </div>
                <img src="comm.jpg" alt="Community Image">
                <div class="join">
                    <a href="communitycomm.php?community_id=1&community_name=Community%20Name" style="text-decoration: none;">Join Community →</a>
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
            <!-- Add more community cards as needed -->
        </div>
    </div>

    <!-- Create Community Modal -->
    <div id="create-community-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeCreateCommunityModal()">&times;</span>
            <h2>Create Community</h2>
            <form id="create-community-form">
                <label for="community-name">Community Name:</label>
                <input type="text" id="community-name" name="community-name" required>

                <label for="community-topic">Topic:</label>
                <input type="text" id="community-topic" name="community-topic" required>

                <label for="interest-1">Interest 1:</label>
                <input type="text" id="interest-1" name="interest-1" required>

                <label for="interest-2">Interest 2:</label>
                <input type="text" id="interest-2" name="interest-2" required>

                <label for="interest-3">Interest 3:</label>
                <input type="text" id="interest-3" name="interest-3" required>

                <label for="community-image">Community Picture:</label>
                <input type="file" id="community-image" name="community-image" accept="image/*">

                <button type="button" onclick="createCommunity()">Create</button>
            </form>
        </div>
    </div>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script>
        function openCreateCommunityModal() {
            document.getElementById('create-community-modal').style.display = 'block';
        }

        function closeCreateCommunityModal() {
            document.getElementById('create-community-modal').style.display = 'none';
        }

        function createCommunity() {
            const communityName = document.getElementById('community-name').value.trim();
            const communityTopic = document.getElementById('community-topic').value.trim();
            const interest1 = document.getElementById('interest-1').value.trim();
            const interest2 = document.getElementById('interest-2').value.trim();
            const interest3 = document.getElementById('interest-3').value.trim();
            const communityImage = document.getElementById('community-image').files[0];

            if (!communityName || !communityTopic || !interest1 || !interest2 || !interest3) {
                alert('Please fill out all fields.');
                return;
            }

            const formData = new FormData();
            formData.append('name', communityName);
            formData.append('topic', communityTopic);
            formData.append('interest1', interest1);
            formData.append('interest2', interest2);
            formData.append('interest3', interest3);
            if (communityImage) {
                formData.append('image', communityImage);
            }

            console.log('FormData:', [...formData.entries()]); // Debugging: Log the FormData

            fetch('add_community.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    console.log('Response:', data); // Debugging: Log the response

                    if (data.success) {
                        // Add the new community to the community grid
                        const communityGrid = document.querySelector('.community-grid');
                        const communityCard = document.createElement('div');
                        communityCard.className = 'community-card';

                        const imageUrl = data.image_url || 'comm.jpg'; // Use the uploaded image or a default image

                        communityCard.innerHTML = `
                            <div class="actions">
                                <button>Report</button>
                                <button>Save</button>
                            </div>
                            <img src="${imageUrl}" alt="Community Image">
                            <div class="join">
                                <a href="communitycomm.php?community_id=${data.id}&community_name=${encodeURIComponent(communityName)}" style="text-decoration: none;">Join Community →</a>
                            </div>
                            <div class="info">
                                <h3>${communityName}</h3>
                                <p>${communityTopic}</p>
                                <div class="interests">
                                    <div>${interest1}</div>
                                    <div>${interest2}</div>
                                    <div>${interest3}</div>
                                </div>
                            </div>
                        `;

                        communityGrid.prepend(communityCard);

                        // Close the modal
                        closeCreateCommunityModal();

                        // Clear the form
                        document.getElementById('create-community-form').reset();

                        alert('Community created successfully!');
                    } else {
                        alert('Error: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error); // Debugging: Log any errors
                    alert('An error occurred while creating the community.');
                });
        }

        function fetchCommunities() {
            fetch('fetch_communities.php')
                .then(response => response.json())
                .then(communities => {
                    const communityGrid = document.querySelector('.community-grid');
                    communityGrid.innerHTML = ''; // Clear existing communities

                    communities.forEach(community => {
                        const communityCard = document.createElement('div');
                        communityCard.className = 'community-card';

                        communityCard.innerHTML = `
                            <div class="actions">
                                <button>Report</button>
                                <button>Save</button>
                            </div>
                            <img src="comm.jpg" alt="Community Image">
                            <div class="join">
                                <a href="communitycomm.php?community_id=${community.id}&community_name=${encodeURIComponent(community.name)}" style="text-decoration: none;">Join Community →</a>
                            </div>
                            <div class="info">
                                <h3>${community.name}</h3>
                                <p>${community.topic}</p>
                                <div class="interests">
                                    <div>${community.interest1}</div>
                                    <div>${community.interest2}</div>
                                    <div>${community.interest3}</div>
                                </div>
                            </div>
                        `;

                        communityGrid.appendChild(communityCard);
                    });
                })
                .catch(error => console.error('Error fetching communities:', error));
        }

        document.addEventListener('DOMContentLoaded', fetchCommunities);
    </script>
</body>
</html>