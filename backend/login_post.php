<?php

require_once('../config.php');
require_once("classes/Login.php");
require_once("classes/Redirect.php");



if (isset($_POST['submit'])) {



    $required_fields = ['email', 'password'];


    foreach ($_POST as $key => $value) {
        if (in_array($key, $required_fields)) {
            $$key = $value;
        }
    }

    if ($email !== null and !empty($email) and $password !== null and !empty($password)) {


        $login = new Login();

        $login->setConnection($con);
        if (!$login->checkEmailValid($email)) {
            Redirect::redirect_to('../index.php?error=2');
        }

        if ($login->login($email, $password) AND $login->checkAccountActivated($email)) {
            $_SESSION['user_email'] = $email;
            Redirect::redirect_to('../profile.php');
        } else {
            Redirect::redirect_to('../index.php?error=3');
        }
    } else {
        Redirect::redirect_to('../index.php?error=1');
    }
}
