<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Interests</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f6ff00;
      font-family: 'Segoe UI', sans-serif;
    }

    .interest-container {
      background: rgba(255, 255, 255, 0.95);
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 0 20px rgba(0,0,0,0.3);
      max-width: 600px;
      margin-bottom: 80px;
    }

    .interest-btn {
      background-color: #fefcbf;
      border: 1px solid #4a4a4a;
      font-weight: bold;
      padding: 15px;
      border-radius: 12px;
      width: 100%;
      transition: 0.2s ease-in-out;
    }

    .interest-btn:hover {
      background-color: #f6f65a;
      transform: scale(1.03);
    }

    .interest-btn.active {
      background-color: #f6f65a !important;
      border-color: #4a4a4a;
      color: #000;
      box-shadow: 0 0 10px rgba(0,0,0,0.15);
    }

    .btn-confirm {
      background-color: #f6f65a;
      font-weight: bold;
      border-radius: 12px;
      padding: 10px 30px;
      margin-top: 20px;
      transition: 0.2s;
    }

    .btn-confirm:hover:enabled {
      background-color: #ffff77;
      transform: scale(1.03);
    }
  </style>
</head>
<body class="d-flex flex-column min-vh-100">

<?php include 'Menu.php'; ?>

  <main class="flex-fill d-flex justify-content-center align-items-center">
    <div class="interest-container text-center w-100">
      <h2 class="mb-4">What are you looking for?</h2>
      <form method="POST" action="Login.php">
        <div class="row g-3">
          <div class="col-md-6">
            <button type="button" class="interest-btn"
              onclick="document.querySelectorAll('.interest-btn').forEach(b=>b.classList.remove('active')); this.classList.add('active'); document.getElementById('confirmBtn').disabled = false;">
              Arts & Creativity
            </button>
          </div>
          <div class="col-md-6">
            <button type="button" class="interest-btn"
              onclick="document.querySelectorAll('.interest-btn').forEach(b=>b.classList.remove('active')); this.classList.add('active'); document.getElementById('confirmBtn').disabled = false;">
              Beauty & Hair
            </button>
          </div>
          <div class="col-md-6">
            <button type="button" class="interest-btn"
              onclick="document.querySelectorAll('.interest-btn').forEach(b=>b.classList.remove('active')); this.classList.add('active'); document.getElementById('confirmBtn').disabled = false;">
              Business & Professional
            </button>
          </div>
          <div class="col-md-6">
            <button type="button" class="interest-btn"
              onclick="document.querySelectorAll('.interest-btn').forEach(b=>b.classList.remove('active')); this.classList.add('active'); document.getElementById('confirmBtn').disabled = false;">
              Caretaking & Sitting
            </button>
          </div>
        </div>
        <button id="confirmBtn" type="submit" class="btn btn-confirm mt-4" disabled>Confirm</button>
      </form>
    </div>
  </div>

</body>
</html>
