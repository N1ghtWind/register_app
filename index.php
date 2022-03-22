<?php


require_once("config.php");
require_once('backend/classes/Login.php');
require_once('backend/classes/Redirect.php');
$login = new Login();

$login->setConnection($con);

if ($login->checkIfUserAuthenticated()) {
    Redirect::redirect_to('profile.php');
}

?>
<?php include("head.php") ?>

<div class="container d-flex justify-content-center bg-body">

    <!--Grid row-->
    <div class="row d-flex justify-content-center m-3">
        <!--Grid column-->
        <div class="col-12 m-3 bg-white">
            <h1>Login:</h1>
            <form action="backend/login_post.php" method="post">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" class="form-control" id="email">
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" name="password" id="password">
                </div>



                <input type="submit" class="btn btn-info" name="submit" value="Login!">

                <a class="link-info" href="register.php">Don't you have an account? Click here!</a>


                <?php
                if (isset($_GET['error'])) {
                    echo  "<div class='alert alert-danger m-1' role='alert'>
                            You need to fill all the fields
                            </div>";
                }
                ?>
            </form>
        </div>
    </div>
</div>
</body>

</html>