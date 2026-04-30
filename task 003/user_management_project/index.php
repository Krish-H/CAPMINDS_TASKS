<?php

// require_once →  file should exist , if not the  program won't run 
require_once "utils/User.php";
require_once "utils/Validator.php";


// include_once → program run even file is not exist 
include_once "utils/helpers.php";

/*
Why require_once is used instead of include:

include => only gives a warning if file is missing
program will continue running it will break the app later
like If User.php is not loaded  Class User not found  error will occur
So we use require_once for safety

*/

//namespace  with aliasing
use Utils\User;
use Utils\Validator as UserValidator;

$user_data = require "data/users.php";

$validator = new UserValidator();

foreach ($user_data as $data) {

    $user = new User($data['username'], $data['email'], $data['password']);
    echo $user->displayUser($validator);
    

}
