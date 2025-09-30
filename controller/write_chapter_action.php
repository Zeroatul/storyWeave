<?php
session_start();
require_once '../model/db_connect.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../view/php/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $story_id = isset($_POST['story_id']) ? (int)$_POST['story_id'] : 0;
    $user_id = $_SESSION['id'];
    $chapter_number = isset($_POST['chapter_number']) ? (int)$_POST['chapter_number'] : 0;
    $title = isset($_POST['chapterTitle']) ? trim($_POST['chapterTitle']) : '';
    $content = isset($_POST['chapterContent']) ? trim($_POST['chapterContent']) : '';

    if ($title === '' || $content === '' || $story_id <= 0) {
        header("location: ../view/php/write-chapter.php?story_id=" . $story_id . "&error=1");
        exit();
    }

    $sql = "INSERT INTO submissions (story_id, for_chapter_number, user_id, title, content) VALUES (?, ?, ?, ?, ?)";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "iiiss", $story_id, $chapter_number, $user_id, $title, $content);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_message'] = "Your chapter has been submitted for review!";
            header("location: ../view/php/read-story.php?story_id=" . $story_id);
            exit();
        } else {
            echo "Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);

} else {
    header("location: ../view/php/home.php");
    exit();
}
?>

