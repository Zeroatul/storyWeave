<?php
// Initialize the session
session_start();
require_once '../../model/db_connect.php';

$loggedin = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
if ($loggedin) {
    $first_initial = !empty($_SESSION["fullName"]) ? substr($_SESSION["fullName"], 0, 1) : '?';
}

// Build the query based on filters
$sql = "SELECT s.id, s.title, s.genre, s.status, s.synopsis, u.uname AS author_name FROM stories s JOIN user u ON s.user_id = u.id WHERE 1=1";
$params = [];
$types = '';

if (!empty($_GET['search'])) {
    $sql .= " AND s.title LIKE ?";
    $params[] = "%" . $_GET['search'] . "%";
    $types .= 's';
}
if (!empty($_GET['genre']) && $_GET['genre'] != 'all') {
    $sql .= " AND s.genre = ?";
    $params[] = $_GET['genre'];
    $types .= 's';
}
if (!empty($_GET['status']) && $_GET['status'] != 'all') {
    $sql .= " AND s.status = ?";
    $params[] = $_GET['status'];
    $types .= 's';
}

if (!empty($_GET['sort']) && $_GET['sort'] == 'recent') {
    $sql .= " ORDER BY s.created_at DESC";
} else {
    // Default sort by popularity (you'd need a popularity metric, for now, let's just use creation date)
    $sql .= " ORDER BY s.created_at DESC";
}

$stories = [];
if ($stmt = mysqli_prepare($conn, $sql)) {
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $stories[] = $row;
    }
    mysqli_stmt_close($stmt);
}
mysqli_close($conn);
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
    <div class="logo"><a href="home.php"><img src="../src/logo.png" alt="Story Weave Logo"></a></div>
    <nav class="main-nav">
        <a href="story-library.php">Browse Stories</a>
        <?php if ($loggedin): ?>
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

<main class="library-wrapper">
    <h1 class="page-title">Story Library</h1>

    <form method="GET" action="story-library.php">
        <div class="filter-controls">
            <div class="filter-group">
                <label for="search">Search Title</label>
                <input type="text" id="search" name="search" placeholder="e.g., The Last Starlight"
                       value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            </div>
            <div class="filter-group">
                <label for="genre">Genre</label>
                <select id="genre" name="genre">
                    <option value="all">All Genres</option>
                    <option value="fantasy" <?php echo(($_GET['genre'] ?? '') == 'fantasy' ? 'selected' : ''); ?>>
                        Fantasy
                    </option>
                    <option value="sci-fi" <?php echo(($_GET['genre'] ?? '') == 'sci-fi' ? 'selected' : ''); ?>>
                        Sci-Fi
                    </option>
                    <option value="mystery" <?php echo(($_GET['genre'] ?? '') == 'mystery' ? 'selected' : ''); ?>>
                        Mystery
                    </option>
                </select>
            </div>
            <div class="filter-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="all">Any Status</option>
                    <option
                        value="In Progress" <?php echo(($_GET['status'] ?? '') == 'In Progress' ? 'selected' : ''); ?>>
                        In Progress
                    </option>
                    <option
                        value="Finished" <?php echo(($_GET['status'] ?? '') == 'Finished' ? 'selected' : ''); ?>>
                        Finished
                    </option>
                </select>
            </div>
            <div class="filter-group">
                <label for="sort">Sort By</label>
                <select id="sort" name="sort">
                    <option
                        value="popularity" <?php echo(($_GET['sort'] ?? '') == 'popularity' ? 'selected' : ''); ?>>
                        Popularity
                    </option>
                    <option value="recent" <?php echo(($_GET['sort'] ?? '') == 'recent' ? 'selected' : ''); ?>>Most
                        Recent
                    </option>
                </select>
            </div>
            <div class="filter-group">
                <button type="submit" class="btn">Filter</button>
            </div>
        </div>
    </form>

    <div class="story-grid">
        <?php if (!empty($stories)): ?>
            <?php foreach ($stories as $story): ?>
                <div class="story-card">
                    <h3><?php echo htmlspecialchars($story['title']); ?></h3>
                    <div class="author">by <?php echo htmlspecialchars($story['author_name']); ?></div>
                    <div class="card-tags">
                        <span class="tag genre-tag"><?php echo htmlspecialchars($story['genre']); ?></span>
                        <span
                            class="tag <?php echo ($story['status'] == 'In Progress') ? 'status-in-progress' : 'status-finished'; ?>">
                                    <?php echo htmlspecialchars($story['status']); ?>
                                </span>
                    </div>
                    <p><?php echo htmlspecialchars(substr($story['synopsis'], 0, 150)) . '...'; ?></p>
                    <a href="read-story.php?story_id=<?php echo $story['id']; ?>" class="read-now-btn">Read Now</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No stories match your criteria.</p>
        <?php endif; ?>
    </div>
</main>

<footer class="main-footer">
    <p>&copy; 2025 Story Weave. All Rights Reserved.</p>
</footer>
</body>
</html>

