<?php
session_start();
require_once 'SkillSwapDatabase.php';
require_once 'SP.php';

$db = new Database();
$conn = $db->getConnection();

$error = false;
$success = false;
$login_error = false;

// Handle Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username AND Password = :password");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $_SESSION['user_id'] = $user['User_ID'];
            $_SESSION['username'] = $user['username'];
            header("Location: home.php");
            exit();
        } else {
            $login_error = true;
        }
    } catch (PDOException $e) {
        $login_error = true;
    }
}

// Handle Registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = 'confirm';
    } else {
        try {
            $crud = new Crud();
            $crud->createUser2($username, $email, $password);
            $success = true;
        } catch (PDOException $e) {
            if ($e->getCode() == '45000') {
                $error = 'email_exists';
            } else {
                $error = 'database';
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
</head>

<body>
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
        }

        .modal {
            display: none; /* Hidden by default */
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
            width: 300px;
            text-align: center;
        }

        .modal-content input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .modal-content button {
            background-color: #ffeb3b;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
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
    <div class="container">
        <div class="form-box login">
            <form method="POST" action="">
                <input type="hidden" name="action" value="login">
                <h1>Log into your account</h1>
                <div class="input-box">
                    <input type="text" name="username" placeholder="Username" required>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <div class="forgot-link">
                    <a href="#">Forgot password?</a>
                </div>
                <button type="submit" class="btn">Sign In</button>
                <p>or login with social platforms</p>
                <div class="social-icons">
                    <a href="#"><i class='bx bxl-facebook'></i></a>
                    <a href="#"><i class='bx bxl-google'></i></a>
                    <a href="#"><i class='bx bxl-linkedin'></i></a>
                </div>
            </form>
        </div>

        
        <div class="form-box register">
            <form method="POST" action="">
                <input type="hidden" name="action" value="register">
                <h1>Create account</h1>
                <div class="input-box">
                    <input type="text" name="username" placeholder="Username" required>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box">
                    <input type="email" name="email" placeholder="Email" required>
                    <i class='bx bxs-envelope'></i>
                    <button type="button" class="get-code-btn" onclick="showModal()">Get Code</button>
                </div>
                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <div class="input-box">
                    <input type="password" name="confirm_password" placeholder="Re-enter Password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <button type="submit" class="btn">Sign Up</button>
                <p>or register with social platforms</p>
                <div class="social-icons">
                    <a href="#"><i class='bx bxl-facebook'></i></a>
                    <a href="#"><i class='bx bxl-google'></i></a>
                    <a href="#"><i class='bx bxl-linkedin'></i></a>
                </div>
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
            <input type="text" placeholder="Enter Code" required>
            <button onclick="closeModal()">Verify</button>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
        function showModal() {
            document.getElementById('verificationModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('verificationModal').style.display = 'none';
        }
    </script>

    <?php if ($login_error): ?>
    <script>
    Swal.fire({
        icon: "error",
        title: "Login Failed",
        text: "Invalid username or password. Please try again."
    });
    </script>
    <?php endif; ?>

    <?php if ($error === 'confirm'): ?>
    <script>
    Swal.fire({
        icon: "error",
        title: "Password Mismatch",
        text: "Passwords do not match!"
    });
    </script>
    <?php elseif ($error === 'email_exists'): ?>
    <script>
    Swal.fire({
        icon: "error",
        title: "Email Already Exists",
        text: "This email is already registered!"
    });
    </script>
    <?php elseif ($error === 'database'): ?>
    <script>
    Swal.fire({
        icon: "error",
        title: "Registration Failed",
        text: "An error occurred while registering. Please try again."
    });
    </script>
    <?php endif; ?>

    <?php if ($success): ?>
    <script>
    Swal.fire({
        icon: "success",
        title: "Registration Successful",
        text: "You can now log in with your credentials."
    });
    </script>
    <?php endif; ?>

    <script src="script.js"></script>

</body>
</html>