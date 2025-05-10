<?php
session_start();
require_once 'SkillSwapDatabase.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Not logged in');
}

$db = new Database();
$conn = $db->getConnection();

try {
    // Prepare the update query
    $updateFields = [];
    $params = [];

    // Handle profile picture if uploaded
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        // Check file size
        if ($_FILES['profile_picture']['size'] > $maxSize) {
            throw new Exception('File too large. Maximum size is 2MB.');
        }

        // Check file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileType = finfo_file($finfo, $_FILES['profile_picture']['tmp_name']);
        finfo_close($finfo);

        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception('Invalid file type. Allowed types: JPG, PNG, GIF, WEBP');
        }

        $imageData = file_get_contents($_FILES['profile_picture']['tmp_name']);
        $updateFields[] = 'profile_picture = :profile_picture';
        $params[':profile_picture'] = $imageData;
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

    if (empty($updateFields)) {
        throw new Exception('No fields to update');
    }

    // Build the SQL query
    $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE User_ID = :user_id";
    $params[':user_id'] = $_SESSION['user_id'];

    // Execute the update
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute($params);

    if (!$result) {
        throw new PDOException("Failed to update profile");
    }

    echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
