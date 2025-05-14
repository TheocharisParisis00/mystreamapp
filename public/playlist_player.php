<?php 
session_start();

require_once 'config.php';
require_once 'assets/functions/functions.php';

// By this point you’ve already authenticated and confirmed the playlist exists
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
        <button class="back-button" onclick="window.history.back();">Go Back</button>
        <span class="playlist-name"><?php echo htmlspecialchars($_SESSION['playlist_name']); ?></span>
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
                height="400"
                frameborder="0"
                allow="autoplay; encrypted-media"
                allowfullscreen>
            </iframe>
        </section>
    </div>

    <script>
        function playSong(videoId) {
            document.getElementById('videoPlayer').src =
                'https://www.youtube.com/embed/' + videoId + '?autoplay=1';
        }
    </script>
    <script src="assets/js/main.js"></script>
</html>