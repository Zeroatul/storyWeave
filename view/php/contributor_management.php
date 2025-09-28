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

// Fetch story details to ensure the logged-in user is the author
$story = null;
$sql_story = "SELECT title FROM stories WHERE id = ? AND user_id = ?";
if($stmt_story = mysqli_prepare($conn, $sql_story)){
    mysqli_stmt_bind_param($stmt_story, "ii", $story_id, $_SESSION['id']);
    if(mysqli_stmt_execute($stmt_story)){
        $result_story = mysqli_stmt_get_result($stmt_story);
        if(mysqli_num_rows($result_story) == 1){
            $story = mysqli_fetch_assoc($result_story);
        } else {
            // This user is not the owner of the story, or story doesn't exist.
            header("location: manage-stories.php");
            exit;
        }
    }
    mysqli_stmt_close($stmt_story);
}

// Fetch current and blocked contributors
$contributors = ['active' => [], 'blocked' => []];
$sql_contributors = "SELECT u.uname, c.status FROM contributors c JOIN user u ON c.user_id = u.id WHERE c.story_id = ?";
if($stmt_contributors = mysqli_prepare($conn, $sql_contributors)){
    mysqli_stmt_bind_param($stmt_contributors, "i", $story_id);
    if(mysqli_stmt_execute($stmt_contributors)){
        $result_contributors = mysqli_stmt_get_result($stmt_contributors);
        while($row = mysqli_fetch_assoc($result_contributors)){
            if($row['status'] == 'Active' || $row['status'] == 'Invited'){
                 $contributors['active'][] = $row;
            } elseif($row['status'] == 'Blocked'){
                 $contributors['blocked'][] = $row;
            }
        }
    }
    mysqli_stmt_close($stmt_contributors);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contributor Management - Story Weave</title>
    <link rel="stylesheet" href="../css/contributor-management.css">
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
    <main class="management-container">
        <header class="page-header">
            <h1>Contributor Management</h1>
            <p>For <em><?php echo htmlspecialchars($story['title']); ?></em></p>
        </header>
        <section class="management-widget">
            <h2 class="widget-title">Invite a Contributor</h2>
            <div class="invite-container">
                <form class="invite-form" id="invite-form" method="POST" action="../../controller/contributor_management_action.php">
                    <input type="hidden" name="story_id" value="<?php echo $story_id; ?>">
                    <input type="email" id="email" name="email" placeholder="Enter user's email address">
                    <button class="btn" type="submit">Send Invite</button>
                </form>
            </div>
        </section>
        <section class="management-widget">
            <h2 class="widget-title">Current Contributors</h2>
            <ul class="user-list">
                <?php if(!empty($contributors['active'])): ?>
                    <?php foreach($contributors['active'] as $contributor): ?>
                        <li class="user-list-item">
                            <span><?php echo htmlspecialchars($contributor['uname']); ?> (<?php echo htmlspecialchars($contributor['status']); ?>)</span>
                            <button class="btn btn-danger">Block</button>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="user-list-item"><span>No active contributors yet.</span></li>
                <?php endif; ?>
            </ul>
        </section>
        <section class="management-widget">
            <h2 class="widget-title">Blocked Users</h2>
            <ul class="user-list">
                 <?php if(!empty($contributors['blocked'])): ?>
                    <?php foreach($contributors['blocked'] as $contributor): ?>
                        <li class="user-list-item">
                            <span><?php echo htmlspecialchars($contributor['uname']); ?></span>
                            <button class="btn btn-secondary">Unblock</button>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="user-list-item"><span>No users have been blocked.</span></li>
                <?php endif; ?>
            </ul>
        </section>
    </main>
    <footer class="main-footer">
        <p>&copy; 2025 Story Weave. All Rights Reserved.</p>
    </footer>
</body>
</html>
