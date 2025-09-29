<?php
session_start();
require_once '../model/db_connect.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo json_encode(['success' => false, 'error' => 'You must be logged in to comment.']);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $story_id = isset($_POST['story_id']) ? (int)$_POST['story_id'] : 0;
    $user_id = $_SESSION['id'];
    $comment_text = trim($_POST['comment']);

    if ($story_id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid story ID.']);
        exit;
    }

    if (empty($comment_text)) {
        echo json_encode(['success' => false, 'error' => 'Comment cannot be empty.']);
        exit;
    }

    $sql = "INSERT INTO comments (story_id, user_id, comment_text) VALUES (?, ?, ?)";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "iis", $story_id, $user_id, $comment_text);

        if (mysqli_stmt_execute($stmt)) {
            echo json_encode([
                'success' => true,
                'author' => $_SESSION['fullName'],
                'comment_text' => htmlspecialchars($comment_text),
                'created_at' => date('M d, Y')
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to post comment.']);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['success' => false, 'error' => 'Database error.']);
    }
    mysqli_close($conn);
}
?>
