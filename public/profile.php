<?php

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require_once 'config.php';
require_once 'assets/classes/User.php';
require_once 'assets/functions/functions.php';

$user = new User($conn, $_SESSION['username']);


$followers = getFollowersUsernames($conn, $user->username);
$followersCount = count($followers);



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <link rel="stylesheet" href="assets/css/theme.css">
    <link rel="stylesheet" href="assets/css/user.css">
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($user->username); ?>'s Profile</h1>
        <button id="theme-toggle">Dark/Light</button>
        <a href="userpage.php" id="go-back-button" class="button-link">Go Back</a>
        <a href="logout.php" id="sign-out" class="button-link">Sign Out</a>
    </header>
    <main class="container">
        <section>
            <h2>Info</h2>
            <p><?php echo $followersCount;?> Followers</p>
        </section>
        <section>
            <h2>My Playlists</h2>
            <p>This section will display your created playlists.</p>
        </section>
        <div style="position: fixed; bottom: 20px; right: 20px;">
            <a href="edit_profile.php" class="button-link">Edit Profile</a>
            <form method="post" style="display: inline;">
                <a href="delete_profile.php" class="button-link">Delete Profile</a>
            </form>
        </div>
    </main>
<script src="assets/js/main.js"></script>
</body>
</html>
