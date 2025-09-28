<?php
session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: home.php");
    exit;
}

// Check for login errors in the session
if (isset($_SESSION['login_errors'])) {
    $emailError = $_SESSION['login_errors']['emailError'] ?? '';
    $passwordError = $_SESSION['login_errors']['passwordError'] ?? '';
    $email = $_SESSION['login_errors']['email'] ?? '';
    // Unset the session variable so errors don't persist
    unset($_SESSION['login_errors']);
} else {
    $emailError = '';
    $passwordError = '';
    $email = '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login to Story Weave</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
<div class="login-container">
    <h1>Welcome Back!</h1>
    <p>Log in to continue your story.</p>

    <form action="../../controller/login_action.php" method="POST" id="loginForm" novalidate>
        <div class="input-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="your.email@example.com" value="<?php echo htmlspecialchars($email); ?>" class="<?php echo !empty($emailError) ? 'invalid' : ''; ?>">
            <div class="error-message" id="emailError"><?php echo $emailError; ?></div>
        </div>

        <div class="input-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" class="<?php echo !empty($passwordError) ? 'invalid' : ''; ?>">
            <div class="error-message" id="passwordError"><?php echo $passwordError; ?></div>
        </div>

        <div class="forgot-password">
            <a href="forget_password.php">Forgot Password?</a>
        </div>

        <button type="submit">Log In</button>

        <p class="signup-link">
            Don't have an account? <a href="registration.php">Sign Up</a>
        </p>
    </form>
</div>
<!-- <script src="../js/login.js"></script> -->
</body>
</html>
