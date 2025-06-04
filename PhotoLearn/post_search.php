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
// Post info to display in order:
// Post user id + name
// Post timestamp
// Post photos
// Post text

// Then all the comments
// comments
// Placeholder for actual search logic
$searchResultMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "SELECT DISTINCT Posts.post_id";
    $sql .= " FROM Posts";
    $sql .= " WHERE 0 = 0";
    
    // Tag search
    $searchTagsString = $_POST["search_tags"] ?? "";
    // Clean tags as trimmed alphanumeric lowercase
    $searchTags = explode(",", $searchTagsString);
    $searchTagsString = "";
    $searchTags = array_map(function($searchTag) {
        $searchTag = preg_replace("/[^a-zA-Z0-9]/", "", trim(strtolower($searchTag)));
        if (!empty($searchTag)) {
            return "\"" . $searchTag . "\"";
        }
        return "";
    }, $searchTags);
    if (!empty($searchTags)) {
        $searchTagsString = implode(",", $searchTags);
    }
    
    if (!empty($searchTagsString)) {
        $sql .= " AND EXISTS (";
        $sql .= " SELECT PostTags.post_id FROM PostTags";
        $sql .= " WHERE PostTags.post_id = Posts.post_id";
        $sql .= " AND PostTags.tag_name in (" . $searchTagsString . "))";
    }
    
    $searchTimeStart = $_POST["search_time_start"] ?? "";
    if (!empty($searchTimeStart)) {
        $sql .= " AND Posts.time_stamp > \"" . $searchTimeStart . "\"";
    }
    
    $searchTimeEnd = $_POST["search_time_end"] ?? "";
    if (!empty($searchTimeEnd)) {
        $sql .= " AND Posts.time_stamp < \"" . $searchTimeEnd . "\"";
    }
    
    echo "SQL query: " . $sql;
    // Search query for matching posts
    $sql_result = $conn->query($sql);
    
    $searchResultMessage = "Search ResultsTags included: " . $searchTagsString . "\n";
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
            <h3>Search Filters</h3>
            <label>Tags</label>
            <input type="text" name="search_tags" placeholder="Enter tags separated by commas">
            <br>
            <label>Start Time:</label>
            <input type="datetime-local" name="search_time_start">
            <label>End Time:</label>
            <input type="datetime-local" name="search_time_end">
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
            echo "Post ID: " . htmlspecialchars($row['post_id']) . "\n";
            
            $post_id = $row['post_id'];
            
            // Get user info
            $sql_user = "SELECT * FROM Posts";
            $sql_user .= " INNER JOIN Users ON Posts.user_id = Users.user_id";
            $sql_user .= " WHERE " . $post_id . " = Posts.post_id";
            $sql_user .= " LIMIT 1";
            
            $sql_user_result = $conn->query($sql_user)->fetch_assoc();
            if (!empty($sql_user_result)) {
                echo "Author: " . htmlspecialchars($sql_user_result['display_name']) . "\n";
                echo "Posted: " . htmlspecialchars($sql_user_result['time_stamp']) . "\n";
            }
            
            // Get photos
            $sql_photos = "SELECT * FROM Posts";
            $sql_photos .= " INNER JOIN PostPhotos ON PostPhotos.post_id = Posts.post_id";
            $sql_photos .= " INNER JOIN Photos ON Photos.photo_id = PostPhotos.photo_id";
            
            $sql_photos_results = $conn->query($sql_photos);
            while (!empty($sql_photos_results) && $sql_photos_result = $sql_photos_results->fetch_assoc()) {
                echo "Image path: " . $sql_photos_result['image_path'] . "\n";
                echo "<img src=\"" . $sql_photos_result['image_path'] . "\" alt=\"image test\">";
            }
            ?>
        </div>
        <?php endwhile; ?>
    </div>
</body>

</html>

<?php
$conn->close();
?>
