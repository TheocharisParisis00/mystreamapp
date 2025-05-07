<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config.php';

    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure password hashing
    $user_id = rand(100, 999);

    try {
        // Check if username or email already exists
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
        $checkStmt->bindParam(':username', $username);
        $checkStmt->bindParam(':email', $email);
        $checkStmt->execute();

        if ($checkStmt->rowCount() > 0) {
            echo "<script>alert('Username or Email already exists. Please try another.'); window.location.href='signup.php';</script>";
            exit();
        }

        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (id, name, surname, username, email, password) VALUES (:id, :name, :surname, :username, :email, :password)");
        $stmt->bindParam(':id', $user_id);
        $stmt->bindParam(':name', $first_name);
        $stmt->bindParam(':surname', $last_name);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        
        // Redirect after successful signup
        header("Location: login.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up - MyStreamApp</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/theme.css">
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>
    <header>
        <h1>MyStreamApp</h1>
        <div class="header-buttons">
            <a href="index.html" class="button-link">Home</a>
            <button id="theme-toggle">Dark/Light</button>
        </div>
    </header>
    <main class="container">
        <h2>Create an Account</h2>
        <form action="signup.php" method="POST" class="card">
            <input type="text" name="first_name" placeholder="First Name" required><br><br>
            <input type="text" name="last_name" placeholder="Last Name" required><br><br>
            <input type="text" name="username" placeholder="Username" required><br><br>
            <input type="email" name="email" placeholder="Email" required><br><br>
            <input
    type="password"
    name="password"
    placeholder="Password"
    required
    pattern="(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).+"
    title="Password must contain at least one uppercase letter, one number, and one special character"
  ><br><br>
            <button type="submit">Sign Up</button>
        </form>
    </main>

    <script src="assets/js/main.js"></script>
</body>
</html>
