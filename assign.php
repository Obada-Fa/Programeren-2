<?php
/** @var mysqli $conn */
session_start();

if (empty($_SESSION['login'])) {
    header('Location: login.php');
    exit();
}

require_once './includes/database.php';
$userID = $_SESSION['user_id'];

if (isset($_GET['task_id'])) {
    $taskId = $_GET['task_id'];

    // Check if the task is not already assigned to the user
    $checkAssignmentQuery = "SELECT * FROM user_tasks WHERE user_id = ? AND task_id = ?";
    $checkAssignmentStmt = $conn->prepare($checkAssignmentQuery);
    $checkAssignmentStmt->bind_param("ii", $userID, $taskId);
    $checkAssignmentStmt->execute();
    $assignmentResult = $checkAssignmentStmt->get_result();

    if ($assignmentResult->num_rows === 0) {
        // Assign the task to the user
        $assignQuery = "INSERT INTO user_tasks (user_id, task_id) VALUES (?, ?)";
        $assignStmt = $conn->prepare($assignQuery);
        $assignStmt->bind_param("ii", $userID, $taskId);
        $assignStmt->execute();
    }
}

header('Location: index.php');
exit();
?>
