<?php
session_start();
require_once 'SkillSwapDatabase.php';

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: loginpagee.php");
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $conn = $db->getConnection();
    
    try {
        // Save all changes at once
        if (isset($_POST['save_all'])) {
            $hasErrors = false;
            
            // Update profile
            if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email'])) {
                $firstName = $_POST['first_name'];
                $lastName = $_POST['last_name'];
                $email = $_POST['email'];
                
                try {
                    $stmt = $conn->prepare("CALL skillswap.UpdateUserProfile(:user_id, :first_name, :last_name, :email)");
                    $stmt->execute([
                        ':user_id' => $_SESSION['user_id'],
                        ':first_name' => $firstName,
                        ':last_name' => $lastName,
                        ':email' => $email
                    ]);
                } catch (Exception $e) {
                    $_SESSION['error_message'] = "Error updating profile: " . $e->getMessage();
                    $hasErrors = true;
                }
            }
            
            // Update password if provided
            if (isset($_POST['current_password']) && isset($_POST['new_password'])) {
                $currentPassword = $_POST['current_password'];
                $newPassword = $_POST['new_password'];
                
                try {
                    $stmt = $conn->prepare("CALL skillswap.UpdateUserPassword(:user_id, :current_password, :new_password)");
                    $stmt->execute([
                        ':user_id' => $_SESSION['user_id'],
                        ':current_password' => $currentPassword,
                        ':new_password' => $newPassword
                    ]);
                } catch (PDOException $e) {
                    if (strpos($e->getMessage(), 'Current password is incorrect') !== false) {
                        $_SESSION['error_message'] = "Current password is incorrect!";
                    } else {
                        $_SESSION['error_message'] = "Error updating password: " . $e->getMessage();
                    }
                    $hasErrors = true;
                }
            }
            
            // Update theme
            if (isset($_POST['theme'])) {
                $theme = $_POST['theme'];
                
                try {
                    $stmt = $conn->prepare("CALL skillswap.UpdateUserTheme(:user_id, :theme)");
                    $stmt->execute([
                        ':user_id' => $_SESSION['user_id'],
                        ':theme' => $theme
                    ]);
                    
                    $_SESSION['theme'] = $theme;
                } catch (Exception $e) {
                    $_SESSION['error_message'] = "Error updating theme: " . $e->getMessage();
                    $hasErrors = true;
                }
            }
            
            if (!$hasErrors) {
                $_SESSION['success_message'] = "All changes saved successfully!";
            }
        }
        
        // Individual updates (keep existing functionality)
        if (isset($_POST['update_profile'])) {
            $firstName = $_POST['first_name'];
            $lastName = $_POST['last_name'];
            $email = $_POST['email'];
            
            // Call stored procedure to update profile
            $stmt = $conn->prepare("CALL UpdateUserProfile(:user_id, :first_name, :last_name, :email)");
            $stmt->execute([
                ':user_id' => $_SESSION['user_id'],
                ':first_name' => $firstName,
                ':last_name' => $lastName,
                ':email' => $email
            ]);
            
            $_SESSION['success_message'] = "Profile updated successfully!";
        }
        
        // Update password
        if (isset($_POST['update_password'])) {
            $currentPassword = $_POST['current_password'];
            $newPassword = $_POST['new_password'];
            $confirmPassword = $_POST['confirm_password'];
            
            // Call stored procedure to update password
            try {
                $stmt = $conn->prepare("CALL UpdateUserPassword(:user_id, :current_password, :new_password)");
                $stmt->execute([
                    ':user_id' => $_SESSION['user_id'],
                    ':current_password' => $currentPassword,
                    ':new_password' => $newPassword
                ]);
                
                $_SESSION['success_message'] = "Password updated successfully!";
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'Current password is incorrect') !== false) {
                    $_SESSION['error_message'] = "Current password is incorrect!";
                } else {
                    $_SESSION['error_message'] = "An error occurred while updating password: " . $e->getMessage();
                }
            }
        }
        
        // Update theme preference
        if (isset($_POST['theme'])) {
            $theme = $_POST['theme'];
            
            // Call stored procedure to update theme
            $stmt = $conn->prepare("CALL UpdateUserTheme(:user_id, :theme)");
            $stmt->execute([
                ':user_id' => $_SESSION['user_id'],
                ':theme' => $theme
            ]);
            
            $_SESSION['theme'] = $theme;
            $_SESSION['success_message'] = "Theme preference updated successfully!";
        }
        
        // Redirect back to settings page
        header("Location: settings.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['error_message'] = "An error occurred: " . $e->getMessage();
        header("Location: settings.php");
        exit();
    }
}

