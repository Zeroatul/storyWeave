<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

if (isset($_SESSION['update_profile_errors'])) {
    $errors = $_SESSION['update_profile_errors'];
    $fullNameError = $errors['fullNameError'] ?? '';
    $emailError = $errors['emailError'] ?? '';
    $fullName = $errors['fullName'] ?? $_SESSION['fullName'];
    $email = $errors['email'] ?? $_SESSION['email'];
    unset($_SESSION['update_profile_errors']);
} else {
    $fullNameError = $emailError = '';
    $fullName = $_SESSION['fullName'];
    $email = $_SESSION['email'];
}

$successMessage = '';
if (isset($_SESSION['update_profile_success'])) {
    $successMessage = $_SESSION['update_profile_success'];
    unset($_SESSION['update_profile_success']);
}
$bio = 'A passionate writer and reader, always looking for the next great story to be a part of.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Profile - Story Weave</title>
    <link rel="stylesheet" href="../css/update-profile.css">
</head>
<body>
<div class="update-profile-container">
    <h1>Update Your Profile</h1>
    <p>Make changes to your profile information below.</p>

    <?php if (!empty($successMessage)): ?>
        <p class="success-message"><?php echo $successMessage; ?></p>
    <?php endif; ?>

    <form action="../../controller/update_profile_action.php" method="POST" id="updateProfileForm" novalidate>
        <div class="input-group">
            <label for="fullName">Full Name</label>
            <input type="text" id="fullName" name="fullName" placeholder="your name" value="<?php echo htmlspecialchars($fullName); ?>" class="<?php echo !empty($fullNameError) ? 'invalid' : ''; ?>">
            <div class="error-message" id="fullNameError"><?php echo $fullNameError; ?></div>
        </div>

        <div class="input-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" class="<?php echo !empty($emailError) ? 'invalid' : ''; ?>">
            <div class="error-message" id="emailError"><?php echo $emailError; ?></div>
        </div>

        <div class="input-group">
            <label for="bio">Bio</label>
            <textarea id="bio" name="bio" placeholder="Tell us a little about yourself..."><?php echo htmlspecialchars($bio); ?></textarea>
        </div>

        <button type="submit">Save Changes</button>
        <p class="back-link" style="text-align: center; margin-top: 15px;">
            <a href="home.php">&larr; Back to Home</a>
        </p>
    </form>
</div>
</body>
</html>
