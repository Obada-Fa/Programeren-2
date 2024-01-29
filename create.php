<?php
/** @var mysqli $conn */
session_start();

// Redirect to login if not logged in
if (empty($_SESSION['login'])) {
    header('Location: login.php');
    exit();
}

require_once './includes/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');
    $dueDate = htmlspecialchars($_POST['due_date'], ENT_QUOTES, 'UTF-8');
    $status = htmlspecialchars($_POST['status'], ENT_QUOTES, 'UTF-8');
    $isPublic = isset($_POST['is_public']) ? 1 : 0; // Check if the checkbox is checked

    // Validate input (add more validation if needed)
    if (empty($title) || empty($dueDate)) {
        $error = 'Title and Due Date are required.';
    } else {
        $userID = $_SESSION['user_id'];

        $insertQuery = "INSERT INTO tasks (user_id, title, description, due_date, status, is_public) 
                        VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("issssi", $userID, $title, $description, $dueDate, $status, $isPublic);
        $stmt->execute();

        // Redirect to index after adding the task
        header('Location: index.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management - Add Task</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<div class="header">
    <h1>Add Task</h1>
    <a href="logout.php" class="logout-btn">Logout</a>
</div>

<form action="#" method="post" class="task-form">
    <label for="title">Title:</label>
    <input type="text" id="title" name="title" required>

    <label for="description">Description:</label>
    <textarea id="description" name="description" rows="4"></textarea>

    <label for="due_date">Due Date:</label>
    <input type="date" id="due_date" name="due_date" required>

    <label for="status">Status:</label>
    <select id="status" name="status">
        <option value="Not Started">Not Started</option>
        <option value="In Progress">In Progress</option>
        <option value="Completed">Completed</option>
    </select>

    <label for="is_public">Public Task:</label>
    <input type="checkbox" id="is_public" name="is_public">

    <?php if (isset($error)) { ?>
        <p class="error-message"><?php echo $error; ?></p>
    <?php } ?>

    <button type="submit">Add Task</button>
</form>
</body>
</html>
