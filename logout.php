<?php
require_once('config.php');
$login = new Login();

$login->setConnection($con);

if (!$login->checkIfUserAuthenticated()) {
    Redirect::redirect_to('index.php');
} else {
    session_destroy();
    Redirect::redirect_to('index.php');
}
