<?php
$emailError = isset($_GET['emailError']) ? htmlspecialchars($_GET['emailError']) : '';
$email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';

$success = isset($_GET['success']) && $_GET['success'] == 1;
$sent_email = isset($_GET['sent_email']) ? htmlspecialchars($_GET['sent_email']) : '';
$successMessage = "";
if ($success) {
    $successMessage = "A password reset link has been sent to <strong>" . $sent_email . "</strong>. Please check your inbox.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - Story Weave</title>
    <link rel="stylesheet" href="../css/forgot-password.css">
</head>
<body>
<div class="forgot-password-container">
    <?php if (!empty($successMessage)): ?>
        <h1>Check Your Email</h1>
        <p class="success-message"><?php echo $successMessage; ?></p>
        <p class="back-to-login-link">
            <a href="login.php">&larr; Back to Login</a>
        </p>
    <?php else: ?>
        <h1>Forgot Your Password?</h1>
        <p>No problem. Enter your email address below and we'll send you a link to reset it.</p>

        <form action="../../controller/forget_password_action.php" method="POST" id="forgotPasswordForm" novalidate>
            <div class="input-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="<?php echo $email; ?>" class="<?php echo !empty($emailError) ? 'invalid' : ''; ?>">
                <div class="error-message" id="emailError"><?php echo $emailError; ?></div>
            </div>

            <button type="submit">Send Reset Link</button>

            <p class="back-to-login-link">
                <a href="login.php">&larr; Back to Login</a>
            </p>
        </form>
    <?php endif; ?>
</div>
<script src="../js/forgot-password.js"></script>
</body>
</html>
