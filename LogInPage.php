<?php
session_start(); 
require_once 'SkillSwapDatabase.php';
require_once 'SP.php';

$db = new Database();
$conn = $db->getConnection();

$error = false;
$success = false;
$activeTab = isset($_SESSION['activeTab']) ? $_SESSION['activeTab'] : 'login'; // Set default to 'login'
// Retrieve and clear register success flag
if (isset($_SESSION['registerSuccess']) && $_SESSION['registerSuccess'] === true) {
  $success = true;
  unset($_SESSION['registerSuccess']); // Clear it so it doesn't show again on refresh
}

// Handle Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'login') {
  $email = $_POST['email'];
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM users WHERE Email = :email");
  $stmt->bindParam(':email', $email);
  $stmt->execute();
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user && $password === $user['Password']) { // Compare the entered password to the stored plaintext one
      $_SESSION['user_id'] = $user['User_ID']; 
      $_SESSION['user_email'] = $user['Email'];
      header("Location: Interests.php?submit=success");
      exit(); 
  } else {
      $error = 'login';
      $_SESSION['activeTab'] = 'login'; // Set active tab to 'login' if login fails
  }
}


// Handle Registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'register') {
  $first = $_POST['first_name'];
  $last = $_POST['last_name'];
  $email = $_POST['email1'];
  $password = $_POST['password'];
  $confirm = $_POST['confirm_password'];

  if ($password !== $confirm) {
      $error = 'confirm';
      $_SESSION['activeTab'] = 'register'; // Stay on register tab if error
  } else {
      $crud = new Crud();
      $crud->createUser($first, $last, $email, $password);

      $_SESSION['activeTab'] = 'login'; // Go to login tab after successful register
      $_SESSION['registerSuccess'] = true; // Set success flag
      header("Location: " . $_SERVER['PHP_SELF']); // Redirect back to same page
      exit();
  }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>SkillSwap - Login & Registration</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      background: #f6ff00;
      color:rgb(0, 0, 0);
      font-family: 'Segoe UI', sans-serif;
    }

    .form-container {
      background: rgba(255, 255, 255, 0.95);
      color: #000;
      border-radius: 12px;
      padding: 40px;
      box-shadow: 0 0 20px rgba(0,0,0,0.3);
      max-width: 500px;
      width: 100%;
      margin-bottom: 90px;
    }

    .btn-yellow {
      background-color: #f6f65a;
      color: #000;
      font-weight: bold;
      border-radius: 12px;
      transition: 0.2s;
    }

    .btn-yellow:hover {
      background-color: #ffff77;
      transform: scale(1.02);
    }

    .nav-tabs .nav-link.active {
      background-color: #f6f65a;
      color: #000;
      font-weight: bold;
      border-radius: 12px 12px 0 0;
    }

    .nav-tabs .nav-link {
      border: none;
      color: #000;
    }

    a {
      color: #007bff;
      text-decoration: none;
    }

    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body class="d-flex flex-column min-vh-100">

<?php include 'Menu.php'; ?>

  <main class="flex-fill d-flex justify-content-center align-items-center">
    <div class="form-container text-center">
      <ul class="nav nav-tabs justify-content-center mb-4" id="authTabs" role="tablist">
        <li class="nav-item" role="presentation">
        <button class="nav-link <?php echo $activeTab === 'login' ? 'active' : ''; ?>" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab">Login</button>
        </li>
        <li class="nav-item" role="presentation">
        <button class="nav-link <?php echo $activeTab === 'register' ? 'active' : ''; ?>" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab">Register</button>
        </li>
      </ul>

      <div class="tab-content" id="authTabsContent">
        <!-- Login Tab -->
        <div class="tab-pane fade <?php echo $activeTab === 'login' ? 'show active' : ''; ?>" id="login" role="tabpanel">
        <form method="POST" action="">
            <input type="hidden" name="action" value="login">
            <div class="mb-3">
            <input type="email" class="form-control" name="email" placeholder="Enter your Email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" required>
            </div>
            <div class="mb-3">
              <input type="password" class="form-control" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn btn-yellow w-100">Sign In</button>
          </form>
        </div>

        <!-- Register Tab -->
        <div class="tab-pane fade <?php echo $activeTab === 'register' ? 'show active' : ''; ?>" id="register" role="tabpanel">
        <form method="POST" action="">
            <input type="hidden" name="action" value="register">
            <div class="row g-3">
              <div class="col-md-6">
                <input type="text" class="form-control" name="first_name" placeholder="First name" value="<?php echo isset($_POST['first_name']) ? $_POST['first_name'] : ''; ?>" required>
              </div>
              <div class="col-md-6">
                <input type="text" class="form-control" name="last_name" placeholder="Last name" value="<?php echo isset($_POST['last_name']) ? $_POST['last_name'] : ''; ?>" required>
              </div>
              <div class="col-md-12">
                <input type="email" class="form-control" name="email1" placeholder="Email address" value="<?php echo isset($_POST['email1']) ? $_POST['email1'] : ''; ?>" required>
              </div>
              <div class="col-md-12">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
              </div>
              <div class="col-md-12">
                <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" required>
              </div>
            </div>
            <div class="mt-4">
              <button type="submit" class="btn btn-yellow w-100">Create Account</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

