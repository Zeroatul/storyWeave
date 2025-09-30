<?php
session_start();
require_once '../model/db_connect.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../view/php/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $submission_id = isset($_POST['submission_id']) ? (int)$_POST['submission_id'] : 0;
    $story_id = isset($_POST['story_id']) ? (int)$_POST['story_id'] : 0;

    if ($submission_id > 0 && $story_id > 0) {
        // Start transaction
        mysqli_begin_transaction($conn);

        try {
            // 1. Get submission data
            $submission_sql = "SELECT user_id, for_chapter_number, title, content FROM submissions WHERE id = ? AND story_id = ?";
            $stmt = mysqli_prepare($conn, $submission_sql);
            mysqli_stmt_bind_param($stmt, "ii", $submission_id, $story_id);
            mysqli_stmt_execute($stmt);
            $submission_result = mysqli_stmt_get_result($stmt);
            $submission = mysqli_fetch_assoc($submission_result);
            mysqli_stmt_close($stmt);

            if ($submission) {
                // 2. Insert into chapters table
                $insert_chapter_sql = "INSERT INTO chapters (story_id, chapter_number, title, content, author_id) VALUES (?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $insert_chapter_sql);
                mysqli_stmt_bind_param($stmt, "iissi", $story_id, $submission['for_chapter_number'], $submission['title'], $submission['content'], $submission['user_id']);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                // 3. Update the winning submission status
                $update_winner_sql = "UPDATE submissions SET status = 'Approved' WHERE id = ?";
                $stmt = mysqli_prepare($conn, $update_winner_sql);
                mysqli_stmt_bind_param($stmt, "i", $submission_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                // 4. Reject other submissions for the same chapter
                $reject_others_sql = "UPDATE submissions SET status = 'Rejected' WHERE story_id = ? AND for_chapter_number = ? AND id != ?";
                $stmt = mysqli_prepare($conn, $reject_others_sql);
                mysqli_stmt_bind_param($stmt, "iii", $story_id, $submission['for_chapter_number'], $submission_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                // Commit transaction
                mysqli_commit($conn);

                $_SESSION['success_message'] = "Winner selected successfully! A new chapter has been added to the story.";
                header("location: ../view/php/review-submissions.php?story_id=" . $story_id);
                exit();
            } else {
                throw new Exception("Submission not found.");
            }
        } catch (Exception $e) {
            mysqli_rollback($conn);
            header("location: ../view/php/review-submissions.php?story_id=" . $story_id . "&error=" . urlencode($e->getMessage()));
            exit();
        }
    }
}
?>

