<?php
session_start();

require_once 'SkillSwapDatabase.php';
require_once 'SP.php';

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: loginpagee.php");
    exit();
}

// Get user data
$db = new Database();
$conn = $db->getConnection();
$stmt = $conn->prepare("SELECT * FROM users WHERE User_ID = :user_id");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en" data-theme="<?php echo isset($_SESSION['theme']) ? $_SESSION['theme'] : 'light'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - SkillSwap</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <!-- ... -->
    <style>
        :root {
            --bg-color: #ffffff;
            --text-color: #333333;
            --card-bg: #f8f9fa;
            --border-color: #dee2e6;
            --primary-color: #4CAF50;
        }

        [data-theme="dark"] {
            --bg-color: #1a1a1a;
            --text-color: #ffffff;
            --card-bg: #2d2d2d;
            --border-color: #444444;
            --primary-color: #66BB6A;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .profile-container {
            max-width: 1000px;
            width: 90%;
            margin: 100px auto 40px;
            padding: 30px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 30px;
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 2px solid #f0f0f0;
            flex-wrap: wrap;
        }

        .profile-avatar {
            min-width: 150px;
            height: 150px;
            border-radius: 50%;
            background: #ffeb3b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 64px;
            color: #333;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .profile-info {
            flex: 1;
            min-width: 250px;
        }

        .profile-name {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Edit Profile Function
        function editProfile() {
            // Get current user's data
            const currentBio = document.querySelector('.bio-text').textContent;
            const currentSkillsShare = document.querySelector('#skills-can-share').textContent;
            const currentSkillsLearn = document.querySelector('#skills-want-to-learn').textContent;

            Swal.fire({
                title: 'Edit Profile',
                html: `
                    <div class="input-group">
                        <label>Profile Picture</label>
                        <input type="file" id="profilePicInput" accept="image/*" class="swal2-file">
                    </div>
                    <div class="input-group">
                        <label>Bio</label>
                        <textarea class="swal2-textarea" placeholder="Tell us about yourself">${currentBio}</textarea>
                    </div>
                    <div class="input-group">
                        <label>Skills I Can Share</label>
                        <input type="text" class="swal2-input" placeholder="Add skills (comma separated)" value="${currentSkillsShare}">
                    </div>
                    <div class="input-group">
                        <label>Skills I Want to Learn</label>
                        <input type="text" class="swal2-input" placeholder="Add skills (comma separated)" value="${currentSkillsLearn}">
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Save Changes',
                confirmButtonColor: '#ffeb3b',
                cancelButtonText: 'Cancel',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    const fileInput = document.getElementById('profilePicInput');
                    const formData = new FormData();
                    if (fileInput.files.length > 0) {
                        formData.append('profile_picture', fileInput.files[0]);
                    }
                    formData.append('bio', document.querySelector('.swal2-textarea').value);
                    formData.append('skills_share', document.querySelectorAll('.swal2-input')[0].value);
                    formData.append('skills_learn', document.querySelectorAll('.swal2-input')[1].value);

                    return fetch('update_profile.php', {
                        method: 'POST',
                        body: formData
                    }).then(response => {
                        if (!response.ok) {
                            return response.text().then(text => {
                                throw new Error(text || 'Failed to update profile');
                            });
                        }
                        return response.text();
                    }).catch(error => {
                        Swal.showValidationMessage(`Upload failed: ${error.message}`);
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Profile Updated!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        }

        // Skills Modal Functions


        function openSkillsModal(event) {
            event.preventDefault();
            const modal = document.getElementById('skillsModal');
            modal.style.display = 'block';
            
            // Determine which section was clicked
            const target = event.target;
            const container = target.closest('.skills-container');
            
            if (container.id === 'skills-container') {
                currentSkillType = 'want_to_learn';
            } else {
                currentSkillType = 'can_share';
            }
            
            // Update modal title based on skill type
            document.querySelector('.modal-content h2').textContent = 
                currentSkillType === 'can_share' ? 'Select Up to 3 Skills You Can Share' : 'Select Up to 3 Skills You Want to Learn';
        }

        function saveSkills() {
    const selectedSkills = Array.from(document.querySelectorAll('.skills-list input:checked')).map(input => input.value);

    console.log('Selected Skills:', selectedSkills); // Debug selected skills
    console.log('Skill Type:', currentSkillType); // Debug skill type

    fetch('save_skills.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            skills: selectedSkills,
            skill_type: currentSkillType
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Response from backend:', data); // Debug backend response
        if (data.success) {
            Swal.fire({
                title: 'Success!',
                text: 'Skills saved successfully!',
                icon: 'success'
            });
            loadSavedSkills(currentSkillType); // Reload saved skills
        } else {
            alert(data.error || 'An error occurred while saving skills');
        }
    })
    .catch(error => console.error('Error saving skills:', error));

    closeSkillsModal();
}
        function closeSkillsModal() {
            const modal = document.getElementById('skillsModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        function loadSavedSkills(skillType) {
            fetch(`fetch_skills.php?skill_type=${skillType}`)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById(skillType === 'can_share' ? 'skills-can-share' : 'skills-want-to-learn');
                    container.innerHTML = ''; // Clear existing skills

                    if (data.success && data.skills.length > 0) {
                        data.skills.forEach(skill => {
                            const skillTag = document.createElement('div');
                            skillTag.className = 'skill-tag';
                            skillTag.textContent = skill.skill_name;
                            container.appendChild(skillTag);
                        });
                    } else {
                        container.innerHTML = '<p>No skills added yet.</p>';
                    }
                })
                .catch(error => console.error('Error fetching skills:', error));
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadSavedSkills('can_share');
            loadSavedSkills('want_to_learn');
        });

        fetch('fetch_all_skills.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const tableBody = document.getElementById('skillsTableBody');
                    data.skills.forEach(skill => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${skill.user_id}</td>
                            <td>${skill.first_name}</td>
                            <td>${skill.last_name}</td>
                            <td>${skill.skill_name}</td>
                            <td>${skill.skill_type}</td>
                            <td>${skill.created_at}</td>
                        `;
                        tableBody.appendChild(row);
                    });
                } else {
                    alert('Error fetching skills: ' + data.error);
                }
            })
            .catch(error => console.error('Error fetching skills:', error));
    </script>
    <style>
        :root {
            --bg-color: #ffffff;
            --text-color: #333333;
            --card-bg: #f8f9fa;
            --border-color: #dee2e6;
            --primary-color: #4CAF50;
        }

        [data-theme="dark"] {
            --bg-color: #1a1a1a;
            --text-color: #ffffff;
            --card-bg: #2d2d2d;
            --border-color: #444444;
            --primary-color: #66BB6A;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .profile-container {
            max-width: 1000px;
            width: 90%;
            margin: 100px auto 40px;
            padding: 30px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 30px;
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 2px solid #f0f0f0;
            flex-wrap: wrap;
        }

        .profile-avatar {
            min-width: 150px;
            height: 150px;
            border-radius: 50%;
            background: #ffeb3b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 64px;
            color: #333;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .profile-info {
            flex: 1;
            min-width: 250px;
        }

        .profile-name {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }

        .profile-email {
            color: #666;
            margin-bottom: 20px;
            font-size: 16px;
        }

        .profile-stats {
            display: flex;
            gap: 40px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .stat-item {
            text-align: center;
            min-width: 100px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .stat-value {
            font-size: 28px;
            color: #2c3e50;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #7f8c8d;
            font-size: 14px;
        }

        .connections-section {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #f0f0f0;
        }

        .connections-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .connections-count {
            color: #3498db;
            font-weight: bold;
        }

        .connection-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .connection-card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .connection-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #e1f5fe;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }

        .connection-name {
            font-weight: bold;
            color: #2c3e50;
        }

        .connection-skill {
            color: #7f8c8d;
            font-size: 14px;
        }

        .profile-sections {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }

        .profile-section {
            background: #fff;
            padding: 25px;
            border-radius: 15px;
            border: 2px solid #f0f0f0;
            min-height: 200px;
        }

        .section-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: #ffeb3b;
        }

        .edit-btn {
            background-color: #ffeb3b;
            color: #333;
            border: none;
            padding: 12px 25px;
            border-radius: 10px;
            cursor: pointer;
               transition: all 0.3s ease;
            font-size: 16px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 10px rgba(255, 235, 59, 0.3);
        }

        .edit-btn:hover {
            background-color: #ffd600;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(255, 235, 59, 0.4);
        }

        .skill-tag {
            display: inline-block;
            background: #fff8e1;
            color: #333;
            padding: 8px 20px;
            border-radius: 20px;
            margin: 5px;
            font-size: 14px;
            border: 2px solid #ffeb3b;
            transition: all 0.3s ease;
        }

        .skill-tag:hover {
            background: #ffeb3b;
            transform: translateY(-2px);
        }

        .bio-text {
            color: #666;
            line-height: 1.8;
            font-size: 15px;
        }

        .activity-list {
            color: #666;
            font-style: italic;
        }

        @media (max-width: 768px) {
            .profile-container {
                margin: 80px auto 20px;
                padding: 20px;
                width: 95%;
            }

            .profile-header {
                flex-direction: column;
                text-align: center;
                gap: 20px;
            }

            .profile-info {
                width: 100%;
            }

            .profile-stats {
                justify-content: center;
            }

            .edit-btn {
                margin: 0 auto;
            }
        }

        .add-skill-btn {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .add-skill-btn:hover {
            background-color: #45a049;
        }

        .skills-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .skill-tag {
            display: inline-block;
            background: #fff8e1;
            color: #333;
            padding: 8px 20px;
            border-radius: 20px;
            margin: 5px;
            font-size: 14px;
            border: 2px solid #ffeb3b;
            transition: all 0.3s ease;
        }

        .skill-tag:hover {
            background: #ffeb3b;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <?php include 'Menu2.php'; ?>

    <div class="profile-container">
        <?php
        // Get ongoing chats
        $stmt = $conn->prepare("SELECT COUNT(DISTINCT CASE WHEN sender_id = :user_id THEN request_id ELSE sender_id END) as chat_count 
                              FROM messages 
                              WHERE (sender_id = :user_id OR request_id = :user_id)");
        $stmt->execute([':user_id' => $_SESSION['user_id']]);
        $chatCount = $stmt->fetch(PDO::FETCH_ASSOC)['chat_count'];

        // Get connections based on ongoing chats
        $stmt = $conn->prepare("SELECT DISTINCT u.* 
                              FROM users u
                              JOIN messages m ON 
                                  (u.User_ID = m.sender_id AND m.request_id = :user_id) OR 
                                  (u.User_ID = m.request_id AND m.sender_id = :user_id)
                              AND u.User_ID != :user_id");
        $stmt->execute([':user_id' => $_SESSION['user_id']]);
        $connections = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <div class="profile-header">
            <div class="profile-avatar">
                <i class='bx bxs-user'></i>
            </div>
            <div class="profile-info">
                <div class="profile-name"><?php echo htmlspecialchars($user['First_Name'] . ' ' . $user['Last_Name']); ?></div>
                <div class="profile-email"><?php echo htmlspecialchars($user['Email']); ?></div>
                <div class="profile-stats">
                    <div class="stat-item">
                    </div>
                        <!-- <div class="stat-value"><?php echo $user['Skills']; ?></div>
                        <div class="stat-label">Skills</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $user['Experience']; ?></div>
                        <div class="stat-label">Experience</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $user['Education']; ?></div>
                        <div class="stat-label">Education</div> -->
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $chatCount; ?></div>
                        <div class="stat-label">Matches</div>
                    </div>
                </div>
            </div>
            <button class="edit-btn" onclick="editProfile()">
                <i class='bx bxs-edit'></i> Edit Profile
            </button>
        </div>

        <div class="profile-sections">
            <div class="profile-section">
                <div class="section-title">About Me</div>
                <div class="bio-text">
                    No bio added yet. Click edit to add your bio.
                </div>
            </div>

            <div class="profile-section">
                <div class="section-title">Skills I Can Share</div>
                <div class="skills-container" id="skills-can-share">
                    <!-- Skills will be dynamically loaded here -->
                </div>
                <button class="add-skill-btn" onclick="openSkillsModal('can_share')">Add Skills You Can Share</button>
            </div>

            <div class="profile-section">
                <div class="section-title">Skills I Want to Learn</div>
                <div class="skills-container" id="skills-want-to-learn">
                    <!-- Skills will be dynamically loaded here -->
                </div>
                <button class="add-skill-btn" onclick="openSkillsModal('want_to_learn')">Add Skills You Want to Learn</button>
            </div>

            <!-- Skills Modal -->
            <div id="skillsModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeSkillsModal()">&times;</span>
                    <h2>Select Up to 3 Skills</h2>
                    <div class="skills-list">
                        <!-- Skills will be dynamically loaded here -->
                    </div>
                    <div class="modal-buttons">
                        <button onclick="saveSkills()" class="save-btn">Save</button>
                        <button onclick="closeSkillsModal()" class="cancel-btn">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="recent-activity">
            <h2>Recent Activity</h2>
            <div class="activity-list">
                <?php
                // Get user's posts with community information
                $stmt = $conn->prepare("SELECT p.*, c.name as community_name 
                                    FROM posts p 
                                    JOIN communities c ON p.Community_ID = c.Community_ID 
                                    WHERE p.User_ID = :user_id 
                                    ORDER BY p.Created_At DESC 
                                    LIMIT 5");
                $stmt->execute([':user_id' => $_SESSION['user_id']]);
                $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (empty($posts)) {
                    echo '<div class="no-activity">No recent activity yet</div>';
                } else {
                    foreach ($posts as $post) {
                        echo '<div class="activity-item">';
                        echo '<div class="activity-content">' . htmlspecialchars($post['Content']) . '</div>';
                        echo '<div class="activity-meta">';
                        echo '<span class="community">Community: ' . htmlspecialchars($post['community_name']) . '</span>';
                        echo '<span class="timestamp">' . date('M d, Y', strtotime($post['Created_At'])) . '</span>';
                        echo '</div>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <style>
        .recent-activity {
            margin-top: 30px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .recent-activity h2 {
            margin: 0 0 15px 0;
            font-size: 1.2em;
            color: #333;
        }

        .activity-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .activity-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .activity-content {
            font-size: 0.9em;
            color: #555;
        }

        .activity-meta {
            display: flex;
            justify-content: space-between;
            font-size: 0.8em;
            color: #777;
        }

        .community {
            font-weight: 500;
        }

        .timestamp {
            opacity: 0.8;
        }

        .no-activity {
            text-align: center;
            color: #777;
            padding: 20px;
        }

        .activity-list::-webkit-scrollbar {
            width: 8px;
        }

        .activity-list::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .activity-list::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .activity-list::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>

    <style>
        .recent-activity {
            margin-top: 30px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .recent-activity h2 {
            margin: 0 0 15px 0;
            font-size: 1.2em;
            color: #333;
        }

        .activity-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .activity-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .activity-content {
            font-size: 0.9em;
            color: #555;
        }

        .activity-meta {
            display: flex;
            justify-content: space-between;
            font-size: 0.8em;
            color: #777;
        }

        .community {
            font-weight: 500;
        }

        .timestamp {
            opacity: 0.8;
        }

        .no-activity {
            text-align: center;
            color: #777;
            padding: 20px;
        }

        .activity-list::-webkit-scrollbar {
            width: 8px;
        }

        .activity-list::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .activity-list::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .activity-list::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Skills Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 10px;
            position: relative;
            max-height: 80vh;
            overflow-y: auto;
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

        .skill-category {
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .skill-category h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 1.1em;
        }

        .skill-items {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 8px;
        }

        .skill-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .skill-item label {
            cursor: pointer;
            font-size: 0.9em;
        }

        .modal-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .modal-buttons button {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9em;
        }

        .modal-buttons button:first-child {
            background-color: #4CAF50;
            color: white;
        }

        .modal-buttons button:last-child {
            background-color: #f44336;
            color: white;
        }

        .skill-tag {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .selected-skills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
        }

        .selected-skill {
            background-color: #e3f2fd;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.9em;
            color: #1976d2;
            white-space: nowrap;
        }

        .skill-tag:hover {
            opacity: 0.8;
        }
        .activity-list {
                max-height: 400px;
                overflow-y: auto;
            }

            .activity-item {
                padding: 15px;
                border-bottom: 1px solid #eee;
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .activity-content {
                font-size: 0.9em;
                color: #555;
            }

            .activity-meta {
                display: flex;
                justify-content: space-between;
                font-size: 0.8em;
                color: #777;
            }

            .community {
                font-weight: 500;
            }

            .timestamp {
                opacity: 0.8;
            }

            .no-activity {
                text-align: center;
                color: #777;
                padding: 20px;
            }

            .activity-list::-webkit-scrollbar {
                width: 8px;
            }

            .activity-list::-webkit-scrollbar-track {
                background: #f1f1f1;
            }

            .activity-list::-webkit-scrollbar-thumb {
                background: #888;
                border-radius: 4px;
            }

            .activity-list::-webkit-scrollbar-thumb:hover {
                background: #555;
            }

            /* Skills Modal Styles */
            .modal {
                display: none;
                position: fixed;
                z-index: 1000;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0,0,0,0.5);
            }

            .modal-content {
                background-color: #fefefe;
                margin: 10% auto;
                padding: 20px;
                border: 1px solid #888;
                width: 80%;
                max-width: 600px;
                border-radius: 10px;
                position: relative;
                max-height: 80vh;
                overflow-y: auto;
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

            .skill-category {
                margin-bottom: 15px;
                border-bottom: 1px solid #eee;
                padding-bottom: 10px;
            }

            .skill-category h3 {
                margin: 0 0 10px 0;
                color: #333;
                font-size: 1.1em;
            }

            .skill-items {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
                gap: 8px;
            }

            .skill-item {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .skill-item label {
                cursor: pointer;
                font-size: 0.9em;
            }

            .modal-buttons {
                display: flex;
                justify-content: flex-end;
                gap: 10px;
                margin-top: 20px;
            }

            .modal-buttons button {
                padding: 8px 16px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 0.9em;
            }

            .modal-buttons button:first-child {
                background-color: #4CAF50;
                color: white;
            }

            .modal-buttons button:last-child {
                background-color: #f44336;
                color: white;
            }

            .skill-tag {
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .selected-skills {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                margin-top: 8px;
            }

            .selected-skill {
                background-color: #e3f2fd;
                padding: 6px 12px;
                border-radius: 15px;
                font-size: 0.9em;
                color: #1976d2;
                white-space: nowrap;
            }

            .skill-tag:hover {
                opacity: 0.8;
            }
        
    </style>

    <script>


        function openSkillsModal(event) {
            event.preventDefault();
            const modal = document.getElementById('skillsModal');
            modal.style.display = 'block';
            
            // Determine which section was clicked
            const target = event.target;
            const container = target.closest('.skills-container');
            
            if (container.id === 'skills-container') {
                currentSkillType = 'want_to_learn';
            } else {
                currentSkillType = 'can_share';
            }
            
            // Update modal title based on skill type
            document.querySelector('.modal-content h2').textContent = 
                currentSkillType === 'can_share' ? 'Select Up to 3 Skills You Can Share' : 'Select Up to 3 Skills You Want to Learn';
        }

        function saveSkills() {
            const selectedSkills = Array.from(document.querySelectorAll('.skills-list input:checked')).map(input => input.value);

            console.log('Selected Skills:', selectedSkills);
            console.log('Skill Type:', currentSkillType);

            fetch('save_skills.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    skills: selectedSkills,
                    skill_type: currentSkillType
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Response from backend:', data);
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Skills saved successfully!',
                        icon: 'success'
                    });
                    loadSavedSkills(currentSkillType); // Reload saved skills
                } else {
                    alert('You can only select up to 3 skills');
                }
            });
            closeSkillsModal();
        }

        function closeSkillsModal() {
            const modal = document.getElementById('skillsModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        // Edit Profile Function
        function editProfile() {
            // Get current user's data
            const currentBio = document.querySelector('.bio-text').textContent;
            const currentSkillsShare = document.querySelector('#skills-can-share').textContent;
            const currentSkillsLearn = document.querySelector('#skills-want-to-learn').textContent;

            Swal.fire({
                title: 'Edit Profile',
                html: `
                    <div class="input-group">
                        <label>Profile Picture</label>
                        <input type="file" id="profilePicInput" accept="image/*" class="swal2-file">
                    </div>
                    <div class="input-group">
                        <label>Bio</label>
                        <textarea class="swal2-textarea" placeholder="Tell us about yourself">${currentBio}</textarea>
                    </div>
                    <div class="input-group">
                        <label>Skills I Can Share</label>
                        <input type="text" class="swal2-input" placeholder="Add skills (comma separated)" value="${currentSkillsShare}">
                    </div>
                    <div class="input-group">
                        <label>Skills I Want to Learn</label>
                        <input type="text" class="swal2-input" placeholder="Add skills (comma separated)" value="${currentSkillsLearn}">
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Save Changes',
                confirmButtonColor: '#ffeb3b',
                cancelButtonText: 'Cancel',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    const fileInput = document.getElementById('profilePicInput');
                    const formData = new FormData();
                    if (fileInput.files.length > 0) {
                        formData.append('profile_picture', fileInput.files[0]);
                    }
                    formData.append('bio', document.querySelector('.swal2-textarea').value);
                    formData.append('skills_share', document.querySelectorAll('.swal2-input')[0].value);
                    formData.append('skills_learn', document.querySelectorAll('.swal2-input')[1].value);

                    return fetch('update_profile.php', {
                        method: 'POST',
                        body: formData
                    }).then(response => {
                        if (!response.ok) {
                            return response.text().then(text => {
                                throw new Error(text || 'Failed to update profile');
                            });
                        }
                        return response.text();
                    }).catch(error => {
                        Swal.showValidationMessage(`Upload failed: ${error.message}`);
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Profile Updated!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        }

        // function saveProfileChanges(bio, canShareSkills, wantToLearnSkills) {
        //     // Add your save logic here
        //     return new Promise((resolve) => {
        //         setTimeout(() => {
        //             resolve();
        //             Swal.fire({
        //                 icon: 'success',
        //                 title: 'Profile Updated!',
        //                 html: 'Your profile has been updated successfully.',
        //                 showCancelButton: true,
        //                 showConfirmButton: true,
        //                 confirmButtonText: 'OK',
        //                 cancelButtonText: 'Cancel',
        //                 confirmButtonColor: '#ffeb3b',
        //                 cancelButtonColor: '#6c757d',
        //                 allowOutsideClick: true,

        // Edit Profile Function
        function editProfile() {
            console.log('Edit profile button clicked');
            // Get current user's data
            const currentBio = document.querySelector('.bio-text').textContent;

            Swal.fire({
                title: 'Edit Profile',
                html: `
                    <div class="input-group">
                        <label>Profile Picture</label>
                        <input type="file" id="profilePicInput" accept="image/*" class="swal2-file">
                    </div>
                    <div class="input-group">
                        <label>Bio</label>
                        <textarea class="swal2-textarea" placeholder="Tell us about yourself">${currentBio}</textarea>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Save Changes',
                confirmButtonColor: '#ffeb3b',
                cancelButtonText: 'Cancel',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    const fileInput = document.getElementById('profilePicInput');
                    const formData = new FormData();
                    if (fileInput.files.length > 0) {
                        formData.append('profile_picture', fileInput.files[0]);
                    }
                    formData.append('bio', document.querySelector('.swal2-textarea').value);
                    formData.append('skills_share', document.querySelectorAll('.swal2-input')[0].value);
                    formData.append('skills_learn', document.querySelectorAll('.swal2-input')[1].value);
                    formData.append('user_id', '<?php echo $_SESSION["user_id"]; ?>');

                    return fetch('update_profile.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update the bio text on the page
                            document.querySelector('.bio-text').textContent = document.querySelector('.swal2-textarea').value;
                            document.querySelector('#skills-can-share').textContent = document.querySelectorAll('.swal2-input')[0].value;
                            document.querySelector('#skills-want-to-learn').textContent = document.querySelectorAll('.swal2-input')[1].value;
                            
                            // Refresh the profile picture if it was updated
                            if (fileInput.files.length > 0) {
                                const profilePicElement = document.querySelector('.profile-avatar img');
                                if (profilePicElement) {
                                    profilePicElement.src = data.profile_pic_url;
                                }
                            }
                        }
                        return data;
                    })
                    .catch(error => {
                        console.error('Error updating profile:', error);
                        throw new Error('Failed to update profile');
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Profile Updated!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        }

        function saveSkills() {
            fetch('save_skills.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    skills: currentSkills,
                    skill_type: currentSkillType
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Skills saved successfully!',
                        icon: 'success'
                    });
                    loadSavedSkills(currentSkillType); // Reload saved skills
                } else {
            alert(data.error || 'An error occurred while saving skills'); // Display the backend error message
        }
            });
            closeSkillsModal();
        }

        function closeSkillsModal() {
            const modal = document.getElementById('skillsModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        let currentSkillType = ''; // Track the current skill type
        let currentSkills = []; // Track selected skills

        function openSkillsModal(skillType) {
            currentSkillType = skillType; // Set the current skill type
            const modal = document.getElementById('skillsModal');
            modal.style.display = 'block';

            // Update modal title based on skill type
            document.querySelector('.modal-content h2').textContent =
                skillType === 'can_share' ? 'Select Skills You Can Share' : 'Select Skills You Want to Learn';

            // Load predefined skills into the modal
            fetch('fetch_predefined_skills.php')
                .then(response => response.json())
                .then(data => {
                    const skillsList = document.querySelector('.skills-list');
                    skillsList.innerHTML = ''; // Clear existing skills

                    if (data.success && data.skills.length > 0) {
                        data.skills.forEach(skill => {
                            const skillItem = document.createElement('div');
                            skillItem.className = 'skill-item';
                            skillItem.innerHTML = `
                                <input type="checkbox" id="skill_${skill.id}" value="${skill.skill_name}">
                                <label for="skill_${skill.id}">${skill.skill_name}</label>
                            `;
                            skillsList.appendChild(skillItem);
                        });
                    } else {
                        skillsList.innerHTML = '<p>No skills available.</p>';
                    }
                })
                .catch(error => console.error('Error fetching predefined skills:', error));
        }
    </script>
    <style>
        .skills-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
            margin-top: 20px;
        }

        .skill-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .skill-item input {
            cursor: pointer;
        }

        .skill-item label {
            cursor: pointer;
            font-size: 14px;
            color: #333;
        }
    </style>
</body>
</html>
