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
            if (!$stmt) {
                die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
            }
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
    
    // Update ban form
    if (isset($_POST['update_ban_user'])) {
        $update_ban_id = $_POST['update_ban_id'] ?? '';
        $update_ban_reason = $_POST['update_ban_reason'] ?? '';
        $update_ban_start = $_POST['update_start_date'] ?? '';
        $update_ban_end = $_POST['update_end_date'] ?? '';

        if (!empty($update_ban_id) && !empty($update_ban_reason) && !empty($update_ban_start) && !empty($update_ban_end))  {
            $stmt = $conn->prepare("UPDATE Bans SET reason = ?, ban_start = ?, ban_end = ? WHERE ban_id = ?");
            if (!$stmt) {
                die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
            }
            $stmt->bind_param("sssi", $update_ban_reason, $update_ban_start, $update_ban_end, $update_ban_id);
            if ($stmt->execute()) {
                $message = "User ban $update_ban_id updated.";
            } else {
                $message = "Failed to update user ban.";
            }
            $stmt->close();
        } else if (!empty($update_ban_id) && !empty($update_ban_reason)) {
            $stmt = $conn->prepare("UPDATE Bans SET reason = ? WHERE ban_id = ?");
            if (!$stmt) {
                die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
            }
            $stmt->bind_param("si", $update_ban_reason, $update_ban_id);
            if ($stmt->execute()) {
                $message = "User ban $update_ban_id updated.";
            } else {
                $message = "Failed to update user ban.";
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
        RIGHT JOIN Subscriptions s ON u.user_id = s.user_id";
$result = $conn->query($sql);

$banlist = $conn->query("
    SELECT b.ban_id, b.user_id, u.username, b.reason, b.ban_start, b.ban_end
    FROM Bans b
    JOIN Users u ON b.user_id = u.user_id
");

$user_list = $conn->query("SELECT user_id, username, display_name, email, date_created FROM Users");

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

    <?php if (!empty($message)) echo "<p class='msg'>$message</p>"; ?>

    <!-- Ban Form -->
    <h3>Ban a User</h3>
    <form method="POST">
        <label>User ID:</label>
        <input type="number" name="ban_user_id" required><br>

        <label>Reason:</label>
        <textarea name="ban_reason" required></textarea>
        <input type="submit" name="ban_user" value="Ban User">
    </form>

    <!-- Unban Form -->
    <h3>Unban a User</h3>
    <form method="POST">
        <label>User ID:</label>
        <input type="number" name="unban_user_id" required>

        <input type="submit" name="unban_user" value="Unban User">
    </form>

    <!-- Unban Form -->
    <h3>Update User Ban</h3>
    <form method="POST">
        <label>Ban ID:</label>
        <input type="number" name="update_ban_id" required><br>

        <label>Reason:</label>
        <textarea name="update_ban_reason" required></textarea><br>

        <label>Start Date:</label>
        <input type="date" name="update_ban_start_date">

        <label>End Date:</label>
        <input type="date" name="update_ban_end_date"><br>

        <input type="submit" name="update_ban_user" value="Update Ban">
    </form>

    <!-- Ban List -->
    <h3>Banned Users</h3>
    <table>
        <tr>
            <th>Ban ID</th>
            <th>User ID</th>
            <th>Username</th>
            <th>Reason</th>
            <th>Start</th>
            <th>End</th>
        </tr>
        <?php while ($row = $banlist->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['ban_id']) ?></td>
            <td><?= htmlspecialchars($row['user_id']) ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['reason']) ?></td>
            <td><?= htmlspecialchars($row['ban_start']) ?></td>
            <td><?= htmlspecialchars($row['ban_end']) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

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

    <h3>Registered Users</h3>
    <table>
        <tr>
            <th>User ID</th>
            <th>Username</th>
            <th>Display Name</th>
            <th>Email</th>
            <th>Date Created</th>
        </tr>
        <?php while ($row = $user_list->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['user_id']) ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['display_name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['date_created']) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

</body>

</html>

<?php
$conn->close();
?>