
<?php
session_start();
require_once 'SkillSwapDatabase.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    // Check if bio column exists, if not create it
    $checkBioColumn = $conn->query("SHOW COLUMNS FROM users LIKE 'bio'");
    if ($checkBioColumn->rowCount() === 0) {
        $conn->exec("ALTER TABLE users ADD COLUMN bio TEXT");
    }

    $response = ['success' => true, 'message' => 'Profile updated successfully'];
    $updateFields = [];
    $params = [];

    // Handle profile picture if uploaded
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        error_log("Profile picture upload started");
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        // Check file size
        if ($_FILES['profile_picture']['size'] > $maxSize) {
            throw new Exception('File too large. Maximum size is 2MB.');
        }

        // Check file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if (!$finfo) {
            throw new Exception('Failed to open file info');
        }
        
        $fileType = finfo_file($finfo, $_FILES['profile_picture']['tmp_name']);
        finfo_close($finfo);

        error_log("File type detected: " . $fileType);

        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception('Invalid file type. Allowed types: JPG, PNG, GIF, WEBP');
        }

        // Read the image
        $imageData = file_get_contents($_FILES['profile_picture']['tmp_name']);
        if ($imageData === false) {
            throw new Exception('Failed to read image file');
        }
        
        error_log("Image data read successfully, size: " . strlen($imageData));

        // Update the profile picture in the database
        $stmt = $conn->prepare("UPDATE users SET profile_picture = :profile_picture WHERE User_ID = :user_id");
        if (!$stmt) {
            throw new Exception("Failed to prepare profile picture update statement: " . print_r($conn->errorInfo(), true));
        }

        $result = $stmt->execute([
            ':profile_picture' => $imageData,
            ':user_id' => $_SESSION['user_id']
        ]);

        if (!$result) {
            error_log("Database update failed: " . print_r($stmt->errorInfo(), true));
            throw new Exception("Failed to update profile picture in database");
        }

        error_log("Profile picture updated in database successfully");

        // Add the profile picture URL to the response
        $response['profile_pic_url'] = 'data:' . $fileType . ';base64,' . base64_encode($imageData);
    }

    // Handle bio update
    if (isset($_POST['bio'])) {
        $updateFields[] = 'bio = :bio';
        $params[':bio'] = $_POST['bio'];
    }

    // Handle skills share update
    if (isset($_POST['skills_share'])) {
        $updateFields[] = 'skills_can_share = :skills_share';
        $params[':skills_share'] = $_POST['skills_share'];
    }

    // Handle skills learn update
    if (isset($_POST['skills_learn'])) {
        $updateFields[] = 'skills_want_to_learn = :skills_learn';
        $params[':skills_learn'] = $_POST['skills_learn'];
    }

    // If there are other fields to update
    if (!empty($updateFields)) {
        $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE User_ID = :user_id";
        $params[':user_id'] = $_SESSION['user_id'];

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare profile update statement: " . print_r($conn->errorInfo(), true));
        }

        $result = $stmt->execute($params);

        if (!$result) {
            error_log("Failed to update other profile fields: " . print_r($stmt->errorInfo(), true));
            throw new Exception("Failed to update profile");
        }
    }

    error_log("Profile update completed successfully");
    echo 'success';

} catch (Exception $e) {
    error_log("Profile update error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo 'Failed to update profile: ' . $e->getMessage();
}
