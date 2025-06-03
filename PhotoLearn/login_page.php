<?php
session_start();
/**
 * Created by Houming Ge
 * User: houming@uw.edu
 * Date: 6/2/2025
 */

$valid_username = "admin";
$valid_password = "password123";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === $valid_username && $password === $valid_password) {
        $_SESSION['username'] = $username;
        header("login_page.php"); // Redirect on success
        exit;
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<html>

<head>
    <title>Photo learn</title>
    <link rel="stylesheet" href="login_page.css">
</head>

<body>
    <!-- top header  -->
    <?php require_once 'header.inc.php';?>

    <!-- Main body -->
    <h2>Login Page</h2>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="post" action="login_page.php">
        <label for="username">Username:</label><br>
        <input type="text" name="username" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" name="password" required><br><br>

        <input type="submit" value="Login">
    </form>


</body>

</html>