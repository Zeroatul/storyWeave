<?php
session_start();
require_once '../../model/db_connect.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$story_id = filter_input(INPUT_GET, 'story_id', FILTER_VALIDATE_INT);
if (!$story_id) {
    header("location: story-library.php");
    exit;
}

// Fetch story details
$story = null;
$sql_story = "SELECT title FROM stories WHERE id = ?";
if ($stmt_story = mysqli_prepare($conn, $sql_story)) {
    mysqli_stmt_bind_param($stmt_story, "i", $story_id);
    mysqli_stmt_execute($stmt_story);
    $result_story = mysqli_stmt_get_result($stmt_story);
    if (mysqli_num_rows($result_story) == 1) {
        $story = mysqli_fetch_assoc($result_story);
    }
    mysqli_stmt_close($stmt_story);
}

if (!$story) {
    echo "Story not found.";
    exit;
}

// Determine the next chapter number
$sql_count = "SELECT COUNT(id) as chapter_count FROM chapters WHERE story_id = ?";
$chapter_count = 0;
if($stmt_count = mysqli_prepare($conn, $sql_count)){
    mysqli_stmt_bind_param($stmt_count, "i", $story_id);
    mysqli_stmt_execute($stmt_count);
    $result = mysqli_stmt_get_result($stmt_count);
    $row = mysqli_fetch_assoc($result);
    $chapter_count = $row['chapter_count'];
    mysqli_stmt_close($stmt_count);
}
$next_chapter_number = $chapter_count + 1;

$first_initial = !empty($_SESSION["fullName"]) ? substr($_SESSION["fullName"], 0, 1) : '?';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write Chapter <?php echo $next_chapter_number; ?> - Story Weave</title>
    <link href="../css/write-chapter.css" rel="stylesheet">
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
                    <li><a href="logout.php">Log Out</a></li>
                </ul>
            </div>
        </nav>
    </header>
    <main class="write-chapter-container">
        <h1>Write Chapter <?php echo $next_chapter_number; ?> for <em><?php echo htmlspecialchars($story['title']); ?></em></h1>
        <p>Submit your version of the next chapter. The story's author will review all submissions and select one to become the official chapter.</p>

        <form action="../../controller/write_chapter_action.php" method="POST">
            <input type="hidden" name="story_id" value="<?php echo $story_id; ?>">
            <input type="hidden" name="for_chapter_number" value="<?php echo $next_chapter_number; ?>">

            <div class="input-group">
                <label for="chapterTitle">Proposed Chapter Title</label>
                <input type="text" id="chapterTitle" name="chapterTitle" placeholder="Enter a title for your chapter" required>
            </div>

            <div class="input-group">
                <label for="chapterContent">Chapter Content</label>
                <textarea id="chapterContent" name="chapterContent" placeholder="Once upon a time..." rows="15" required></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn">Submit Chapter</button>
            </div>
        </form>
    </main>

    <footer class="main-footer">
        <p>&copy; 2025 Story Weave. All Rights Reserved.</p>
    </footer>
</body>
</html>

