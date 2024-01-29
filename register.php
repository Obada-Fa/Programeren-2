<?php
include('includes/database.php');
/** @var mysqli $conn */
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Use prepared statements
    $emailCheckQuery = "SELECT * FROM users WHERE email = ?";
    $usernameCheckQuery = "SELECT * FROM users WHERE username = ?";

    $stmtEmail = $conn->prepare($emailCheckQuery);
    $stmtEmail->bind_param("s", $email);
    $stmtEmail->execute();
    $emailCheckResult = $stmtEmail->get_result();

    $stmtUsername = $conn->prepare($usernameCheckQuery);
    $stmtUsername->bind_param("s", $username);
    $stmtUsername->execute();
    $usernameCheckResult = $stmtUsername->get_result();

    if ($emailCheckResult->num_rows > 0) {
        echo "Error: Email already exists.";
    } elseif ($usernameCheckResult->num_rows > 0) {
        echo "Error: Username already exists.";
    } else {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $registrationQuery = "INSERT INTO users (email, username, password) VALUES (?, ?, ?)";

        $stmtRegistration = $conn->prepare($registrationQuery);
        $stmtRegistration->bind_param("sss", $email, $username, $passwordHash);
        $registrationResult = $stmtRegistration->execute();

        if ($registrationResult) {
            echo "Successfully registered!";
            header('Location: login.php');
        } else {
            echo "Error: Failed to register user.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management - Register</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<h1>Register</h1>
<form action="#" method="POST">
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>
    <br>
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required>
    <br>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>
    <br>
    <button type="submit">Register</button>
</form>
</body>
</html>
