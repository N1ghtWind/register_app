<?php

require_once("config.php");
require_once('backend/classes/Login.php');
require_once("backend/classes/Redirect.php");
require_once("backend/classes/Profile.php");
require_once("backend/classes/Address.php");

$login = new Login();
$profile = new Profile();

$profile->setConnection($con);
$user_id = $profile->getUserID($_SESSION['user_email']);

$addressObject = new Address($con);
$login->setConnection($con);

$is_address_edited = false;

if (!$login->checkIfUserAuthenticated() || !isset($_GET['id'])) {
    Redirect::redirect_to('index.php');
}

$fetched_address = $addressObject->getAddressByUserIdAndAddressId($_GET['id'], $user_id);

if (!$fetched_address) {
    Redirect::redirect_to('index.php');
}

if (isset($_POST['submit_address_info'])) {
    $accepted_fields = ["country", "city", "pcode", "address", "floor", "apartman", "submit"];
    foreach ($_POST as $key => $value) {
        if (in_array($key, $accepted_fields)) {
            $$key = $value;
        }
    }

    $updated = $addressObject->updateSpecificAddress($country, $city, $pcode, $address, $floor, $apartman);
    if ($updated) {
        $is_address_edited = true;
    }
}



?>



<?php require_once("head.php"); ?>


<div class="container d-flex justify-content-center bg-body">

    <!--Grid row-->
    <div class="row d-flex justify-content-center ">
        <!--Grid column-->
        <div class="col-12 m-3 bg-white text-center">
            <h1>Edit Address:</h1>


            <form action="" class="m-3 p-2" method="post">
                <h2>Update address data:</h2>

                <?php
                if ($is_address_edited) {
                    echo "<div class='alert alert-success m-1' role='alert'>
                        You successfully edited the address!
                        </div>";
                }
                ?>

                <?php
                $addressObject->printOutErrorsInHtml();
                ?>
                <div class="form-group">
                    <label for="country">*Country:</label>
                    <select name="country" id="country" class="form-control">
                        <?php $countries = ['Serbia', 'Hungary', 'Germany']; ?>
                        <?php

                        foreach ($countries as $key => $country) {
                            $increment = $key + 1;
                            echo "<option value=" . $increment . " " . ($fetched_address['country'] === $country ? 'selected' : '') . ">$country</option>";
                        }
                        ?>
                        <!-- <option value="1">Serbia</option>
                        <option value="2">Hungary</option>
                        <option value="3">Germany</option> -->
                    </select>
                </div>


                <div class="form-group">
                    <label for="city">*City:</label>
                    <input type="text" class="form-control" name="city" id="city" value="<?php echo  $fetched_address['city']; ?>">
                </div>


                <div class="form-group">
                    <label for="pcode">*Postal code:</label>
                    <input type="text" class="form-control" placeholder="utca, hsz" name="pcode" value="<?php echo  $fetched_address['zip_code']; ?>" id="pcode">
                </div>

                <div class="form-group">
                    <label for="address">*Address:</label>
                    <input type="text" class="form-control" name="address" id="address" value="<?php echo  $fetched_address['address']; ?>">

                </div>

                <div class="form-group">
                    <label for="floor">Floor:</label>
                    <input type="text" class="form-control" name="floor" id="floor" value="<?php echo ($fetched_address['floor'] === "0" ? "" : $fetched_address['floor']); ?>">

                </div>

                <div class="form-group">
                    <label for="apartman">Apartman No:</label>
                    <input type="text" class="form-control" name="apartman" id="apartman" value="<?php echo  $fetched_address['apartman_no']; ?>">
                </div>
                <br><br>
                <input type="submit" class="btn btn-info" name="submit_address_info" value="Update Address info!">
            </form>


        </div>
    </div>
</div>