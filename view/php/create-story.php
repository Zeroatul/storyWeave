<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$first_initial = !empty($_SESSION["fullName"]) ? substr($_SESSION["fullName"], 0, 1) : '?';

$draft = isset($_COOKIE['story_draft']) ? json_decode($_COOKIE['story_draft'], true) : null;
$draftTitle = $draft['title'] ?? '';
$draftGenre = $draft['genre'] ?? '';
$draftSynopsis = $draft['synopsis'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create a New Story - Story Weave</title>
    <link href="../css/create-story.css" rel="stylesheet">
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
    <main class="create-story-container">
        <h1>Start Your Next Masterpiece</h1>
        <p>Fill out the details below to begin your new story. A draft will be saved automatically.</p>

        <form action="../../controller/create_story_action.php" method="POST" id="createStoryForm">
            <div class="input-group">
                <label for="storyTitle">Story Title</label>
                <input type="text" id="storyTitle" name="storyTitle" placeholder="Enter a captivating title" value="<?php echo htmlspecialchars($draftTitle); ?>" required>
            </div>

            <div class="input-group">
                <label for="genre">Genre</label>
                <select id="genre" name="genre" required>
                    <option value="" disabled <?php if(empty($draftGenre)) echo 'selected'; ?>>Select a genre</option>
                    <option value="fantasy" <?php if($draftGenre == 'fantasy') echo 'selected'; ?>>Fantasy</option>
                    <option value="sci-fi" <?php if($draftGenre == 'sci-fi') echo 'selected'; ?>>Sci-Fi</option>
                    <option value="mystery" <?php if($draftGenre == 'mystery') echo 'selected'; ?>>Mystery</option>
                    <option value="romance" <?php if($draftGenre == 'romance') echo 'selected'; ?>>Romance</option>
                    <option value="thriller" <?php if($draftGenre == 'thriller') echo 'selected'; ?>>Thriller</option>
                    <option value="horror" <?php if($draftGenre == 'horror') echo 'selected'; ?>>Horror</option>
                </select>
            </div>

            <div class="input-group">
                <label for="synopsis">Synopsis</label>
                <textarea id="synopsis" name="synopsis" placeholder="Give a brief summary of your story's plot..." rows="6" required><?php echo htmlspecialchars($draftSynopsis); ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn">Create Story</button>
                <button type="button" id="clearDraftBtn" class="btn btn-secondary">Clear Draft</button>
            </div>
        </form>
    </main>

    <footer class="main-footer">
        <p>&copy; 2025 Story Weave. All Rights Reserved.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('createStoryForm');
            const titleInput = document.getElementById('storyTitle');
            const genreInput = document.getElementById('genre');
            const synopsisInput = document.getElementById('synopsis');
            const clearDraftBtn = document.getElementById('clearDraftBtn');

            const saveDraft = () => {
                const draft = {
                    title: titleInput.value,
                    genre: genreInput.value,
                    synopsis: synopsisInput.value
                };
                document.cookie = `story_draft=${JSON.stringify(draft)};max-age=604800;path=/`;
            };

            form.addEventListener('input', saveDraft);

            clearDraftBtn.addEventListener('click', () => {
                titleInput.value = '';
                genreInput.value = '';
                synopsisInput.value = '';
                document.cookie = 'story_draft=;max-age=0;path=/';
            });

            form.addEventListener('submit', () => {
                 document.cookie = 'story_draft=;max-age=0;path=/';
            });
        });
    </script>
</body>
</html>

