<?php
/** @var mysqli $conn */
session_start();

// Redirect to login if not logged in
if (empty($_SESSION['login'])) {
    header('Location: login.php');
    exit();
}

require_once './includes/database.php';
$userID = $_SESSION['user_id'];

$tasksQuery = "SELECT tasks.*, GROUP_CONCAT(users.username SEPARATOR ', ') AS assigned_users
               FROM tasks
               LEFT JOIN user_tasks ON tasks.task_id = user_tasks.task_id
               LEFT JOIN users ON user_tasks.user_id = users.user_id
               WHERE tasks.user_id = ?
               GROUP BY tasks.task_id";
$stmt = $conn->prepare($tasksQuery);
$stmt->bind_param("i", $userID);
$stmt->execute();
$tasksResult = $stmt->get_result();

$tasks = [];

while ($task = $tasksResult->fetch_assoc()) {
    // Escape HTML characters to prevent XSS
    $task['title'] = htmlspecialchars($task['title'], ENT_QUOTES, 'UTF-8');
    $task['description'] = htmlspecialchars($task['description'], ENT_QUOTES, 'UTF-8');
    $task['due_date'] = htmlspecialchars($task['due_date'], ENT_QUOTES, 'UTF-8');
    $task['status'] = htmlspecialchars($task['status'], ENT_QUOTES, 'UTF-8');
    $task['assigned_users'] = htmlspecialchars($task['assigned_users'], ENT_QUOTES, 'UTF-8');

    $tasks[] = $task;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management - Index</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<div class="header">
    <h1>Welcome to the Task Manager</h1>
    <p><a href="public_task.php" class="logout-btn">Shared tasks</a></p>
    <p><a href="logout.php" class="logout-btn">Logout</a></p>
</div>

<h2>Your Tasks</h2>

<table>
    <thead>
    <tr>
        <th>Title</th>
        <th>Description</th>
        <th>Due Date</th>
        <th>Status</th>
        <th>Assigned Users</th>
        <th>Edit</th>
        <th>Delete</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($tasks as $task) { ?>
        <tr>
            <td><?php echo $task['title']; ?></td>
            <td><?php echo $task['description']; ?></td>
            <td><?php echo $task['due_date']; ?></td>
            <td><?php echo $task['status']; ?></td>
            <td><?php echo $task['assigned_users']; ?></td>
            <td><a href="edit.php?task_id=<?php echo isset($task['task_id']) ? $task['task_id'] : ''; ?>">Edit</a></td>
            <td><a href="delete.php?task_id=<?php echo isset($task['task_id']) ? $task['task_id'] : ''; ?>" onclick="return confirm('Are you sure you want to delete this task?')">Delete</a></td>
            <td>
                <?php if (!in_array($userID, explode(', ', $task['assigned_users']))) { ?>
                    <a href="assign.php?task_id=<?php echo isset($task['task_id']) ? $task['task_id'] : ''; ?>">Assign to Me</a>
                <?php } else { ?>
                    Assigned
                <?php } ?>
            </td>
        </tr>
    <?php } ?>
    <td><a href="create.php" class="add-task-link">Add New Task</a></td>
    </tbody>
</table>

</body>
</html>
