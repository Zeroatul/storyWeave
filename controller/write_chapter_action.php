<?php
session_start();
require_once '../model/db_connect.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Content-Type: application/json");
    echo json_encode(['success' => false, 'error' => 'User not logged in.']);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $story_id = isset($_POST['story_id']) ? (int)$_POST['story_id'] : 0;
    $user_id = $_SESSION['id'];
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if ($story_id > 0 && !empty($title) && !empty($content)) {
        // Get the next chapter number
        $chapter_query = "SELECT MAX(for_chapter_number) as max_chapter FROM submissions WHERE story_id = ?";
        $next_chapter_number = 1;
        if($stmt_chap = mysqli_prepare($conn, $chapter_query)) {
            mysqli_stmt_bind_param($stmt_chap, "i", $story_id);
            mysqli_stmt_execute($stmt_chap);
            $result = mysqli_stmt_get_result($stmt_chap);
            if($row = mysqli_fetch_assoc($result)){
                 //This should check chapters table, not submissions
                 $chapter_query_main = "SELECT MAX(chapter_number) as max_chapter FROM chapters WHERE story_id = ?";
                 if($stmt_chap_main = mysqli_prepare($conn, $chapter_query_main)){
                    mysqli_stmt_bind_param($stmt_chap_main, "i", $story_id);
                    mysqli_stmt_execute($stmt_chap_main);
                    $result_main = mysqli_stmt_get_result($stmt_chap_main);
                    if($row_main = mysqli_fetch_assoc($result_main)){
                        $next_chapter_number = $row_main['max_chapter'] + 1;
                    }
                 }
            }
        }


        $sql = "INSERT INTO submissions (story_id, user_id, for_chapter_number, title, content) VALUES (?, ?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "iiiss", $story_id, $user_id, $next_chapter_number, $title, $content);

            if (mysqli_stmt_execute($stmt)) {
                // Set a session variable for the success message
                $_SESSION['success_message'] = "Your chapter has been submitted successfully for review!";
                header("location: ../view/php/read-story.php?story_id=" . $story_id);
                exit();
            }
        }
    }
    // Redirect with error if something fails
    header("location: ../view/php/write-chapter.php?story_id=" . $story_id . "&error=1");
    exit();
}
?>

