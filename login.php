<?php
/** @var mysqli $conn */
require_once "includes/database.php";
// required when working with sessions
session_start();
// Is user logged in?

if (isset($_POST['submit'])) {
    // Get form data
    $email = $_POST['email'];
    $password = $_POST['password'];
    // Server-side validation
    $errors = [];
    if($email == ""){
        $errors['email'] = 'Please enter in your e-mail.';
    }
    if($password == ""){
        $errors['password'] = 'Please enter in your password..';
    }

    // If data valid
    if(empty($errors)){
        // SELECT the user from the database, based on the email address.
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        // check if the user exists
        if($result->num_rows == 1){
            // Get user data from result
            $user = $result->fetch_assoc();
            // Check if the provided password matches the stored password in the database
            if(password_verify($password, $user['password'])){
                // Store the user in the session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['email'] = $email;
                $_SESSION['username'] = $user['username'];
                // Redirect to secure page (index.php)
                $_SESSION['login'] = true;
                header('Location: index.php');
                exit(); // Ensure script stops after redirection
            }else{
                //error incorrect log in
                $errors['loginFailed'] = 'The password is not valid.';
            }
        }else{
            //error incorrect log in
            $errors['loginFailed'] = 'The credentials are not valid.';
        }
    }
    // Output error messages
    foreach ($errors as $error) {
        echo '<p class="error-message">' . $error . '</p>';
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management - Login</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<h1>Login</h1>
<form action="#" method="POST">
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>
    <br>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>
    <br>
    <button type="submit" name="submit">Login</button>
</form>
<p>Don't have an account? <a href="register.php">Register here</a></p>
</body>
</html>
