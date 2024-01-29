<?php
/** @var mysqli $conn */
session_start();

// Redirect to login if not logged in
if (empty($_SESSION['login'])) {
    header('Location: login.php');
    exit();
}

require_once './includes/database.php';

if (isset($_GET['task_id'])) {
    $taskId = $_GET['task_id'];

    // Confirm deletion
    if (confirmDeletion($conn, $taskId, $_SESSION['user_id'])) {
        header('Location: index.php');
        exit();
    } else {
        echo "Error deleting task.";
    }
} else {
    // Task ID not provided, redirect to index
    header('Location: index.php');
    exit();
}

function confirmDeletion($conn, $taskId, $userId) {
    // Fetch task details to confirm ownership
    $taskQuery = "SELECT * FROM tasks WHERE task_id = ? AND user_id = ?";
    $stmt = $conn->prepare($taskQuery);
    $stmt->bind_param("ii", $taskId, $userId);
    $stmt->execute();
    $taskResult = $stmt->get_result();

    if ($taskResult->num_rows === 1) {
        // Task belongs to the user, proceed with deletion
        $deleteQuery = "DELETE FROM tasks WHERE task_id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $taskId);
        $stmt->execute();

        return true;
    }

    return false;
}
?>
