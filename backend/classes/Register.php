<?php
class Register
{
    private $con;
    private $error_array = array();

    public function setConnection($con)
    {
        $this->con = $con;
    }

    public function validateUserData($email, $password, $password_verify, $user, $gender, $country, $city, $pcode, $address,$phone_no = null, $floor = null, $apartman = null)
    {
        $this->emailValidation($email);
        $this->passwordValidation($password, $password_verify);
        $this->genderValidation($gender);
        $this->userValidation($user);

        $this->countryValidation($country);
        $this->cityValidation($city);
        $this->pCodeValidation($pcode);
        $this->addressValidation($address);
        if (!$floor == null) {
            $this->floorValidation($floor);
        }
        else {
            $floor = 0;
        }
        
        if (!$apartman == null) {
            $this->apartmanValidation($apartman);
        }

        if (!$phone_no == null) {
            $this->phoneValidation($phone_no);
        }
        if (empty($this->error_array)) {
            return true;
        } else {
            return false;
        }
    }

    public function insertUserData($email, $password, $password_verify, $user, $gender, $country, $city, $pcode, $address,$phone_no = null, $floor = null, $apartman = null)
    {
        $password = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO users (email,password,user,gender,phone_no)
		VALUES (:em, :pass, :user, :gen,:phone)";

        $query = $this->con->prepare($sql);
        $query->bindValue(":em", $email);
        $query->bindValue(":pass", $password);
        $query->bindValue(":user", $user);
        $query->bindValue(":gen", $gender);
        $query->bindValue(":phone", $phone_no);

        if (empty($this->error_array)) {
            if ($query->execute()) {
                $_SESSION['user_id'] = $this->con->lastInsertId();
                $this->insertUserAddress($country, $city, $pcode, $address, $floor, $apartman);
                return true;
            }
        } else {
            return false;
        }
    }

    public function insertUserAddress($country, $city, $pcode, $address, $floor = null, $apartman = null)
    {
        $countries = ['Serbia', 'Hungary', 'Germany'];



        $country = intval($country - 1);
        $sql =
            "INSERT INTO address 
        (user_id,country,city,zip_code,
        address,floor,apartman_no,is_main_address,is_billing_address)
		VALUES (:uid, :country, :city, :zip,:addr,:floor,:apart,:main_add,:bill_add)";

        $query = $this->con->prepare($sql);
        $query->bindValue(":uid", $_SESSION['user_id']);
        $query->bindValue(":country", $countries[$country]);
        $query->bindValue(":city", $city);
        $query->bindValue(":zip", $pcode);
        $query->bindValue(":addr", $address);
        $query->bindValue(":floor", $floor);
        $query->bindValue(":apart", $apartman);
        $query->bindValue(":main_add", "1");
        $query->bindValue(":bill_add", "1");

        if (empty($this->error_array)) {        
            return $query->execute();
        } else {
            return false;
        }
    }

    public function registerUserProfile()
    {
        $sql = "INSERT INTO profiles (user_id,picture_path)
		VALUES (:user_id, :pic)";

        $query = $this->con->prepare($sql);
        $query->bindValue(":user_id", $_SESSION['user_id']);
        $query->bindValue(":pic", "default.png");

        return $query->execute();
    }

    public function emailValidation($email)
    {
        $regex = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,10})$/";
        $email = strtolower($email);

        $query = $this->con->prepare("SELECT email FROM users WHERE email = :email");
        $query->BindValue(":email", $email);
        $query->execute();
        if ($query->rowCount() > 0) {
            array_push($this->error_array, "This email already in use!");
            return;
        }
        if (!preg_match($regex, $email)) {
            array_push($this->error_array, "This email is invalid");
        }
    }

    public function genderValidation($gender)
    {
        $accepted_values = ["1", "2", "3"];
        if (!in_array($gender, $accepted_values)) {
            array_push($this->error_array, "Gender wasn't valid");
            return;
        }
        return;
    }



    public function passwordValidation($password, $password_verify)
    {
        if (strcmp($password, $password_verify) !== 0 || empty($password)) {
            array_push($this->error_array, "Passwords wasn't equal or wasn't filled");
            return;
        } else {
            return;
        }
    }

    public function userValidation($user)
    {
        $regex = "/[a-zA-ZşŞÇçÖöüÜıIiİĞğéáűőúöüó]+$/";
        if (strlen($user) < 4 || !preg_match($regex, $user)) {
            array_push($this->error_array, "User needs to be at least 4 character long, and can't contain special characters, numbers");
        }
    }

    public function phoneValidation($phone_no)
    {
        $regex = "/^(\+\d{1,2}\s)?\(?\d{3}\)?[\s.-]\d{3}[\s.-]\d{4}$/";
        if (!preg_match($regex, $phone_no)) {
            array_push($this->error_array, "Please enter a valid 10 digit long phone number.");
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
            array_push($this->error_array, "City can't contains special charaxters");
        }
        return;
    }

    public function pCodeValidation($pcode)
    {
        $regex = "/[\[^\'£$%^&*()}{@:\'#~?><>,;@\|\\\=\_+\¬\`\]]/";
        if (empty($pcode) || $pcode === null || preg_match($regex, $pcode)) {
            array_push($this->error_array, "Zip code can't contains special charaxters");
        }
        return;
    }
    public function addressValidation($address)
    {
        $regex = "/[\[^\'£$%^&*()}{@:\'#~?><>,;@\|\\\=\_+\¬\`\]]/";
        if (empty($address) || $address === null || preg_match($regex, $address)) {
            array_push($this->error_array, "Address can't contains special charaxters");
        }
        return;
    }

    public function floorValidation($floor)
    {
        $regex = "/^\d{1,}$/";
        if (empty($floor) || $floor === null || !preg_match($regex, $floor)) {
            array_push($this->error_array, "Floor can't contains special charaxters");
        }
        return;
    }

    public function apartmanValidation($apartman)
    {
        $regex = "/[\[^\'£$%^&*()}{@:\'#~?><>,;@\|\\\=\_+\¬\`\]]/";
        if (empty($apartman) || $apartman === null || preg_match($regex, $apartman)) {
            array_push($this->error_array, "Apartman can't contains special charaxters");
        }
        return;
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
