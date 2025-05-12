<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
require_once 'config.php';
require_once 'assets/functions/functions.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Songs</title>
</head>
<body>
</body>
    <h2>Search for Songs to Add</h2>
    <form method="GET" action="">
        <input type="text" name="query" placeholder="Search for songs..." required>
        <button type="submit">Search</button>
    </form>
</html>


