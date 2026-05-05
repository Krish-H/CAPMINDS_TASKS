<?php


 // Validate username: must not be empty and should have at least 3 characters.

function validate_username($username) {
    $username = trim($username);
    if (empty($username)) {
        return "Username is required.";
    }
    if (strlen($username) < 3) {
        return "Username must be at least 3 characters long.";
    }
    return true; 
}

 // Validate email using PHP's built-in filter.

function validate_email($email) {
    $email = trim($email);
    if (empty($email)) {
        return "Email is required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Invalid email format.";
    }
    return true; 
}


 // Validate password: must not be empty.
 
function validate_password($password) {
    if (empty($password)) {
        return "Password is required.";
    }
    return true; 
}
