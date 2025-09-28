<?php
// Initialize the session
session_start();

$loggedin = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
if($loggedin){
    $first_initial = !empty($_SESSION["fullName"]) ? substr($_SESSION["fullName"], 0, 1) : '?';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Story Weave - Story Library</title>
  	<link href="../css/story-library.css" rel="stylesheet">
</head>
<body>
    <header class="main-header">
        <div class="logo"><a href="home.php"><img src="../src/logo.png" ></a></div>
        <nav class="main-nav">
            <?php if($loggedin): ?>
                <a href="story-library.php">Browse Stories</a>
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
                <a href="registration.php">Sign Up</a>
            <?php endif; ?>
        </nav>
    </header>

    <main class="library-wrapper">
        <h1 class="page-title">Story Library</h1>

        <div class="filter-controls">
            <div class="filter-group">
                <label for="search">Search Title</label>
                <input type="text" id="search" name="search" placeholder="e.g., The Last Starlight">
            </div>
            <div class="filter-group">
                <label for="genre">Genre</label>
                <select id="genre" name="genre">
                    <option value="all">All Genres</option>
                    <option value="fantasy">Fantasy</option>
                    <option value="sci-fi">Sci-Fi</option>
                    <option value="mystery">Mystery</option>
                    <option value="romance">Romance</option>
                    <option value="thriller">Thriller</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="all">Any Status</option>
                    <option value="in-progress">In Progress</option>
                    <option value="finished">Finished</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="sort">Sort By</label>
                <select id="sort" name="sort">
                    <option value="popularity">Popularity</option>
                    <option value="recent">Most Recent</option>
                </select>
            </div>
        </div>

        <div class="story-grid">
            <div class="story-card">
                <h3>The Last Starlight</h3>
                <div class="author">by Amelia Vance</div>
                <div class="card-tags">
                    <span class="tag genre-tag">Fantasy</span>
                    <span class="tag status-in-progress">In Progress</span>
                </div>
                <p>An ancient prophecy awakens, and a young librarian is the only one who can decipher the stars to prevent eternal darkness...</p>
                <a href="read-story.php" class="read-now-btn">Read Now</a>
            </div>

            <div class="story-card">
                <h3>Echoes of Mars</h3>
                <div class="author">by Kenji Tanaka</div>
                <div class="card-tags">
                    <span class="tag genre-tag">Sci-Fi</span>
                    <span class="tag status-in-progress">In Progress</span>
                </div>
                <p>A lone colonist on Mars discovers a mysterious signal that isn't from Earth, forcing her to question the true purpose of her mission.</p>
                <a href="read-story.php" class="read-now-btn">Read Now</a>
            </div>

            <div class="story-card">
                <h3>The Clockwork Detective</h3>
                <div class="author">by Eleanor Bishop</div>
                <div class="card-tags">
                    <span class="tag genre-tag">Mystery</span>
                    <span class="tag status-finished">Finished</span>
                </div>
                <p>In a Victorian city powered by steam, a retired inspector is called back to solve a murder where the only witness is an automaton.</p>
                <a href="read-story.php" class="read-now-btn">Read Now</a>
            </div>

            <div class="story-card">
                <h3>Ocean's Heart</h3>
                <div class="author">by Maria Flores</div>
                <div class="card-tags">
                    <span class="tag genre-tag">Romance</span>
                    <span class="tag status-finished">Finished</span>
                </div>
                <p>A marine biologist studying a rare coral reef finds an old diary that leads her to a lost treasure and an unexpected romance.</p>
                <a href="read-story.php" class="read-now-btn">Read Now</a>
            </div>

             <div class="story-card">
                <h3>The Crimson Cipher</h3>
                <div class="author">by David Chen</div>
                <div class="card-tags">
                    <span class="tag genre-tag">Thriller</span>
                    <span class="tag status-in-progress">In Progress</span>
                </div>
                <p>A cryptographer is pulled into a global conspiracy when he accidentally deciphers a message hidden within a famous painting.</p>
                <a href="read-story.php" class="read-now-btn">Read Now</a>
            </div>

             <div class="story-card">
                <h3>Gatekeepers of Eldoria</h3>
                <div class="author">by Sarah Jenkins</div>
                <div class="card-tags">
                    <span class="tag genre-tag">Fantasy</span>
                    <span class="tag status-in-progress">In Progress</span>
                </div>
                <p>Two rival apprentices must join forces to close a magical rift before their world is consumed by creatures from another dimension.</p>
                <a href="read-story.php" class="read-now-btn">Read Now</a>
            </div>
        </div>
    </main>

    <footer class="main-footer">
        <p>&copy; 2025 Story Weave. All Rights Reserved.</p>
    </footer>
</body>
</html>
