<?php

require_once("config.php");
require_once('backend/classes/Login.php');
require_once("backend/classes/Redirect.php");
require_once("backend/classes/Profile.php");
require_once("backend/classes/Address.php");

$login = new Login();
$addressObject = new Address($con);
$login->setConnection($con);

if (!$login->checkIfUserAuthenticated()) {
    Redirect::redirect_to('index.php');
}
?>

<?php

$is_address_added = false;

require_once("head.php");

if (isset($_POST['submit'])) {
    $accepted_fields = ["country", "city", "pcode", "address","floor","apartman","submit"];
    foreach ($_POST as $key => $value) {
        if(in_array($key,$accepted_fields)) {
            $$key = $value;
        }    
    }
    $success = $addressObject->addNewAddressToDatabase($country, $city, $pcode, $address,$floor,$apartman);

    if($success) {
        $is_address_added = true;
    }
}


?>

<div class="container d-flex justify-content-center bg-body">

    <!--Grid row-->
    <div class="row d-flex justify-content-center m-3">
        <!--Grid column-->
        <div class="col-12 m-3 bg-white">
            <form method="POST">
                <h2>Add Address:</h2>


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
                <input type="submit" class="btn btn-info" name="submit" value="Add Address!">

                <?php
                    if($is_address_added) {
                        echo "<div class='alert alert-success m-2' role='alert'>
                        New Address has been added!
                      </div>";
                    }
                ?>
            </form>
        </div>
    </div>
</div>