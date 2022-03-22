<?php
class Profile
{
    private $con;
    private $error_array = array();

    public function setConnection($con)
    {
        $this->con = $con;
    }

    public function updateProfilePicture($email, $new_path, $id)
    {
        $query = $this->con->prepare("UPDATE profiles SET picture_path=:path WHERE user_id=:id");

        $query->BindValue(":path", $new_path);
        $query->BindValue(":id", $id);
        if ($query->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function updateUserInformation($email, $password, $password_verify, $user, $gender, $phone_no)
    {
        $genders = ['men', 'women', 'other'];
        $fields = ["email", "password", "user", "gender", "phone_no"];
        $datas_to_change = array();
        foreach ($fields as $field) {
            if ($$field !== "") {

                array_push($datas_to_change, $field);
            }
        }





        $this->emailValidation($email);
        $this->passwordValidation($password, $password_verify);
        $this->userValidation($user);
        $this->genderValidation($gender);



        if (!$phone_no == null) {
            $this->phoneValidation($phone_no);
        }



        $user_id = $this->getUserID($_SESSION['user_email']);


        $gender = intval($gender - 1);


        isset($genders[$gender]) ?  $gender = $genders[$gender] : $gender = "";

        $password = password_hash($password, PASSWORD_BCRYPT);


        if (empty($this->error_array)) {
            $sql = "UPDATE users SET ";
            foreach ($datas_to_change as $key => $data) {
                $bind_element = $$data;
                if ($key === array_key_last($datas_to_change)) {
                    $sql .= "$data = " . "'$bind_element'" . " ";
                } else {
                    $sql .= "$data = " . "'$bind_element'" . ", ";
                }
            }
            $sql .= " WHERE user_id=:user_id";
            $query = $this->con->prepare($sql);
            $query->BindValue(":user_id", $user_id);
            var_dump($query);
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

    public function getUserID($email)
    {
        $query = $this->con->prepare("SELECT user_id FROM users WHERE email=:em");

        $query->BindValue(":em", $email);
        $query->execute();

        $result = $query->fetch(PDO::FETCH_ASSOC);

        return $result["user_id"];
    }

    public function getUserProfilePicture($id)
    {
        $query = $this->con->prepare("SELECT picture_path FROM profiles WHERE user_id=:id");

        $query->BindValue(":id", $id);
        $query->execute();

        $result = $query->fetch(PDO::FETCH_ASSOC);

        return $result["picture_path"];
    }
    public function emailValidation($email)
    {
        if ($email === "") {
            return;
        }
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
        if ($gender === "") {
            return;
        }
        $accepted_values = ["1", "2", "3"];
        if (!in_array($gender, $accepted_values)) {
            array_push($this->error_array, "Gender wasn't valid");
            return;
        }
        return;
    }



    public function passwordValidation($password, $password_verify)
    {
        if ($password === "" && $password_verify === "") {
            return;
        }

        if (strcmp($password, $password_verify) !== 0) {
            array_push($this->error_array, "Passwords wasn't equal");
            return;
        } else {
            return;
        }
    }

    public function userValidation($user)
    {
        if ($user === "") {
            return;
        }
        $regex = "/[a-zA-ZşŞÇçÖöüÜıIiİĞğéáűőúöüó]+$/";
        if (strlen($user) < 4 || !preg_match($regex, $user)) {
            array_push($this->error_array, "User needs to be at least 4 character long, and can't contain special characters, numbers");
        }
    }

    public function phoneValidation($phone_no)
    {
        if ($phone_no === "") {
            return;
        }
        $regex = "/^\s*(?:\+?(\d{1,3}))?[-. (]*(\d{3})[-. )]*(\d{3})[-. ]*(\d{4})(?: *x(\d+))?\s*$/";
        if (!preg_match($regex, $phone_no)) {
            array_push($this->error_array, "Please enter a valid 10 digit long phone number.");
        }
    }

    public function countryValidation($country)
    {
        if ($country === "") {
            return;
        }
        $accepted_value = ["1", "2", "3"];

        if (!in_array($country, $accepted_value)) {
            array_push($this->error_array, "Invalid country");
        }
        return;
    }
    public function cityValidation($city)
    {
        if ($city === "") {
            return;
        }
        $regex = "/[\[^\'£$%^&*()}{@:\'#~?><>,;@\|\\\=\_+\¬\`\]]/";

        if (empty($city) || $city === null || preg_match($regex, $city)) {
            array_push($this->error_array, "City can't contains special charaxters");
        }
        return;
    }

    public function pcodeValidation($pcode)
    {
        if ($pcode === "") {
            return;
        }
        $regex = "/[\[^\'£$%^&*()}{@:\'#~?><>,;@\|\\\=\_+\¬\`\]]/";
        if (empty($pcode) || $pcode === null || preg_match($regex, $pcode)) {
            array_push($this->error_array, "Zip code can't contains special charaxters");
        }
        return;
    }
    public function addressValidation($address)
    {
        if ($address === "") {
            return;
        }
        $regex = "/[\[^\'£$%^&*()}{@:\'#~?><>,;@\|\\\=\_+\¬\`\]]/";
        if (empty($address) || $address === null || preg_match($regex, $address)) {
            array_push($this->error_array, "Address can't contains special charaxters");
        }
        return;
    }

    public function floorValidation($floor)
    {
        if ($floor === "") {
            return;
        }
        $regex = "/^\d{1,}$/";
        if (empty($floor) || $floor === null || !preg_match($regex, $floor)) {
            array_push($this->error_array, "Floor can't contains special charaxters, only numbers allowed");
        }
        return;
    }

    public function apartmanValidation($apartman)
    {
        if ($apartman === "") {
            return;
        }
        $regex = "/[\[^\'£$%^&*()}{@:\'#~?><>,;@\|\\\=\_+\¬\`\]]/";
        if (empty($apartman) || $apartman === null || preg_match($regex, $apartman)) {
            array_push($this->error_array, "Apartman can't contains special charaxters");
        }
        return;
    }
}
