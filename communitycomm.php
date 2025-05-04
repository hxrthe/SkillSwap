<?php include 'menuu.php'; ?>

<?php
$communityName = isset($_GET['community_name']) ? htmlspecialchars($_GET['community_name']) : 'Community';
$communityId = isset($_GET['community_id']) ? intval($_GET['community_id']) : 0;
?>

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

        .add-post-container {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border: 2px solid #007bff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .add-post-container textarea {
            width: 95%;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            font-size: 16px;
            resize: none;
            margin-bottom: 10px;
        }

        .add-post-container button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .add-post-container button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Back Button -->
        <a href="community.php" class="back-button">&lt; Back</a>

        <!-- Community Header -->
        <div class="community-header">
            <h2><?php echo $communityName; ?></h2>
        </div>

        <!-- Search Bar -->
        <div class="search-bar-container">
            <ion-icon name="search-outline"></ion-icon>
            <input type="text" id="search-bar" class="search-bar" placeholder="Search posts" oninput="filterPosts()">
        </div>

        <!-- Add Post Section -->
        <div class="add-post-container">
            <textarea id="post-content" placeholder="What's on your mind?" rows="3"></textarea>
            <button onclick="addPost()">Post</button>
        </div>

        

        <!-- Posts -->
        <div class="post-container">
            <!-- Posts will be dynamically loaded here -->
        </div>
    </div>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script>
        function addPost() {
            const postContent = document.getElementById('post-content').value.trim();
            const communityId = <?php echo $communityId; ?>;

            if (!postContent) {
                alert('Please enter some content for your post.');
                return;
            }

            fetch('add_post.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `content=${encodeURIComponent(postContent)}&community_id=${communityId}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        fetchPosts(); // Reload posts
                        document.getElementById('post-content').value = ''; // Clear input
                    } else {
                        alert('Error: ' + data.error);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function fetchPosts() {
            const communityId = <?php echo $communityId; ?>;

            fetch(`fetch_posts.php?community_id=${communityId}`)
                .then(response => response.json())
                .then(posts => {
                    const postContainer = document.querySelector('.post-container');
                    postContainer.innerHTML = ''; // Clear existing posts

                    posts.forEach(post => {
                        const postElement = document.createElement('div');
                        postElement.className = 'post';

                        postElement.innerHTML = `
                            <div class="post-header">
                                <img src="default-profile.png" alt="User Picture">
                                <div class="user-info">
                                    <h3>${post.user_name}</h3>
                                    <p>${new Date(post.created_at).toLocaleString()}</p>
                                </div>
                            </div>
                            <div class="post-content">
                                ${post.content}
                            </div>
                            <div class="post-actions">
                                <div><ion-icon name="thumbs-up-outline"></ion-icon> Like</div>
                                <div><ion-icon name="chatbubble-outline"></ion-icon> Comment</div>
                                <div><ion-icon name="share-social-outline"></ion-icon> Share</div>
                            </div>
                        `;

                        postContainer.appendChild(postElement);
                    });
                })
                .catch(error => console.error('Error fetching posts:', error));
        }

        function filterPosts() {
            const searchTerm = document.getElementById('search-bar').value.toLowerCase();
            const posts = document.querySelectorAll('.post');

            posts.forEach(post => {
                const content = post.querySelector('.post-content').textContent.toLowerCase();
                const userName = post.querySelector('.user-info h3').textContent.toLowerCase();

                if (content.includes(searchTerm) || userName.includes(searchTerm)) {
                    post.style.display = 'block'; // Show the post
                } else {
                    post.style.display = 'none'; // Hide the post
                }
            });
        }

        // Fetch posts on page load
        document.addEventListener('DOMContentLoaded', fetchPosts);
    </script>
</body>
</html>