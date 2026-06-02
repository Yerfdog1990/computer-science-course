<?php
session_start();
$conn = require __DIR__ . '/../config/dbcon.php';
if (isset($_POST['registration_btn']))
{
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirmed_password = mysqli_real_escape_string($conn, $_POST['confirmed_password']);

    // Check if email already exists
    $check_email_query = "SELECT email FROM users WHERE email='$email'";
    $check_email_query_run = mysqli_query($conn, $check_email_query);
    if (mysqli_num_rows($check_email_query_run) > 0) {
        $_SESSION['message'] = "Email already exists";
        header("Location: ../register.http-and-session");
        exit();
    }

    // Check if the password matches the confirmed password
    if ($password == $confirmed_password) {
        // Insert user data
        $insert_query = "INSERT INTO users (name, phone, email, password) VALUES ('$name', '$phone', '$email', '$password')";
        $insert_query_run = mysqli_query($conn, $insert_query);
        if ($insert_query_run) {
            $_SESSION['message'] = "Registration successful";
            header("Location: ../login.http-and-session");
        }
        else {
            $_SESSION['message'] = "Registration failed";
            header("Location: ../register.http-and-session");
        }
    }
    else
    {
       $_SESSION['message'] = "Password do not match";
       header("Location: ../register.http-and-session");
    }
    exit();
}
