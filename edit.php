<?php
/** @var mysqli $conn */
session_start();

require_once './includes/database.php';

// check if session exists other wise redirect to login page
if (empty($_SESSION['login'])) {
    header('Location: login.php');
    exit();
}

// check  if task id is provided
if (isset($_GET['task_id'])) {
    $taskId = $_GET['task_id'];

    // Fetch task details
    $taskQuery = "SELECT * FROM tasks WHERE task_id = ? AND user_id = ?";
    $stmt = $conn->prepare($taskQuery);
    $stmt->bind_param("ii", $taskId, $_SESSION['user_id']);
    $stmt->execute();
    $taskResult = $stmt->get_result();

    if ($taskResult->num_rows === 1) {
        $task = $taskResult->fetch_assoc();
    } else {
        // Task not found, redirect to index
        header('Location: index.php');
        exit();
    }
} else {
    // Task ID not provided, redirect to index
    header('Location: index.php');
    exit();
}

// Update task details in the database if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');
    $dueDate = htmlspecialchars($_POST['due_date'], ENT_QUOTES, 'UTF-8');
    $status = htmlspecialchars($_POST['status'], ENT_QUOTES, 'UTF-8');
    $isPublic = isset($_POST['is_public']) ? 1 : 0; // Check if the checkbox is checked

    // Validate and update task in the database
    $updateQuery = "UPDATE tasks SET title = ?, description = ?, due_date = ?, status = ?, is_public = ? WHERE task_id = ? AND user_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssssiii", $title, $description, $dueDate, $status, $isPublic, $taskId, $_SESSION['user_id']);
    $stmt->execute();

    // Check if the update was successful
    if ($stmt->affected_rows > 0) {
        // Redirect to index after updating
        header('Location: index.php');
        exit();
    } else {
        // Handle the case where the update failed
        echo "Error updating task.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task - Task Management</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<div class="header">
    <h1>Edit Task</h1>
    <p><a href="logout.php" class="logout-btn">Logout</a></p>
</div>

<form action="#" method="POST">
    <label for="title">Title:</label>
    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($task['title'], ENT_QUOTES, 'UTF-8'); ?>" required>
    <br>
    <label for="description">Description:</label>
    <textarea id="description" name="description" required><?php echo htmlspecialchars($task['description'], ENT_QUOTES, 'UTF-8'); ?></textarea>
    <br>
    <label for="due_date">Due Date:</label>
    <input type="date" id="due_date" name="due_date" value="<?php echo htmlspecialchars($task['due_date'], ENT_QUOTES, 'UTF-8'); ?>" required>
    <br>
    <label for="status">Status:</label>
    <select id="status" name="status" required>
        <option value="Pending" <?php echo ($task['status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
        <option value="Completed" <?php echo ($task['status'] === 'Completed') ? 'selected' : ''; ?>>Completed</option>
    </select>
    <br>
    <label for="is_public">Public Task:</label>
    <input type="checkbox" id="is_public" name="is_public" <?php echo ($task['is_public'] == 1) ? 'checked' : ''; ?>>
    <br>
    <button type="submit">Update Task</button>
</form>

</body>
</html>