// Get user data
$db = new Database();
$conn = $db->getConnection();
$stmt = $conn->prepare("SELECT * FROM users WHERE User_ID = :user_id");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Set default theme if not set
if (!isset($_SESSION['theme'])) {
    $_SESSION['theme'] = $user['Theme_Preference'] ?? 'light';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - SkillSwap</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(to right, #fdfd96, #fff);
            box-sizing: border-box;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .back-button {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            cursor: pointer;
            color: #666;
            text-decoration: none;
        }

        .back-button:hover {
            color: #333;
        }

        .settings-card {
            background-color: var(--card-bg);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 20px;
            border: 2px solid #ddd;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .settings-section {
            margin-bottom: 30px;
        }

        .settings-section h2 {
            margin-bottom: 15px;
            color: #333;
            font-size: 1.5rem;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #666;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 10px;
            background-color: #fff;
            color: #333;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        select:focus {
            border-color: #4CAF50;
            outline: none;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s ease;
        }

        button:hover {
            background-color: #45a049;
            transform: translateY(-2px);
        }

        .theme-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        select {
            padding: 8px;
            border-radius: 5px;
            background-color: #fff;
            color: #333;
        }

        .success-message, .error-message {
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 16px;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 2px solid #c3e6cb;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 2px solid #f5c6cb;
        }

        @media (max-width: 600px) {
            .container {
                padding: 10px;
            }

            .settings-card {
                padding: 15px;
            }

            .settings-section h2 {
                font-size: 1.2rem;
            }
        }

        /* Theme variables */
        :root {
            --card-bg: #fff;
            --text-color: #333;
            --border-color: #ddd;
            --primary-color: #4CAF50;
        }

        [data-theme="dark"] {
            --card-bg: #1a1a1a;
            --text-color: #fff;
            --border-color: #444;
            --primary-color: #66BB6A;
        }

        /* Smooth theme transition */
        .theme-transition {
            transition: background-color 0.3s ease, color 0.3s ease;
        }
    </style>
</head>
<body data-theme="<?php echo $_SESSION['theme']; ?>">
    <div class="container">
        <div class="back-button" onclick="history.back()">
            <i class='bx bx-arrow-back'></i> Back
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="success-message">
                <?php 
                echo $_SESSION['success_message'];
                unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="error-message">
                <?php 
                echo $_SESSION['error_message'];
                unset($_SESSION['error_message']);
                ?>
            </div>
        <?php endif; ?>

        <!-- Profile Settings -->
        <div class="settings-card">
            <h2>Profile Settings</h2>
            <form action="settings.php" method="POST">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['First_Name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['Last_Name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['Email']); ?>" required>
                </div>
                <input type="hidden" name="update_profile" value="1">
                <button type="submit">Update Profile</button>
            </form>
        </div>

        <!-- Password Settings -->
        <div class="settings-card">
            <h2>Password Settings</h2>
            <form action="settings.php" method="POST">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <input type="hidden" name="update_password" value="1">
                <button type="submit">Change Password</button>
            </form>
        </div>

        <!-- Theme Settings -->
        <div class="settings-card">
            <h2>Theme Settings</h2>
            <form action="settings.php" method="POST">
                <div class="theme-toggle">
                    <label for="theme">Theme Preference:</label>
                    <select id="theme" name="theme" onchange="this.form.submit()">
                        <option value="light" <?php echo $_SESSION['theme'] === 'light' ? 'selected' : ''; ?>>Light</option>
                        <option value="dark" <?php echo $_SESSION['theme'] === 'dark' ? 'selected' : ''; ?>>Dark</option>
                    </select>
                </div>
            </form>
        </div>

        <!-- Save All Changes -->
        <div class="settings-card">
            <h2>Save All Changes</h2>
            <form action="settings.php" method="POST">
                <div class="form-group">
                    <input type="hidden" name="save_all" value="1">
                    <button type="submit" class="save-all-button">Save All Changes</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .save-all-button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .save-all-button:hover {
            background-color: #45a049;
            transform: translateY(-2px);
        }
    </style>

    <script>
        // Add smooth transition for theme changes
        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.add('theme-transition');
            setTimeout(() => {
                document.body.classList.remove('theme-transition');
            }, 1000);
        });
    </script>
</body>
</html>
