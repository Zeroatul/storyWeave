<?php
session_start();
require_once '../../model/db_connect.php';

$successMessage = '';
if (isset($_SESSION['success_message'])) {
    $successMessage = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

$loggedin = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
$story_id = isset($_GET['story_id']) ? (int)$_GET['story_id'] : 0;
$chapter_number = isset($_GET['chapter']) ? (int)$_GET['chapter'] : 1;

if ($story_id <= 0) {
    die("Invalid story ID.");
}

$story_sql = "SELECT s.title, u.uname AS author_name, s.synopsis FROM stories s JOIN user u ON s.user_id = u.id WHERE s.id = ?";
$story = null;
if ($stmt = mysqli_prepare($conn, $story_sql)) {
    mysqli_stmt_bind_param($stmt, "i", $story_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $story = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

if (!$story) {
    die("Story not found.");
}

$chapters_sql = "SELECT chapter_number, title FROM chapters WHERE story_id = ? ORDER BY chapter_number ASC";
$chapters = [];
if ($stmt = mysqli_prepare($conn, $chapters_sql)) {
    mysqli_stmt_bind_param($stmt, "i", $story_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $chapters[] = $row;
    }
    mysqli_stmt_close($stmt);
}

$next_chapter_number = count($chapters) + 1;

$chapter_content = null;
if (!empty($chapters)) {
    $current_chapter_sql = "SELECT title, content FROM chapters WHERE story_id = ? AND chapter_number = ?";
    if ($stmt = mysqli_prepare($conn, $current_chapter_sql)) {
        mysqli_stmt_bind_param($stmt, "ii", $story_id, $chapter_number);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $chapter_content = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    }
}

$comments_sql = "SELECT c.comment_text, u.uname AS author, c.created_at FROM comments c JOIN user u ON c.user_id = u.id WHERE c.story_id = ? ORDER BY c.created_at DESC";
$comments = [];
if ($stmt = mysqli_prepare($conn, $comments_sql)) {
    mysqli_stmt_bind_param($stmt, "i", $story_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while($row = mysqli_fetch_assoc($result)) {
        $comments[] = $row;
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);

if ($loggedin) {
    $first_initial = !empty($_SESSION["fullName"]) ? substr($_SESSION["fullName"], 0, 1) : '?';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($story['title']); ?> - Story Weave</title>
    <link href="../css/read-story.css" rel="stylesheet">
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
    <div class="story-container">
        <main class="story-main-content">

            <?php if($successMessage): ?>
                <div class="success-message"><?php echo $successMessage; ?></div>
            <?php endif; ?>

            <h1 class="story-title"><?php echo htmlspecialchars($story['title']); ?></h1>
            <div class="story-author">by <?php echo htmlspecialchars($story['author_name']); ?></div>

            <?php if ($chapter_content): ?>
                <h2 class="chapter-title"><?php echo htmlspecialchars($chapter_content['title']); ?></h2>
                <div class="chapter-content">
                    <?php echo nl2br(htmlspecialchars($chapter_content['content'])); ?>
                </div>
            <?php else: ?>
                <h2 class="chapter-title">Synopsis</h2>
                <div class="chapter-content">
                    <p><?php echo nl2br(htmlspecialchars($story['synopsis'])); ?></p>
                </div>
            <?php endif; ?>

            <div class="comment-section">
                <h3 class="widget-title">Comments</h3>
                <div class="comments-list" id="comments-list">
                    <?php if (!empty($comments)): ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="comment">
                                <strong><?php echo htmlspecialchars($comment['author']); ?></strong>
                                <span class="comment-date"><?php echo date('M d, Y', strtotime($comment['created_at'])); ?></span>
                                <p><?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p id="no-comments-message">No comments yet. Be the first to share your thoughts!</p>
                    <?php endif; ?>
                </div>

                <?php if($loggedin): ?>
                    <h3 class="widget-title">Leave a Comment</h3>
                    <form class="comment-form" id="comment-form">
                        <input type="hidden" name="story_id" value="<?php echo $story_id; ?>">
                        <textarea name="comment" id="comment-text" placeholder="Share your thoughts on this story..." required></textarea>
                        <button class="btn" type="submit">Post Comment</button>
                    </form>
                <?php else: ?>
                    <p><a href="login.php">Log in</a> to leave a comment.</p>
                <?php endif; ?>
            </div>
        </main>
        <aside class="story-sidebar">
            <div class="sidebar-widget">
                <h3 class="widget-title">Story Actions</h3>
                <a href="write-chapter.php?story_id=<?php echo $story_id; ?>" class="btn">Write Chapter <?php echo $next_chapter_number; ?></a>
            </div>
            <div class="sidebar-widget">
                <h3 class="widget-title">Chapters</h3>
                <ul class="chapter-list">
                    <?php if (empty($chapters)): ?>
                        <li><a>Synopsis</a></li>
                    <?php else: ?>
                        <?php foreach ($chapters as $chap): ?>
                            <li class="<?php echo ($chap['chapter_number'] == $chapter_number) ? 'active' : ''; ?>">
                                <a href="read-story.php?story_id=<?php echo $story_id; ?>&chapter=<?php echo $chap['chapter_number']; ?>">
                                    Chapter <?php echo $chap['chapter_number']; ?>: <?php echo htmlspecialchars($chap['title']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </aside>
    </div>
    <footer class="main-footer">
        <p>&copy; 2025 Story Weave. All Rights Reserved.</p>
    </footer>

    <?php if ($loggedin): ?>
        <script src="../js/read-story.js"></script>
    <?php endif; ?>
</body>
</html>

