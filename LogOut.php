<?php
session_start();
require_once 'SkillSwapDatabase.php';
require_once 'SP.php';

if (isset($_GET['confirm']) && $_GET['confirm'] === 'true') {
    session_unset();
    session_destroy();
    header("Location: LogInPage.php?logout=success");
    exit();
}
?>