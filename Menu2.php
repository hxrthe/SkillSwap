<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKILLSWAP</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<<<<<<< HEAD
=======
        <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Great+Vibes:wght@400;700&display=swap" rel="stylesheet">

>>>>>>> maris
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');

        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 50px;
            background-color: yellow;
            color: black;
            font-family: 'Poppins', sans-serif;
            border: 2px solid black;
            box-shadow: 0 4px 8px rgba(87, 87, 87, 0.09);
            border-radius: 0 0 30px 30px;
            box-sizing: border-box;
            z-index: 1000;
        }
        .header .logo {
            display: flex;
            align-items: center;
            font-size: 30px;
            font-weight: bold;
        }
        .header nav {
            display: flex;
            align-items: center;
            gap: 50px;
            margin-right: 20px;
        }
        .header nav a {
            color: black;
            text-decoration: none;
            font-size: 20px;
            transition: all 0.3s ease;
        }
        .header nav a:hover {
            color: #333;
            transform: translateY(-2px);
        }
        .menu-icon {
            display: none;
            font-size: 24px;
            cursor: pointer;
        }
        @media (max-width: 768px) {
            .header {
                padding: 15px 20px;
            }
            .header nav {
                display: none;
                flex-direction: column;
                background-color: yellow;
                position: absolute;
                top: 70px;
                right: 20px;
                padding: 20px;
                border: 2px solid black;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(87, 87, 87, 0.09);
                margin-right: 0;
            }
            .header nav.active {
                display: flex;
            }
            .header nav a {
                padding: 10px 0;
            }
            .menu-icon {
                display: block;
            }
        }

        @media screen and (max-width: 650px) {
            .header {
                padding: 10px 20px;
            }
        }

        .sidebar {
            display: none;
            position: fixed;
            top: 0;
            right: 0;
            width: 300px;
            height: 50%; /* Extend the sidebar to full height */
            background-color: yellow;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            padding: 30px;
            box-sizing: border-box;
            border-radius: 20px 0 0 20px;
            overflow-y: auto;
        }
        .sidebar.active {
            display: block;
        }
        .sidebar-header {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .sidebar-header .user-image {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        .sidebar-header h3 {
            margin: 0;
            font-size: 30px;
            font-weight: 600;
        }
        .sidebar-menu {
            list-style: none;
            padding-top: 40px;
            margin: 0;
        }
        .sidebar-menu li {
            margin-bottom: 15px;
        }
        .sidebar-menu li a {
            text-decoration: none;
            color: #333;
            font-size: 30x;
        }
        .sidebar-menu li a:hover {
            text-decoration: underline;
        }
        ion-icon {
            margin-right: 0;
            font-size: 24px;
            color: black;
        }
        .logout-button {
            background-color:rgb(0, 0, 0);
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 115px;
            margin-left: 75px;
        }
        .logout-button:hover {
            background-color:rgb(82, 82, 82);
        }
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent black */
            z-index: 999; /* Ensure it appears below the modal but above other content */
        }

        .overlay.active {
            display: block;
        }

        .blurred {
            filter: blur(8px);
        }

        .site-logo {
            width: 40px; /* Adjust the size of the logo */
            height: 40px;
            margin-right: 10px; /* Add spacing between the logo and the text */
            vertical-align: middle; /* Align the logo with the text */
        }

    </style>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>
<body>
    <header class="header">
        <div class="logo">
        <img src="sslogo.png" alt="Site Logo" class="site-logo">
        SkillSwap
        </div>
        <nav>
            <a href="home.php">HOME</a>
            <a href="inbox.php">INBOX</a>
            <a href="search.php">SEARCH</a>
            <a href="community.php">COMMUNITY</a>
            <a href="javascript:void(0)" onclick="toggleSidebar()">
                <ion-icon name="person-outline"></ion-icon>
            </a>
        </nav>
        <div class="menu-icon" onclick="toggleMenu()">☰</div>
    </header>

    <div id="overlay" class="overlay" onclick="closeAllModals()"></div>
    <div id="sidebar" class="sidebar">
        <div class="sidebar-header">
        </div>
        <ul class="sidebar-menu">
            <li>
                <ion-icon name="create-outline"></ion-icon>
                <a href="Profile.php">Profile</a>
            </li>
            <li>
                <ion-icon name="settings-outline"></ion-icon>
                <a href="#settings">Settings</a>
            </li>
        </ul>
        <button class="logout-button" onclick="logout()">Logout</button>
    </div>

    <div class="bg">
        <img src="ssbg4.png" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: -1;">
    </div>

    <!-- Edit Profile Modal
    <div id="editProfileModal" class="modal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 600px; background-color: yellow; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2); z-index: 1001;">
        <h3>Edit Your Profile</h3>
        <form id="editProfileForm">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="MOCHI" style="width: 80%; padding: 10px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 5px;">
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="mochi@example.com" style="width: 80%; padding: 10px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 5px;">
            
            <button type="button" onclick="showPasswordModal()" style="background-color: black; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">Update</button>
        </form>
    </div> -->

    <!-- Password Verification Modal -->
    <div id="passwordModal" class="modal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 300px; background-color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2); z-index: 1001;">
        <h3>Verify Your Password</h3>
        <form id="passwordForm">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" style="width: 80%; padding: 10px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 5px;">
            
            <button type="button" onclick="showSuccessModal()" style="background-color: yellow; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">Done</button>
        </form>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="modal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 300px; background-color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2); z-index: 1001;">
        <h3>Profile Updated</h3>
        <p>Your profile has been successfully updated.</p>
        <button type="button" onclick="closeAllModals()" style="background-color: yellow; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">Close</button>
    </div>

    <script>
        function toggleMenu() {
            const nav = document.querySelector('.header nav');
            nav.classList.toggle('active');
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');

            sidebar.classList.toggle('active'); // Toggle the sidebar visibility
            overlay.classList.toggle('active'); // Toggle the overlay visibility
        }

        // Close the sidebar when clicking outside
        window.addEventListener('click', function (event) {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');

            if (sidebar.classList.contains('active') && !sidebar.contains(event.target) && !event.target.closest('.header nav a')) {
                sidebar.classList.remove('active'); // Hide the sidebar
                overlay.classList.remove('active'); // Hide the overlay
            }
        });

        function logout() {
            // Redirect to login.php
            window.location.href = 'loginpagee.php';
        }

        function toggleEditProfileModal() {
            const editProfileModal = document.getElementById('editProfileModal');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');

            if (editProfileModal.style.display === 'none' || editProfileModal.style.display === '') {
                editProfileModal.style.display = 'block'; // Show the modal
                sidebar.classList.remove('active'); // Hide the sidebar
                overlay.classList.add('active'); // Show the overlay
            } else {
                editProfileModal.style.display = 'none'; // Hide the modal
                overlay.classList.remove('active'); // Hide the overlay
            }
        }

        function showPasswordModal() {
            document.getElementById('editProfileModal').style.display = 'none';
            document.getElementById('passwordModal').style.display = 'block';
        }

        function showSuccessModal() {
            document.getElementById('passwordModal').style.display = 'none';
            document.getElementById('successModal').style.display = 'block';
        }

        function closeAllModals() {
            document.getElementById('editProfileModal').style.display = 'none';
            document.getElementById('passwordModal').style.display = 'none';
            document.getElementById('successModal').style.display = 'none';
            document.getElementById('overlay').classList.remove('active'); // Hide the overlay
        }

        // Close modals when clicking outside
        window.addEventListener('click', function (event) {
            const editProfileModal = document.getElementById('editProfileModal');
            const passwordModal = document.getElementById('passwordModal');
            const successModal = document.getElementById('successModal');

            if (event.target === editProfileModal) {
                editProfileModal.style.display = 'none';
            }
            if (event.target === passwordModal) {
                passwordModal.style.display = 'none';
            }
            if (event.target === successModal) {
                successModal.style.display = 'none';
            }
        });
    </script>
</body>
</html>