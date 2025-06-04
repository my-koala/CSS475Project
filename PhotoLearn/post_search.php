<?php
/**
 * Created by James Brusewitz
 * Email: jbruse@uw.edu
 * Date: 6/3/2025
*/?>

<?php

// Getting the information for the config file
require_once 'config.inc.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $database, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

class PostComment {
    public $comment_id = 0;
    public $comment_user_id = 0;
    public $comment_username = "";
    public $comment_like_count = 0;
}

class Post {
    public $post_id = 0;
    public $post_username = "";
    public $post_title = "";
    public $post_timestamp = "";
    public $post_text = "";
    public $post_like_count = 0;
    
}

// Placeholder for actual search logic
$searchResultMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $searchTagsString = $_POST['tags'] ?? "";
    $searchTags = explode(",", $searchTagsString);
    $searchTags = array_map(function($searchTag) {
        // Clean trimmed tags to alphanumeric lowercase
        return preg_replace("/[^a-zA-Z0-9]/", "", trim(strtolower($searchTag)));
    }, $searchTags);
    $searchTagsString = implode(",", searchTags);
    
    $sql = "SELECT * FROM Posts";
    $sql .= " INNER JOIN Users ON Posts.user_id = Users.user_id";
    $sql .= " INNER JOIN PostPhotos ON PostPhotos.post_id = Posts.post_id";
    if (!empty($searchTagsString)) {
        $sql .= " INNER JOIN PostTags ON PostTags.post_id = Posts.post_id";
        $sql .= " WHERE PostTags.tag in (" . $searchTagsString . ")";
    }
    
    $sql .= ";";
    
    $sql_result = $conn->query($sql);
    
    $searchResultMessage = "Tags included: ";
    foreach ($searchTags as $searchTag) {
        $searchResultMessage .= $searchTag . ", ";
    }
}

?>
<html>

<head>
    <title>Photo learn</title>
    <link rel="stylesheet" href="post_search.css">
</head>

<body>
    <?php
require_once 'header.inc.php';
?>
    <div>
        <h2>Search Posts</h2>
        <form method="post" action="post_search.php">
            <input type="text" name="tags" placeholder="Enter tags separated by commas" required>
            <input type="submit" value="Search">
        </form>
        
        <?php if (!empty($searchResultMessage)): ?>
        <div class="results">
            <?php
            echo htmlspecialchars($searchResultMessage);
            ?>
        </div>
        
        <?php endif; ?>
        <?php while (!empty($sql_result) && ($row = $sql_result->fetch_assoc())): ?>
        <div class="results">
            <?php
            echo htmlspecialchars($row['post_id']) . "\n";
            echo htmlspecialchars($row['user_id']);
            ?>
        </div>
        <?php endwhile; ?>
    </div>
</body>

</html>

<?php
$conn->close();
?>
