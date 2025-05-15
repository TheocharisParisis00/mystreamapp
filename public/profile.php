<?php

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require_once 'config.php';

require_once 'assets/functions/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_playlist'])) {
    $playlistId = $_POST['playlist_id'];
    deleteList($conn, $playlistId);
    header('Location: profile.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['play_playlist'])) {
    $_SESSION['playlist_name'] = $_POST['playlist_name'];
    header('Location: playlist_player.php');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_songs'])) {
    $_SESSION['playlist_id'] = $_POST['playlist_id'];
    header('Location: add_songs.php');
    exit();
}

$followers = getFollowersUsernames($conn, $_SESSION['username']);
$followersCount = count($followers);
$playlists = getAllPlaylists($conn, $_SESSION['username']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <link rel="stylesheet" href="assets/css/theme.css">
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="stylesheet" href="assets/css/profile.css">
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($_SESSION['username']); ?>'s Profile</h1>
        <button id="theme-toggle">Dark/Light</button>
        <a href="userpage.php" id="go-back-button" class="button-link">Go Back</a>
        <a href="logout.php" id="sign-out" class="button-link">Sign Out</a>
    </header>
    <main class="container">
        <section>
            <h2><?php echo $followersCount;?> Followers</h2>
        </section>
        <section>
            <h2>My Playlists</h2>
            <ul>
                <?php if (empty($playlists)): ?>
                    <p>No playlists available.</p>
                <?php else: ?>
                    <?php foreach ($playlists as $playlist): ?>
                        <li class="playlist-item">
                            <div class="playlist-info">
                                <span class="playlist-name"><?php echo htmlspecialchars($playlist['name']); ?></span>
                                <div class="playlist-actions">
                                    <form method="post" class="playlist-action-form">
                                        <input type="hidden" name="playlist_name" value="<?php echo htmlspecialchars($playlist['name']); ?>">
                                        <button type="submit" name="play_playlist">Play</button>
                                    </form>
                                    <form method="post" class="playlist-action-form">
                                        <input type="hidden" name="playlist_id" value="<?php echo htmlspecialchars($playlist['id']); ?>">
                                        <button type="submit" name="add_songs">Add Songs</button>
                                    </form>
                                    <form method="post" class="playlist-action-form">
                                        <input type="hidden" name="playlist_id" value="<?php echo htmlspecialchars($playlist['id']); ?>">
                                        <button type="submit" name="delete_playlist">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
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
