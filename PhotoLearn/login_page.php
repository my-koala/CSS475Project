<?php
session_start();
/**
 * Created by Houming Ge
 * User: houming@uw.edu
 * Date: 6/2/2025
 */

require_once 'config.inc.php';

$valid_username = 'admin';
$valid_password = 'admin123';

// Create connection
$conn = new mysqli($servername, $username, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === $valid_username && $password === $valid_password) {
        $_SESSION['username'] = $username;
        header("Location: admin_dashboard.php");
        exit();
    } else {

        // TODO: Fixing the to the our sql database search for this part
        // Prevent SQL injection
        $stmt = $conn->prepare("SELECT * FROM Users WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->bind_result();

        // Check if user exists
        if ($stmt->fetch()) {
            if ($result && $result->num_rows === 1) {
                $user = $result->fetch_assoc();

                // TODO: unsure this part is working, need to check
                if (hash('sha256', $password) === $user['password']) {
                    $_SESSION['username'] = $username;
                    header("Location: index.php");
                    exit;
                } else {
                    $error = "Invalid username or password!";
                }
            } else {
                $error = "Invalid username or password!";
            }
        } else {
            $error = "Invalid username or password!";
        }

        $stmt->close();
    }
}

$conn->close();
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