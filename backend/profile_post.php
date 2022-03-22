<?php
require_once("../config.php");
require_once("../backend/classes/Redirect.php");
require_once("../backend/classes/Profile.php");
require_once("../backend/classes/Login.php");

$login = new Login();

$login->setConnection($con);

if (!$login->checkIfUserAuthenticated()) {
    Redirect::redirect_to('index.php?error=4');
}

$profile = new Profile();
$profile->setConnection($con);
$email = $_SESSION['user_email'];

if (isset($_FILES['image']) && $_FILES['image']["error"] === 0) {

    // Get Image Dimension
    $fileinfo = @getimagesize($_FILES["image"]["tmp_name"]);
    $width = $fileinfo[0];
    $height = $fileinfo[1];



    $errors = array();
    $file_name = $_FILES['image']['name'];
    $file_size = $_FILES['image']['size'];
    $file_tmp = $_FILES['image']['tmp_name'];
    $file_type = $_FILES['image']['type'];
    $tmp = explode('.', $file_name);
    $file_ext = strtolower(end($tmp));

    $filename = pathinfo($_FILES['image']['name'], PATHINFO_FILENAME);
    $filename .= time();





    $extensions = array("jpeg", "jpg", "png");

    if (!in_array($file_ext, $extensions)) {
        $errors[] = "extension not allowed, please choose a JPEG or PNG file.";
    }

    if ($file_size > 2097152) {
        $errors[] = 'File size must be excately 2 MB';
    }

    if ($width > "600" || $height > "600") {
        $errors[] = "Image dimension should be  600X600";
    }

    if (empty($errors)) {
        $file_new_name = $filename . "." . $file_ext;

        isset($_SESSION['user_email']) ?
            $email = $_SESSION['user_email'] :
            Redirect::redirect_to('../profile.php?error=1');

        $user_id = $profile->getUserID($email);
        
        if (!file_exists("../storage/users/$user_id/profile/")) {
            mkdir("../storage/users/$user_id/profile/", 0777, true);
        }

        move_uploaded_file($file_tmp, "../storage/users/$user_id/profile/" . $file_new_name);
        $user_id = $profile->getUserID($email);
        $file_new_path = "storage/users/$user_id/profile/" . $file_new_name;
        $profile->updateProfilePicture($email, $file_new_path, $user_id);

        Redirect::redirect_to("../profile.php?success=true");
    } else {
        Redirect::redirect_to("../profile.php?success=false");
    }
} else {
    Redirect::redirect_to('../profile.php?error=1');
}
