<?php
require_once 'config.inc.php';
$conn = new mysqli($servername, $username, $password, $database, $port);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$user_id = '';
$campaign = null;
$campaignPosts = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_user_id'])) {
    $user_id = intval($_POST['search_user_id']);

    // Get campaign info
    $stmt = $conn->prepare("
        SELECT c.campaign_id, u.username, c.title, c.campaign_start, c.campaign_end
        FROM Campaigns c
        JOIN Users u ON c.user_id = u.user_id
        WHERE c.user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    if ($stmt->fetch()) {
    $campaign = [
        'campaign_id' => $campaign_id,
        'username' => $username,
        'title' => $title,
        'campaign_start' => $campaign_start,
        'campaign_end' => $campaign_end
    ];
} else {
    $campaign = null;
}
    $stmt->close();

    // Get campaign posts if campaign exists
    if ($campaign) {
    $campaign_id = $campaign['campaign_id'];
    $stmt2 = $conn->prepare("SELECT post_id FROM CampaignPosts WHERE campaign_id = ?");
    $stmt2->bind_param("i", $campaign_id);
    $stmt2->execute();
    $stmt2->bind_result($post_id);

    $campaignPosts = [];
    while ($stmt2->fetch()) {
        $campaignPosts[] = ['post_id' => $post_id];
    }

    $stmt2->close();
}
}
?>

<html>

<head>
    <title>Campaign Page</title>
</head>

<body>
    <!-- top header  -->
    <?php require_once 'header.inc.php';?>

    <!-- Main body -->

    <h2>Search Campaign by User ID</h2>

    <form method="post">
        <label>Enter User ID:</label><br>
        <input type="number" name="search_user_id" value="<?= htmlspecialchars($user_id) ?>" required>
        <input type="submit" value="Search">
    </form>

    <?php if ($campaign): ?>
    <h3>Campaign Details</h3>
    <ul>
        <li><strong>Campaign ID:</strong> <?= htmlspecialchars($campaign['campaign_id']) ?></li>
        <li><strong>User:</strong> <?= htmlspecialchars($campaign['username']) ?></li>
        <li><strong>Title:</strong> <?= htmlspecialchars($campaign['title']) ?></li>
        <li><strong>Start:</strong> <?= htmlspecialchars($campaign['campaign_start']) ?></li>
        <li><strong>End:</strong> <?= htmlspecialchars($campaign['campaign_end']) ?></li>
    </ul>

    <h3>Campaign Posts</h3>
    <?php if (count($campaignPosts) > 0): ?>
    <table border="1" cellpadding="8">
        <tr>
            <th>Post ID</th>
        </tr>
        <?php foreach ($campaignPosts as $post): ?>
        <tr>
            <td><?= htmlspecialchars($post['post_id']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
    <p>No posts found for this campaign.</p>
    <?php endif; ?>

    <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
    <p>No campaign found for user ID <?= htmlspecialchars($user_id) ?>.</p>
    <?php endif; ?>

</body>

</html>

<?php $conn->close(); ?>