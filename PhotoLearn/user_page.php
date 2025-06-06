<?php
session_start();

/**
 * Created by Houming Ge
 * User: houming@uw.edu
 * Date: 6/4/2025
 */

require_once 'config.inc.php';

// Connect to DB
$conn = new mysqli($servername, $username, $password, $database, $port);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$message = "";

// Create user
if (isset($_POST['create_user'])) {
    $username = trim($_POST['username']);
    $password = hash("sha256", $_POST['password']);
    $birthdate = $_POST['birthdate'];
    $email = trim($_POST['email']);
    $display_name = trim($_POST['display_name']);
    $joined_date = date("Y-m-d");
    $private_acc = 1;

    $stmt = $conn->prepare("INSERT INTO Users (username, pass_hash, birthdate, email, display_name, join_date, private_acc) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssi", $username, $password, $birthdate, $email, $display_name, $joined_date, $private_acc);
    if ($stmt->execute()) {
        $message = "User created with ID: " . $stmt->insert_id;
    } else {
        $message = "Error creating user: " . $stmt->error;
    }
    $stmt->close();
}

// Update user
if (isset($_POST['update_user'])) {
    $user_id = intval($_POST['update_user_id']);
    $username = trim($_POST['username']);
    $password = !empty($_POST['password']) ? hash("sha256", $_POST['password']) : null;
    $birthdate = $_POST['birthdate'];
    $email = trim($_POST['email']);
    $display_name = trim($_POST['display_name']);
    $profile_photo_id = $_POST['profile_photo_id'] !== '' ? intval($_POST['profile_photo_id']) : null;

    $fields = [];
    $params = [];
    $types = "";

    if (!empty($username)) { $fields[] = "username = ?"; $params[] = $username; $types .= "s"; }
    if (!empty($password)) { $fields[] = "pass_hash = ?"; $params[] = $password; $types .= "s"; }
    if (!empty($birthdate)) { $fields[] = "birthdate = ?"; $params[] = $birthdate; $types .= "s"; }
    if (!empty($email)) { $fields[] = "email = ?"; $params[] = $email; $types .= "s"; }
    if (!empty($display_name)) { $fields[] = "display_name = ?"; $params[] = $display_name; $types .= "s"; }
    if ($profile_photo_id !== null) { $fields[] = "profile_photo_id = ?"; $params[] = $profile_photo_id; $types .= "i"; }

    if (!empty($fields)) {
        $types .= "i"; // For user_id
        $params[] = $user_id;

        $sql = "UPDATE Users SET " . implode(", ", $fields) . " WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            $message = "User updated.";
        } else {
            $message = "Update failed: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "No fields to update.";
    }

}

// Delete user
if (isset($_POST['delete_user'])) {
    $user_id = intval($_POST['delete_user_id']);
    $username = trim($_POST['delete_username']);
    $stmt = $conn->prepare("DELETE FROM Users WHERE user_id = ? AND username = ?");
    $stmt->bind_param("is", $user_id, $username);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        $message = "User deleted.";
    } else {
        $message = "No matching user found.";
    }
    $stmt->close();
}

$user_list = $conn->query("SELECT user_id, username, display_name, join_date FROM Users");

