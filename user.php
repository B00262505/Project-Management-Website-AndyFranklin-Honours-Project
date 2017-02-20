<?php

/**
 * Created by PhpStorm.
 * User: Andy
 * Date: 15/10/2016
 * Time: 03:13
 */
require_once 'database-connect.php';


if (!empty($_POST['firstName'])){
    $first_name = $_POST["firstName"];
}
if (!empty($_POST['lastName'])){
    $last_name = $_POST["lastName"];
}
if (!empty($_POST['email'])){
    $user_email = $_POST["email"];
}
if (!empty($_POST['password'])){
    $user_password = $_POST["password"];
}
if (!empty($_POST['action'])){
    $action = $_POST["action"];
    switch ($action) {
        case "register":
            $user->register($first_name, $last_name, $user_email, $user_password);
            break;
        case "login":
            $user->login($user_email,$user_password);
            break;
        case "logout":
            $user->logout();
            break;
        case "update-fname":
            $user->update_fname($first_name);
            break;
        case "update-lname":
            $user->update_lname($last_name);
            break;
        case "update-password":
            $user->update_password($user_password);
            break;

        default:
            break;
    };
}



class user
{
    /** @var $db PDO  */
    private $db;

    function __construct($db_con)
    {
        $this->db = $db_con;
    }

    public function register($firstName, $lastName, $email, $password){
        try {
//            Look up whether email is in use
//            IF used then return error - email in use
//            else carry on registration
            $statement = $this->db->prepare("SELECT email FROM users WHERE email=:user_email");
            $statement->bindParam(":user_email",$email);
            $statement->execute();
            $result = $statement->fetchAll();
            if (count($result) !== 0 ){
                echo "email exists";
            } else {
                $hashedPass = password_hash($password, PASSWORD_DEFAULT);
                /** @var $statement PDOStatement */
                $statement  = $this->db->prepare("INSERT INTO users (email,password,firstname,lastname) 
                                                  VALUES (:user_email, :user_password,:user_fname, :user_lname)");
                $statement->bindParam(":user_email",$email);
                $statement->bindParam(":user_password",$hashedPass);
                $statement->bindParam(":user_fname",$firstName);
                $statement->bindParam(":user_lname",$lastName);
                $statement->execute();
                echo "registered";
            }

        } catch (PDOException $e){
            echo $e->getMessage();
        }
    }

    public function login($email, $password){
        try {
            $statement = $this->db->prepare("SELECT email,password FROM users WHERE email=:user_email");
            $statement->bindParam(":user_email",$email);
            $statement->execute();
            $result = $statement->fetchAll();
            if (count($result) > 0){ //user found
                if (password_verify($password,$result[0][1])){
                    $_SESSION['user_session'] = $result[0][0];
                    echo "user login";
                    return true;
                } else {
                    echo "no match";
                    return false;
                }
            } else {
                echo "no user";
                return false;
            }
        } catch (PDOException $e){
            echo $e->getMessage();
        }
    }

    public function logout(){
        try {
            unset($_SESSION['user_session']);
            session_destroy();

        } catch (PDOException $e){
            echo $e->getMessage();
        }
    }

    public function is_loggedin(){
        if (isset($_SESSION['user_session'])){
            return true;
        }
    }

//    public function update_email(){
//
//    }
    public function update_fname($first_name){
        try {
            $user_email = $_SESSION['user_session'];
            $statement = $this->db->prepare("UPDATE users SET firstname=:user_firstname WHERE email='$user_email'");
            $statement->bindParam(":user_firstname", $first_name);
            $statement->execute();
            echo "updated";
        } catch (PDOException $e){
            echo $e->getMessage();
        }
    }
    public function update_lname($last_name){
        try {
            $user_email = $_SESSION['user_session'];
            $statement = $this->db->prepare("UPDATE users SET lastname=:user_lastname WHERE email='$user_email'");
            $statement->bindParam(":user_lastname", $last_name);
            $statement->execute();
            echo "updated";
        } catch (PDOException $e){
            echo $e->getMessage();
        }
    }
    public function update_password($password){
        $hashedPass = password_hash($password, PASSWORD_DEFAULT);
        try {
            $user_email = $_SESSION['user_session'];
            $statement = $this->db->prepare("UPDATE users SET password=:user_hashedpass WHERE email='$user_email'");
            $statement->bindParam(":user_hashedpass", $hashedPass);
            $statement->execute();
            echo "updated";
        } catch (PDOException $e){
            echo $e->getMessage();
        }
    }

}