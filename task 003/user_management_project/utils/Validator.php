<?php

namespace utils;

class Validator{
public function validateUsername($u_name){
return strlen($u_name) >= 3 ? "Valid" : "Invalid";
}

public function validateEmail($u_email){
return filter_var($u_email, FILTER_VALIDATE_EMAIL) ? "Valid" : "Invalid";
}

public function validatePassword($u_password){
 return strlen($u_password) >= 6 ? "Strong" : "Weak";
}

}

?>