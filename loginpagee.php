<?php

session_start(); 
require_once 'SkillSwapDatabase.php';
require_once 'SP.php';
require_once 'EmailVerification.php';

$db = new Database();
$conn = $db->getConnection();

// Check if email exists in database
if (isset($_POST['check_email'])) {
    $email = $_POST['email'];
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE Email = :email");
    $stmt->execute([':email' => $email]);
    $exists = $stmt->fetchColumn() > 0;
    echo json_encode(['exists' => $exists]);
    exit;
}

// Store verification code in session
if (isset($_POST['store_verification'])) {
    $_SESSION['temp_verification_code'] = $_POST['code'];
    $_SESSION['temp_verification_email'] = $_POST['email'];
    echo json_encode(['status' => 'success']);
    exit;
}

$error = false;
$success = false;
$activeTab = isset($_SESSION['activeTab']) ? $_SESSION['activeTab'] : 'login';

// Show success message if registration was successful
if (isset($_SESSION['registerSuccess']) && $_SESSION['registerSuccess'] === true) {
    $success = true;
    unset($_SESSION['registerSuccess']);
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'login') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // First check if it's an admin
    $stmt = $conn->prepare("SELECT * FROM admins WHERE Email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        if (!$admin['Is_Active']) {
            $error = 'deactivated_admin';
            $_SESSION['activeTab'] = 'login';
        } elseif (password_verify($password, $admin['Password'])) {
            // Update last login time
            $updateStmt = $conn->prepare("UPDATE admins SET Last_Login = CURRENT_TIMESTAMP WHERE Admin_ID = :admin_id");
            $updateStmt->execute([':admin_id' => $admin['Admin_ID']]);

            // Set admin session
            $_SESSION['admin_id'] = $admin['Admin_ID'];
            $_SESSION['admin_email'] = $admin['Email'];
            $_SESSION['admin_role'] = $admin['Role'];
            $_SESSION['admin_name'] = $admin['First_Name'] . ' ' . $admin['Last_Name'];

            header("Location: admin.php");
            exit();
        } else {
            $error = 'login';
            $_SESSION['activeTab'] = 'login';
        }
    }

    // If not admin, check if it's a regular user
    $stmt = $conn->prepare("SELECT * FROM users WHERE Email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Check if user is banned
        $banCheck = $conn->prepare("SELECT 1 FROM user_restrictions WHERE User_ID = ? AND Status = 'banned' AND (Restricted_Until IS NULL OR Restricted_Until > NOW())");
        $banCheck->execute([$user['User_ID']]);
        if ($banCheck->fetchColumn()) {
            header("Location: loginpagee.php?banned=1");
            exit();
        }
        if ($user['Is_Verified'] == 0) {
            $error = 'not_verified';
            $_SESSION['activeTab'] = 'login';
        } elseif ($password === $user['Password']) {
            $_SESSION['user_id'] = $user['User_ID']; 
            $_SESSION['user_email'] = $user['Email'];
            header("Location: home.php?submit=success");
            exit(); 
        } else {
            $error = 'login';
            $_SESSION['activeTab'] = 'login';
        }
    } else {
        $error = 'login';
        $_SESSION['activeTab'] = 'login';
    }
}

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'register') {
    $first = $_POST['first_name'];
    $last = $_POST['last_name'];
    $email = $_POST['email1'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $verificationCode = $_POST['verification_code'];

    if ($password !== $confirm) {
        $error = 'confirm';
        $_SESSION['activeTab'] = 'register';
    } else {
        $crud = new Crud();
        try {
            // Get the verification code from session
            if (!isset($_SESSION['temp_verification_code']) || !isset($_SESSION['temp_verification_email']) || 
                $_SESSION['temp_verification_email'] !== $email || $_SESSION['temp_verification_code'] !== $verificationCode) {
                throw new Exception('invalid_verification');
            }

            // Create user with Is_Verified set to 1 since they've already verified their email
            $crud->createUser($first, $last, $email, $password, $verificationCode, true);
            
            // Clear the temporary verification data
            unset($_SESSION['temp_verification_code']);
            unset($_SESSION['temp_verification_email']);
            
            $_SESSION['registerSuccess'] = true;
            $_SESSION['activeTab'] = 'login';
            header("Location: loginpagee.php");
            exit();
        } catch (Exception $e) {
            if ($e->getMessage() === 'email_exists') {
                $error = 'email_exists';
                $_SESSION['activeTab'] = 'register';
            } elseif ($e->getMessage() === 'invalid_verification') {
                $error = 'invalid_verification';
                $_SESSION['activeTab'] = 'register';
            } else {
                throw $e;
            }
        }
    }
}
?>

<?php include 'menu.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .input-box {
            position: relative;
        }

        .get-code-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background-color: #ffeb3b;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .get-code-btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
            opacity: 0.7;
        }

        .get-code-btn:not(:disabled):hover {
            background-color: #ffd600;
            transform: translateY(-50%) scale(1.05);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            width: 500px;
            text-align: center;
            position: relative;
        }

        .modal-close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
            cursor: pointer;
            color: #666;
            background: none;
            border: none;
            padding: 5px;
        }

        .modal-close:hover {
            color: #333;
        }

        .modal-content input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .modal-content .resend-btn {
            background-color: #ffeb3b;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: static;
            transform: none;
            min-width: 100px;
            color: white;
            font-weight: bold;
            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }

        .modal-content .resend-btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
            opacity: 0.7;
            color: #fff;
        }

        .modal-content .resend-btn:not(:disabled):hover {
            background-color: #ffd600;
            transform: scale(1.05);
        }

        .verify-btn {
            background-color: #ffeb3b !important;
            color: white;
            font-weight: bold;
            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
            width: 100%;
            margin-top: 10px;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .verify-btn:hover {
            background-color: #ffd600 !important;
            transform: scale(1.02);
        }

        .input-group {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
            align-items: center;
        }

        .input-group input {
            flex: 1;
            margin: 0;
        }

        header {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }
        body {
            margin-top: 60px;
        }
    </style>
</head>

<body>
<?php if (isset($_GET['banned'])): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
Swal.fire({
    icon: 'error',
    title: 'You are banned',
    text: 'Your account has been banned. You cannot log in at this time.',
    confirmButtonText: 'I understand'
}).then(() => {
    window.location.href = 'loginpagee.php';
});
</script>
<?php endif; ?>
    <div class="container">
        <div class="form-box login">
            <form method="POST" action="">
                <input type="hidden" name="action" value="login">
                <h1>Log into your account</h1>
                <div class="input-box">
                    <input type="email" name="email" placeholder="Email" required>
                    <i class='bx bxs-envelope'></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <div class="forgot-link">
                    <a href="#" onclick="showForgotPasswordModal()">Forgot password?</a>
                </div>
                <button type="submit" class="btn">Sign In</button>
            </form>
        </div>
        
        <div class="form-box register">
            <form method="POST" action="">
                <input type="hidden" name="action" value="register">
                <input type="hidden" id="verification_code" name="verification_code" value="">
                <h1>Create account</h1>
                <div class="input-box">
                    <input type="text" name="first_name" placeholder="First Name" required>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box">
                    <input type="text" name="last_name" placeholder="Last Name" required>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box">
                    <input type="email" id="reg-email" name="email1" placeholder="Email" required>
                    <button type="button" class="get-code-btn" id="sendCodeBtn">Get Code</button>
                </div>
                <div class="input-box">
                    <input type="password" id="password" name="password" placeholder="Password" required disabled>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <div class="input-box">
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter Password" required disabled>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <button type="submit" class="btn">Sign Up</button>
            </form>
        </div>

        <div class="toggle-box">
            <div class="toggle-panel toggle-left">
                <h1>SKILLSWAP</h1>
                <p>Don't have an account yet?</p>
                <button class="btn register-btn">Sign Up</button>
            </div>
            <div class="toggle-panel toggle-right">
                <h1>Welcome Back!</h1>
                <p>Already have an account?</p>
                <button class="btn login-btn">Sign In</button>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal" id="verificationModal">
        <div class="modal-content">
            <h2>Email Verification</h2>
            <p>Enter the code sent to your email:</p>
            <div class="input-group">
                <input type="text" id="modalVerificationCode" placeholder="Enter Code" required>
                <button type="button" id="resendCodeBtn" class="resend-btn">Resend</button>
            </div>
            <button onclick="verifyCode()" class="verify-btn">Verify</button>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
        let cooldownTime = 60;
        let cooldownInterval;
        let currentVerificationCode = '';
        let fpCurrentVerificationCode = '';
        let fpCooldownInterval;

        function showModal() {
            document.getElementById('verificationModal').style.display = 'flex';
            startCooldown();
        }

        function closeModal() {
            document.getElementById('verificationModal').style.display = 'none';
            if (cooldownInterval) {
                clearInterval(cooldownInterval);
            }
        }

        function startCooldown() {
            const resendCodeBtn = document.getElementById("resendCodeBtn");
            
            resendCodeBtn.disabled = true;
            let remainingTime = cooldownTime;
            
            resendCodeBtn.textContent = `Resend (${remainingTime}s)`;
            
            if (cooldownInterval) {
                clearInterval(cooldownInterval);
            }
            
            cooldownInterval = setInterval(() => {
                remainingTime--;
                resendCodeBtn.textContent = `Resend (${remainingTime}s)`;
                
                if (remainingTime <= 0) {
                    clearInterval(cooldownInterval);
                    resendCodeBtn.disabled = false;
                    resendCodeBtn.textContent = 'Resend';
                }
            }, 1000);
        }

        function sendVerificationCode() {
            const email = document.getElementById("reg-email").value;
            
            if (!email) {
                Swal.fire({
                    icon: "error",
                    title: "Missing Email",
                    text: "Please enter your email address first."
                });
                return;
            }

            // Show loading state
            Swal.fire({
                title: 'Sending verification code...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Generate new verification code
            currentVerificationCode = Math.floor(100000 + Math.random() * 900000).toString();

            fetch("Code.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: new URLSearchParams({
                    email: email,
                    subject: "SkillSwap Email Verification Code",
                    message: `Your verification code is: <b>${currentVerificationCode}</b>`,
                    verification_code: currentVerificationCode
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: "success",
                        title: "Verification Code Sent",
                        text: "Please check your email."
                    });
                    startCooldown();
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Failed to Send Email",
                        text: data.message || "Error sending email. Please try again later."
                    });
                }
            })
            .catch(error => {
                console.error("Error details:", error);
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "An error occurred: " + error.message
                });
            });
        }

        // Add event listener for resend code button
        document.getElementById("resendCodeBtn").addEventListener("click", sendVerificationCode);

        // Add event listener for initial Get Code button
        document.getElementById("sendCodeBtn").addEventListener("click", function () {
            // Check if button is disabled (already verified)
            if (this.disabled) {
                return;
            }
            
            const email = document.getElementById("reg-email").value;

            if (!email) {
                Swal.fire({
                    icon: "error",
                    title: "Missing Email",
                    text: "Please enter your email address first."
                });
                return;
            }

            // First check if email exists
            fetch("loginpagee.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: new URLSearchParams({
                    check_email: true,
                    email: email
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    Swal.fire({
                        icon: "error",
                        title: "Email Already Registered",
                        text: "This email is already in use. Please try logging in or use a different email."
                    });
                    return;
                }

                // Generate verification code
                currentVerificationCode = Math.floor(100000 + Math.random() * 900000).toString();

                // Show loading state
                Swal.fire({
                    title: 'Sending verification code...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch("Code.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: new URLSearchParams({
                        email: email,
                        subject: "SkillSwap Email Verification Code",
                        message: `Your verification code is: <b>${currentVerificationCode}</b>`,
                        verification_code: currentVerificationCode
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: "success",
                            title: "Verification Code Sent",
                            text: "Please check your email."
                        }).then(() => {
                            showModal();
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Failed to Send Email",
                            text: data.message || "Error sending email. Please try again later."
                        });
                    }
                })
                .catch(error => {
                    console.error("Error details:", error);
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "An error occurred: " + error.message
                    });
                });
            })
            .catch(error => {
                console.error("Error checking email:", error);
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Failed to check email availability. Please try again."
                });
            });
        });

        function verifyCode() {
            const enteredCode = document.getElementById('modalVerificationCode').value;
            const email = document.getElementById('reg-email').value;
            
            if (enteredCode === currentVerificationCode) {
                // Store verification code and email in session via AJAX
                fetch("loginpagee.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: new URLSearchParams({
                        store_verification: true,
                        email: email,
                        code: currentVerificationCode
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Enable password fields
                        document.getElementById("password").disabled = false;
                        document.getElementById("confirm_password").disabled = false;
                        
                        // Instead of disabling email input, make it readonly
                        document.getElementById("reg-email").readOnly = true;
                        document.getElementById("reg-email").style.backgroundColor = '#f0f0f0';
                        document.getElementById("reg-email").style.cursor = 'not-allowed';
                        
                        // Update Get Code button
                        const getCodeBtn = document.getElementById("sendCodeBtn");
                        getCodeBtn.disabled = true;
                        getCodeBtn.style.backgroundColor = '#ccc';
                        getCodeBtn.style.cursor = 'not-allowed';
                        getCodeBtn.textContent = 'Verified';
                        
                        // Add hidden input for verification code
                        document.getElementById("verification_code").value = currentVerificationCode;
                        
                        closeModal();
                        Swal.fire({
                            icon: "success",
                            title: "Verification Successful",
                            text: "You can now complete your registration."
                        });
                    }
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Invalid Code",
                    text: "Please enter the correct verification code."
                });
            }
        }

        // Error handling
        <?php if ($error === 'login'): ?>
        Swal.fire({
            icon: "error",
            title: "Login Failed",
            text: "Invalid email or password!"
        });
        <?php elseif ($error === 'confirm'): ?>
        Swal.fire({
            icon: "error",
            title: "Password Mismatch",
            text: "Passwords do not match!"
        });
        <?php elseif ($error === 'email_exists'): ?>
        Swal.fire({
            icon: "error",
            title: "Email Already Registered",
            text: "This email is already in use. Please try logging in or use a different email."
        });
        <?php elseif ($error === 'not_verified'): ?>
        Swal.fire({
            icon: "error",
            title: "Email Not Verified",
            text: "Please verify your email before logging in. Check your inbox for the verification code."
        });
        <?php elseif ($error === 'invalid_verification'): ?>
        Swal.fire({
            icon: "error",
            title: "Invalid Verification",
            text: "The verification code is invalid or has expired. Please request a new code."
        });
        <?php elseif ($error === 'deactivated_admin'): ?>
        Swal.fire({
            icon: "error",
            title: "Account Deactivated",
            text: "Your admin account has been deactivated. Please contact the super admin for assistance."
        });
        <?php endif; ?>

        <?php if ($success): ?>
        Swal.fire({
            icon: "success",
            title: "Registration Successful",
            text: "You can now log in with your credentials."
        });
        <?php endif; ?>

        // Forgot Password Functions
        function showForgotPasswordModal() {
            document.getElementById('forgotPasswordModal').style.display = 'flex';
            document.getElementById('fpStep1').style.display = 'block';
            document.getElementById('fpStep2').style.display = 'none';
            document.getElementById('fpStep3').style.display = 'none';
        }

        function closeForgotPasswordModal() {
            const modal = document.getElementById('forgotPasswordModal');
            if (modal) {
                modal.style.display = 'none';
                // Clear any entered data
                document.getElementById('fpEmail').value = '';
                document.getElementById('fpVerificationCode').value = '';
                document.getElementById('fpNewPassword').value = '';
                document.getElementById('fpConfirmPassword').value = '';
                // Reset to step 1
                document.getElementById('fpStep1').style.display = 'block';
                document.getElementById('fpStep2').style.display = 'none';
                document.getElementById('fpStep3').style.display = 'none';
            }
        }

        function sendForgotPasswordCode() {
            const email = document.getElementById('fpEmail').value;
            
            if (!email) {
                Swal.fire({
                    icon: "error",
                    title: "Missing Email",
                    text: "Please enter your email address."
                });
                return;
            }

            // Show loading state
            Swal.fire({
                title: 'Sending verification code...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Generate new verification code
            fpCurrentVerificationCode = Math.floor(100000 + Math.random() * 900000).toString();

            // Send verification code email
            fetch("Code.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: new URLSearchParams({
                    email: email,
                    subject: "SkillSwap Password Reset Code",
                    message: `Your password reset verification code is: <b>${fpCurrentVerificationCode}</b>`,
                    verification_code: fpCurrentVerificationCode
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: "success",
                        title: "Code Sent",
                        text: "Please check your email for the verification code."
                    });
                    document.getElementById('fpStep1').style.display = 'none';
                    document.getElementById('fpStep2').style.display = 'block';
                    startForgotPasswordCooldown();
                } else {
                    throw new Error(data.message || 'Failed to send verification code');
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: error.message
                });
            });
        }

        function verifyForgotPasswordCode() {
            const enteredCode = document.getElementById('fpVerificationCode').value;
            
            if (enteredCode === fpCurrentVerificationCode) {
                document.getElementById('fpStep2').style.display = 'none';
                document.getElementById('fpStep3').style.display = 'block';
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Invalid Code",
                    text: "The verification code is incorrect. Please try again."
                });
            }
        }

        function updatePassword() {
            const email = document.getElementById('fpEmail').value;
            const newPassword = document.getElementById('fpNewPassword').value;
            const confirmPassword = document.getElementById('fpConfirmPassword').value;

            if (newPassword !== confirmPassword) {
                Swal.fire({
                    icon: "error",
                    title: "Password Mismatch",
                    text: "New password and confirmation do not match."
                });
                return;
            }

            // Call the update password endpoint
            fetch("ResetPassword.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: new URLSearchParams({
                    email: email,
                    new_password: newPassword,
                    verification_code: fpCurrentVerificationCode
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: "success",
                        title: "Password Updated",
                        text: "Your password has been successfully updated. You can now login with your new password."
                    }).then(() => {
                        document.getElementById('forgotPasswordModal').style.display = 'none';
                    });
                } else {
                    throw new Error(data.message || 'Failed to update password');
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: error.message
                });
            });
        }

        function startForgotPasswordCooldown() {
            const resendBtn = document.getElementById("fpResendCodeBtn");
            let remainingTime = 60;
            
            resendBtn.disabled = true;
            
            fpCooldownInterval = setInterval(() => {
                remainingTime--;
                resendBtn.textContent = `Resend (${remainingTime}s)`;
                
                if (remainingTime <= 0) {
                    clearInterval(fpCooldownInterval);
                    resendBtn.disabled = false;
                    resendBtn.textContent = 'Resend';
                }
            }, 1000);
        }

        // Add event listener for resend code button
        document.getElementById("fpResendCodeBtn").addEventListener("click", sendForgotPasswordCode);
    </script>

    <!-- Forgot Password Modal -->
    <div class="modal" id="forgotPasswordModal">
        <div class="modal-content">
            <button class="modal-close" onclick="closeForgotPasswordModal()">&times;</button>
            <!-- Step 1: Email Input -->
            <div id="fpStep1">
                <h2>Forgot Password</h2>
                <p>Enter your email address to receive a verification code.</p>
                <div class="input-box">
                    <input type="email" id="fpEmail" placeholder="Enter your email" required>
                    
                </div>
                <button onclick="sendForgotPasswordCode()" class="verify-btn">Send Code</button>
            </div>

            <!-- Step 2: Verification Code -->
            <div id="fpStep2" style="display: none;">
                <h2>Verify Code</h2>
                <p>Enter the verification code sent to your email.</p>
                <div class="input-group">
                    <input type="text" id="fpVerificationCode" placeholder="Enter Code" required>
                    <button type="button" id="fpResendCodeBtn" class="resend-btn">Resend</button>
                </div>
                <button onclick="verifyForgotPasswordCode()" class="verify-btn">Verify Code</button>
            </div>

            <!-- Step 3: New Password -->
            <div id="fpStep3" style="display: none;">
                <h2>Set New Password</h2>
                <p>Enter your new password.</p>
                <div class="input-box">
                    <input type="password" id="fpNewPassword" placeholder="New Password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <div class="input-box">
                    <input type="password" id="fpConfirmPassword" placeholder="Confirm New Password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <button onclick="updatePassword()" class="verify-btn">Update Password</button>
            </div>
        </div>
    </div>
</body>
</html>