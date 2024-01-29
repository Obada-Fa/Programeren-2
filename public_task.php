<?php
/** @var mysqli $conn */
session_start();

require_once './includes/database.php';

$publicTasksQuery = "SELECT tasks.*, GROUP_CONCAT(users.username SEPARATOR ', ') AS assigned_users
                    FROM tasks
                    LEFT JOIN user_tasks ON tasks.task_id = user_tasks.task_id
                    LEFT JOIN users ON user_tasks.user_id = users.user_id
                    WHERE tasks.is_public = 1
                    GROUP BY tasks.task_id";
$publicTasksResult = $conn->query($publicTasksQuery);

$publicTasks = [];

while ($publicTask = $publicTasksResult->fetch_assoc()) {
    $publicTasks[] = $publicTask;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Public Tasks - Task Management</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<div class="header">
    <h1>Public Tasks</h1>
    <p><a href="index.php" class="logout-btn">Home</a></p>
    <p><a href="logout.php" class="logout-btn">Logout</a></p>
</div>

<h2>Public Tasks</h2>

<table>
    <thead>
    <tr>
        <th>Title</th>
        <th>Description</th>
        <th>Due Date</th>
        <th>Status</th>
        <th>Assigned Users</th>
        <th>Assign</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($publicTasks as $publicTask) { ?>
        <tr>
            <td><?php echo $publicTask['title']; ?></td>
            <td><?php echo $publicTask['description']; ?></td>
            <td><?php echo $publicTask['due_date']; ?></td>
            <td><?php echo $publicTask['status']; ?></td>
            <td><?php echo $publicTask['assigned_users']; ?></td>
            <td>
                <a href="assign.php?task_id=<?php echo $publicTask['task_id']; ?>">Assign to Me</a>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>
</body>
</html>
