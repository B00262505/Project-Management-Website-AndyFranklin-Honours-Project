<?php
require_once 'database-connect.php';

if (!empty($_POST['projectid'])) {
    $projectID = $_POST["projectid"];
}
if (!empty($_POST['title'])) {
    $title = $_POST["title"];
}
if (!empty($_POST['body'])) {
    $body = $_POST["body"];
}
$image64 = "";
if (!empty($_POST['image'])){
    $image64 = $_POST['image'];
}


if (!empty($_POST['action'])){
    $action = $_POST["action"];
    switch ($action) {
        case "getprojectmessages":
            $message->getProjectMessages();
            break;
        case "getprojectmessagescount":
            $message->getProjectMessagesCount();
            break;
        case "newnotice":
            $message->addMessage($title,$body,$image64);
            break;
        default:
            break;
    };
}


class message
{
    /** @var $db PDO */
    private $db;

    function __construct($db_con)
    {
        $this->db = $db_con;
    }

    function getProjectMessagesCount(){
        try {
            $projectID = $_SESSION['project_id'];
            $statement = $this->db->prepare("SELECT message.id FROM message JOIN projects on message.projectID=projects.id WHERE projects.id = :project_id" );
            $statement->bindParam(":project_id",$projectID);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            $json = json_encode($result);
            echo $json;
        } catch (PDOException $e){
            echo $e->getMessage();
        }
    }
    function getProjectMessages(){
        try {
            $projectID = $_SESSION['project_id'];
            $statement = $this->db->prepare("SELECT users.firstname, users.lastname, message.*, images.name FROM message JOIN users on message.userID=users.email JOIN projects on message.projectID=projects.id LEFT OUTER JOIN images on message.imageID=images.id WHERE projects.id = :project_id ORDER BY message.timeDate desc" );
            $statement->bindParam(":project_id",$projectID);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            $json = json_encode($result);
            echo $json;
        } catch (PDOException $e){
            echo $e->getMessage();
        }
    }

    function addMessage($title,$body,$image64){
        try{
            $target_dir = "uploads/".$_SESSION['project_id']."/";
            $projectID = $_SESSION['project_id'];
            $user_email = $_SESSION['user_session'];
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $imageID = null;
            $uploadOk = 1;
            if (isset($_FILES["image"])) {
                if ($_FILES["image"]["size"]!=0){
                    $target_file = $target_dir . basename($_FILES["image"]["name"]);
                    $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

                    if (file_exists($target_file)) {
                        echo "Sorry, a file with that name already exists.<br/>";
                        $uploadOk = 0;
                    }
                    if ($_FILES["image"]["size"] > 500000) {
                        echo "Sorry, your file is too large (500KB Max).<br/>";
                        $uploadOk = 0;
                    }
                    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.<br/>";
                        $uploadOk = 0;
                    }
                    if ($uploadOk == 1) {
                        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
                        $statement = $this->db->prepare("INSERT INTO images (name) VALUES (:imagename)");
                        $statement->bindParam(":imagename", $_FILES["image"]["name"]);
                        $statement->execute();
                        $imageID = $this->db->lastInsertId();
                    } else {

                    }
                }
            }

            if ($image64 !== ""){
                //Base 64 from app.
                $data = base64_decode($image64);
                $filename =  date("Y-m-d-h-i-s-").$projectID.'.png';
                file_put_contents( $target_dir.$filename, $data);
                $statement = $this->db->prepare("INSERT INTO images (name) VALUES (:imagename)");
                $statement->bindParam(":imagename",$filename);
                $statement->execute();
                $imageID = $this->db->lastInsertId();
            }
            if ($uploadOk == 1){
                echo "success";
                $statement = $this->db->prepare("INSERT INTO message (userID, projectID, title, body, imageID) VALUES (:userID, :projectID, :title, :body, :imageID)");
                $statement->bindParam(":userID",$user_email);
                $statement->bindParam(":projectID",$projectID);
                $statement->bindParam(":title",$title);
                $statement->bindParam(":body",$body);
                $statement->bindParam(":imageID",$imageID);
                $statement->execute();


                $this->addToProjectLog("addMessage"," added a message to the noticeboard.");
            }



        } catch (PDOException $e){
            echo $e->getMessage();
        }
    }
    public function addToProjectLog($user_action,$text){

        $userID = $_SESSION["user_session"];

        $target_dir = "projectlogs/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        if ($user_action == "create"){
            $projectID = $_SESSION["new_project"];
        } else (
        $projectID = $_SESSION["project_id"]
        );
        if ($user_action == "create"){
            $projectLog = fopen($target_dir."/".$projectID.".txt",'w') or die("unable to open file");
        } else {
            $projectLog = fopen($target_dir."/".$projectID.".txt","a") or die ("unable to open file");
        }

        $statement = $this->db->prepare("SELECT firstname, lastname FROM users WHERE email = :userID");
        $statement->bindParam(":userID",$userID);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        $username = $result["firstname"]." ".$result["lastname"];
        $timestamp = date("Y-m-d");
        $logString = $timestamp." -- ".$username." -- ".$text."<hr/><br/>";

        fwrite($projectLog,$logString);
        fclose($projectLog);
    }
}