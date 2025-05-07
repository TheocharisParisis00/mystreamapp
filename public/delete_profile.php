<?php 

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
require_once 'config.php';
require_once 'assets/functions/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete']) && $_POST['confirm_delete'] === 'yes') {
    deleteProfile($conn, $_SESSION['username']);
    session_destroy();
    header("Location: login.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/theme.css">
    <link rel="stylesheet" href="assets/css/delete_profile.css">
    <title>Delete Profile</title>
</head>
<body>
    <h1>Are you sure?</h1>
    <form method="post">
        <input type="hidden" name="confirm_delete" value="yes">
        <button type="submit">Delete Profile</button>
    </form>
    <a href="profile.php" class="">Go Back</a>
    <script src="assets/js/main.js"></script>
</body>

</html>
