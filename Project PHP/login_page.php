<?php
/**
 * Created by PhpStorm.
 * Modiflity by Houming Ge
 * User: markk@uw.edu
 * Date: 7/24/2018
 * Time: 2:45 PM
*/?>
<html>

<head>
    <title>Photo learn</title>
    <link rel="stylesheet" href="login_page.css">
</head>

<body>
    <?php require_once 'header.inc.php';?>
    <h2>Login Page</h2>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="post" action="login.php">
        <label for="username">Username:</label><br>
        <input type="text" name="username" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" name="password" required><br><br>

        <input type="submit" value="Login">
    </form>


</body>

</html>