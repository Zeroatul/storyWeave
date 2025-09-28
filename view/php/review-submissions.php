<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$first_initial = !empty($_SESSION["fullName"]) ? substr($_SESSION["fullName"], 0, 1) : '?';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Submissions: The Last Starlight - Story Weave</title>
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
                    <li><a href="story-analytics.php">Story Analytics</a></li>
                    <li><a href="review-submissions.php">Submissions</a></li>
                    <li><a href="logout.php">Log Out</a></li>
                </ul>
            </div>
        </nav>
    </header>
    <main class="review-container">
        <header class="review-header">
            <h1>Review Submissions for Chapter 2</h1>
            <p>Story: <em>The Last Starlight</em></p>
        </header>
        <div class="submissions-grid">
            <article class="submission-card is-winner">
                <header>
                    <h3>Submission by Alex Griffin</h3>
                </header>
                <div class="submission-content">
                    <p>The living map reveals a hidden chamber beneath the archives. Elara descends, discovering not just old relics, but a dormant guardian awakened by her presence. The guardian, made of stone and starlight, poses a riddle she must solve to truly understand the map's purpose. It speaks of a 'celestial key' that was shattered and hidden long ago.</p>
                </div>
                <footer class="card-footer">
                    <button class="btn btn-selected" disabled>✓ Winning Chapter</button>
                </footer>
            </article>
            <article class="submission-card">
                <header>
                    <h3>Submission by Maria Sanchez</h3>
                </header>
                <div class="submission-content">
                    <p>The map's light projects the spectral form of its creator, the long-dead Royal Cartographer. The ghost warns Elara that the book is a prison for a celestial entity, and opening it was a grave mistake. He explains that the constellations on the map are the bars of the cage, and they are slowly weakening. Elara must find a way to reinforce the ancient magic before it's too late.</p>
                </div>
                <footer class="card-footer">
                    <button class="btn btn-secondary">Select as Winner</button>
                </footer>
            </article>
            <article class="submission-card">
                <header>
                    <h3>Submission by David Chen</h3>
                </header>
                <div class="submission-content">
                    <p>Before Elara can decipher the map, a ruthless collector from a secret society arrives, revealing he has been hunting the book for decades. He offers her wealth and knowledge in exchange for it, but she senses a dark motive. A chase through the labyrinthine stacks of the library ensues, with Elara using her wits and knowledge of the archives to evade the powerful pursuer.</p>
                </div>
                <footer class="card-footer">
                    <button class="btn btn-secondary">Select as Winner</button>
                </footer>
            </article>
        </div>
    </main>
    <footer class="main-footer">
        <p>&copy; 2025 Story Weave. All Rights Reserved.</p>
    </footer>
</body>
</html>
