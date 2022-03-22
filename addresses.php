<?php

require_once("config.php");
require_once('backend/classes/Login.php');
require_once("backend/classes/Redirect.php");
require_once("backend/classes/Profile.php");
require_once("backend/classes/Address.php");

$login = new Login();

$login->setConnection($con);

if (!$login->checkIfUserAuthenticated()) {
    Redirect::redirect_to('index.php');
}
?>

<?php

require_once("head.php");

$error_happened = false;
$address = new Address($con);

$user_addresses = $address->getAddressesForUser($_SESSION['user_email']);

if (isset($_POST['main_address'])) {
    $main_address_validated = $address->validateAddressForChange($_SESSION['user_email'], $_POST['main_address']);
    
    if ($main_address_validated) {
        $successfully_inserted = $address->selectAddress($_POST['main_address'], 'is_main_address');
        if ($successfully_inserted) {
            header("Location: addresses.php");
        }
    } else {
      
        $error_happened = true;
    }
}

if (isset($_POST['billing_address'])) {
    $billing_address_validated = $address->validateAddressForChange($_SESSION['user_email'], $_POST['billing_address']);
    if ($billing_address_validated) {
        $successfully_inserted = $address->selectAddress($_POST['billing_address'], 'is_billing_address');
        if ($successfully_inserted) {
            header("Location: addresses.php");
        }
    } else {
        $error_happened = true;
    }
}

?>

<div class="container d-flex justify-content-center bg-body">

    <!--Grid row-->
    <div class="row d-flex justify-content-center m-3">
        <!--Grid column-->
        <div class="col-12 m-3 bg-white">
            <h1 class="text-center">Addresses:</h1>
            <?php
            foreach ($user_addresses as $key => $user_address) {
                $id = $user_address['id'];
                $increment = $key + 1;

                echo  "<div class='card m-4'>
                <div class='card-header'>
                    # " . $increment . " Address
                </div>
                <div class='card-body'>


                        <div class='d-flex'>";
                if ($user_address['is_main_address']) {
                    echo "<h5><span class='badge badge-success m-1'>Main Address</span></h5>";
                }
                if ($user_address['is_billing_address']) {
                    echo "<h5><span class='badge badge-warning m-1'>Billing Address</span></h5>";
                }
                echo "</div>
                    <p class='card-text'>Country: " . $user_address["country"] . "</p>

                    <p class='card-text'>City: " . $user_address["city"] . "</p>
                    <p class='card-text'>Postal Code: " . $user_address["address"] . "</p>
                    <p class='card-text'>Postal Code: " . $user_address["zip_code"] . "</p>"
                    . ($user_address["floor"] !== '0' ? "<p class='card-text'>Floor: " . $user_address["floor"] . "</p>" : "")

                    . ($user_address["apartman_no"] !== '' && $user_address["apartman_no"]  ? "<p class='card-text'>Apartman no: " . $user_address["apartman_no"] . "</p>" : "") .
                    "<form class='d-inline mx-2' action='' method='POST'>
                        <input type='hidden' name='main_address' value=" . $user_address["id"] . ">
                        <input class='btn btn-primary' type='submit' value='Set as main address'>
                    </form>
                        <form class='d-inline mx-2' action='' method='POST'>
                        <input type='hidden' name='billing_address' value=" . $user_address["id"] . ">
                        <input class='btn btn-primary' type='submit' value='Set as billing address'>
                    </form>
                    <a class='btn btn-warning mx-2' href='edit_address.php?id=$id'>Change</a>
                </div>
            </div>";
            }
            if ($error_happened) {
                echo "<div class='alert alert-danger ' role='alert'>
               Something went wrong! 
               </div>";
            }

            ?>





        </div>

    </div>
</div>