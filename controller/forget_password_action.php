<?php
session_start();
$email = "";
$emailError = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["email"]))) {
        $emailError = "Email address is required.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $emailError = "Please enter a valid email address.";
    } else {
        $email = trim($_POST["email"]);
    }

    if (!empty($emailError)) {
        $_SESSION['forgot_password_errors'] = [
            'emailError' => $emailError,
            'email' => trim($_POST["email"])
        ];
        header("location: ../view/php/forget_password.php");
        exit();
    } else {
        $_SESSION['forgot_password_success'] = [
            'sent_email' => $email
        ];
        header("location: ../view/php/forget_password.php");
        exit();
    }
} else {
    header("location: ../view/php/forget_password.php");
    exit();
}
?>
