<?php

require_once("config.php");
require_once('backend/classes/Login.php');
require_once("backend/classes/Redirect.php");
require_once("backend/classes/Profile.php");

$login = new Login();

$profile = new Profile();
$profile->setConnection($con);

$login->setConnection($con);

if (!$login->checkIfUserAuthenticated()) {
    Redirect::redirect_to('index.php');
}

$profile = new Profile();
$profile->setConnection($con);
$is_user_updated = false;

if (isset($_POST['submit_user_info'])) {
    $accepted_fields = ["email", "user", "password", "password_verify", "gender", "phone", "submit_user_info"];
    foreach ($_POST as $key => $value) {
        if (in_array($key, $accepted_fields)) {
            $$key = $value;
        }
    }
    $updated_user = $profile->updateUserInformation($email, $password, $password_verify, $user, $gender, $phone);
    if ($updated_user) {
        session_destroy();
        Redirect::redirect_to('index.php');
    }
}
?>
<?php include("head.php") ?>


<div class="container d-flex justify-content-center bg-body">

    <!--Grid row-->
    <div class="row d-flex justify-content-center ">
        <!--Grid column-->
        <div class="col-12 m-3 bg-white text-center">
            <h1>Profile Page:</h1>


            <p>Profile picture</p>
            <?php
            $user_id = $profile->getUserID($_SESSION['user_email']);
            $picture_path = $profile->getUserProfilePicture($user_id);
            ?>
            <img src="<?php echo $picture_path ?>" width="300" class="rounded-circle mx-auto d-block" alt="default">


            <form action="backend/profile_post.php" method="post" enctype="multipart/form-data">

                <input class="form-control" style="max-width: 300px;margin: 1em auto;" type="file" name="image" id="image">

                <input class="btn btn-info m-1" type="submit" value="Update profile picture">
            </form>

            <?php if (isset($_GET['success']) && $_GET['success'] === "false") {
                echo "<div class='alert alert-danger m-auto' style='max-width: 300px' role='alert'>
                        Upload valid image, with resolution 600x600, Maximum file size can be 2MB, and only JPG,PNG formats are accepted.
                    </div>";
            } ?>

            <?php if (isset($_GET['success']) && $_GET['success'] === "true") {
                echo "<div class='alert alert-success m-auto' style='max-width: 300px' role='alert'>
                        Successfully uploaded!
                    </div>";
            } ?>
        </div>


        <form action="" class="m-3 p-2" method="post">
            <h2>Update your information:</h2>

            <?php $profile->printOutErrorsInHtml() ?>
            <?php
            if ($is_user_updated) {
                echo "<div class='alert alert-success m-1' role='alert'>
                        You successfully edited the user information!
                        </div>";
            }
            ?>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" name="email" id="email">
            </div>

            <div class="form-group">
                <label for="user">Username:</label>
                <input type="text" class="form-control" name="user" id="user">
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" name="password" id="password">
            </div>

            <div class="form-group">
                <label for="password_verify">Password Verify:</label>
                <input type="password" class="form-control" name="password_verify" id="password_verify">
            </div>
            <div class="form-group">
                <label for="gender">Gender:</label>

                <select name="gender" id="gender" class="form-control">
                    <?php $genders = ['men', 'women', 'other']; ?>
                    <?php

                    foreach ($genders as $key => $gender) {
                        $increment = $key + 1;
                        echo "<option value=". $increment . ">$gender</option>";
                    }
                    ?>
                </select>
                </label>
            </div>


            <div class="form-group">
                <label for="phone">Phone no:</label>
                <input type="tel" class="form-control" name="phone" id="phone">
            </div>

            <input type="submit" class="btn btn-info" name="submit_user_info" value="Update user info!">

        </form>


    </div>




</div>

</div>
</body>

</html>