<?php
session_start();
require_once '../model/db_connect.php'; // Ensure you have the database connection

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../view/php/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['storyTitle']);
    $genre = trim($_POST['genre']);
    $synopsis = trim($_POST['synopsis']);
    $user_id = $_SESSION["id"]; // Get the logged-in user's ID

    if (!empty($title) && !empty($genre) && !empty($synopsis)) {

        // Prepare an insert statement using user_id for consistency
        $sql = "INSERT INTO stories (title, user_id, genre, synopsis) VALUES (?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "siss", $title, $user_id, $genre, $synopsis);

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Story created successfully, redirect to manage stories page
                header("location: ../view/php/manage-stories.php?story_created=1");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    } else {
        // If fields are empty, redirect back with an error
        header("location: ../view/php/create-story.php?error=1");
        exit();
    }

    // Close connection
    mysqli_close($conn);

} else {
    // If not a POST request, redirect to the create story page
    header("location: ../view/php/create-story.php");
    exit();
}
?>

