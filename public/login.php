<?php
session_start();
$login_error = '';
$success_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config.php';

    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $success_message = "Login successful! Redirecting to your dashboard...";
            header("Refresh: 2; URL=userpage.php");
        } else {
            $login_error = "Invalid username or password. Please try again.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Log In - MyStreamApp</title>
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
        <h2>Welcome Back</h2>
        <?php if (!empty($success_message)): ?>
            <div class="success-message" style="color:green; margin-bottom:10px;">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($login_error)): ?>
            <div class="error-message" style="color:red; margin-bottom:10px;">
                <?php echo $login_error; ?>
            </div>
        <?php endif; ?>
        <form action="login.php" method="POST" class="card">
            <input type="text" name="username" placeholder="Username" required><br><br>
            <input type="password" name="password" placeholder="Password" required><br><br>
            <button type="submit">Log In</button>
        </form>
    </main>

    <script src="assets/js/main.js"></script>
</body>
</html>
