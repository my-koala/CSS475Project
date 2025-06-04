<?php
require_once 'config.inc.php';
$conn = new mysqli($servername, $username, $password, $database, $port);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);


$message = "";
$error = "";

// $user_id = '';
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
    $stmt->bind_result($campaign_id, $username, $title, $campaign_start, $campaign_end);

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

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['create_campaign'])) {
    $user_id = intval($_POST['user_id']);
    $title = trim($_POST['title']);
    $campaign_start = $_POST['campaign_start'];
    $campaign_end = $_POST['campaign_end'];

    if (!empty($user_id) && !empty($title) && !empty($campaign_start) && !empty($campaign_end)) {
        $stmt = $conn->prepare("INSERT INTO Campaigns (user_id, title, campaign_start, campaign_end) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $title, $campaign_start, $campaign_end);

        if ($stmt->execute()) {
            $message = "Campaign created successfully!";
            $error = "";
        } else {
            $error =  "Failed to create campaign: " . htmlspecialchars($stmt->error);
        }

        $stmt->close();
    } else {
        $error = "All fields are required to create a campaign.";
    }
}
?>

<html>

<head>
    <title>Campaign Page</title>
    <link rel="stylesheet" href="marketing_dashboard.css">
</head>

<body>
    <!-- top header  -->
    <?php require_once 'header.inc.php';?>

    <!-- Main body -->

    <h2>Search Campaign by User ID</h2>

    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if (!empty($message)) echo "<p style='color:green;'>$message</p>"; ?>

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

    <h3>Create a New Campaign</h3>
    <form method="post">
        <label for="user_id">Your User ID:</label><br>
        <input type="number" name="user_id" id="user_id" required><br><br>

        <label for="title">Campaign Title:</label><br>
        <input type="text" name="title" id="title" required><br><br>

        <label for="campaign_start">Start Date:</label><br>
        <input type="date" name="campaign_start" id="campaign_start" required><br><br>

        <label for="campaign_end">End Date:</label><br>
        <input type="date" name="campaign_end" id="campaign_end" required><br><br>

        <input type="submit" name="create_campaign" value="Create Campaign">
    </form>


</body>

</html>

<?php $conn->close(); ?>