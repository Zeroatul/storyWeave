<?php
session_start();
require_once '../../model/db_connect.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION['id'];

$stories_sql = "SELECT id, title, genre, status FROM stories WHERE user_id = ? ORDER BY created_at DESC";
$stories = [];
if ($stmt = mysqli_prepare($conn, $stories_sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $stories[] = $row;
    }
    mysqli_stmt_close($stmt);
}
mysqli_close($conn);

$first_initial = !empty($_SESSION["fullName"]) ? substr($_SESSION["fullName"], 0, 1) : '?';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage My Stories - Story Weave</title>
    <link href="../css/manage-stories.css" rel="stylesheet">
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
    <main class="manage-container">
        <header class="page-header">
            <h1>My Stories</h1>
            <a href="create-story.php" class="btn">Create New Story</a>
        </header>
        <div class="stories-list">
            <?php if (!empty($stories)): ?>
                <?php foreach ($stories as $story): ?>
                <div class="story-item">
                    <div class="story-info">
                        <h3><?php echo htmlspecialchars($story['title']); ?></h3>
                        <p><?php echo htmlspecialchars($story['genre']); ?></p>
                    </div>
                    <div class="story-actions">
                        <span class="status-toggle <?php echo ($story['status'] == 'In Progress') ? 'status-in-progress' : 'status-finished'; ?>">
                            <?php echo htmlspecialchars($story['status']); ?>
                        </span>
                        <a href="review-submissions.php?story_id=<?php echo $story['id']; ?>" class="btn btn-secondary">Submissions</a>
                        <a href="contributor_management.php?story_id=<?php echo $story['id']; ?>" class="btn btn-secondary">Contributors</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>You haven't created any stories yet. <a href="create-story.php">Start one now</a>!</p>
            <?php endif; ?>
        </div>
    </main>
    <footer class="main-footer">
        <p>&copy; 2025 Story Weave. All Rights Reserved.</p>
    </footer>
</body>
</html>

