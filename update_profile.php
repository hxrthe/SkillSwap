<?php
session_start();
require_once 'SkillSwapDatabase.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Not logged in');
}

$db = new Database();
$conn = $db->getConnection();

// Handle profile picture upload
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 2 * 1024 * 1024; // 2MB

    // Check file size
    if ($_FILES['profile_picture']['size'] > $maxSize) {
        http_response_code(400);
        exit('File too large. Maximum size is 2MB.');
    }

    // Check file type using finfo
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $fileType = finfo_file($finfo, $_FILES['profile_picture']['tmp_name']);
    finfo_close($finfo);

    if (!in_array($fileType, $allowedTypes)) {
        http_response_code(400);
        exit('Invalid file type. Allowed types: JPG, PNG, GIF, WEBP');
    }

    try {
        $imageData = file_get_contents($_FILES['profile_picture']['tmp_name']);
        
        $stmt = $conn->prepare("UPDATE users SET profile_picture = :profile_picture WHERE User_ID = :user_id");
        $result = $stmt->execute([
            ':profile_picture' => $imageData,
            ':user_id' => $_SESSION['user_id']
        ]);

        if (!$result) {
            throw new PDOException("Failed to update profile picture");
        }

        // Handle other profile updates
        if (isset($_POST['bio'])) {
            // Handle bio update
            // Add your bio logic here
        }

        if (isset($_POST['skills_share'])) {
            // Handle skills share update
            // Add your skills update logic here
        }

        if (isset($_POST['skills_learn'])) {
            // Handle skills learn update
            // Add your skills update logic here
        }

        echo 'success';
    } catch (Exception $e) {
        error_log("Profile update error: " . $e->getMessage());
        http_response_code(500);
        exit('Failed to update profile: ' . $e->getMessage());
    }
} else {
    http_response_code(400);
    exit('No file uploaded or upload error occurred');
}
