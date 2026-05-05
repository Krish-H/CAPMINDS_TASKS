<?php
session_start();
require_once 'includes/validation.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    //  Validate Inputs
    $username_valid = validate_username($username);
    if ($username_valid !== true) {
        $_SESSION['error'] = $username_valid;
        header("Location: login.php");
        exit();
    }

    $email_valid = validate_email($email);
    if ($email_valid !== true) {
        $_SESSION['error'] = $email_valid;
        header("Location: login.php");
        exit();
    }

    $password_valid = validate_password($password);
    if ($password_valid !== true) {
        $_SESSION['error'] = $password_valid;
        header("Location: login.php");
        exit();
    }

    // Authentication Rules & Theme Assignment
    $authenticated = false;
    $theme = 'light'; // Default theme

    if ($username === 'admin' && $email === 'admin@gmail.com' && $password === 'Admin@123') {
        $authenticated = true;
        $theme = 'dark';
    } elseif ($username === 'user2' && $email === 'user2@gmail.com' && $password === 'User2@123') {
        $authenticated = true;
        $theme = 'warm';
    } elseif ($username === 'user3' && $email === 'user3@gmail.com' && $password === 'User3@123') {
        $authenticated = true;
        $theme = 'light';
    }

    //  Handle Authentication Results
    if ($authenticated) {
        // Create Session Variables
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        $_SESSION['theme'] = $theme;

        // Cookie Logic
        $cookie_expiry = time() + 60; 

        if ($remember) {
            setcookie("remember_username", $username, $cookie_expiry, "/");
        } else {
            // Clear username cookie if remember me is not checked
            setcookie("remember_username", "", time() - 3600, "/");
        }
        

        setcookie("user_theme", $theme, $cookie_expiry, "/");

        header("Location: dashboard.php");
        exit();
    } else {
        // Invalid Auth
        $_SESSION['error'] = "Invalid username, email, or password.";
        header("Location: login.php");
        exit();
    }
} else {
  
    header("Location: login.php");
    exit();
}
