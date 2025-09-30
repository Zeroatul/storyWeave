<?php
session_start();
require_once '../../model/db_connect.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$story_id = isset($_GET['story_id']) ? (int)$_GET['story_id'] : 0;
$error = isset($_GET['error']) && $_GET['error'] == 1 ? "All fields are required. Please fill out both the title and content." : "";

if ($story_id <= 0) {
    die("Invalid story ID.");
}

$story_sql = "SELECT title FROM stories WHERE id = ?";
$story_title = '';
if ($stmt = mysqli_prepare($conn, $story_sql)) {
    mysqli_stmt_bind_param($stmt, "i", $story_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($story = mysqli_fetch_assoc($result)) {
        $story_title = $story['title'];
    } else {
        die("Story not found.");
    }
    mysqli_stmt_close($stmt);
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
$next_chapter_number = $current_chapter + 1;

mysqli_close($conn);

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
    <main class="write-chapter-container">
        <h1>Submit Your Version of Chapter <?php echo $next_chapter_number; ?></h1>
        <p>For the story: <em><?php echo htmlspecialchars($story_title); ?></em></p>

        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="../../controller/write_chapter_action.php" method="POST">
            <input type="hidden" name="story_id" value="<?php echo $story_id; ?>">
            <input type="hidden" name="chapter_number" value="<?php echo $next_chapter_number; ?>">

            <div class="input-group">
                <label for="chapterTitle">Proposed Chapter Title</label>
                <input type="text" id="chapterTitle" name="chapterTitle" placeholder="Enter a title for your chapter" required>
            </div>

            <div class="input-group">
                <label for="chapterContent">Chapter Content</label>
                <textarea id="chapterContent" name="chapterContent" placeholder="Continue the story here..." rows="15" required></textarea>
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

