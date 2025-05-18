<?php
session_start();
<<<<<<< HEAD
require_once 'SkillSwapDatabase.php';
require_once 'SP.php';

if (isset($_GET['confirm']) && $_GET['confirm'] === 'true') {
    session_unset();
    session_destroy();
    header("Location: LogInPage.php?logout=success");
    exit();
}
?>
=======

//

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: loginpagee.php");
exit(); 
>>>>>>> maris
