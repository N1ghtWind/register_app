<?php

class Verification
{

  private $con;

  public function __construct($con)
  {
    $this->con = $con;
  }
  public function generateUniqueCode()
  {
    $code = md5(uniqid(rand(), true));
    $code .= time();

    return $code;
  }

  public function insertCodeToDatabase($code, $email)
  {
    $query = $this->con->prepare("INSERT INTO verification (code,email) VALUES (:code,:email)");
    $query->BindValue(":code", $code);
    $query->BindValue(":email", $email);
    $query->execute();
  }

  public function checkTheCodeInDatabase($code)
  {
    $query = $this->con->prepare("SELECT * FROM verification WHERE code = :code");
    $query->BindValue(":code", $code);
    $query->execute();

    if (!$query->rowCount() > 0) {
      return false;
    } else {
      return true;
    }
  }

  public function activateTheAccount($code)
  {

    $query = $this->con->prepare("SELECT email FROM verification WHERE code = :code");
    $query->BindValue(":code", $code);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    $email = $result['email'];

    $activate_query = $this->con->prepare("UPDATE users SET is_verified = 1 WHERE email = :email");
    $activate_query->BindValue(":email", $email);
    $activate_query->execute();
    $this->deleteRecordAfterActivation($email);
  }
  public function deleteRecordAfterActivation($email)
  {
    $sql = "DELETE FROM verification WHERE email = :email";
    $delete_query = $this->con->prepare($sql);
    $delete_query->BindValue(":email", $email);
    $delete_query->execute();
  }

  public function getEmailByCode($table, $code)
  {
    $sql = "SELECT email FROM $table WHERE code = :code";
    $select_query = $this->con->prepare($sql);
    $select_query->BindValue(":code", $code);
    $select_query->execute();

    $result = $select_query->fetch(PDO::FETCH_ASSOC);

    if ($result) {
      return $result["email"];
    }
  }
}
