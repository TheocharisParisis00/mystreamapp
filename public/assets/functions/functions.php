<?php 

function userExists($conn, $username) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    return $stmt->fetchColumn() > 0;
}
function getUserIdByUsername($conn, $username) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    return $stmt->fetchColumn();
}
function isFollowing($conn, $followerUsername, $followedUsername) {
    $followerId = getUserIdByUsername($conn, $followerUsername);
    $followedId = getUserIdByUsername($conn, $followedUsername);

    if (!$followerId || !$followedId) return false;

    $stmt = $conn->prepare("SELECT COUNT(*) FROM user_follows WHERE follower_id = :follower AND followed_id = :followed");
    $stmt->bindParam(':follower', $followerId);
    $stmt->bindParam(':followed', $followedId);
    $stmt->execute();
    return $stmt->fetchColumn() > 0;
}
function followUser($conn, $followerUsername, $followedUsername) {
    if (!isFollowing($conn, $followerUsername, $followedUsername)) {
        $followerId = getUserIdByUsername($conn, $followerUsername);
        $followedId = getUserIdByUsername($conn, $followedUsername);

        if (!$followerId || !$followedId) return;

        $stmt = $conn->prepare("INSERT INTO user_follows (follower_id, followed_id) VALUES (:follower, :followed)");
        $stmt->bindParam(':follower', $followerId);
        $stmt->bindParam(':followed', $followedId);
        $stmt->execute();
    }
}
function unfollowUser($conn, $followerUsername, $followedUsername) {
    $followerId = getUserIdByUsername($conn, $followerUsername);
    $followedId = getUserIdByUsername($conn, $followedUsername);

    if (!$followerId || !$followedId) return;

    $stmt = $conn->prepare("DELETE FROM user_follows WHERE follower_id = :follower AND followed_id = :followed");
    $stmt->bindParam(':follower', $followerId);
    $stmt->bindParam(':followed', $followedId);
    $stmt->execute();
}
function getFollowersUsernames($conn, $username){
    $userId = getUserIdByUsername($conn, $username);
    if(!$userId) return [];
    $stmt = $conn->prepare("SELECT u.username FROM user_follows uf JOIN users u ON uf.follower_id = u.id WHERE uf.followed_id = :userId");
    $stmt->bindParam(':userId', $userId);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);

}
function getFollowingUsernames($conn, $username){
    $userId = getUserIdByUsername($conn, $username);
    if(!$userId) return [];
    $stmt = $conn->prepare("SELECT u.username FROM user_follows uf JOIN users u ON uf.followed_id = u.id WHERE uf.follower_id = :userId");
    $stmt->bindParam(':userId', $userId);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}
function toggleFollow($conn, $follower, $following){

    if (isFollowing($conn, $follower, $following)) 
    {
        unfollowUser($conn, $follower, $following);
    } else 
    {
        followUser($conn, $follower, $following);
    }
    header("Location: other_profile.php");
    exit();
}
function deleteProfile($conn, $username) {
    $stmt = $conn->prepare("DELETE FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
}
function updateProfile($conn, $username, $fieldsToUpdate) {
    if (empty($fieldsToUpdate) || !is_array($fieldsToUpdate)) {
        return false;
    }
    $allowedFields = ['username', 'name', 'surname', 'email', 'password'];
    $setParts = [];
    $params = [':original_username' => $username];

    foreach ($fieldsToUpdate as $field => $value) {
        if (!in_array($field, $allowedFields)) {
            continue;
        }
        if ($field === 'password') {
            $value = password_hash($value, PASSWORD_DEFAULT);
        }
        $setParts[] = "$field = :$field";
        $params[":$field"] = $value;
    }
    if (empty($setParts)) {
        return false;
    }

    $sql = "UPDATE users SET " . implode(', ', $setParts) . " WHERE username = :original_username";
    $stmt = $conn->prepare($sql);

    return $stmt->execute($params);
}
function createList($conn, $username, $userId, $listname, $description) {
    $stmt = $conn->prepare("INSERT INTO playlists (user_id, name, description) VALUES (:user_id, :name, :description)");
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':name', $listname);
    $stmt->bindParam(':description', $description);
    $stmt->execute();
    return $conn->lastInsertId();
}
function deleteList($conn, $listId){
    $stmt = $conn->prepare("DELETE FROM playlists WHERE id = :listId");
    $stmt->bindParam(':listId', $listId);
    $stmt->execute();
} 
function returnListName($conn, $listId) {
    $stmt = $conn->prepare("SELECT name FROM playlists WHERE id = :listId");
    $stmt->bindParam(':listId', $listId);
    $stmt->execute();
    return $stmt->fetchColumn();
}
function listExists($conn, $listname) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM playlists WHERE name = :listname");
    $stmt->bindParam(':listname', $listname);
    $stmt->execute();
    return $stmt->fetchColumn() > 0;
}
function getlist($conn, $listname){
    $sql = "
    SELECT 
        s.id,
        s.title,
        s.artist,
        s.youtube_id,
        ps.position,
        ps.added_at
    FROM playlists p
    INNER JOIN playlist_songs ps
        ON p.id = ps.playlist_id
    INNER JOIN songs s
        ON ps.song_id = s.id
    WHERE p.name = :listname
    ORDER BY ps.position ASC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':listname', $listname);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);   
}
function createSong($conn, $song) {
    $stmt = $conn->prepare("SELECT id FROM songs WHERE youtube_id = :youtube_id");
    $stmt->bindParam(':youtube_id', $song['youtube_id']);
    $stmt->execute();
    $songId = $stmt->fetchColumn();
    if ($songId) {
        return $songId;
    }
    $insert = $conn->prepare("INSERT INTO songs (title, artist, youtube_id) VALUES (:title, :artist, :youtube_id)");
    $insert->bindParam(':title', $song['title']);
    $insert->bindParam(':artist', $song['artist']);
    $insert->bindParam(':youtube_id', $song['youtube_id']);
    $insert->execute();
    return $conn->lastInsertId();
}
function addToList($conn, $songId, $listId) {
    error_log("addToList called with songId: $songId, listId: $listId");
    try {
        $stmt = $conn->prepare("SELECT COALESCE(MAX(position), 0) + 1 FROM playlist_songs WHERE playlist_id = :playlist_id");
        $stmt->bindParam(':playlist_id', $listId);
        $stmt->execute();
        $position = $stmt->fetchColumn();
        $insert = $conn->prepare("INSERT INTO playlist_songs (playlist_id, song_id, position) VALUES (:playlist_id, :song_id, :position)");
        $insert->bindParam(':playlist_id', $listId);
        $insert->bindParam(':song_id', $songId);
        $insert->bindParam(':position', $position);
        $insert->execute();
        error_log("addToList executed successfully.");
    } catch (PDOException $e) {
        error_log("Failed to add song to playlist: " . $e->getMessage());
    }
}
function deleteSong($conn, $songId){
}
function getAllPlaylists($conn, $username){
    $userd = getUserIdByUsername($conn, $username);
    $stmt = $conn->prepare("SELECT * FROM playlists WHERE user_id = :userId");
    $stmt->bindParam(':userId', $userd);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getAllFollowerPlaylists($conn, $username){
    $follows = getFollowingUsernames($conn, $username);
    $playlists = [];
    if (empty($follows)) {
        return $playlists;
    }
    $placeholders = implode(',', array_fill(0, count($follows), '?'));
    $sql = "SELECT p.* FROM playlists p
            JOIN users u ON p.user_id = u.id
            WHERE u.username IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $stmt->execute($follows);
    $playlists = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $playlists;
}
?>