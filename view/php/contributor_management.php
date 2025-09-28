<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$emailError = isset($_GET['emailError']) ? htmlspecialchars($_GET['emailError']) : '';
$email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';

$success = isset($_GET['success']) && $_GET['success'] == 1;
$invited_email = isset($_GET['invited_email']) ? htmlspecialchars($_GET['invited_email']) : '';
$successMessage = "";
if ($success) {
    $successMessage = "An invitation has been sent to <strong>" . $invited_email . "</strong>.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contributor Management - Story Weave</title>
    <link rel="stylesheet" href="../css/contributor-managemnt.css">
</head>
<body>
    <header class="main-header">
        <div class="logo"><a href="home.php"><img src="../src/logo.png" alt="Story Weave Logo"></a></div>
        <nav class="main-nav">
            <a href="story-library.php">Browse Stories</a>
            <div class="profile-dropdown">
                <div class="profile-avatar">A</div>
                <ul class="dropdown-menu">
                    <li><a href="update_profile.php">My Profile</a></li>
                    <li><a href="manage-stories.php">My Stories</a></li>
                    <li><a href="story-analytics.php">Story Analytics</a></li>
                    <li><a href="review-submissions.php">Submissions</a></li>
                    <li><a href="logout.php">Log Out</a></li>
                </ul>
            </div>
        </nav>
    </header>
    <main class="management-container">
        <header class="page-header">
            <h1>Contributor Management</h1>
            <p>For <em>The Last Starlight</em></p>
        </header>
        <section class="management-widget">
            <h2 class="widget-title">Invite a Contributor</h2>
            <div class="invite-container">
            <?php if (!empty($successMessage)): ?>
                <p class="success-message"><?php echo $successMessage; ?></p>
                <p class="back-to-home-link">
                    <a href="contributor_management.php">&larr; Invite another</a>
                </p>
            <?php else: ?>
                <form class="invite-form" id="invite-form" method="POST" action="../../controller/contributor_management_action.php">
                    <input type="email" id="email" name="email" placeholder="Enter user's email address" value="<?php echo $email; ?>" class="<?php echo !empty($emailError) ? 'invalid' : ''; ?>">
                    <button class="btn" type="submit">Send Invite</button>
                    <div class="error-message" id="emailError"><?php echo $emailError; ?></div>
                </form>
            <?php endif; ?>
            </div>
        </section>
        <section class="management-widget">
            <h2 class="widget-title">Current Contributors</h2>
            <ul class="user-list">
                <li class="user-list-item">
                    <span>Alex Griffin</span>
                    <button class="btn btn-danger">Block</button>
                </li>
                <li class="user-list-item">
                    <span>Maria Sanchez</span>
                    <button class="btn btn-danger">Block</button>
                </li>
                 <li class="user-list-item">
                    <span>David Chen</span>
                    <button class="btn btn-danger">Block</button>
                </li>
            </ul>
        </section>
        <section class="management-widget">
            <h2 class="widget-title">Blocked Users</h2>
            <ul class="user-list">
                <li class="user-list-item">
                    <span>UserBlocked404</span>
                    <button class="btn btn-secondary">Unblock</button>
                </li>
            </ul>
        </section>
    </main>
    <footer class="main-footer">
        <p>&copy; 2025 Story Weave. All Rights Reserved.</p>
    </footer>
<script src="../js/contributor-management.js"></script>
</body>
</html>
