<?php
require_once("config.php");
require_once("head.php");

if ($login->checkIfUserAuthenticated()) {
    Redirect::redirect_to('profile.php');
}

if (isset($_GET['code']) && !empty($_GET['code'])) {
    $code = $_GET['code'];
    $verification = new Verification($con);
    $isCodeValid = $verification->checkTheCodeInDatabase($code);

    if (!$isCodeValid) {
        header("Location: index.php");
    } else {
        echo "<h1 id='valtext'>Valid activation!</h1>";
        $verification->activateTheAccount($code);
        $email = $verification->getEmailByCode("verification",$code); 
        $verification->deleteRecordAfterActivation($email);
    }
} else {
    header("Location: index.php");
}
?>

<button class="btn btn-primary"><a class="text-light" href="index.php">Login In!</a></button>