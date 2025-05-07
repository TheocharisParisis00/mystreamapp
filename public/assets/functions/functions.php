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

?>