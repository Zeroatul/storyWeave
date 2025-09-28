<?php
session_start();
require_once '../../model/db_connect.php';

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
if(!isset($_GET['story_id']) || empty($_GET['story_id'])){
    header("location: manage-stories.php");
    exit;
}

$story_id = $_GET['story_id'];
$first_initial = !empty($_SESSION["fullName"]) ? substr($_SESSION["fullName"], 0, 1) : '?';

// Fetch story details
$story = null;
$sql_story = "SELECT title FROM stories WHERE id = ? AND user_id = ?";
if($stmt_story = mysqli_prepare($conn, $sql_story)){
    mysqli_stmt_bind_param($stmt_story, "ii", $story_id, $_SESSION['id']);
    if(mysqli_stmt_execute($stmt_story)){
        $result_story = mysqli_stmt_get_result($stmt_story);
        if(mysqli_num_rows($result_story) == 1){
            $story = mysqli_fetch_assoc($result_story);
        } else {
            // Not the owner of the story or story doesn't exist
            header("location: manage-stories.php");
            exit;
        }
    }
    mysqli_stmt_close($stmt_story);
}


// Find the next chapter number
$next_chapter_number = 1;
$sql_last_chapter = "SELECT MAX(chapter_number) as last_chapter FROM chapters WHERE story_id = ?";
if($stmt_last = mysqli_prepare($conn, $sql_last_chapter)){
     mysqli_stmt_bind_param($stmt_last, "i", $story_id);
     if(mysqli_stmt_execute($stmt_last)){
        $result_last = mysqli_stmt_get_result($stmt_last);
        $last_chapter_row = mysqli_fetch_assoc($result_last);
        if($last_chapter_row && $last_chapter_row['last_chapter']){
            $next_chapter_number = $last_chapter_row['last_chapter'] + 1;
        }
     }
     mysqli_stmt_close($stmt_last);
}


// Fetch pending submissions for the next chapter
$submissions = [];
$sql_submissions = "SELECT s.id, s.content, u.uname AS author_name FROM submissions s JOIN user u ON s.user_id = u.id WHERE s.story_id = ? AND s.for_chapter_number = ? AND s.status = 'Pending'";
if($stmt_submissions = mysqli_prepare($conn, $sql_submissions)){
    mysqli_stmt_bind_param($stmt_submissions, "ii", $story_id, $next_chapter_number);
    if(mysqli_stmt_execute($stmt_submissions)){
        $result_submissions = mysqli_stmt_get_result($stmt_submissions);
        while($row = mysqli_fetch_assoc($result_submissions)){
            $submissions[] = $row;
        }
    }
    mysqli_stmt_close($stmt_submissions);
}
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Submissions: <?php echo htmlspecialchars($story['title']); ?> - Story Weave</title>
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
                    <li><a href="review-submissions.php">Submissions</a></li>
                    <li><a href="logout.php">Log Out</a></li>
                </ul>
            </div>
        </nav>
    </header>
    <main class="review-container">
        <header class="review-header">
            <h1>Review Submissions for Chapter <?php echo $next_chapter_number; ?></h1>
            <p>Story: <em><?php echo htmlspecialchars($story['title']); ?></em></p>
        </header>
        <div class="submissions-grid">
            <?php if(!empty($submissions)): ?>
                <?php foreach($submissions as $submission): ?>
                    <article class="submission-card">
                        <header>
                            <h3>Submission by <?php echo htmlspecialchars($submission['author_name']); ?></h3>
                        </header>
                        <div class="submission-content">
                            <p><?php echo nl2br(htmlspecialchars($submission['content'])); ?></p>
                        </div>
                        <footer class="card-footer">
                            <button class="btn btn-secondary">Select as Winner</button>
                        </footer>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align:center; grid-column: 1 / -1;">There are no pending submissions for this chapter yet.</p>
            <?php endif; ?>
        </div>
    </main>
    <footer class="main-footer">
        <p>&copy; 2025 Story Weave. All Rights Reserved.</p>
    </footer>
</body>
</html>
