<?php
session_start();
/**
 * Created by Houming Ge
 * User: houming@uw.edu
*/

require_once 'config.inc.php';

$conn = new mysqli($servername, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $plan = "basic";
    $date_start = date("Y-m-d H:i:s");
    $date_end = date("Y-m-d H:i:s", strtotime("+1 year"));

    if (!empty($username)) {

        // TODO: i need to check if this part of SQL call is wortking
        $stmt = $conn->prepare("SELECT user_id FROM Users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($user_id);

        if ($stmt->fetch()) {
            $stmt->close();

            // Step 2: Insert into Subscriptions
            $insert = $conn->prepare("INSERT INTO Subscriptions (user_id, plan, date_start, date_end) VALUES (?, ?, ?, ?)");
            $insert->bind_param("isss", $user_id, $plan, $date_start, $date_end);

            if ($insert->execute()) {
                $message = "Subscription purchased successfully for '$username'.";
            } else {
                $message = "Error while creating subscription.";
            }

            $insert->close();
        } else {
            $message = "Username not found.";
        }
    } else {
        $message = "Please enter a username.";
    }
}

$conn->close();
?>

<html>

<head>
    <title>Subscribe | PhotoLearn</title>
    <link rel="stylesheet" href="subscription_page.css">
</head>

<body>
    <?php
require_once 'header.inc.php';
?>
    <h2>Buy Subscription</h2>

    <?php if (!empty($message)) echo "<p style='color:green;'>$message</p>"; ?>

    <form method="POST" action="subscription_page.php">
        <label for="username">Username:</label><br>
        <input type="text" name="username" required><br><br>

        <input type="submit" value="Buy Subscription">
    </form>

</body>

</html>