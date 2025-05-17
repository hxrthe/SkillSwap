<?php
require_once 'SkillSwapDatabase.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $communityName = isset($_POST['name']) ? trim($_POST['name']) : '';
    $communityTopic = isset($_POST['topic']) ? trim($_POST['topic']) : '';
    $interest1 = isset($_POST['interest1']) ? trim($_POST['interest1']) : '';
    $interest2 = isset($_POST['interest2']) ? trim($_POST['interest2']) : '';
    $interest3 = isset($_POST['interest3']) ? trim($_POST['interest3']) : '';

    if (!$communityName || !$communityTopic || !$interest1 || !$interest2 || !$interest3) {
        echo json_encode(['error' => 'All fields are required.']);
        exit();
    }

    // Handle file upload
    $imageUrl = 'comm.jpg'; // Default image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $fileName = uniqid() . '-' . basename($_FILES['image']['name']);
        $uploadFile = $uploadDir . $fileName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create the uploads directory if it doesn't exist
        }

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $imageUrl = $uploadFile;
        } else {
            echo json_encode(['error' => 'Failed to upload image.']);
            exit();
        }
    }

    try {
        $db = new Database();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("
            INSERT INTO communities (name, topic, interest1, interest2, interest3, image_url)
            VALUES (:name, :topic, :interest1, :interest2, :interest3, :image_url)
        ");
        $stmt->execute([
            ':name' => $communityName,
            ':topic' => $communityTopic,
            ':interest1' => $interest1,
            ':interest2' => $interest2,
            ':interest3' => $interest3,
            ':image_url' => $imageUrl
        ]);

        echo json_encode(['success' => true, 'id' => $conn->lastInsertId(), 'image_url' => $imageUrl]);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method.']);
}