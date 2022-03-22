<?php
class Login
{
    private $con;
    private $error_array = array();

    public function __construct()
    {
        
    }

    public function setConnection($con)
    {
        $this->con = $con;
    }

    function login($email, $password)
    {

        $query = $this->con->prepare("SELECT email,password from users WHERE email = :em");

        $query->BindValue(":em", $email);
        $query->execute();


        $result = $query->fetch(PDO::FETCH_ASSOC);


        if ($result == false) {
            array_push($this->error_array, "Invalid email or password");
            return false;
        }
        if (count($result) == 2 && !empty($result) && password_verify($password, $result['password'])) {
            return true;
        } else {
            array_push($this->error_array, "Invalid email or password");
            return false;
        }
    }

    public function checkEmailValid($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) and filter_var($email, FILTER_SANITIZE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }



    public function checkIfUserAuthenticated()
    {
        if (isset($_SESSION['user_email']) and $_SESSION['user_email'] != null) {
            return true;
        } else {

            return false;
        }
    }

    public function checkAccountActivated($email)
    {
        $query = $this->con->prepare("SELECT is_verified FROM users WHERE email = :email");
        $query->BindValue(":email", $email);
        $query->execute();

        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result["is_verified"] == 1) {
            return true;
        } else {
            array_push($this->error_array, "Email wasn't activated");
            return false;
        }
    }
}
