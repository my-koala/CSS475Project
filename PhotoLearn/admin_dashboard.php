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

$message = "";

// Handle ban form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['ban_user'])) {
        $ban_user_id = $_POST['ban_user_id'] ?? '';
        $ban_reason = $_POST['ban_reason'] ?? '';
        $ban_start = date("Y-m-d");
        $ban_end = date("Y-m-d", strtotime("+1 year"));

        if (!empty($ban_user_id) && !empty($ban_reason)) {
            $stmt = $conn->prepare("INSERT INTO Bans (reason, user_id, ban_start, ban_end) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("siss", $ban_reason, $ban_user_id, $ban_start, $ban_end);
            if ($stmt->execute()) {
                $message = "User $ban_user_id has been banned.";
            } else {
                $message = "Failed to ban user.";
            }
            $stmt->close();
        }
    }

    // Handle unban form
    if (isset($_POST['unban_user'])) {
        $unban_user_id = $_POST['unban_user_id'] ?? '';

        if (!empty($unban_user_id)) {
            $stmt = $conn->prepare("DELETE FROM Bans WHERE user_id = ?");
            $stmt->bind_param("i", $unban_user_id);
            if ($stmt->execute()) {
                $message = "User $unban_user_id has been unbanned.";
            } else {
                $message = "Failed to unban user.";
            }
            $stmt->close();
        }
    }
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


    <!-- Ban Form -->
    <h3>Ban a User</h3>
    <form method="POST">
        <label>User ID:</label>
        <input type="text" name="ban_user_id" required>
        <label>Reason:</label>
        <textarea name="ban_reason" required></textarea>
        <input type="submit" name="ban_user" value="Ban User">
    </form>

    <!-- Unban Form -->
    <h3>Unban a User</h3>
    <form method="POST">
        <label>User ID:</label>
        <input type="text" name="unban_user_id" required>
        <input type="submit" name="unban_user" value="Unban User">
    </form>

</body>

</html>

<?php
$conn->close();
?>