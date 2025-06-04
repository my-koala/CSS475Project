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
            echo "Post ID: " . htmlspecialchars($row['post_id']) . "<br>";
            
            $post_id = $row['post_id'];
            
            // Get user info
            $sql_user = "SELECT * FROM Posts";
            $sql_user .= " INNER JOIN Users ON Posts.user_id = Users.user_id";
            $sql_user .= " WHERE Posts.post_id = " . $post_id;
            $sql_user .= " LIMIT 1";
            
            $sql_user_result = $conn->query($sql_user)->fetch_assoc();
            if (!empty($sql_user_result)) {
                echo "Author: " . htmlspecialchars($sql_user_result['display_name']) . "<br>";
                echo "Posted: " . htmlspecialchars($sql_user_result['time_stamp']) . "<br>";
            }
            
            // Get post tags
            $sql_tags = "SELECT * FROM Posts";
            $sql_tags .= " INNER JOIN PostTags ON PostTags.post_id = Posts.post_id";
            $sql_tags .= " WHERE Posts.post_id = " . $post_id;
            
            echo "Tags:";
            $sql_tags_results = $conn->query($sql_tags);
            while (!empty($sql_tags_results) && $sql_tags_result = $sql_tags_results->fetch_assoc()) {
                echo " " . $sql_tags_result['tag_name'];
            }
            echo "<br>";
            
            // Get photos
            $sql_photos = "SELECT * FROM Posts";
            $sql_photos .= " INNER JOIN PostPhotos ON PostPhotos.post_id = Posts.post_id";
            $sql_photos .= " INNER JOIN Photos ON Photos.photo_id = PostPhotos.photo_id";
            $sql_photos .= " INNER JOIN CameraModels ON (Photos.camera_model, Photos.camera_manufacturer) = (CameraModels.camera_model, CameraModels.camera_manufacturer)";
            $sql_photos .= " WHERE Posts.post_id = " . $post_id;
            
            $sql_photos_results = $conn->query($sql_photos);
            while (!empty($sql_photos_results) && $sql_photos_result = $sql_photos_results->fetch_assoc()) {
                echo "Image path: " . $sql_photos_result['image_path'] . "<br>";
                echo "<img src=\"" . $sql_photos_result['image_path'] . "\" alt=\"image test\" width=\"512\" class=\"res_image\"><br>";
                echo "<h3>Photo Information</h3>";
                echo "Resolution: " . $sql_photos_result['resolution_x'] . "x" . $sql_photos_result['resolution_y'] . "<br>";
                echo "Camera Model: " . $sql_photos_result['camera_manufacturer'] . " " . $sql_photos_result['camera_model'] . " (" . $sql_photos_result['device'] . ")<br>";
                echo "Image Format: " . $sql_photos_result['image_format'] . "<br>";
                echo "Image Description: " . $sql_photos_result['image_description'] . "<br>";
                echo "Camera Aperture: " . $sql_photos_result['aperture'] . "<br>";
                echo "Camera Shutter Speed: " . $sql_photos_result['shutter_speed'] . "<br>";
                echo "Camera ISO: " . $sql_photos_result['iso'] . "<br>";
                echo "Camera Focal Length: " . $sql_photos_result['focal_length'] . "<br>";
                echo "Geolocation: " . $sql_photos_result['geolocation'] . "<br>";
            }
            
            // Get like count
            $sql_like_count = "SELECT COUNT(PostLikes.user_id) AS like_count FROM PostLikes";
            $sql_like_count .= " WHERE PostLikes.post_id = " . $post_id;
            $sql_like_count .= " GROUP BY PostLikes.post_id";
            
            $sql_like_count_result = $conn->query($sql_like_count)->fetch_assoc();
            if (!empty($sql_like_count_result)) {
                echo "Like Count: " . htmlspecialchars($sql_like_count_result['like_count']) . "<br>";
            }
            
            // Get likers
            $sql_likers = "SELECT * FROM PostLikes";
            $sql_likers .= " INNER JOIN Users ON Users.user_id = PostLikes.user_id";
            $sql_likers .= " WHERE PostLikes.post_id = " . $post_id;
            
            $sql_likers_results = $conn->query($sql_likers);
            echo "Liked by";
            while (!empty($sql_likers_results) && $sql_likers_result = $sql_likers_results->fetch_assoc()) {
                echo " " . $sql_likers_result['display_name'];
            }
            echo "<br>";
            
            // Get comments
            $sql_comments = "SELECT * FROM PostComments";
            $sql_comments .= " INNER JOIN Users ON Users.user_id = PostComments.user_id";
            $sql_comments .= " WHERE PostComments.post_id = " . $post_id;
            $sql_comments .= " ORDER BY PostComments.comment_timestamp";
            
            $sql_comments_results = $conn->query($sql_comments);
            echo "<h3>Comments</h3>";
            while (!empty($sql_comments_results) && $sql_comments_result = $sql_comments_results->fetch_assoc()) {
                echo $sql_comments_results['display_name'] . " commented " . $sql_comments_results['comment_timestamp'] . "<br>";
                echo $sql_comments_results['comment_text'] . "<br>";
                
            }
            
            $sql_tags_results = $conn->query($sql_photos);
            
            ?>
        </div>
        <?php endwhile; ?>
    </div>
</body>

</html>

<?php
$conn->close();
?>