// block form handling
if (isset($_POST['ban_submit'])) {
    $your_id = intval($_POST['your_user_id']);
    $target_id = intval($_POST['target_user_id']);

    if ($your_id !== $target_id) {
        $stmt = $conn->prepare("INSERT INTO UserBlocks (blocker_id, blockee_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $your_id, $target_id);
        if ($stmt->execute()) {
            $message = "User $target_id has been blocks.";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "You cannot blocks yourself";
    }
}

// Unblock form handling
if (isset($_POST['unban_submit'])) {
    $your_id = intval($_POST['your_user_id_unban']);
    $target_id = intval($_POST['target_user_id_unban']);

    if ($your_id !== $target_id) {
        $stmt = $conn->prepare("DELETE FROM UserBlocks WHERE blocker_id = ?" . " AND blockee_id = ?");
        $stmt->bind_param("ii", $your_id, $target_id);
        if ($stmt->execute()) {
            $message = "User $target_id has been unblocks.";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "You cannot unblocks yourself.";
    }
}

// block form handling
if (isset($_POST['Follows_submit'])) {
    $your_id = intval($_POST['your_user_id']);
    $target_id = intval($_POST['target_user_id']);

    if ($your_id !== $target_id) {
        $stmt = $conn->prepare("INSERT INTO UserFollows (follower_id, followee_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $your_id, $target_id);
        if ($stmt->execute()) {
            $message = "User $target_id has been Follows.";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "You cannot Follows yourself";
    }
}

// Unblock form handling
if (isset($_POST['unFollows_submit'])) {
    $your_id = intval($_POST['your_user_id_unFollows']);
    $target_id = intval($_POST['target_user_id_unFollows']);

    if ($your_id !== $target_id) {
        $stmt = $conn->prepare("DELETE FROM UserFollows WHERE follower_id = ?" . " AND followee_id = ?");
        $stmt->bind_param("ii", $your_id, $target_id);
        if ($stmt->execute()) {
            $message = "User $target_id has been unFollows.";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "You cannot unFollows yourself.";
    }
}
?>


<html>

<head>
    <title>User Management</title>
    <link rel="stylesheet" href="user_page.css">
</head>

<body>
    <!-- top header  -->
    <?php require_once 'header.inc.php';?>

    <!-- Main body -->
    <h2>User Management</h2>
    <?php if (!empty($message)) echo "<p><strong>$message</strong></p>"; ?>

    <!-- Create User -->
    <form method="post">
        <h3>Create User</h3>
        <label>Username:</label>
        <input type="text" name="username" required>
        <label>Password:</label>
        <input type="password" name="password" required>
        <label>Birthdate:</label>
        <input type="date" name="birthdate">
        <label>Email:</label>
        <input type="email" name="email">
        <label>Display Name:</label>
        <input type="text" name="display_name">
        <input type="submit" name="create_user" value="Create User">
    </form>

    <!-- Update User -->
    <form method="post">
        <h3>Update User</h3>
        <label>User ID:</label>
        <input type="number" name="update_user_id" required>
        <label>New Username:</label>
        <input type="text" name="username">
        <label>New Password:</label>
        <input type="password" name="password">
        <label>Birthdate:</label>
        <input type="date" name="birthdate">
        <label>Email:</label>
        <input type="email" name="email">
        <label>Display Name:</label>
        <input type="text" name="display_name">
        <label>Profile Photo ID:</label>
        <input type="number" name="profile_photo_id">
        <input type="submit" name="update_user" value="Update User">
    </form>

    <!-- Delete User -->
    <form method="post">
        <h3>Delete User</h3>
        <label>User ID:</label>
        <input type="number" name="delete_user_id" required>
        <label>Username:</label>
        <input type="text" name="delete_username" required>
        <input type="submit" name="delete_user" value="Delete User">
    </form>

    <h3>Registered Users</h3>
    <table>
        <tr>
            <th>User ID</th>
            <th>Username</th>
            <th>Display Name</th>
            <th>Date Created</th>
        </tr>
        <?php
    $today = new DateTime();
    while ($row = $user_list->fetch_assoc()):
        $joined = new DateTime($row['join_date']);
        $diffDays = $today->diff($joined)->days;

        // Determine status
        if ($diffDays <= 7) {
            $status = "<span style='color: silver;'>Silver</span>";
        } elseif ($diffDays <= 30) {
            $status = "<span style='color: gold;'>Gold</span>";
        } elseif ($diffDays <= 365) {
            $status = "<span style='color: darkorange;'>God</span>";
        } else {
            $status = "<span style='color: gray;'>Veteran</span>";
        }
    ?>
        <tr>
            <td><?= htmlspecialchars($row['user_id']) ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['display_name']) ?></td>
            <td><?= $status ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <?php if (!empty($message)) echo "<p><strong>$message</strong></p>"; ?>

    <h3>blocks a User</h3>
    <form method="post">
        <label>Your User ID:</label><br>
        <input type="number" name="your_user_id" required><br><br>

        <label>User ID to blocks:</label><br>
        <input type="number" name="target_user_id" required><br><br>

        <input type="submit" name="ban_submit" value="Ban User">
    </form>

    <h3>unblocks a User</h3>
    <form method="post">
        <label>Your User ID:</label><br>
        <input type="number" name="your_user_id_unban" required><br><br>

        <label>User ID to unblocks:</label><br>
        <input type="number" name="target_user_id_unban" required><br><br>

        <input type="submit" name="unban_submit" value="Unban User">
    </form>

    <h3>Follows a User</h3>
    <form method="post">
        <label>Your User ID:</label><br>
        <input type="number" name="your_user_id" required><br><br>

        <label>User ID to Follows:</label><br>
        <input type="number" name="target_user_id" required><br><br>

        <input type="submit" name="Follows_submit" value="Follows User">
    </form>

    <h3>unFollows a User</h3>
    <form method="post">
        <label>Your User ID:</label><br>
        <input type="number" name="your_user_id_unFollows" required><br><br>

        <label>User ID to Follows:</label><br>
        <input type="number" name="target_user_id_unFollows" required><br><br>

        <input type="submit" name="unFollows_submit" value="UnFollows User">
    </form>

</body>

</html>

<?php $conn->close(); ?>