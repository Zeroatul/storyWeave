<?php
session_start();
require_once '../model/db_connect.php';

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../view/php/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $story_id = $_POST["story_id"];

    if (empty($email) || empty($story_id)) {
        header("location: ../view/php/manage-stories.php");
        exit();
    }

    // Find user_id from email
    $user_id_to_invite = null;
    $sql_find_user = "SELECT id FROM user WHERE email = ?";
    if($stmt_find = mysqli_prepare($conn, $sql_find_user)){
        mysqli_stmt_bind_param($stmt_find, "s", $email);
        if(mysqli_stmt_execute($stmt_find)){
            $result = mysqli_stmt_get_result($stmt_find);
            if(mysqli_num_rows($result) == 1){
                $user_row = mysqli_fetch_assoc($result);
                $user_id_to_invite = $user_row['id'];
            }
        }
        mysqli_stmt_close($stmt_find);
    }

    if($user_id_to_invite){
        // Add user to contributors table with "Invited" status
        $sql_invite = "INSERT INTO contributors (story_id, user_id, status) VALUES (?, ?, 'Invited') ON DUPLICATE KEY UPDATE status = 'Invited'";
         if($stmt_invite = mysqli_prepare($conn, $sql_invite)){
            mysqli_stmt_bind_param($stmt_invite, "ii", $story_id, $user_id_to_invite);
            mysqli_stmt_execute($stmt_invite);
            mysqli_stmt_close($stmt_invite);
        }
    }

    header("location: ../view/php/contributor_management.php?story_id=" . $story_id);
    exit();

} else {
    header("location: ../view/php/manage-stories.php");
    exit();
}
?>
