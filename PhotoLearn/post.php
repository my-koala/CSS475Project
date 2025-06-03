<?php
/**
 * Created by Houming Ge
 * User: markk@uw.edu
 * Date: 7/24/2018
 * Time: 2:45 PM
*/?>

<html>

<head>
    <title>Posts | Photo learn</title>
    <link rel="stylesheet" href="post.css">
    <?php
require_once 'config.inc.php';
?>
</head>

<body>
    <?php
require_once 'header.inc.php';
?>

    <h2>Featured Posters</h2>

    <div class="poster-container">
        <?php

$conn = new mysqli($servername, $username, $password, $database, $port);

if ($conn->connect_error) {
    echo "<p>Database connection failed: " . $conn->connect_error . "</p>";
} else {

    // TODO: Replace with our actual SQL query to fetch posters
    $sql = "SELECT * FROM posters LIMIT 5";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($poster = $result->fetch_assoc()) {
            ?>
        <div class="poster">
            <img src="<?php echo htmlspecialchars($poster['image_url'] ?? 'placeholder.jpg'); ?>" alt="Poster Image">
            <h3><?php echo htmlspecialchars($poster['title'] ?? 'Untitled'); ?></h3>
            <p><?php echo htmlspecialchars($poster['description'] ?? 'No description available.'); ?></p>
        </div>
        <?php
        }
    } else {
        echo "<p>No posters found.</p>";
    }

    $conn->close();
}
?>
    </div>
</body>

</html>