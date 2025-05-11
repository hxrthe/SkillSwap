<?php
session_start();

require_once 'SkillSwapDatabase.php';
require_once 'sp.php';

// Create a new database instance
$db = new Database();
$conn = $db->getConnection();
$crud = new Crud();


// Handle AJAX request for community request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    header('Content-Type: application/json');
    $name = $_POST['name'];
    $topic = $_POST['topic'];
    $interest1 = $_POST['interest1'];
    $interest2 = $_POST['interest2'];
    $interest3 = $_POST['interest3'];
    $created_by = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // or set to null/0 if not logged in

    // Handle image upload
    $image_url = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/communities/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $fileName = uniqid() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $fileName;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $image_url = $targetFile;
        }
    }

    try {
        $crud->requestCommunity($name, $topic, $interest1, $interest2, $interest3, $image_url);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

$communities = $conn->query("SELECT * FROM view_approvedcommunities")->fetchAll(PDO::FETCH_ASSOC);
?>

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
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: auto;
        }

        .community-card-image-container {
            position: relative;
            width: 100%;
        }

        .community-card img {
            width: 100%;
            height: auto;
            border-radius: 10px;
            object-fit: cover;
            margin-bottom: 0;
        }

        .community-card .join {
            position: absolute;
            bottom: 10px;
            right: 1px;
            font-size: 14px;
            font-weight: bold;
            color: #7c2ae8;
            cursor: pointer;
            margin-top: 0;
            text-align: right;
            width: auto;
            background: rgba(255,255,255,0.85);
            padding: 4px 10px;
            border-radius: 6px;
            text-decoration: none;
        }

        .community-card .actions {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .community-card .actions button {
            background-color: #fdfd96;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 14px;
        }

        .community-card .info {
            margin-top: 10px;
            flex-grow: 1;
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

        .create-community-container {
            display: flex;
            justify-content: flex-end;
            padding: 20px;
        }

        #create-community-btn {
            background-color: white;
            color: #333;
            border: 2px solid #fdfd96;
            border-radius: 5px;
            padding: 8px 15px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        #create-community-btn:hover {
            background-color: #fdfd96;
            color: #333;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
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
            background-color: rgb(75, 76, 76);
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

        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Create Community Button -->
        <?php if (isset($_SESSION['user_id'])): ?>
        <div class="create-community-container">
            <button id="create-community-btn" onclick="openCreateCommunityModal()">Request Community</button>
        </div>
        <?php endif; ?>

        <!-- Search Bar -->
        <div class="search-bar-container">
            <ion-icon name="menu-outline"></ion-icon>
            <input type="text" class="search-bar" placeholder="Search community">
            <ion-icon name="search-outline"></ion-icon>
        </div>

        <!-- Community Grid -->
        <div class="community-grid">
            <?php if (!empty($communities)): ?>
                <?php foreach ($communities as $community): ?>
                    <div class="community-card">
                        <div class="actions">
                            <button>Report</button>
                            <button>Save</button>
                        </div>
                        <div class="community-card-image-container">
                            <img src="<?php echo htmlspecialchars($community['image_url']); ?>" alt="Community Image">
                            <div class="join">
                                <a href="communitycomm.php?community_id=<?php echo $community['Community_ID']; ?>" style="text-decoration: none;">Join Community →</a>
                            </div>
                        </div>
                        <div class="info">
                            <h3><?php echo htmlspecialchars($community['name']); ?></h3>
                            <p><?php echo htmlspecialchars($community['topic']); ?></p>
                            <div class="interests">
                                <div><?php echo htmlspecialchars($community['interest1']); ?></div>
                                <div><?php echo htmlspecialchars($community['interest2']); ?></div>
                                <div><?php echo htmlspecialchars($community['interest3']); ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No communities found.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Create Community Modal -->
    <div id="create-community-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeCreateCommunityModal()">&times;</span>
            <h2>Request Community</h2>
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

                <button type="button" onclick="requestCommunity()">Request</button>
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

        function requestCommunity() {
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

            fetch('Community(User).php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(text => {
                console.log('Raw response:', text);
                let data = {};
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    alert('Invalid JSON response: ' + text);
                    return;
                }
                if (data.success) {
                    alert('Community request submitted!');
                    location.reload();
                } else {
                    alert('Error: ' + data.error);
                }
            })
        }
    </script>
</body>
</html>