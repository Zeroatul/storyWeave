<?php
session_start();
require_once '../model/db_connect.php';

$email = $password = "";
$emailError = $passwordError = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty(trim($_POST["email"]))) {
        $emailError = "Email address is required.";
    } else {
        $email = trim($_POST["email"]);
    }

    if (empty(trim($_POST["password"]))) {
        $passwordError = "Password is required.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty($emailError) && empty($passwordError)) {
        $sql = "SELECT id, uname, email, pass FROM user WHERE email = ?";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = $email;

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $id, $uname, $db_email, $db_password);
                    if (mysqli_stmt_fetch($stmt)) {
                        if ($password == $db_password) {
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["email"] = $db_email;
                            $_SESSION["fullName"] = $uname;

                            header("location: ../view/php/home.php");
                            exit;
                        } else {
                            $passwordError = "The password you entered was not valid.";
                        }
                    }
                } else {
                    $emailError = "No account found with that email.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    if (!empty($emailError) || !empty($passwordError)) {
        // Store errors and email in session instead of URL
        $_SESSION['login_errors'] = [
            'emailError' => $emailError,
            'passwordError' => $passwordError,
            'email' => trim($_POST["email"])
        ];
        // Redirect without query string
        header("location: ../view/php/login.php");
        exit();
    }

    mysqli_close($conn);
} else {
    header("location: ../view/php/login.php");
    exit();
}
?>

