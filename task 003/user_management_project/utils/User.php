<?php

namespace utils;

class User {

    public $username;
    public $email;
    public $password;

    //constructor
    function __construct($user_name, $user_email, $user_password) {
        $this->username = $user_name;
        $this->email = $user_email;
        $this->password = $user_password;
    }

    public function displayUser() {
        return "User: {$this->username}, Email: {$this->email}";
    }
}



?>
