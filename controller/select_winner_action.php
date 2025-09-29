<?php
session_start();
require_once '../model/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../view/php/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $submission_id = filter_input(INPUT_POST, 'submission_id', FILTER_VALIDATE_INT);
    $story_id = filter_input(INPUT_POST, 'story_id', FILTER_VALIDATE_INT);
    $user_id = $_SESSION['id'];

    if (!$submission_id || !$story_id) {
        // Redirect if parameters are missing or invalid
        header("location: ../view/php/manage-stories.php?error=invalid_params");
        exit;
    }

    // --- Verify that the current user is the author of the story ---
    $sql_verify_author = "SELECT user_id FROM stories WHERE id = ? AND user_id = ?";
    $is_author = false;
    if($stmt_verify = mysqli_prepare($conn, $sql_verify_author)){
        mysqli_stmt_bind_param($stmt_verify, "ii", $story_id, $user_id);
        mysqli_stmt_execute($stmt_verify);
        mysqli_stmt_store_result($stmt_verify);
        if(mysqli_stmt_num_rows($stmt_verify) == 1){
            $is_author = true;
        }
        mysqli_stmt_close($stmt_verify);
    }

    if (!$is_author) {
        // If the user is not the author, they cannot approve submissions
        header("location: ../view/php/read-story.php?story_id=$story_id&error=not_author");
        exit;
    }

    // --- Proceed with selecting the winner ---

    // Start transaction
    mysqli_begin_transaction($conn);

    try {
        // 1. Get submission content (now including title)
        $submission_content = null;
        $sql_get_submission = "SELECT user_id, content, for_chapter_number, title FROM submissions WHERE id = ? AND story_id = ?";
        if ($stmt_get = mysqli_prepare($conn, $sql_get_submission)) {
            mysqli_stmt_bind_param($stmt_get, "ii", $submission_id, $story_id);
            mysqli_stmt_execute($stmt_get);
            $result = mysqli_stmt_get_result($stmt_get);
            $submission_content = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt_get);
        }

        if (!$submission_content) {
            throw new Exception("Submission not found.");
        }

        // 2. Update submission status to 'Approved'
        $sql_update_sub = "UPDATE submissions SET status = 'Approved' WHERE id = ?";
        $stmt_update = mysqli_prepare($conn, $sql_update_sub);
        mysqli_stmt_bind_param($stmt_update, "i", $submission_id);
        if (!mysqli_stmt_execute($stmt_update)) {
            throw new Exception("Failed to update submission status.");
        }
        mysqli_stmt_close($stmt_update);

        // 3. Insert new chapter with the submission's content and title
        $sql_insert_chapter = "INSERT INTO chapters (story_id, chapter_number, title, content, author_id) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert = mysqli_prepare($conn, $sql_insert_chapter);
        mysqli_stmt_bind_param($stmt_insert, "iissi",
            $story_id,
            $submission_content['for_chapter_number'],
            $submission_content['title'], // Use the title from the submission
            $submission_content['content'],
            $submission_content['user_id']
        );
         if (!mysqli_stmt_execute($stmt_insert)) {
            throw new Exception("Failed to create new chapter.");
        }
        mysqli_stmt_close($stmt_insert);

        // 4. Reject all other pending submissions for this chapter number
        $sql_reject_others = "UPDATE submissions SET status = 'Rejected' WHERE story_id = ? AND for_chapter_number = ? AND status = 'Pending'";
        $stmt_reject = mysqli_prepare($conn, $sql_reject_others);
        mysqli_stmt_bind_param($stmt_reject, "ii", $story_id, $submission_content['for_chapter_number']);
        if (!mysqli_stmt_execute($stmt_reject)) {
            throw new Exception("Failed to reject other submissions.");
        }
        mysqli_stmt_close($stmt_reject);

        // If all queries were successful, commit the transaction
        mysqli_commit($conn);
        header("location: ../view/php/review-submissions.php?story_id=$story_id&success=winner_selected");
        exit();

    } catch (Exception $e) {
        // If any query fails, roll back the changes
        mysqli_rollback($conn);
        header("location: ../view/php/review-submissions.php?story_id=$story_id&error=" . urlencode($e->getMessage()));
        exit();
    }

} else {
    header("location: ../view/php/manage-stories.php");
    exit();
}
?>

