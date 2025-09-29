<?php
// Initialize the session
session_start();
require_once '../../model/db_connect.php'; // Ensure you have the database connection

// Check if the user is logged in
$loggedin = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
if($loggedin){
    $first_initial = !empty($_SESSION["fullName"]) ? substr($_SESSION["fullName"], 0, 1) : '?';
}

// Fetch the 3 most recent stories
$stories = [];
// Corrected the column name from s.author_id to s.user_id
$sql = "SELECT s.id, s.title, s.genre, s.status, s.synopsis, u.uname AS author_name
        FROM stories s
        JOIN user u ON s.user_id = u.id
        ORDER BY s.created_at DESC
        LIMIT 3";

if ($result = mysqli_query($conn, $sql)) {
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $stories[] = $row;
        }
    }
    mysqli_free_result($result);
}
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Story Weave - Weave a Story, Together</title>
	<link href="../css/home.css" rel="stylesheet">
</head>
<body>

    <header class="main-header">
        <div class="logo"><a href="home.php"><img src="../src/logo.png" alt="Story Weave Logo"></a></div>
        <nav class="main-nav">
            <a href="story-library.php">Browse Stories</a>
            <?php if($loggedin): ?>
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

    <main>
        <section class="hero">
            <?php if($loggedin): ?>
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION["fullName"]); ?>!</h1>
                <p style="color: #606770; font-size: 20px; margin-top: -10px; margin-bottom: 30px;">Let's weave a story, together.</p>
                <a href="create-story.php" class="cta-button">Start a New Story</a>
            <?php else: ?>
                <h1>Weave a Story, Together.</h1>
                <a href="registration.php" class="cta-button">Get Started</a>
            <?php endif; ?>
        </section>

        <section class="featured-stories">
            <h2 class="section-title">Featured Stories</h2>
            <div class="story-grid">
                <?php if (!empty($stories)): ?>
                    <?php foreach ($stories as $story): ?>
                        <div class="story-card">
                            <h3><?php echo htmlspecialchars($story['title']); ?></h3>
                            <div class="author">by <?php echo htmlspecialchars($story['author_name']); ?></div>
                            <div class="card-tags">
                                <span class="tag genre-tag"><?php echo htmlspecialchars($story['genre']); ?></span>
                                <span class="tag <?php echo ($story['status'] == 'In Progress') ? 'status-in-progress' : 'status-finished'; ?>">
                                    <?php echo htmlspecialchars($story['status']); ?>
                                </span>
                            </div>
                            <p><?php echo htmlspecialchars(substr($story['synopsis'], 0, 150)) . '...'; ?></p>
                            <a href="read-story.php?story_id=<?php echo $story['id']; ?>" class="read-now-btn">Read Now</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center; color: #606770;">No stories have been created yet. Be the first!</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer class="main-footer">
        <p>&copy; 2025 Story Weave. All Rights Reserved.</p>
    </footer>

</body>
</html>
