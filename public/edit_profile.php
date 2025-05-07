<?php 
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
require_once 'config.php';
require_once 'assets/functions/functions.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $originalUsername = $_POST['original_username'];
    $fieldsToUpdate = [];

    $allowedFields = ['username', 'name', 'surname', 'email', 'password'];
    foreach ($allowedFields as $field) {
        if (!empty($_POST[$field])) {
            $fieldsToUpdate[$field] = $_POST[$field];
        }
    }

    // Prevent username duplication if changed
    if (isset($fieldsToUpdate['username']) && $fieldsToUpdate['username'] !== $originalUsername) {
        if (userExists($conn, $fieldsToUpdate['username'])) {
            echo "<p style='color: red;'>Username already taken.</p>";
            return;
        }
    }

    if (updateProfile($conn, $originalUsername, $fieldsToUpdate)) {
        $_SESSION['username'] = $fieldsToUpdate['username'] ?? $originalUsername;
        $_SESSION['profile_updated'] = true;
        header("Location: edit_profile.php");
        exit();
    } else {
        echo "<p style='color: red;'>Failed to update profile.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/theme.css">
    <link rel="stylesheet" href="assets/css/edit_profile.css">
    <title>Profile Update</title>
</head>
<body>
<?php
if (isset($_SESSION['profile_updated']) && $_SESSION['profile_updated']) {
    echo "<script>alert('Profile has been successfully updated.');</script>";
    unset($_SESSION['profile_updated']);
}
?>
<div class="top-right">
    <a href="profile.php"><button>Go back</button></a>
</div>

<div class="container">
    <h2>Edit Your Profile</h2>
    <form action="edit_profile.php" method="POST">
        <input type="hidden" name="original_username" value="<?php echo htmlspecialchars($_SESSION['username']); ?>">

        <label for="username">New Username:</label>
        <input type="text" id="username" name="username" placeholder="Enter new username">

        <label for="name">First Name:</label>
        <input type="text" id="name" name="name" placeholder="Enter first name">

        <label for="surname">Last Name:</label>
        <input type="text" id="surname" name="surname" placeholder="Enter last name">

        <label for="email">Email Address:</label>
        <input type="email" id="email" name="email" placeholder="Enter email">

        <label for="password">New Password:</label>
        <input type="password" id="password" name="password" placeholder="Enter new password (optional)" 
               pattern="^(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*]).{8,}$" 
               title="Password must contain at least 8 characters, one number, one uppercase letter, and one special character (e.g. !@#$%^&*).">

        <button type="submit" name="update_profile">Update Profile</button>
    </form>
</div>
<script src="assets/js/main.js"></script>
</body>
</html>
