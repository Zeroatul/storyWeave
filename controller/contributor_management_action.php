<?php
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
        $errors = [
            'emailError' => $emailError,
            'email' => trim($_POST["email"])
        ];
        header("location: ../view/php/contributor_management.php?" . http_build_query($errors));
        exit();
    } else {
        $successData = [
            'success' => 1,
            'invited_email' => $email
        ];
        header("location: ../view/php/contributor_management.php?" . http_build_query($successData));
        exit();
    }
} else {
    header("location: ../view/php/contributor_management.php");
    exit();
}
?>
