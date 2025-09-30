<?php
session_start();
require_once '../model/db_connect.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../view/php/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['storyTitle']);
    $genre = trim($_POST['genre']);
    $synopsis = trim($_POST['synopsis']);
    $user_id = $_SESSION["id"];

    if (!empty($title) && !empty($genre) && !empty($synopsis)) {
        $sql = "INSERT INTO stories (title, user_id, genre, synopsis) VALUES (?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "siss", $title, $user_id, $genre, $synopsis);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success_message'] = "Story created successfully!";
                header("location: ../view/php/manage-stories.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        header("location: ../view/php/create-story.php?error=1");
        exit();
    }
    mysqli_close($conn);

} else {
    header("location: ../view/php/create-story.php");
    exit();
}
?>
