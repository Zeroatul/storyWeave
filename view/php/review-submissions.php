<?php
session_start();
require_once '../../model/db_connect.php';

$successMessage = '';
if (isset($_SESSION['success_message'])) {
    $successMessage = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

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
    die("You do not have permission to review submissions for this story.");
}

$chapters_sql = "SELECT MAX(chapter_number) as max_chapter FROM chapters WHERE story_id = ?";
$current_chapter = 0;
if ($stmt = mysqli_prepare($conn, $chapters_sql)) {
    mysqli_stmt_bind_param($stmt, "i", $story_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if($row = mysqli_fetch_assoc($result)){
        $current_chapter = $row['max_chapter'] ?? 0;
    }
    mysqli_stmt_close($stmt);
}
$reviewing_chapter_number = $current_chapter + 1;

$submissions_sql = "SELECT s.id, s.content, s.title, u.uname as author_name FROM submissions s JOIN user u ON s.user_id = u.id WHERE s.story_id = ? AND s.for_chapter_number = ? AND s.status = 'Pending'";
$submissions = [];
if ($stmt = mysqli_prepare($conn, $submissions_sql)) {
    mysqli_stmt_bind_param($stmt, "ii", $story_id, $reviewing_chapter_number);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $submissions[] = $row;
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
    <title>Review Submissions - <?php echo htmlspecialchars($story_info['title']); ?></title>
    <link href="../css/review-submissions.css" rel="stylesheet">
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
    <main class="review-container">
        <header class="review-header">
            <h1>Review Submissions for Chapter <?php echo $reviewing_chapter_number; ?></h1>
            <p>Story: <em><?php echo htmlspecialchars($story_info['title']); ?></em></p>
        </header>

        <?php if($successMessage): ?>
            <div class="success-message"><?php echo $successMessage; ?></div>
        <?php endif; ?>

        <div class="submissions-grid">
            <?php if (!empty($submissions)): ?>
                <?php foreach($submissions as $submission): ?>
                <article class="submission-card">
                    <header>
                        <h3><?php echo htmlspecialchars($submission['title']); ?></h3>
                        <p>by <?php echo htmlspecialchars($submission['author_name']); ?></p>
                    </header>
                    <div class="submission-content">
                        <p><?php echo nl2br(htmlspecialchars($submission['content'])); ?></p>
                    </div>
                    <footer class="card-footer">
                        <form action="../../controller/select_winner_action.php" method="POST">
                            <input type="hidden" name="submission_id" value="<?php echo $submission['id']; ?>">
                            <input type="hidden" name="story_id" value="<?php echo $story_id; ?>">
                            <button type="submit" class="btn btn-secondary">Select as Winner</button>
                        </form>
                    </footer>
                </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p>There are no pending submissions for this chapter yet.</p>
            <?php endif; ?>
        </div>
    </main>
    <footer class="main-footer">
        <p>&copy; 2025 Story Weave. All Rights Reserved.</p>
    </footer>
</body>
</html>

