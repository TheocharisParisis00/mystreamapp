<?php
session_start();
if (!isset($_SESSION['username'])) 
{
    header("Location: login.php");
    exit();
}

require_once 'config.php';
require_once 'assets/functions/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['video_id'], $_POST['title'], $_POST['artist'])) 
{
    $title = $_POST['title'];
    $artist = $_POST['artist'];
    $youtubeId = $_POST['video_id'];
    $song = [
        'title' => $title,
        'artist' => $artist,
        'youtube_id' => $youtubeId
    ];
    $createdId = createSong($conn, $song);
    if (!isset($_SESSION['playlist_id'])) {
        error_log("Playlist ID is not set in session.");
        die("Playlist ID is missing.");
    }
    error_log("Adding song ID $createdId to playlist ID " . $_SESSION['playlist_id']);
    addToList($conn, $createdId, $_SESSION['playlist_id']);
}

$apiKey = 'AIzaSyAvvIRNU7TgPEemKZ8nTpxfwLSj2vrCMZQ'; 
$searchResults = [];

if (isset($_GET['query']) && !empty($_GET['query'])) 
{
    $query = urlencode($_GET['query']);
    $maxResults = 10;
    $url = "https://www.googleapis.com/youtube/v3/search?part=snippet&q={$query}&maxResults={$maxResults}&type=video&key={$apiKey}";
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    if (!empty($data['items'])) 
    {
        foreach ($data['items'] as $item) 
        {
            $videoId = $item['id']['videoId'];
            $title = $item['snippet']['title'];
            $artist = $item['snippet']['channelTitle'];
            $searchResults[] = ['videoId' => $videoId, 'title' => $title, 'artist' => $artist];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Songs</title>
</head>
<body>
<h1><?= $_SESSION['playlist_id']; ?></h1>
<a href="userpage.php"><button>Go back</button></a>
<h2>Search for Songs to Add</h2>
<form method="GET" action="">
    <input type="text" name="query" placeholder="Search for songs..." required>
    <button type="submit">Search</button>
</form>

<?php if (!empty($searchResults)): ?>
    <h3>Results:</h3>
    <ul>
        <?php foreach ($searchResults as $song): ?>
            <li>
                <?php echo htmlspecialchars($song['title']); ?>
                <form method="POST" action="" style="display:inline;">
                    <input type="hidden" name="video_id" value="<?php echo htmlspecialchars($song['videoId']); ?>">
                    <input type="hidden" name="title" value="<?php echo htmlspecialchars($song['title']); ?>">
                    <input type="hidden" name="artist" value="<?php echo htmlspecialchars($song['artist']); ?>">
                    <button type="submit">Add</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
</body>
</html>
