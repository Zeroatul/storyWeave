<?php
// Initialize the session
session_start();
require_once '../../model/db_connect.php';

// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$first_initial = !empty($_SESSION["fullName"]) ? substr($_SESSION["fullName"], 0, 1) : '?';

// --- Fetch Story and Submissions Data ---
$story_id = filter_input(INPUT_GET, 'story_id', FILTER_VALIDATE_INT);
if (!$story_id) {
    header("location: manage-stories.php");
    exit;
}

// Fetch story details
$story = null;
$sql_story = "SELECT id, title, user_id FROM stories WHERE id = ?";
if($stmt_story = mysqli_prepare($conn, $sql_story)){
    mysqli_stmt_bind_param($stmt_story, "i", $story_id);
    mysqli_stmt_execute($stmt_story);
    $result = mysqli_stmt_get_result($stmt_story);
    if(mysqli_num_rows($result) == 1){
        $story = mysqli_fetch_assoc($result);
    }
    mysqli_stmt_close($stmt_story);
}

if(!$story){
    echo "Story not found.";
    exit;
}

// Determine the next chapter number by counting existing chapters
$sql_count_chapters = "SELECT COUNT(id) as chapter_count FROM chapters WHERE story_id = ?";
$chapter_count = 0;
if($stmt_count = mysqli_prepare($conn, $sql_count_chapters)) {
    mysqli_stmt_bind_param($stmt_count, "i", $story_id);
    mysqli_stmt_execute($stmt_count);
    $result_count = mysqli_stmt_get_result($stmt_count);
    $row_count = mysqli_fetch_assoc($result_count);
    $chapter_count = $row_count['chapter_count'];
    mysqli_stmt_close($stmt_count);
}
$for_chapter_number = $chapter_count + 1;


// Fetch pending submissions for the next chapter
$submissions = [];
$sql_submissions = "SELECT s.id, s.content, s.status, u.uname AS author_name
                    FROM submissions s
                    JOIN user u ON s.user_id = u.id
                    WHERE s.story_id = ? AND s.for_chapter_number = ?
                    ORDER BY s.submitted_at DESC";
if($stmt_submissions = mysqli_prepare($conn, $sql_submissions)){
    mysqli_stmt_bind_param($stmt_submissions, "ii", $story_id, $for_chapter_number);
    mysqli_stmt_execute($stmt_submissions);
    $result = mysqli_stmt_get_result($stmt_submissions);
    while($row = mysqli_fetch_assoc($result)){
        $submissions[] = $row;
    }
    mysqli_stmt_close($stmt_submissions);
}

// Check if the logged-in user is the author of the story
$is_author = ($_SESSION['id'] == $story['user_id']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Submissions - Story Weave</title>
    <link href="../css/review-submissions.css" rel="stylesheet">
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
    <main class="review-container">
        <header class="review-header">
            <h1>Review Submissions for Chapter <?php echo $for_chapter_number; ?></h1>
            <p>Story: <em><?php echo htmlspecialchars($story['title']); ?></em></p>
        </header>
        <div class="submissions-grid">
            <?php if (empty($submissions)): ?>
                <p style="text-align: center; grid-column: 1 / -1;">There are no submissions for this chapter yet.</p>
            <?php else: ?>
                <?php foreach ($submissions as $submission): ?>
                    <article class="submission-card <?php if($submission['status'] == 'Approved') echo 'is-winner'; ?>">
                        <header>
                            <h3>Submission by <?php echo htmlspecialchars($submission['author_name']); ?></h3>
                        </header>
                        <div class="submission-content">
                            <p><?php echo nl2br(htmlspecialchars($submission['content'])); ?></p>
                        </div>
                        <footer class="card-footer">
                            <?php if ($is_author): ?>
                                <?php if ($submission['status'] == 'Pending'): ?>
                                    <form action="../../controller/select_winner_action.php" method="POST">
                                        <input type="hidden" name="submission_id" value="<?php echo $submission['id']; ?>">
                                        <input type="hidden" name="story_id" value="<?php echo $story_id; ?>">
                                        <button type="submit" class="btn btn-secondary">Select as Winner</button>
                                    </form>
                                <?php elseif ($submission['status'] == 'Approved'): ?>
                                     <button class="btn btn-selected" disabled>✓ Winning Chapter</button>
                                <?php else: ?>
                                     <button class="btn btn-rejected" disabled>Rejected</button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </footer>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
    <footer class="main-footer">
        <p>&copy; 2025 Story Weave. All Rights Reserved.</p>
    </footer>
</body>
</html>

