<?php
session_start();
require_once '../model/db_connect.php';

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../view/php/login.php");
    exit;
}

$oldPassword = $newPassword = $confirmPassword = "";
$oldPasswordError = $newPasswordError = $confirmPasswordError = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty(trim($_POST["oldPassword"]))) {
        $oldPasswordError = "Old password is required.";
    } else {
        $oldPassword = trim($_POST["oldPassword"]);
    }

    if (empty(trim($_POST["newPassword"]))) {
        $newPasswordError = "New password is required.";
    } elseif (strlen(trim($_POST["newPassword"])) < 8) {
        $newPasswordError = "Password must be at least 8 characters.";
    } else {
        $newPassword = trim($_POST["newPassword"]);
    }

    if (empty(trim($_POST["confirmPassword"]))) {
        $confirmPasswordError = "Please confirm your new password.";
    } else {
        $confirmPassword = trim($_POST["confirmPassword"]);
        if (empty($newPasswordError) && ($newPassword != $confirmPassword)) {
            $confirmPasswordError = "Passwords do not match.";
        }
    }

    if (empty($oldPasswordError) && empty($newPasswordError) && empty($confirmPasswordError)) {
        $sql_get_pass = "SELECT pass FROM user WHERE id = ?";
        if ($stmt_get_pass = mysqli_prepare($conn, $sql_get_pass)) {
            mysqli_stmt_bind_param($stmt_get_pass, "i", $_SESSION["id"]);

            if (mysqli_stmt_execute($stmt_get_pass)) {
                mysqli_stmt_store_result($stmt_get_pass);
                if (mysqli_stmt_num_rows($stmt_get_pass) == 1) {
                    mysqli_stmt_bind_result($stmt_get_pass, $current_password);
                    if (mysqli_stmt_fetch($stmt_get_pass)) {
                        if ($oldPassword != $current_password) {
                            $oldPasswordError = "The old password you entered is incorrect.";
                        }
                    }
                }
            } else {
                echo "Oops! Something went wrong fetching password.";
            }
            mysqli_stmt_close($stmt_get_pass);
        }

        if (empty($oldPasswordError)) {
            $sql_update_pass = "UPDATE user SET pass = ? WHERE id = ?";
            if ($stmt_update_pass = mysqli_prepare($conn, $sql_update_pass)) {
                mysqli_stmt_bind_param($stmt_update_pass, "si", $newPassword, $_SESSION["id"]);

                if (mysqli_stmt_execute($stmt_update_pass)) {
                    session_destroy();
                    header("location: ../view/php/login.php?status=password_changed");
                    exit();
                } else {
                    echo "Something went wrong. Please try again later.";
                }
                mysqli_stmt_close($stmt_update_pass);
            }
        }
    }

    if (!empty($oldPasswordError) || !empty($newPasswordError) || !empty($confirmPasswordError)) {
        $errors = [
            'oldPasswordError' => $oldPasswordError,
            'newPasswordError' => $newPasswordError,
            'confirmPasswordError' => $confirmPasswordError,
        ];
        header("location: ../view/php/change_password.php?" . http_build_query($errors));
        exit();
    }
    mysqli_close($conn);
} else {
    header("location: ../view/php/change_password.php");
    exit();
}
?>

