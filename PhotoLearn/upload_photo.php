<?php
session_start();


require_once 'config.inc.php';

$conn = new mysqli($servername, $username, $password, $database, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$uploadDir = "images/";
$maxSize = 2 * 1024 * 1024; // 2MB

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["photo"])) {
    if ($_FILES["photo"]["size"] > $maxSize) {
        $message = "File size exceeds 2MB limit.";
    } else {
        $fileTmp = $_FILES["photo"]["tmp_name"];
        $fileName = basename($_FILES["photo"]["name"]);
        $filePath = $uploadDir . $fileName;
        $imageFormat = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if (!in_array($imageFormat, ['jpg', 'jpeg', 'png', 'gif'])) {
            $message = "Invalid file format.";
        } else if (move_uploaded_file($fileTmp, $filePath)) {
            $imgInfo = getimagesize($filePath);
            $resX = $imgInfo[0];
            $resY = $imgInfo[1];

            $username = $_POST['username'] ?? '';
            $camera_model = $_POST['camera_model'] ?? '';
            $image_description = $_POST['image_description'] ?? '';
            $aperture = $_POST['aperture'] ?? '';
            $shutter_speed = $_POST['shutter_speed'] ?? '';
            $iso = $_POST['iso'] ?? 0;
            $focal_length = $_POST['focal_length'] ?? '';
            $geolocation = $_POST['geolocation'] ?? '';

            // Get user_id
            $stmt = $conn->prepare("SELECT user_id FROM Users WHERE username = ?");
            if (!$stmt) die("User SELECT prepare failed: " . $conn->error);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->bind_result($user_id);
            if (!$stmt->fetch()) {
                $message = "User not found.";
                $stmt->close();
            } else {
                $stmt->close();

                // Check if camera_model exists in CameraModels
                $stmt = $conn->prepare("SELECT camera_model FROM CameraModels WHERE camera_model = ?");
                if (!$stmt) die("Camera SELECT prepare failed: " . $conn->error);
                $stmt->bind_param("s", $camera_model);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows === 0 && !empty($camera_model)) {
                    // Insert into CameraModels
                    $stmt->close();
                    $stmt = $conn->prepare("INSERT INTO CameraModels (camera_model, camera_manufacturer, device) VALUES (?, NULL, NULL)");
                    if (!$stmt) die("Camera INSERT prepare failed: " . $conn->error);
                    $stmt->bind_param("s", $camera_model);
                    $stmt->execute();
                }
                $stmt->close();

                // Insert into Photos
                $stmt = $conn->prepare("INSERT INTO Photos 
                    (user_id, resolution_x, resolution_y, camera_model, image_format, image_description, aperture, shutter_speed, iso, focal_length, geolocation, image_path)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                if (!$stmt) die("Photos INSERT prepare failed: " . $conn->error);
                $stmt->bind_param("iiisssssisss", $user_id, $resX, $resY, $camera_model, $imageFormat, $image_description, $aperture, $shutter_speed, $iso, $focal_length, $geolocation, $filePath);

                if ($stmt->execute()) {
                    $message = "Upload and metadata saved successfully!";
                } else {
                    $message = "Failed to save to database.";
                }
                $stmt->close();
            }
        } else {
            $message = "Failed to upload file.";
        }
    }
}
?>

<html>

<head>
    <title>Upload Photo</title>
    <link rel="stylesheet" href="upload_photo.css">
</head>

<body>
    <!-- top header  -->
    <?php require_once 'header.inc.php';?>

    <!-- Main body -->
    <h2 style="text-align:center;">Upload Photo</h2>

    <?php if (!empty($message)) echo "<p class='msg'>$message</p>"; ?>

    <form method="POST" enctype="multipart/form-data">
        <h3>Photo Upload</h3>

        <label for="username">Username:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Select Photo:</label>
        <input type="file" name="photo" required>

        <label>Camera Model:</label>
        <input type="text" name="camera_model">

        <label>Image Description:</label>
        <textarea name="image_description"></textarea>

        <label>Aperture:</label>
        <input type="text" name="aperture">

        <label>Shutter Speed:</label>
        <input type="text" name="shutter_speed">

        <label>ISO:</label>
        <input type="number" name="iso">

        <label>Focal Length:</label>
        <input type="text" name="focal_length">

        <label>Geolocation:</label>
        <input type="text" name="geolocation">

        <input type="submit" value="Upload">
    </form>
</body>

</html>

<?php $conn->close(); ?>