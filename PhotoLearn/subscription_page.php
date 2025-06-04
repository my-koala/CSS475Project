<?php
session_start();
/**
 * Created by Houming Ge
 * User: houming@uw.edu
*/

require_once 'config.inc.php';

$conn = new mysqli($servername, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['buy'])) {
      $username = $_POST['username'] ?? '';
      $plan = $_POST['type'] ?? '';
      $date_start = date("Y-m-d H:i:s");
      $date_end = date("Y-m-d H:i:s", strtotime("+1 year"));
  
      if (!empty($username)) {
  
          // TODO: i need to check if this part of SQL call is wortking
          $stmt = $conn->prepare("SELECT user_id FROM Users WHERE username = ?");
          $stmt->bind_param("s", $username);
          $stmt->execute();
          $stmt->bind_result($user_id);
  
          if ($stmt->fetch()) {
              $stmt->close();
  
              // Step 2: Insert into Subscriptions
              $stmt = $conn->prepare("SELECT * FROM Subscriptions WHERE user_id = ? AND plan = ?");
              $stmt->bind_param("ss", $user_id, $plan);
              $stmt->execute();
              
              if (!$stmt->fetch()) {
                $stmt->close();
              
                $insert = $conn->prepare("INSERT INTO Subscriptions (user_id, plan, date_start, date_end) VALUES (?, ?, ?, ?)");
                $insert->bind_param("isss", $user_id, $plan, $date_start, $date_end);
    
                if ($insert->execute()) {
                    $message = "Subscription purchased successfully for '$username'.";
                } else {
                    $error = "Error while creating subscription.";
                }
    
                $insert->close();
              } else {
                  $error = "Subscription already exists.";
              }
              
          } else {
              $error = "Username not found.";
          }
      } else {
          $error = "Please enter a username.";
      }
    }
    
    
    if (isset($_POST['cancel'])) {
        $rem_username = $_POST['rem_username'] ?? '';
        $rem_plan = $_POST['rem_type'] ?? '';
        
        if (!empty($rem_username)) {
  
          // TODO: i need to check if this part of SQL call is wortking
          $stmt = $conn->prepare("SELECT user_id FROM Users WHERE username = ?");
          $stmt->bind_param("s", $rem_username);
          $stmt->execute();
          $stmt->bind_result($rem_user_id);
  
          if ($stmt->fetch()) {
              $stmt->close();
  
              // Step 2: Insert into Subscriptions
              //$stmt = $conn->prepare("SELECT * FROM Subscriptions WHERE user_id = ? AND plan = ?");
              //$stmt->bind_param("ss", $rem_user_id, $rem_plan);
              //$stmt->execute();
              
              //if ($stmt->fetch()) {
              //  $stmt->close();
              
                $insert = $conn->prepare("DELETE FROM Subscriptions WHERE user_id = ? AND plan = ?");
                $insert->bind_param("is", $rem_user_id, $rem_plan);
    
                if ($insert->execute()) {
                    $message = "Subscription canceled successfully for '$rem_username'.";
                } else {
                    $error = "Error while canceling subscription.";
                }
    
                $insert->close();
              //} else {
              //    $error = "Subscription does not exist.";
              //}
              
          } else {
              $error = "Username not found.";
          }
      } else {
          $error = "Please enter a username.";
      }
    }
}

$conn->close();
?>

<html>

<head>
    <title>Subscribe | PhotoLearn</title>
    <link rel="stylesheet" href="subscription_page.css">
</head>

<body>
    <?php
require_once 'header.inc.php';
?>
    <h2>Buy Subscription</h2>

    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

    <?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>

    <form method="POST" action="subscription_page.php">
        <label for="username">Username:</label><br>
        <input type="text" name="username" required><br><br>
        
        <label for="type">Type:</label><br>
        <select name="type">
            <option value="premium">Premium</option>
            <option value="admin">Admin</option>
            <option value="marketing">Marketing</option>
        </select><br>

        <input type="submit", name="buy", value="Buy Subscription">
    </form>
    
    <h3>Cancel Subscription</h3>
    <form method="POST">
        <label>Username:</label>
        <input type="text" name="rem_username" required><br><br>
        
        <label for="rem_type">Type:</label><br>
        <select name="rem_type">
            <option value="premium">Premium</option>
            <option value="admin">Admin</option>
            <option value="marketing">Marketing</option>
        </select><br>
        
        <input type="submit" name="cancel" value="Cancel Subscription">
    </form>

</body>

</html>