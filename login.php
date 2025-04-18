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
            margin-top: 60px; /* Adjust this value based on the height of your header */
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="form-box login">
            <form action="">
                <h1>Log into your account</h1>
                <div class="input-box">
                    <input type="text" placeholder="Username" required>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box">
                    <input type="password" placeholder="Password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <div class="forgot-link">
                    <a href="#">Forgot password?</a>
                </div>
                <button type="submit" class="btn" formaction="home.php">Sign In</button>
                <p>or login with social platforms</p>
                <div class="social-icons">
                    <a href="#"><i class='bx bxl-facebook'></i></a>
                    <a href="#"><i class='bx bxl-google'></i></a>
                    <a href="#"><i class='bx bxl-linkedin'></i></a>
                </div>
            </form>
        </div>

        
        <div class="form-box register">
            <form action="">
                <h1>Create account</h1>
                <div class="input-box">
                    <input type="text" placeholder="Username" required>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box">
                    <input type="email" placeholder="Email" required>
                    <i class='bx bxs-envelope'></i>
                    <button type="button" class="get-code-btn" onclick="showModal()">Get Code</button>
                </div>
                <div class="input-box">
                    <input type="password" placeholder="Password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <div class="input-box">
                    <input type="password" placeholder="Re-enter Password" required>
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

</body>
</html>