<?php
require_once 'config.inc.php';

$conn = new mysqli($servername, $username, $password, $database, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = intval($_POST['user_id']);
    $post_text = trim($_POST['post_text']);
    $time_stamp = date("Y-m-d-H:i:s");
    $ctr = 0;

    $stmt = $conn->prepare("INSERT INTO Posts (user_id, post_text, time_stamp, ctr) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $user_id, $post_text, $time_stamp, $ctr);

    if ($stmt->execute()) {
        $message = "Post created successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<html>

<head>
    <title>Make a Post</title>
</head>

<body>
    <!-- top header  -->
    <?php require_once 'header.inc.php';?>

    <!-- Main body -->
    <h2>Create New Post</h2>
    <?php if (!empty($message)) echo "<p><strong>$message</strong></p>"; ?>

    <form method="post">
        <label>User ID:</label><br>
        <input type="number" name="user_id" required><br><br>

        <label>Post Text:</label><br>
        <textarea name="post_text" rows="5" cols="40" required></textarea><br><br>

        <input type="submit" value="Submit Post">
    </form>
</body>

</html>

<?php $conn->close(); ?>