<?php if ($error === 'login'): ?>
<script>
Swal.fire({
  icon: "error",
  title: "Login Failed",
  text: "Invalid email or password!"
});
</script>
<?php elseif ($error === 'confirm'): ?>
<script>
Swal.fire({
  icon: "error",
  title: "Password Mismatch",
  text: "Passwords do not match!"
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


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
=======
<?php
session_start(); 
require_once 'SkillSwapDatabase.php';
require_once 'SP.php';

$db = new Database();
$conn = $db->getConnection();

$error = false;
$success = false;
$activeTab = isset($_SESSION['activeTab']) ? $_SESSION['activeTab'] : 'login'; // Set default to 'login'
// Retrieve and clear register success flag
if (isset($_SESSION['registerSuccess']) && $_SESSION['registerSuccess'] === true) {
  $success = true;
  unset($_SESSION['registerSuccess']); // Clear it so it doesn't show again on refresh
}

// Handle Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'login') {
  $email = $_POST['email'];
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM users WHERE Email = :email");
  $stmt->bindParam(':email', $email);
  $stmt->execute();
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user && $password === $user['Password']) { // Compare the entered password to the stored plaintext one
      $_SESSION['user_id'] = $user['User_ID']; 
      $_SESSION['user_email'] = $user['Email'];
      header("Location: Interests.php?submit=success");
      exit(); 
  } else {
      $error = 'login';
      $_SESSION['activeTab'] = 'login'; // Set active tab to 'login' if login fails
  }
}


// Handle Registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'register') {
  $first = $_POST['first_name'];
  $last = $_POST['last_name'];
  $email = $_POST['email1'];
  $password = $_POST['password'];
  $confirm = $_POST['confirm_password'];

  if ($password !== $confirm) {
      $error = 'confirm';
      $_SESSION['activeTab'] = 'register'; // Stay on register tab if error
  } else {
      $crud = new Crud();
      $crud->createUser($first, $last, $email, $password);

      $_SESSION['activeTab'] = 'login'; // Go to login tab after successful register
      $_SESSION['registerSuccess'] = true; // Set success flag
      header("Location: " . $_SERVER['PHP_SELF']); // Redirect back to same page
      exit();
  }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>SkillSwap - Login & Registration</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      background: #f6ff00;
      color: #ffffff;
      font-family: 'Segoe UI', sans-serif;
    }

    .header {
      position: absolute;
      top: 20px;
      left: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .logo-img {
      height: 60px;
      width: auto;
    }

    .site-name {
      font-weight: bold;
      font-size: 30px;
      color: black;
      text-decoration: none;
    }

    .site-name:hover {
      text-decoration: none;
    }

    .form-container {
      background: rgba(255, 255, 255, 0.95);
      color: #000;
      border-radius: 12px;
      padding: 40px;
      box-shadow: 0 0 20px rgba(0,0,0,0.3);
      max-width: 500px;
      width: 100%;
    }

    .btn-yellow {
      background-color: #f6f65a;
      color: #000;
      font-weight: bold;
      border-radius: 12px;
      transition: 0.2s;
    }

    .btn-yellow:hover {
      background-color: #ffff77;
      transform: scale(1.02);
    }

    .nav-tabs .nav-link.active {
      background-color: #f6f65a;
      color: #000;
      font-weight: bold;
      border-radius: 12px 12px 0 0;
    }

    .nav-tabs .nav-link {
      border: none;
      color: #000;
    }

    a {
      color: #007bff;
      text-decoration: none;
    }

    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="header">
    <img src="486355471_1047694797262653_8440216834434319319_n-removebg-preview.png" alt="Logo" class="logo-img">
    <a href="Dashboard.php" class="site-name">SkillSwap</a>
  </div>

  <div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="form-container text-center">
      <ul class="nav nav-tabs justify-content-center mb-4" id="authTabs" role="tablist">
        <li class="nav-item" role="presentation">
        <button class="nav-link <?php echo $activeTab === 'login' ? 'active' : ''; ?>" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab">Login</button>
        </li>
        <li class="nav-item" role="presentation">
        <button class="nav-link <?php echo $activeTab === 'register' ? 'active' : ''; ?>" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab">Register</button>
        </li>
      </ul>

      <div class="tab-content" id="authTabsContent">
        <!-- Login Tab -->
        <div class="tab-pane fade <?php echo $activeTab === 'login' ? 'show active' : ''; ?>" id="login" role="tabpanel">
        <form method="POST" action="">
            <input type="hidden" name="action" value="login">
            <div class="mb-3">
            <input type="email" class="form-control" name="email" placeholder="Enter your Email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" required>
            </div>
            <div class="mb-3">
              <input type="password" class="form-control" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn btn-yellow w-100">Sign In</button>
          </form>
        </div>

        <!-- Register Tab -->
        <div class="tab-pane fade <?php echo $activeTab === 'register' ? 'show active' : ''; ?>" id="register" role="tabpanel">
        <form method="POST" action="">
            <input type="hidden" name="action" value="register">
            <div class="row g-3">
              <div class="col-md-6">
                <input type="text" class="form-control" name="first_name" placeholder="First name" value="<?php echo isset($_POST['first_name']) ? $_POST['first_name'] : ''; ?>" required>
              </div>
              <div class="col-md-6">
                <input type="text" class="form-control" name="last_name" placeholder="Last name" value="<?php echo isset($_POST['last_name']) ? $_POST['last_name'] : ''; ?>" required>
              </div>
              <div class="col-md-12">
                <input type="email" class="form-control" name="email1" placeholder="Email address" value="<?php echo isset($_POST['email1']) ? $_POST['email1'] : ''; ?>" required>
              </div>
              <div class="col-md-12">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
              </div>
              <div class="col-md-12">
                <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" required>
              </div>
            </div>
            <div class="mt-4">
              <button type="submit" class="btn btn-yellow w-100">Create Account</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

<?php if ($error === 'login'): ?>
<script>
Swal.fire({
  icon: "error",
  title: "Login Failed",
  text: "Invalid email or password!"
});
</script>
<?php elseif ($error === 'confirm'): ?>
<script>
Swal.fire({
  icon: "error",
  title: "Password Mismatch",
  text: "Passwords do not match!"
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


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
