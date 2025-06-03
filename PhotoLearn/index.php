<?php
/**
 * Created by PhpStorm.
 * Modiflity by Houming Ge
 * User: markk@uw.edu
 * Date: 7/24/2018
 * Time: 2:45 PM
*/?>

<?php
// Placeholder for actual search logic
$searchResults = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $searchTerm = $_POST['search'] ?? '';

    $searchResults = "You searched for: <strong>" . htmlspecialchars($searchTerm) . "</strong><br>";
    $searchResults .= "<em>[SQL query would be executed here]</em>";
}
?>
<html>

<head>
    <title>Photo learn</title>
    <link rel="stylesheet" href="index.css">
</head>

<body>
    <?php
require_once 'header.inc.php';
?>
    <div>
        <h2>About</h2>
        <form method="post" action="index.php">
            <input type="text" name="search" placeholder="Enter search term..." required>
            <input type="submit" value="Search">
        </form>

        <?php if ($searchResults): ?>
        <div class="results">
            <?php echo $searchResults; ?>
        </div>
        <?php endif; ?>
    </div>
</body>

</html>