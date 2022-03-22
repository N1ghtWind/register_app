<?php

require_once("config.php");

$isRegisted = false;
$register = new Register();
$register->setConnection($con);
if (isset($_POST['submit'])) {
    $required_fields = ["email", "user", "password", "password_verify", "gender", "country", "city", "pcode", "address"];
    $accepted_fields = [
        "email", "user", "password",
        "password_verify", "gender", "phone",
        "country", "city", "pcode", "address", "floor", "apartman"
    ];

    foreach ($_POST as $key => $value) {
        if (in_array($key, $accepted_fields)) {
            $$key = $value;
        }
    }
    $success_user_data = $register->validateUserData(
        $email,
        $password,
        $password_verify,
        $user,
        $gender,
        $country,
        $city,
        $pcode,
        $address,
        $phone,
        $floor,
        $apartman
    );



    if ($success_user_data) {
        $success_user_address = $register->insertUserData(
            $email,
            $password,
            $password_verify,
            $user,
            $gender,
            $country,
            $city,
            $pcode,
            $address,
            $phone,
            $floor,
            $apartman
        );

        if ($success_user_address and $register->registerUserProfile()) {
            $verification = new Verification($con);
            $code = $verification->generateUniqueCode();
            $verification->insertCodeToDatabase($code, $email);
            $link = "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/verify.php?code=" . $code;
            $mailer = new Mailer();
            $mailer->sendActivateMail($email, $link);
            $isRegisted = true;
        }
    } else {
    }
}

if (isset($_SESSION["user_email"])) {
    header("Location: profile.php");
}

?>


<!-- FRONT-END -->

<?php include("head.php") ?>
<div class="container d-flex justify-content-center bg-body">

    <!--Grid row-->
    <div class="row d-flex justify-content-center m-3 w-100">
        <!--Grid column-->
        <div class="col-12 m-3 bg-white" style="max-width: 400px">
            <div class="text-center justify-content-center d-flex m-auto flex-column" style="max-width:500px">
                <?php $register->printOutErrorsInHtml();
                ?>
            </div>
            <h1>Register</h1>

            <a href="index.php" class="link-info">Go to login Page</a>
            <?php if ($isRegisted) {
                echo "<div class='alert alert-success mt-2 mb-2' role='alert'>
                <strong>Successfuly registered</strong>
             </div>";
            }
            ?>
            <form method="post">
                <div class="form-group">
                    <label for="email">*Email:</label>
                    <input type="email" class="form-control" name="email" id="email">
                </div>

                <div class="form-group">
                    <label for="user">*Username:</label>
                    <input type="text" class="form-control" name="user" id="user">
                </div>

                <div class="form-group">
                    <label for="password">*Password:</label>
                    <input type="password" class="form-control" name="password" id="password">
                </div>

                <div class="form-group">
                    <label for="password_verify">*Password Verify:</label>
                    <input type="password" class="form-control" name="password_verify" id="password_verify">
                </div>

                <div class="form-group">
                    <label for="gender">*Gender:</label>

                    <select name="gender" id="gender" class="form-control">
                        <option value="1" selected>Man</option>
                        <option value="2">Woman</option>
                        <option value="3">Other</option>
                    </select>
                    </label>
                </div>


                <div class="form-group">
                    <label for="phone">*Phone no:</label>
                    <input type="tel" class="form-control" name="phone" id="phone">
                </div>


                <h2>Address data:</h2>


                <div class="form-group">
                    <label for="xountry">*Country:</label>
                    <select name="country" id="country" class="form-control">
                        <option value="1">Serbia</option>
                        <option value="2">Hungary</option>
                        <option value="3">Germany</option>
                    </select>
                </div>


                <div class="form-group">
                    <label for="city">*City:</label>
                    <input type="text" class="form-control" name="city" id="city">
                </div>


                <div class="form-group">
                    <label for="pcode">*Postal code:</label>
                    <input type="text" class="form-control" placeholder="utca, hsz" name="pcode" id="pcode">
                </div>

                <div class="form-group">
                    <label for="address">*Address:</label>
                    <input type="text" class="form-control" name="address" id="address">

                </div>

                <div class="form-group">
                    <label for="floor">Floor:</label>
                    <input type="text" class="form-control" name="floor" id="floor">

                </div>

                <div class="form-group">
                    <label for="apartman">Apartman No:</label>
                    <input type="text" class="form-control" name="apartman" id="apartman">
                </div>
                <br><br>
                <input type="submit" class="btn btn-info" name="submit" value="Register!">
            </form>





        </div>
        <!--Grid column-->

    </div>
</div>
</body>

</html>