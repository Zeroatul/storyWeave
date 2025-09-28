<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comment'])) {
    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && !empty(trim($_POST['comment']))){
        $new_comment = [
            'author' => $_SESSION["fullName"],
            'text' => htmlspecialchars(trim($_POST['comment'])),
            'timestamp' => time()
        ];

        $comments = [];
        if(isset($_COOKIE['story_comments'])) {
            $comments = json_decode($_COOKIE['story_comments'], true);
        }

        $comments[] = $new_comment;

        setcookie('story_comments', json_encode($comments), time() + (86400 * 30), "/");

        header("Location: read-story.php");
        exit();
    }
}

$loggedin = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
if($loggedin){
    $first_initial = !empty($_SESSION["fullName"]) ? substr($_SESSION["fullName"], 0, 1) : '?';
}

$comments = [];
if(isset($_COOKIE['story_comments'])) {
    $comments = json_decode($_COOKIE['story_comments'], true);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Story Weave</title>
	<link href="../css/read-story.css" rel="stylesheet">
    <style>
        .comments-list { margin-bottom: 30px; }
        .comment { border-bottom: 1px solid #f0f2f5; padding: 15px 0; }
        .comment:last-child { border-bottom: none; }
        .comment strong { color: #1c1e21; }
        .comment .comment-date { font-size: 12px; color: #606770; margin-left: 8px; }
        .comment p { margin: 5px 0 0 0; }
    </style>
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
        <main class="story-main-content">
            <h1 class="story-title">The Last Starlight</h1>
            <div class="story-author">by Amelia Vance</div>
            <h2 class="chapter-title">Chapter 1: The Fading Firmament</h2>
            <div class="chapter-content">
                <p>In a world where stars are fading, a young archivist discovers a living map that leads to the last source of light. But the map is coveted by those who would rather rule in darkness. Elara traced the glowing constellations on the cover of the ancient tome. It felt warm to the touch, humming with a forgotten energy. The Grand Archive was her sanctuary, a place of dust and silence, but this book felt alive.</p>
                <p>For generations, the people of Atheria had watched the night sky grow dimmer. The stories of a thousand glittering stars were now just that—stories. Only a handful of the brightest remained, pale ghosts of their former glory. The Royal Astronomers claimed it was a natural cycle, but the whispers in the forgotten texts spoke of a stolen light, a celestial heart that had been shattered.</p>
                <p>The map on the book's cover was not of Atheria. The constellations were alien, yet hauntingly familiar. As her fingers brushed against a spiraling galaxy, the pages of the book fluttered open, revealing not words, but a swirling vortex of light. It pulled at her, a silent invitation to a world beyond the veil of their dying sky. With a deep breath, she plunged her hand into the light.</p>
            </div>
            <div class="comment-section">
                <h3 class="widget-title">Comments</h3>
                <div class="comments-list">
                    <?php if (!empty($comments)): ?>
                        <?php foreach (array_reverse($comments) as $comment): ?>
                            <div class="comment">
                                <strong><?php echo htmlspecialchars($comment['author']); ?></strong>
                                <span class="comment-date"><?php echo date('M d, Y', $comment['timestamp']); ?></span>
                                <p><?php echo nl2br(htmlspecialchars($comment['text'])); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No comments yet. Be the first to share your thoughts!</p>
                    <?php endif; ?>
                </div>

                <?php if($loggedin): ?>
                    <h3 class="widget-title">Leave a Comment</h3>
                    <form class="comment-form" method="POST" action="read-story.php">
                        <textarea name="comment" placeholder="Share your thoughts on this chapter..."></textarea>
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
                <a href="#" class="btn">Subscribe to Story</a>
                <a href="#" class="btn btn-secondary" style="margin-top: 10px;">View Story Branches</a>
            </div>
            <div class="sidebar-widget">
                <h3 class="widget-title">Chapters</h3>
                <ul class="chapter-list">
                    <li class="active"><a href="#">Chapter 1: The Fading Firmament</a></li>
                    <li><a href="#">Chapter 2: The Celestial Key</a></li>
                    <li><a href="#">Chapter 3 (Coming Soon)</a></li>
                </ul>
            </div>
        </aside>
    </div>
    <footer class="main-footer">
        <p>&copy; 2025 Story Weave. All Rights Reserved.</p>
    </footer>
</body>
</html>

