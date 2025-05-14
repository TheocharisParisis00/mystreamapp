<?php 
session_start();

require_once 'config.php';
require_once 'assets/functions/functions.php';

if(!listExists($conn, $_SESSION['playlist_name'])) {
    echo "<script>
        alert('Playlist does not exist.');
        window.location.href = 'userpage.php';
    </script>";
    exit();
}
$list = getlist($conn, $_SESSION['playlist_name']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($_SESSION['playlist_name']) ?></title>
    <link rel="stylesheet" href="assets/css/play.css">
    <link rel="stylesheet" href="assets/css/theme.css">
</head>
<body>
    <header class="page-header">
        <span class="playlist-name"><?php echo htmlspecialchars($_SESSION['playlist_name']); ?></span>
        <a href="userpage.php"><button class="back-button">Go Back</button></a>
    </header>

    <div class="content-container">
        <section class="playlist-list">
            <?php if (empty($list)): ?>
                <p>No songs in this playlist.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($list as $song): ?>
                        <li>
                            <?php echo htmlspecialchars($song['position']) . '. ' . htmlspecialchars($song['title']); ?>
                            <?php if (!empty($song['artist'])): ?>
                                by <?php echo htmlspecialchars($song['artist']); ?>
                            <?php endif; ?>
                            <button onclick="playSong('<?php echo htmlspecialchars($song['youtube_id']); ?>')">
                                Play
                            </button>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

        <section class="player-section">
            <iframe
                id="videoPlayer"
                width="100%"
                height="360"
                frameborder="0"
                allow="autoplay; encrypted-media"
                allowfullscreen>
            </iframe>
        </section>
    </div>

    <script src="assets/js/play.js"></script>
    <script src="assets/js/main.js"></script>
</html>