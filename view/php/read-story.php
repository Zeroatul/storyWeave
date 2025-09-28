<?php
session_start();
require_once '../../model/db_connect.php';

$loggedin = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
if($loggedin){
    $first_initial = !empty($_SESSION["fullName"]) ? substr($_SESSION["fullName"], 0, 1) : '?';
}

if(!isset($_GET['story_id']) || empty($_GET['story_id'])){
    header("location: story-library.php");
    exit;
}

$story_id = $_GET['story_id'];

// Fetch story details
$story = null;
$sql_story = "SELECT s.title, u.uname AS author_name FROM stories s JOIN user u ON s.user_id = u.id WHERE s.id = ?";
if($stmt_story = mysqli_prepare($conn, $sql_story)){
    mysqli_stmt_bind_param($stmt_story, "i", $story_id);
    if(mysqli_stmt_execute($stmt_story)){
        $result_story = mysqli_stmt_get_result($stmt_story);
        if(mysqli_num_rows($result_story) == 1){
            $story = mysqli_fetch_assoc($result_story);
        } else {
            // Story not found
            header("location: story-library.php");
            exit;
        }
    }
    mysqli_stmt_close($stmt_story);
}

// Fetch chapters
$chapters = [];
$sql_chapters = "SELECT id, chapter_number, title, content FROM chapters WHERE story_id = ? ORDER BY chapter_number ASC";
if($stmt_chapters = mysqli_prepare($conn, $sql_chapters)){
    mysqli_stmt_bind_param($stmt_chapters, "i", $story_id);
    if(mysqli_stmt_execute($stmt_chapters)){
        $result_chapters = mysqli_stmt_get_result($stmt_chapters);
        while($row = mysqli_fetch_assoc($result_chapters)){
            $chapters[] = $row;
        }
    }
    mysqli_stmt_close($stmt_chapters);
}

// For now, we will display the first chapter.
// A more advanced version could take a chapter number from the URL.
$current_chapter = !empty($chapters) ? $chapters[0] : null;

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $story ? htmlspecialchars($story['title']) : 'Story'; ?> - Story Weave</title>
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
                        <li><a href="logout.php">Log Out</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <a href="login.php">Log In</a>
            <?php endif; ?>
        </nav>
    </header>
    <div class="story-container">
        <?php if ($story && $current_chapter): ?>
        <main class="story-main-content">
            <h1 class="story-title"><?php echo htmlspecialchars($story['title']); ?></h1>
            <div class="story-author">by <?php echo htmlspecialchars($story['author_name']); ?></div>
            <h2 class="chapter-title">Chapter <?php echo htmlspecialchars($current_chapter['chapter_number']); ?>: <?php echo htmlspecialchars($current_chapter['title']); ?></h2>
            <div class="chapter-content">
                <?php echo nl2br(htmlspecialchars($current_chapter['content'])); ?>
            </div>
        </main>
        <aside class="story-sidebar">
            <div class="sidebar-widget">
                <h3 class="widget-title">Chapters</h3>
                <ul class="chapter-list">
                    <?php foreach($chapters as $chapter): ?>
                         <li class="<?php echo ($chapter['id'] == $current_chapter['id']) ? 'active' : ''; ?>">
                             <a href="#">Chapter <?php echo htmlspecialchars($chapter['chapter_number']); ?>: <?php echo htmlspecialchars($chapter['title']); ?></a>
                         </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </aside>
        <?php else: ?>
            <main class="story-main-content">
                <h1 class="story-title">Story Not Found</h1>
                <p>This story or its chapters could not be found. It might be under construction!</p>
            </main>
        <?php endif; ?>
    </div>
    <footer class="main-footer">
        <p>&copy; 2025 Story Weave. All Rights Reserved.</p>
    </footer>
</body>
</html>
