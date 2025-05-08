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

<?php include 'menuu.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - SkillSwap</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            position: relative;
        }

        .profile-container {
            max-width: 1000px;
            width: 90%;
            margin: 100px auto 40px;
            padding: 30px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            position: relative;
            z-index: 1;
        }

        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
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
        }

        .stat-item {
            text-align: center;
            min-width: 100px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #ffeb3b;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .stat-label {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
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
    </style>
</head>
<body>

    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-avatar">
                <?php if (!empty($user['profile_picture'])): ?>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($user['profile_picture']); ?>" alt="Profile Picture" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; display: block;">
                <?php else: ?>
                    <i class='bx bxs-user'></i>
                <?php endif; ?>
            </div>
            <div class="profile-info">
                <div class="profile-name"><?php echo htmlspecialchars($user['First_Name'] . ' ' . $user['Last_Name']); ?></div>
                <div class="profile-email"><?php echo htmlspecialchars($user['Email']); ?></div>
                <div class="profile-stats">
                    <div class="stat-item">
                        <div class="stat-value">0</div>
                        <div class="stat-label">Skills Shared</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">0</div>
                        <div class="stat-label">Skills Learned</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">0</div>
                        <div class="stat-label">Connections</div>
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
                <div class="skills-container">
                    <div class="skill-tag">Add your skills</div>
                </div>
            </div>

            <div class="profile-section">
                <div class="section-title">Skills I Want to Learn</div>
                <div class="skills-container">
                    <div class="skill-tag">Add skills you want to learn</div>
                </div>
            </div>

            <div class="profile-section">
                <div class="section-title">Recent Activity</div>
                <div class="activity-list">
                    No recent activity
                </div>
            </div>
        </div>
    </div>

    <script>
        function editProfile() {
            Swal.fire({
                title: 'Edit Profile',
                html: `
                    <div class="input-group">
                        <label>Profile Picture</label>
                        <input type="file" id="profilePicInput" accept="image/*" class="swal2-file">
                    </div>
                    <div class="input-group">
                        <label>Bio</label>
                        <textarea class="swal2-textarea" placeholder="Tell us about yourself"></textarea>
                    </div>
                    <div class="input-group">
                        <label>Skills I Can Share</label>
                        <input type="text" class="swal2-input" placeholder="Add skills (comma separated)">
                    </div>
                    <div class="input-group">
                        <label>Skills I Want to Learn</label>
                        <input type="text" class="swal2-input" placeholder="Add skills (comma separated)">
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
    </script>
</body>
</html>
