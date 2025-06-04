<?php
require_once 'config.inc.php';

$conn = new mysqli($servername, $username, $password, $database, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$erroe = "";

$uploadDir = "uploads/";

if (!file_exists($uploadDir)) mkdir($uploadDir);

if (isset($_POST["submit_post"])) {
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

            $message = "Image uploaded successfully!";
        } else {
            $error = "Failed to upload image.";
        }
    }

    // Create post
    $stmt = $conn->prepare("INSERT INTO Posts (user_id, post_text, time_stamp, ctr, photo_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issii", $user_id, $post_text, $time_stamp, $ctr, $photo_id);
    if ($stmt->execute()) {
        $message = "Post created successfully!";
    } else {
        $error = "Error creating post.";
    }
    $stmt->close();
}

// Handle form submission
    if (isset($_POST["attach_photo"])) {
        $post_id = intval($_POST['post_id']);
        $photo_id = intval($_POST['photo_id']);
        $user_id = intval($_POST['user_id_attach']);

        // Verify ownership
        $stmt = $conn->prepare("SELECT photo_id FROM Photos WHERE photo_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $photo_id, $user_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO PostPhotos (post_id, photo_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $post_id, $photo_id);

            if ($stmt->execute()) {
                $message = "Photo attached to post!";
            } else {
                $error = "Error attaching photo: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $error = "This photo doesn't belong to the provided user ID.";
            $stmt->close();
        }
    }

?>

<html>

<head>
    <title>Make a Post</title>
    <link rel="stylesheet" href="making_post.css">
</head>

<body>
    <!-- top header  -->
    <?php require_once 'header.inc.php';?>

    <!-- Main body -->
    <h2>Create New Post</h2>

    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if (!empty($message)) echo "<p style='color:green;'>$message</p>"; ?>

    <form method="post">
        <label>User ID:</label><br>
        <input type="number" name="user_id" required><br><br>

        <label>Post Text:</label><br>
        <textarea name="post_text" rows="5" cols="40" required></textarea><br><br>

        <input type="submit" name= "submit_post" value="Submit Post">
    </form>

    <h2>Attach Photo to Post</h2>
    <?php if (!empty($message)) echo "<p><strong>$message</strong></p>"; ?>

    <form method="post">
        <label for="post_id">Enter Post ID:</label><br>
        <input type="number" name="post_id" id="post_id" required><br><br>

        <label for="photo_id">Enter Photo ID:</label><br>
        <input type="number" name="photo_id" id="photo_id" required><br><br>

        <label for="user_id_attach">Enter User ID (owner of photo):</label><br>
        <input type="number" name="user_id_attach" id="user_id_attach" required><br><br>

        <input type="submit" name="attach_photo" value="Attach Photo">
    </form>
</body>

</html>

<?php $conn->close(); ?>