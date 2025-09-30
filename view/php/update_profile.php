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
$first_initial = !empty($_SESSION["fullName"]) ? substr($_SESSION["fullName"], 0, 1) : '?';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Profile - Story Weave</title>
    <link rel="stylesheet" href="../css/update-profile.css">
</head>
<body>
    <header class="main-header">
        <div class="logo"><a href="home.php"><img src="../src/logo.png" alt="Story Weave Logo"></a></div>
        <nav class="main-nav">
            <a href="story-library.php">Browse Stories</a>
            <?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                <div class="profile-dropdown">
                    <div class="profile-avatar"><?php echo htmlspecialchars($first_initial); ?></div>
                    <ul class="dropdown-menu">
                        <li><a href="update_profile.php">My Profile</a></li>
                        <li><a href="manage-stories.php">My Stories</a></li>
                        <li><a href="story-analytics.php">Story Analytics</a></li>
                        <li><a href="review-submissions.php">Submissions</a></li>
                        <li><a href="change_password.php">Change Password</a></li>
                        <li><a href="logout.php">Log Out</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <a href="login.php">Log In</a>
                <a href="registration.php">Sign Up</a>
            <?php endif; ?>
        </nav>
    </header>
    <main class="form-wrapper">
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
            </form>
        </div>
    </main>
    <footer class="main-footer">
        <p>&copy; 2025 Story Weave. All Rights Reserved.</p>
    </footer>
</body>
</html>

