<?php

$dir =  __DIR__;
require_once('backend/classes/Login.php');
require_once("backend/classes/Redirect.php");
require_once("backend/classes/Profile.php");
require_once("backend/classes/Register.php");
require_once("backend/classes/Verification.php");
require_once("backend/classes/Mailer.php");
// require_once("backend/classes/Address.php");


$servername = "{servername}";
$username = "{username}";
$password = "{password}";
$DB = "{database}";

session_start();


$con = null;
try {
    $con = new PDO("mysql:host=$servername;dbname=$DB", $username, $password);
    // set the PDO error mode to exception
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Can't connect to database";
    die;
}
