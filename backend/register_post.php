<?php

require_once('../config.php');
$error_array = [];

// string(30) "email =>terezsak@gmail.com
// " string(16) "user =>Jozsi
// " string(22) "password =>test123
// " string(29) "password_verify =>test123
// " string(14) "gender =>0
// " string(21) "phone =>064321232
// " string(15) "country =>1
// " string(19) "city =>Budapest
// " string(16) "pcode =>1024
// " string(32) "address =>Petofi Sandor u 38
// " string(12) "floor =>
// " string(16) "apartman =>2
// " string(22) "submit =>Register!
// "

if (isset($_POST['submit'])) {
    $required_fields = ["email", "user", "password", "password_verify", "gender", "country", "city", "pcode", "address"];

    $gender_options = ["1", "2", "3"];

    foreach ($required_fields as $key => $item) {

        if (empty($_POST[$item]) || $_POST[$item] === "" || $_POST[$item] === null) {
            array_push($error_array, $item);
            // var_dump($error_array);
        }
    }
    if (!checkEmail($_POST['email'])) {
        array_push($error_array, "Email was not valid");
    }
    if (!in_array($_POST['gender'], $gender_options)) {
        array_push($error_array, "Gender");
    }



    if (isErrorArrayEmpty()) {
        // echo "Empty";
    } else {
        // var_dump($error_array);
       
    }
}
function isErrorArrayEmpty()
{
    global $error_array;
    if (count($error_array) === 0) {
        return true;
    } else {
        return false;
    }
}

function checkEmail(string $email)
{
    $email = strtolower($email);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    } else {
        return true;
    }
}
