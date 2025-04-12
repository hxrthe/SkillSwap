<?php
session_start();
require_once 'SkillSwapDatabase.php';
require_once 'SP.php';

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: LogInPage.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container mt-5">
        <h3>Welcome to the Homepage!</h3>
        <p>Hello, <?php echo $_SESSION['user_email']; ?>. You are logged in.</p>

        <!-- Logout Button -->
        <button class="btn btn-danger" onclick="confirmLogout()">Logout</button>
    </div>

    <!-- success message -->
    <?php if (isset($_GET['submit']) && $_GET['submit'] === 'success'): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            position: "center",
            icon: "success",
            title: "Signed in successfully!",
            showConfirmButton: false,
            timer: 1500
        });
    });
    </script>
    <?php endif; ?>

    <!-- SweetAlert Logout Script -->
    <script>
    function confirmLogout() {
        Swal.fire({
            title: "Are you sure you want to logout?",
            text: "You can always log back in later.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Log Out",
            backdrop: true,
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "LogOut.php?confirm=true";
            }
        });
    }
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
