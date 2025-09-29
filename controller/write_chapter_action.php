<?php
session_start();
require_once '../model/db_connect.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../view/php/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $story_id = filter_input(INPUT_POST, 'story_id', FILTER_VALIDATE_INT);
    $for_chapter_number = filter_input(INPUT_POST, 'for_chapter_number', FILTER_VALIDATE_INT);
    $user_id = $_SESSION["id"];
    $content = trim($_POST['chapterContent']);
    $title = trim($_POST['chapterTitle']); // Get the title from the form

    if ($story_id && $for_chapter_number && !empty($content) && !empty($title)) {

        // Prepare an insert statement
        $sql = "INSERT INTO submissions (story_id, for_chapter_number, title, user_id, content) VALUES (?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "iisis", $story_id, $for_chapter_number, $title, $user_id, $content);

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Submission successful, redirect back to the story page
                header("location: ../view/php/read-story.php?story_id=" . $story_id . "&submission=success");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    } else {
        // If fields are empty or invalid, redirect back with an error
        header("location: ../view/php/write-chapter.php?story_id=" . $story_id . "&error=1");
        exit();
    }

    // Close connection
    mysqli_close($conn);

} else {
    // If not a POST request, redirect
    header("location: ../view/php/story-library.php");
    exit();
}
?>

