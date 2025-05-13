<?php

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require_once 'config.php';
require_once 'assets/functions/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_playlist') {
    $playlistName = $_POST['playlist_name'] ?? '';
    $description = $_POST['description'] ?? '';
    $username = $_SESSION['username'];
    $userId = getUserIdByUsername($conn, $username);

    if (!empty($playlistName)) {
        $_SESSION['playlist_id'] = createList($conn, $username, $userId, $playlistName, $description);
        header("Location: add_songs.php");
        exit();
    } else {
        echo "<p>Please enter a playlist name.</p>";
    }
} 

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/theme.css">
    <link rel="stylesheet" href="assets/css/create_playlist.css">

    <title>Create New Playlist</title>
</head> 
<body>
  <div class="top-right">
    <a href="userpage.php"><button>Go back</button></a>
  </div>
  <div class="form-container">
    <form method="POST" action="">
      <input type="hidden" name="action" value="create_playlist">

      <label for="playlist_name">Playlist Name:</label><br>
      <input type="text" id="playlist_name" name="playlist_name" required><br><br>

      <label for="description">Description:</label><br>
      <textarea id="description" name="description" rows="4" cols="50"></textarea><br><br>

      <input type="submit" value="Create Playlist">
    </form>
  </div>
  <script src="assets/js/main.js"></script>
</body>
</html>