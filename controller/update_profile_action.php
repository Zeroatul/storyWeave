<?php
require_once 'auth_check.php';
require_once '../model/db_connect.php';

$fullName = $email = $bio = "";
$fullNameError = $emailError = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty(trim($_POST["fullName"]))) {
        $fullNameError = "Full name is required.";
    } else {
        $fullName = trim($_POST["fullName"]);
    }

    if (empty(trim($_POST["email"]))) {
        $emailError = "Email address is required.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $emailError = "Please enter a valid email address.";
    } else {
        $email = trim($_POST["email"]);
    }

    $bio = trim($_POST["bio"]);

    if ($email != $_SESSION["email"] && empty($emailError)) {
        $sql_check_email = "SELECT id FROM user WHERE email = ?";
        if ($stmt_check = mysqli_prepare($conn, $sql_check_email)) {
            mysqli_stmt_bind_param($stmt_check, "s", $param_email);
            $param_email = $email;
            if (mysqli_stmt_execute($stmt_check)) {
                mysqli_stmt_store_result($stmt_check);
                if (mysqli_stmt_num_rows($stmt_check) > 0) {
                    $emailError = "This email is already taken by another account.";
                }
            }
            mysqli_stmt_close($stmt_check);
        }
    }

    if (empty($fullNameError) && empty($emailError)) {
        $sql = "UPDATE user SET uname = ?, email = ? WHERE id = ?";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssi", $param_uname, $param_email, $param_id);

            $param_uname = $fullName;
            $param_email = $email;
            $param_id = $_SESSION["id"];

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION["fullName"] = $fullName;
                $_SESSION["email"] = $email;
                $_SESSION['update_profile_success'] = "Your profile information has been saved.";
                header("location: ../view/php/update_profile.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    if (!empty($fullNameError) || !empty($emailError)) {
        $_SESSION['update_profile_errors'] = [
            'fullNameError' => $fullNameError,
            'emailError' => $emailError,
            'fullName' => $fullName,
            'email' => $email,
            'bio' => $bio
        ];
        header("location: ../view/php/update_profile.php");
        exit();
    }

    mysqli_close($conn);

} else {
    header("location: ../view/php/update_profile.php");
    exit();
}
?>

