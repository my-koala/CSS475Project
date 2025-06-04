<?php
/**
 * Created by James Brusewitz
 * Email: jbruse@uw.edu
 * Date: 6/3/2025
*/?>

<?php

// Getting the information for the config file
require_once 'config.inc.php';

// Placeholder for actual search logic
$searchResults = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $searchTagsString = $_POST['tags'] ?? "";
    $searchTags = explode(",", $searchTagsString);
    
    $searchTags = array_map(function($searchTag) {
        // Clean trimmed tags to alphanumeric lowercase
        return preg_replace("/[^a-zA-Z0-9]/", "", trim(strtolower($searchTag)));
    }, $searchTags);
    
    $sql = "SELECT * FROM Posts";
    
    $searchResults = "Tags included: ";
    foreach ($searchTags as $searchTag) {
        $searchResults .= $searchTag . " | ";
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

        <?php if ($searchResults): ?>
        <div class="results">
            <?php
            echo $searchResults;
            ?>
        </div>
        <?php endif; ?>
    </div>
</body>

</html>