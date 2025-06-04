<?php
session_start();

/**
 * Created by Houming Ge
 * User: houming@uw.edu
 * Date: 6/2/2025
 */

// Check if admin is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login_page.php");
    exit();
}

require_once 'config.inc.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $database, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get total user count
$total_users = 0;
$user_result = $conn->query("SELECT COUNT(*) as count FROM Users");
if ($row = $user_result->fetch_assoc()) {
    $total_users = $row['count'];
}

// Get user list with subscription info
$sql = "SELECT u.username, s.plan, s.date_start, s.date_end 
        FROM Users u
        LEFT JOIN Subscriptions s ON u.user_id = s.user_id";
$result = $conn->query($sql);
?>

<html>

<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_dashboard.css">
</head>

<body>
    <!-- top header  -->
    <?php require_once 'header.inc.php';?>

    <!-- Main body -->

    <h2>Admin Dashboard</h2>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
    <p>Total Users: <?php echo $total_users; ?></p>

    <h3>User Subscriptions</h3>
    <table>
        <tr>
            <th>Username</th>
            <th>Plan</th>
            <th>Start Date</th>
            <th>End Date</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['username']); ?></td>
            <td><?php echo htmlspecialchars($row['plan'] ?? 'None'); ?></td>
            <td><?php echo htmlspecialchars($row['date_start'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($row['date_end'] ?? ''); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

</body>

</html>

<?php
$conn->close();
?>