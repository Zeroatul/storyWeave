<?php
session_start();
require_once '../../model/db_connect.php';

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$story_id = isset($_GET['story_id']) ? (int)$_GET['story_id'] : 0;
if ($story_id <= 0) {
    die("No story selected.");
}

$story_sql = "SELECT title, user_id FROM stories WHERE id = ?";
$story_info = null;
if ($stmt = mysqli_prepare($conn, $story_sql)) {
    mysqli_stmt_bind_param($stmt, "i", $story_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $story_info = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

if (!$story_info || $story_info['user_id'] != $_SESSION['id']) {
    die("You do not have permission to manage contributors for this story.");
}

$emailError = isset($_SESSION['invite_errors']['emailError']) ? $_SESSION['invite_errors']['emailError'] : '';
$email = isset($_SESSION['invite_errors']['email']) ? $_SESSION['invite_errors']['email'] : '';
unset($_SESSION['invite_errors']);

$successMessage = '';
if (isset($_SESSION['invite_success'])) {
    $successMessage = "An invitation has been sent to <strong>" . htmlspecialchars($_SESSION['invite_success']) . "</strong>.";
    unset($_SESSION['invite_success']);
}

$first_initial = !empty($_SESSION["fullName"]) ? substr($_SESSION["fullName"], 0, 1) : '?';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contributor Management - Story Weave</title>
    <link rel="stylesheet" href="../css/contributor-management.css">
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
    <main class="management-container">
        <header class="page-header">
            <h1>Contributor Management</h1>
            <p>For <em><?php echo htmlspecialchars($story_info['title']); ?></em></p>
        </header>
        <section class="management-widget">
            <h2 class="widget-title">Invite a Contributor</h2>
            <div class="invite-container">
            <?php if (!empty($successMessage)): ?>
                <div class="success-message"><?php echo $successMessage; ?></div>
            <?php endif; ?>
                <form class="invite-form" id="invite-form" method="POST" action="../../controller/contributor_management_action.php">
                    <input type="hidden" name="story_id" value="<?php echo $story_id; ?>">
                    <input type="email" id="email" name="email" placeholder="Enter user's email address" value="<?php echo htmlspecialchars($email); ?>" class="<?php echo !empty($emailError) ? 'invalid' : ''; ?>">
                    <button class="btn" type="submit">Send Invite</button>
                    <?php if (!empty($emailError)): ?>
                        <div class="error-message" id="emailError"><?php echo $emailError; ?></div>
                    <?php endif; ?>
                </form>
            </div>
        </section>
    </main>
    <footer class="main-footer">
        <p>&copy; 2025 Story Weave. All Rights Reserved.</p>
    </footer>
</body>
</html>

