<?php
session_start();
require_once 'SkillSwapDatabase.php';
require_once 'sp.php';

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Get date range from request
$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$end_date = $_GET['end_date'] ?? date('Y-m-d');

$crud = new Crud();

try {
    // Get all statistics
    $data = [
        'user_stats' => $crud->getUserStatistics(null, null),
        'restriction_stats' => $crud->getRestrictionStatistics(null, null),
        'activity_stats' => $crud->getUserActivityStatistics(null, null),
        'system_stats' => $crud->getSystemStatistics(),
        'daily_users' => $crud->getDailyUserRegistrations($start_date, $end_date),
        'daily_restrictions' => $crud->getDailyRestrictions($start_date, $end_date),
        'daily_posts' => $crud->getDailyPosts($start_date, $end_date),
        'daily_comments' => $crud->getDailyComments($start_date, $end_date)
    ];

    header('Content-Type: application/json');
    echo json_encode($data);
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
} 