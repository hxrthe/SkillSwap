<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header("Location: Interests.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Registration</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>

    .form-container {
      background: rgba(255, 255, 255, 0.95);
      color: #000;
      border-radius: 12px;
      padding: 40px;
      box-shadow: 0 0 20px rgba(0,0,0,0.3);
      max-width: 500px;
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


  <div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="form-container text-center w-100">
      <h2 class="mb-4">Create your account</h2>
      <form method="POST" action="">
        <div class="row g-3">
          <div class="col-md-6">
            <input type="text" class="form-control" name="first_name" placeholder="Enter your first name" required>
          </div>
          <div class="col-md-6">
            <input type="text" class="form-control" name="last_name" placeholder="Enter your last name" required>
          </div>
          <div class="col-md-12">
            <input type="date" class="form-control" name="birthday" required>
          </div>
          <div class="col-md-12">
            <input type="email" class="form-control" name="email" placeholder="Enter your email address" required>
          </div>
          <div class="col-md-12">
            <input type="password" class="form-control" name="password" placeholder="Enter your password" required>
          </div>
        </div>
        <div class="mt-4">
          <button type="submit" class="btn btn-yellow w-100">Create</button>
        </div>
        <div class="mt-3">
          Already have an account? Click <a href="Login.php">here</a> to sign in.
        </div>
      </form>
    </div>
  </div>

</body>
</html>