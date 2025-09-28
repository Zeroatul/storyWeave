<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Get user's first initial for avatar
$first_initial = !empty($_SESSION["fullName"]) ? substr($_SESSION["fullName"], 0, 1) : '?';

$oldPasswordError = isset($_GET['oldPasswordError']) ? htmlspecialchars($_GET['oldPasswordError']) : '';
$newPasswordError = isset($_GET['newPasswordError']) ? htmlspecialchars($_GET['newPasswordError']) : '';
$confirmPasswordError = isset($_GET['confirmPasswordError']) ? htmlspecialchars($_GET['confirmPasswordError']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password - Story Weave</title>
    <!-- Combined and adjusted styles for a full page layout -->
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/change-password.css">
    <style>
        /* Override body styles from change-password.css to allow for header/footer */
        body {
            display: block;
            height: auto;
            background-color: #f0f2f5;
        }
        /* New wrapper to center the form on the page */
        .form-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 60px 20px;
        }
    </style>
</head>
<body>

    <header class="main-header">
        <div class="logo"><a href="home.php"><img src="../src/logo.png" alt="Story Weave Logo"></a></div>
        <nav class="main-nav">
            <a href="story-library.php">Browse Stories</a>
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
        </nav>
    </header>

    <main class="form-wrapper">
        <div class="change-password-container">
            <h1>Change Your Password</h1>
            <p>Enter your old password and create a new one.</p>

            <form action="../../controller/change_password_action.php" method="POST" id="changePasswordForm" novalidate>
                <div class="input-group">
                    <label for="oldPassword">Old Password</label>
                    <input type="password" id="oldPassword" name="oldPassword" placeholder="Enter your current password" class="<?php echo !empty($oldPasswordError) ? 'invalid' : ''; ?>">
                    <div class="error-message" id="oldPasswordError"><?php echo $oldPasswordError; ?></div>
                </div>

                <div class="input-group">
                    <label for="newPassword">New Password</label>
                    <input type="password" id="newPassword" name="newPassword" placeholder="Enter your new password" class="<?php echo !empty($newPasswordError) ? 'invalid' : ''; ?>">
                    <div class="error-message" id="newPasswordError"><?php echo $newPasswordError; ?></div>
                </div>

                <div class="input-group">
                    <label for="confirmPassword">Confirm New Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm your new password" class="<?php echo !empty($confirmPasswordError) ? 'invalid' : ''; ?>">
                    <div class="error-message" id="confirmPasswordError"><?php echo $confirmPasswordError; ?></div>
                </div>

                <button type="submit">Update Password</button>
            </form>
        </div>
    </main>

    <footer class="main-footer">
        <p>&copy; 2025 Story Weave. All Rights Reserved.</p>
    </footer>

    <script src="../js/change-password.js"></script>
</body>
</html>
