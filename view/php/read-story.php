<?php
session_start();
require_once '../../model/db_connect.php';

$loggedin = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
if($loggedin){
    $first_initial = !empty($_SESSION["fullName"]) ? substr($_SESSION["fullName"], 0, 1) : '?';
}

// --- Fetch Story and Chapter Data ---
$story_id = $_GET['story_id'] ?? 0;
if ($story_id == 0) {
    header("location: story-library.php");
    exit;
}

// Fetch story details
$story = null;
$sql_story = "SELECT s.title, s.synopsis, u.uname AS author_name FROM stories s JOIN user u ON s.user_id = u.id WHERE s.id = ?";
if($stmt_story = mysqli_prepare($conn, $sql_story)){
    mysqli_stmt_bind_param($stmt_story, "i", $story_id);
    if(mysqli_stmt_execute($stmt_story)){
        $result = mysqli_stmt_get_result($stmt_story);
        if(mysqli_num_rows($result) == 1){
            $story = mysqli_fetch_assoc($result);
        }
    }
    mysqli_stmt_close($stmt_story);
}

if(!$story){
    echo "Story not found.";
    exit;
}

// Fetch all chapters for the story
$chapters = [];
$sql_chapters = "SELECT id, chapter_number, title, content FROM chapters WHERE story_id = ? ORDER BY chapter_number ASC";
if($stmt_chapters = mysqli_prepare($conn, $sql_chapters)){
    mysqli_stmt_bind_param($stmt_chapters, "i", $story_id);
    if(mysqli_stmt_execute($stmt_chapters)){
        $result = mysqli_stmt_get_result($stmt_chapters);
        while($row = mysqli_fetch_assoc($result)){
            $chapters[] = $row;
        }
    }
    mysqli_stmt_close($stmt_chapters);
}

// Determine which chapter to display
$current_chapter_number = $_GET['chapter'] ?? 1;
$current_chapter = null;
if (!empty($chapters)) {
    foreach ($chapters as $chapter) {
        if ($chapter['chapter_number'] == $current_chapter_number) {
            $current_chapter = $chapter;
            break;
        }
    }
    // If an invalid chapter number is given, default to the first chapter
    if (!$current_chapter) {
        $current_chapter = $chapters[0];
    }
}

$next_chapter_number = count($chapters) + 1;

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
            <?php endif; ?>
        </nav>
    </header>
    <div class="story-container">
        <main class="story-main-content">
            <h1 class="story-title"><?php echo htmlspecialchars($story['title']); ?></h1>
            <div class="story-author">by <?php echo htmlspecialchars($story['author_name']); ?></div>

            <?php if (empty($chapters)): ?>
                <h2 class="chapter-title">Synopsis</h2>
                <div class="chapter-content">
                    <p><?php echo nl2br(htmlspecialchars($story['synopsis'])); ?></p>
                </div>
            <?php else: ?>
                <h2 class="chapter-title">Chapter <?php echo $current_chapter['chapter_number']; ?>: <?php echo htmlspecialchars($current_chapter['title']); ?></h2>
                <div class="chapter-content">
                    <p><?php echo nl2br(htmlspecialchars($current_chapter['content'])); ?></p>
                </div>
            <?php endif; ?>

        </main>
        <aside class="story-sidebar">
            <div class="sidebar-widget">
                <h3 class="widget-title">Story Actions</h3>
                <?php if ($loggedin): ?>
                     <a href="write-chapter.php?story_id=<?php echo $story_id; ?>&chapter_number=<?php echo $next_chapter_number; ?>" class="btn">Write Next Chapter (<?php echo $next_chapter_number; ?>)</a>
                <?php endif; ?>
                <a href="#" class="btn btn-secondary" style="margin-top: 10px;">Subscribe</a>
            </div>
            <div class="sidebar-widget">
                <h3 class="widget-title">Chapters</h3>
                <ul class="chapter-list">
                    <?php if (empty($chapters)): ?>
                        <li>No chapters have been written yet.</li>
                    <?php else: ?>
                        <?php foreach($chapters as $chapter): ?>
                            <li class="<?php echo ($current_chapter && $chapter['id'] == $current_chapter['id']) ? 'active' : ''; ?>">
                                <a href="read-story.php?story_id=<?php echo $story_id; ?>&chapter=<?php echo $chapter['chapter_number']; ?>">Chapter <?php echo $chapter['chapter_number']; ?>: <?php echo htmlspecialchars($chapter['title']); ?></a>
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
</body>
</html>

