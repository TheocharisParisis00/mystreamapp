<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once 'config.php';
require_once 'assets/functions/functions.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['other_username'])) {
    $_SESSION['other_profile'] = $_POST['other_username'];
    header("Location: other_profile.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['playlist_name'])) {
    $_SESSION['playlist_name'] = $_POST['playlist_name'];
    header("Location: playlist_player.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Userpge</title>
    <link rel="stylesheet" href="assets/css/theme.css">
    <link rel="stylesheet" href="assets/css/user.css">
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <button id="theme-toggle">Dark/Light</button>
        <a href="profile.php" id="profile-button" class="button-link">Profile</a>
        <a href="logout.php" id="sign-out" class="button-link">Sign Out</a>
    </header>
    <main class="container">

        <section id="user-search">
            <h3>Search Users</h3>
            <form id="user-search-form" method="post">
                <input
                    type="text"
                    id="user-search-query"
                    name="other_username"
                    placeholder="Search for a user..."
                    required
                />
                <button type="submit">Go to Profile</button>
            </form>
            <div id="user-search-results"></div>
        </section>

        <section id="playlist-search">
            <h3>Search Playlists</h3>
            <form id="search-form" method="post">
                <input
                    name="playlist_name"
                    type="text"
                    id="search-query"
                    placeholder="Search for a playlist..."
                    required
                />
                <button type="submit">Search</button>
            </form>
        </section>
        <section id="show_playlists"></section>
        <div style="position: fixed; bottom: 20px; right: 20px;">
            <a href="create_playlist.php" class="button-link">Create New Playlist</a>
        </div>
    </main>
<script src="assets/js/main.js"></script>
</body>
</html>
