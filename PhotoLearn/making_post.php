<?php
require_once 'config.inc.php';

$conn = new mysqli($servername, $username, $password, $database, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$uploadDir = "uploads/";
if (!file_exists($uploadDir)) mkdir($uploadDir);

if (isset($_POST["Submit Post"])) {
    $user_id = intval($_POST['user_id']);
    $post_text = trim($_POST['post_text']);
    $image_description = trim($_POST['image_description']);
    $time_stamp = date("Y-m-d H:i:s");
    $ctr = 0;
    $photo_id = null;

    // Handle image upload
    if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] == 0) {
        $fileTmp = $_FILES["photo"]["tmp_name"];
        $fileName = basename($_FILES["photo"]["name"]);
        $targetPath = $uploadDir . $fileName;
        $imageFormat = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));

        if (!in_array($imageFormat, ['jpg', 'jpeg', 'png', 'gif'])) {
            $message = "Invalid image format.";
        } elseif ($_FILES["photo"]["size"] > 2 * 1024 * 1024) {
            $message = "Image size exceeds 2MB.";
        } elseif (move_uploaded_file($fileTmp, $targetPath)) {
            // Save photo metadata
            $stmt = $conn->prepare("INSERT INTO Photos (user_id, image_description, image_path) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $user_id, $image_description, $targetPath);
            if ($stmt->execute()) {
                $photo_id = $stmt->insert_id;
            }
            $stmt->close();
        } else {
            $message = "Failed to upload image.";
        }
    }

    // Create post
    $stmt = $conn->prepare("INSERT INTO Posts (user_id, post_text, time_stamp, ctr, photo_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issii", $user_id, $post_text, $time_stamp, $ctr, $photo_id);
    if ($stmt->execute()) {
        $message = "Post created successfully!";
    } else {
        $message = "Error creating post.";
    }
    $stmt->close();
}

// Handle form submission
    if (isset($_POST["Attach Photo"])) {
        $post_id = intval($_POST['post_id']);
        $photo_id = intval($_POST['photo_id']);

        $stmt = $conn->prepare("INSERT INTO PostPhotos (post_id, photo_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $post_id, $photo_id);

        if ($stmt->execute()) {
            $message = "Photo attached to post!";
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

    <h2>Attach Photo to Post</h2>
    <?php if (!empty($message)) echo "<p><strong>$message</strong></p>"; ?>

    <form method="update">
        <label for="post_id">Enter Post ID:</label><br>
        <input type="number" name="post_id" id="post_id" required><br><br>

        <label for="photo_id">Enter Photo ID:</label><br>
        <input type="number" name="photo_id" id="photo_id" required><br><br>

        <input type="submit" value="Attach Photo">
    </form>
</body>

</html>

<?php $conn->close(); ?>