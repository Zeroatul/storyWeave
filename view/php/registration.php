<?php
session_start();

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: home.php");
    exit;
}

if (isset($_SESSION['registration_errors'])) {
    $errors = $_SESSION['registration_errors'];
    $fullNameError = $errors['fullNameError'] ?? '';
    $emailError = $errors['emailError'] ?? '';
    $passwordError = $errors['passwordError'] ?? '';
    $fullName = $errors['fullName'] ?? '';
    $email = $errors['email'] ?? '';
    $user_type = $errors['user_type'] ?? 'reader';
    unset($_SESSION['registration_errors']);
} else {
    $fullNameError = $emailError = $passwordError = $fullName = $email = '';
    $user_type = 'reader';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register for Story Weave</title>
    <link rel="stylesheet" href="../css/reg.css">
</head>
<body>
<div class="registration-container">
    <h1>Welcome to Story Weave</h1>
    <p>Create an account to start reading or writing collaborative stories.</p>

    <form action="../../controller/registration_action.php" method="POST" id="registrationForm" novalidate>
        <div class="input-group">
            <label for="fullName">Full Name</label>
            <input type="text" id="fullName" name="fullName" placeholder="your name" value="<?php echo htmlspecialchars($fullName); ?>" class="<?php echo !empty($fullNameError) ? 'invalid' : ''; ?>">
            <div class="error-message" id="fullNameError"><?php echo $fullNameError; ?></div>
        </div>

        <div class="input-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="your.email@example.com" value="<?php echo htmlspecialchars($email); ?>" class="<?php echo !empty($emailError) ? 'invalid' : ''; ?>">
            <div class="error-message" id="emailError"><?php echo $emailError; ?></div>
        </div>

        <div class="input-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Create a secure password" class="<?php echo !empty($passwordError) ? 'invalid' : ''; ?>">
            <div class="error-message" id="passwordError"><?php echo $passwordError; ?></div>
        </div>

        <fieldset class="user-type-selection">
            <legend>Choose your primary role:</legend>
            <div class="radio-options">
                    <span>
                        <input type="radio" id="reader" name="user_type" value="reader" <?php echo ($user_type == 'reader') ? 'checked' : ''; ?>>
                        <label for="reader">📖 Reader</label>
                    </span>
                <span>
                        <input type="radio" id="writer" name="user_type" value="writer" <?php echo ($user_type == 'writer') ? 'checked' : ''; ?>>
                        <label for="writer">✍️ Writer / Co-Author</label>
                    </span>
            </div>
        </fieldset>

        <button type="submit">Sign Up</button>

        <p class="login-link">
            Already a member? <a href="./login.php">Log In</a>
        </p>
    </form>
</div>
<script src="../js/registration.js"></script>
</body>
</html>
