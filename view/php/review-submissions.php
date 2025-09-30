<?php
session_start();
require_once '../../model/db_connect.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$first_initial = !empty($_SESSION["fullName"]) ? substr($_SESSION["fullName"], 0, 1) : '?';
$user_id = $_SESSION['id'];
$story_id = isset($_GET['story_id']) ? (int)$_GET['story_id'] : 0;
$page_title = '';
$story_title_for_header = '';
$submissions_by_story = [];
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
unset($_SESSION['success_message']);

if ($story_id > 0) {
    $story_sql = "SELECT title, user_id FROM stories WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $story_sql)) {
        mysqli_stmt_bind_param($stmt, "i", $story_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($story = mysqli_fetch_assoc($result)) {
            if ($story['user_id'] != $user_id) {
                die("You do not have permission to view these submissions.");
            }
            $story_title_for_header = $story['title'];
            $page_title = "Submissions for " . htmlspecialchars($story_title_for_header);

            $submissions_sql = "SELECT sub.id, sub.title, sub.content, sub.status, u.uname as contributor_name, sub.for_chapter_number FROM submissions sub JOIN user u ON sub.user_id = u.id WHERE sub.story_id = ? AND sub.status = 'Pending' ORDER BY sub.submitted_at DESC";
            if ($sub_stmt = mysqli_prepare($conn, $submissions_sql)) {
                mysqli_stmt_bind_param($sub_stmt, "i", $story_id);
                mysqli_stmt_execute($sub_stmt);
                $sub_result = mysqli_stmt_get_result($sub_stmt);
                $submissions_by_story[$story_title_for_header] = mysqli_fetch_all($sub_result, MYSQLI_ASSOC);
                mysqli_stmt_close($sub_stmt);
            }
        } else {
            die("Story not found.");
        }
        mysqli_stmt_close($stmt);
    }
} else {
    $page_title = "All Pending Submissions";
    $all_submissions_sql = "SELECT s.title as story_title, sub.id, sub.title, sub.content, sub.status, u.uname as contributor_name, sub.for_chapter_number, sub.story_id FROM submissions sub JOIN stories s ON sub.story_id = s.id JOIN user u ON sub.user_id = u.id WHERE s.user_id = ? AND sub.status = 'Pending' ORDER BY s.title, sub.submitted_at DESC";
    if ($stmt = mysqli_prepare($conn, $all_submissions_sql)) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $submissions_by_story[$row['story_title']][] = $row;
        }
        mysqli_stmt_close($stmt);
    }
}
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Story Weave</title>
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
            <h1><?php echo $page_title; ?></h1>
        </header>

        <?php if ($success_message): ?>
            <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <?php if (empty($submissions_by_story)): ?>
            <p style="text-align:center;">There are no pending submissions at this time.</p>
        <?php else: ?>
            <?php foreach ($submissions_by_story as $story_title => $submissions): ?>
                <section class="story-submission-group">
                    <h2>Submissions for <em><?php echo htmlspecialchars($story_title); ?></em></h2>
                    <div class="submissions-grid">
                        <?php foreach ($submissions as $submission): ?>
                        <article class="submission-card">
                            <header>
                                <h3><?php echo htmlspecialchars($submission['title']); ?></h3>
                                <p class="submission-author">By <?php echo htmlspecialchars($submission['contributor_name']); ?> for Chapter <?php echo $submission['for_chapter_number']; ?></p>
                            </header>
                            <div class="submission-content">
                                <p><?php echo nl2br(htmlspecialchars($submission['content'])); ?></p>
                            </div>
                            <footer class="card-footer">
                                <form action="../../controller/select_winner_action.php" method="POST">
                                    <input type="hidden" name="submission_id" value="<?php echo $submission['id']; ?>">
                                    <input type="hidden" name="story_id" value="<?php echo $story_id > 0 ? $story_id : $submission['story_id']; ?>">
                                    <button type="submit" class="btn">Select as Winner</button>
                                </form>
                            </footer>
                        </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>
    <footer class="main-footer">
        <p>&copy; 2025 Story Weave. All Rights Reserved.</p>
    </footer>
</body>
</html>

