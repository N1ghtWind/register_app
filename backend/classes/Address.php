<?php
require_once("backend/classes/Profile.php");
class Address
{
    private $con;
    private $error_array = array();

    public function __construct($con)
    {
        $this->con = $con;
    }
    public function getAddressesForUser($email)
    {
        $profile = new Profile();

        $profile->setConnection($this->con);


        $user_id = $profile->getUserID($email);
        $query = $this->con->prepare("SELECT * FROM `address` WHERE user_id = :uid");

        $query->BindValue(":uid", $user_id);
        $query->execute();

        $result = $query->fetchAll();

        return $result;
    }

    public function addNewAddressToDatabase($country, $city, $pcode, $address, $floor = null, $apartman = null)
    {

        $countries = ['Serbia', 'Hungary', 'Germany'];
        $this->countryValidation($country);


        $this->cityValidation($city);
        $this->pCodeValidation($pcode);
        $this->addressValidation($address);
        if (!$floor == null) {
            $this->floorValidation($floor);
        }
        if (!$apartman == null) {
            $this->apartmanValidation($apartman);
        }

        $country = intval($country - 1);
        $profile = new Profile();
        $profile->setConnection($this->con);


        $user_id = $profile->getUserID($_SESSION['user_email']);

        $sql =
            "INSERT INTO address 
        (user_id,country,city,zip_code,
        address,floor,apartman_no,is_main_address,is_billing_address)
		VALUES (:uid, :country, :city, :zip,:addr,:floor,:apart,:main_add,:bill_add)";

        $query = $this->con->prepare($sql);
        $query->bindValue(":uid", $user_id);
        $query->bindValue(":country", $countries[$country]);
        $query->bindValue(":city", $city);
        $query->bindValue(":zip", $pcode);
        $query->bindValue(":addr", $address);
        $query->bindValue(":floor", $floor);
        $query->bindValue(":apart", $apartman);
        $query->bindValue(":main_add", "0");
        $query->bindValue(":bill_add", "0");

        if (empty($this->error_array)) {
            return $query->execute();
        } else {
            // var_dump($this->error_array);
            // return false;
        }
    }

    public function countryValidation($country)
    {
        $accepted_value = ["1", "2", "3"];

        if (!in_array($country, $accepted_value)) {
            array_push($this->error_array, "Invalid country");
        }
        return;
    }
    public function cityValidation($city)
    {
        $regex = "/[\[^\'£$%^&*()}{@:\'#~?><>,;@\|\\\=\_+\¬\`\]]/";

        if (empty($city) || $city === null || preg_match($regex, $city)) {
            array_push($this->error_array, "City can't contains special charaxters, and need to be filled");
        }
        return;
    }

    public function pcodeValidation($pcode)
    {
        $regex = "/[\[^\'£$%^&*()}{@:\'#~?><>,;@\|\\\=\_+\¬\`\]]/";
        if (empty($pcode) || $pcode === null || preg_match($regex, $pcode)) {
            array_push($this->error_array, "Zip code can't contains special charaxters, and need to be filled");
        }
        return;
    }
    public function addressValidation($address)
    {
        $regex = "/[\[^\'£$%^&*()}{@:\'#~?><>,;@\|\\\=\_+\¬\`\]]/";
        if (empty($address) || $address === null || preg_match($regex, $address)) {
            array_push($this->error_array, "Address can't contains special charaxters, and need to be filled");
        }
        return;
    }

    public function floorValidation($floor)
    {
        $regex = "/^\d{1,}$/";
        if (empty($floor) || $floor === null || !preg_match($regex, $floor)) {
            array_push($this->error_array, "Floor can't contains special charaxters, only numbers are allowed.");
        }
        return;
    }

    public function apartmanValidation($apartman)
    {
        $regex = "/[\[^\'£$%^&*()}{@:\'#~?><>,;@\|\\\=\_+\¬\`\]]/";
        if (empty($apartman) || $apartman === null || preg_match($regex, $apartman)) {
            array_push($this->error_array, "Apartman number can't contains special charaxters, and need to be filled");
        }
        return;
    }

    public function validateAddressForChange($email, $address_id)
    {

        $user_addresses = $this->getAddressesForUser($email);


        $profile = new Profile();
        $profile->setConnection($this->con);
        $user_id = $profile->getUserID($_SESSION['user_email']);
        
        $address_id = intval($address_id);
        
        

        foreach ($user_addresses as $key => $user_address) {
            
            if (intval($user_address['id']) === $address_id && $user_address['user_id'] === $user_id) {
                return true;
            }
        }

        return false;
    }

    public function selectAddress($address_id, $address_type)
    {

        $profile = new Profile();
        $profile->setConnection($this->con);
        $user_id = $profile->getUserID($_SESSION['user_email']);

        $reset_query = $this->con->prepare("UPDATE address SET $address_type='0' WHERE user_id = :user_id");
        $reset_query->BindValue(":user_id", $user_id);
        if (!$reset_query->execute()) {
            return false;
        }


        $add_query = $this->con->prepare("UPDATE address SET $address_type='1' WHERE id=:address_id");

        $add_query->BindValue(":address_id", $address_id);
        if ($add_query->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getAddressByUserIdAndAddressId($id, $user_id)
    {
        $query = $this->con->prepare("SELECT * FROM `address` WHERE id = :uid AND user_id = :user_id");

        $query->BindValue(":uid", $id);
        $query->BindValue(":user_id", $user_id);
        $query->execute();

        if ($query->rowCount()) {
            $result = $query->fetch(PDO::FETCH_ASSOC);

            return $result;
        }
        return false;
    }

    public function updateSpecificAddress($country, $city, $zip_code, $address, $floor = null, $apartman = null)
    {
        $countries = ['Serbia','Hungary','Germany'];

        $fields = ["country", "city", "zip_code", "address", "floor", "apartman"];
        $datas_to_change = array();
        foreach ($fields as $field) {
            if ($$field !== "") {

                array_push($datas_to_change, $field);
            }
        }
        $this->countryValidation($country);
        $this->cityValidation($city);
        $this->pcodeValidation($zip_code);
        $this->addressValidation($address);

        if (!$floor == null) {
            $this->floorValidation($floor);
        }
        if (!$apartman == null) {
            $this->apartmanValidation($apartman);
        }
        $profile = new Profile();
        $profile->setConnection($this->con);
        $user_id = $profile->getUserID($_SESSION['user_email']);
        $address_id = $_GET['id'];

        $country = intval($country - 1);

        $country = $countries[$country];


        if (empty($this->error_array)) {
            $sql = "UPDATE address SET ";
            foreach ($datas_to_change as $key => $data) {
                $bind_element = $$data;
                if ($key === array_key_last($datas_to_change)) {
                    $sql .= "$data = " . "'$bind_element'" . " ";
                } else {
                    $sql .= "$data = " . "'$bind_element'" . ", ";
                }
            }
            $sql .= " WHERE user_id=:user_id AND id=:address_id";
            $query = $this->con->prepare($sql);
            $query->BindValue(":user_id", $user_id);
            $query->BindValue(":address_id", $address_id);
            return $query->execute();
        }
    }

    public function printOutErrorsInHtml()
    {
        if (empty($this->error_array)) {
            return;
        }
        foreach ($this->error_array as $error) {
            echo  "<div class='alert alert-danger m-1' role='alert'>
                            $error
                            </div>";
        }
    }
}
