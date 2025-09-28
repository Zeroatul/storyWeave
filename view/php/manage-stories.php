<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

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
            <div class="profile-dropdown">
                <div class="profile-avatar"><?php echo htmlspecialchars($first_initial); ?></div>
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
    <main class="manage-container">
        <header class="page-header">
            <h1>My Stories</h1>
            <a href="create-story.php" class="btn">Create New Story</a>
        </header>
        <div class="stories-list">
            <div class="story-item">
                <div class="story-info">
                    <h3>The Last Starlight</h3>
                    <p>Fantasy - 120 Subscribers</p>
                </div>
                <div class="story-actions">
                    <button class="status-toggle status-in-progress">In Progress</button>
                    <a href="story-analytics.php" class="btn btn-secondary">Analytics</a>
                    <a href="contributor_management.php" class="btn btn-secondary">Contributors</a>
                </div>
            </div>
            <div class="story-item">
                <div class="story-info">
                    <h3>Metropolis Noir</h3>
                    <p>Mystery - 85 Subscribers</p>
                </div>
                <div class="story-actions">
                    <button class="status-toggle status-finished">Finished</button>
                    <a href="story-analytics.php" class="btn btn-secondary">Analytics</a>
                    <a href="contributor_management.php" class="btn btn-secondary">Contributors</a>
                </div>
            </div>
        </div>
    </main>
    <footer class="main-footer">
        <p>&copy; 2025 Story Weave. All Rights Reserved.</p>
    </footer>
</body>
</html>
