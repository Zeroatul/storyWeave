<?php
session_start();
require_once '../model/db_connect.php';

$fullName = $email = $password = $user_type = "";
$fullNameError = $emailError = $passwordError = "";

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

    if (empty(trim($_POST["password"]))) {
        $passwordError = "Password is required.";
    } elseif (strlen(trim($_POST["password"])) < 8) {
        $passwordError = "Password must be at least 8 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    $user_type = $_POST["user_type"] ?? 'reader';

    if (empty($fullNameError) && empty($emailError) && empty($passwordError)) {

        $sql_check_email = "SELECT id FROM user WHERE email = ?";

        if($stmt_check = mysqli_prepare($conn, $sql_check_email)){
            mysqli_stmt_bind_param($stmt_check, "s", $param_email);
            $param_email = $email;

            if(mysqli_stmt_execute($stmt_check)){
                mysqli_stmt_store_result($stmt_check);

                if(mysqli_stmt_num_rows($stmt_check) == 1){
                    $emailError = "This email is already taken.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt_check);
        }
    }

    if (empty($fullNameError) && empty($emailError) && empty($passwordError)) {
        $sql = "INSERT INTO user (uname, email, pass) VALUES (?, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "sss", $param_uname, $param_email, $param_pass);

            $param_uname = $fullName;
            $param_email = $email;
            $param_pass = $password;

            if (mysqli_stmt_execute($stmt)) {
                header("location: ../view/php/login.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    if (!empty($fullNameError) || !empty($emailError) || !empty($passwordError)) {
        $_SESSION['registration_errors'] = [
            'fullNameError' => $fullNameError,
            'emailError' => $emailError,
            'passwordError' => $passwordError,
            'fullName' => $fullName,
            'email' => $email,
            'user_type' => $user_type
        ];
        header("location: ../view/php/registration.php");
        exit();
    }

    mysqli_close($conn);

} else {
    header("location: ../view/php/registration.php");
    exit();
}
?>

