<?php 
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require_once 'config.php';
require_once 'assets/classes/User.php';
require_once 'assets/functions/functions.php';

if (!userExists($conn, $_SESSION['other_profile'])) {
    echo "<script>
        alert('User does not exist.');
        window.history.back();
    </script>";
    exit();
}
$following = isFollowing($conn, $_SESSION['username'], $_SESSION['other_profile']);
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle'])) 
{
    toggleFollow($conn, $_SESSION['username'], $user->username);
}
$followres = getFollowersUsernames($conn, $_SESSION['other_profile']);
$followers = count($followres);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/theme.css">
    <link rel="stylesheet" href="assets/css/other_profile.css">
    <title><?php echo htmlspecialchars($_SESSION['other_profile']); ?></title>
</head>
<body>
<header class="profile-header">
  <h1><?php echo htmlspecialchars($_SESSION['other_profile']); ?></h1>
  <div class="actions">
    <span class="followers"><?php echo $followers; ?> Followers</span>
    <form method="post" action="" class="follow-form">
      <input type="hidden" name="followee" value="<?php echo htmlspecialchars($_SESSION['other_profile']); ?>">
      <button id="follow-toggle" type="submit" name="toggle">
        <?php echo $following ? 'Following' : 'Follow'; ?>
      </button>
    </form>
  </div>
  <div class="right-actions">
    <button id="theme-toggle">Dark/Light</button>
    <a href="userpage.php"><button>Go Back</button></a>
  </div>
</header>
<script src="assets/js/main.js"></script>
</body>
</html>