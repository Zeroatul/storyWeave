<?php
session_start();
require_once '../../model/db_connect.php';

$loggedin = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
if($loggedin){
    $first_initial = !empty($_SESSION["fullName"]) ? substr($_SESSION["fullName"], 0, 1) : '?';
}

// --- Filter & Sort Logic ---
$search = $_GET['search'] ?? '';
$genre = $_GET['genre'] ?? 'all';
$status = $_GET['status'] ?? 'all';
$sort = $_GET['sort'] ?? 'recent';

// Base SQL query
$sql = "SELECT s.id, s.title, s.genre, s.status, s.synopsis, u.uname AS author_name
        FROM stories s
        JOIN user u ON s.user_id = u.id";

// Dynamically build WHERE clauses
$where_clauses = [];
$params = [];
$types = '';

if (!empty($search)) {
    $where_clauses[] = "s.title LIKE ?";
    $params[] = "%" . $search . "%";
    $types .= 's';
}
if ($genre !== 'all') {
    $where_clauses[] = "s.genre = ?";
    $params[] = $genre;
    $types .= 's';
}
if ($status !== 'all') {
    $where_clauses[] = "s.status = ?";
    $params[] = $status;
    $types .= 's';
}

if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

// Add sorting
if ($sort == 'recent') {
    $sql .= " ORDER BY s.created_at DESC";
} else { // Placeholder for popularity sorting
    $sql .= " ORDER BY s.created_at DESC";
}

// Fetch stories from DB
$stories = [];
if ($stmt = mysqli_prepare($conn, $sql)) {
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    if(mysqli_stmt_execute($stmt)){
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $stories[] = $row;
        }
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
        <div class="logo"><a href="home.php"><img src="../src/logo.png" alt="Story Weave Logo" ></a></div>
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

    <main class="library-wrapper">
        <h1 class="page-title">Story Library</h1>

        <form action="story-library.php" method="GET" class="filter-controls">
            <div class="filter-group">
                <label for="search">Search Title</label>
                <input type="text" id="search" name="search" placeholder="e.g., The Last Starlight" value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="filter-group">
                <label for="genre">Genre</label>
                <select id="genre" name="genre">
                    <option value="all" <?php if($genre == 'all') echo 'selected'; ?>>All Genres</option>
                    <option value="Fantasy" <?php if($genre == 'Fantasy') echo 'selected'; ?>>Fantasy</option>
                    <option value="Sci-Fi" <?php if($genre == 'Sci-Fi') echo 'selected'; ?>>Sci-Fi</option>
                    <option value="Mystery" <?php if($genre == 'Mystery') echo 'selected'; ?>>Mystery</option>
                    <option value="Romance" <?php if($genre == 'Romance') echo 'selected'; ?>>Romance</option>
                    <option value="Thriller" <?php if($genre == 'Thriller') echo 'selected'; ?>>Thriller</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="all" <?php if($status == 'all') echo 'selected'; ?>>Any Status</option>
                    <option value="In Progress" <?php if($status == 'In Progress') echo 'selected'; ?>>In Progress</option>
                    <option value="Finished" <?php if($status == 'Finished') echo 'selected'; ?>>Finished</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="sort">Sort By</label>
                <select id="sort" name="sort">
                    <option value="recent" <?php if($sort == 'recent') echo 'selected'; ?>>Most Recent</option>
                    <option value="popularity" <?php if($sort == 'popularity') echo 'selected'; ?>>Popularity</option>
                </select>
            </div>
             <div class="filter-group">
                <button type="submit" class="filter-btn">Filter</button>
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
                            <span class="tag <?php echo ($story['status'] == 'In Progress') ? 'status-in-progress' : 'status-finished'; ?>">
                                <?php echo htmlspecialchars($story['status']); ?>
                            </span>
                        </div>
                        <p><?php echo htmlspecialchars(substr($story['synopsis'], 0, 150)) . '...'; ?></p>
                        <a href="read-story.php?story_id=<?php echo $story['id']; ?>" class="read-now-btn">Read Now</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; color: #606770; grid-column: 1 / -1;">No stories match your criteria.</p>
            <?php endif; ?>
        </div>
    </main>

    <footer class="main-footer">
        <p>&copy; 2025 Story Weave. All Rights Reserved.</p>
    </footer>
</body>
</html>

