<?php

/**
 *  26/10/2016
 *  15;06
 *  Andy Franklin
 *
 * Project Class

 *$parent_id,$title,$description,$startdate,$enddate
 */
require_once 'database-connect.php';


if (!empty($_POST['title'])){
    $title = $_POST["title"];
}
if (!empty($_POST['desc'])){
    $desc = $_POST["desc"];
}
if (!empty($_POST['startDate'])){
    $startDate = $_POST["startDate"];
}
if (!empty($_POST['endDate'])){
    $endDate = $_POST["endDate"];
}
if (!empty($_POST['email'])){
    $email = $_POST["email"];
}
if (!empty($_POST['permission'])){
    $permission = $_POST["permission"];
}
if (!empty($_POST['projectid'])){
    $projectid = $_POST["projectid"];
}
if (!empty($_POST['parent_id'])){
    $parent_id = $_POST["parent_id"];
} else {
    $parent_id = 0;
}
if (!empty($_POST['taskid'])){
    $taskID = $_POST["taskid"];
}
if (!empty($_POST['userid'])){
    $userID = $_POST["userid"];
}
if (!empty($_POST['status'])){
    $status = $_POST["status"];
}
if (!empty($_POST['action'])){
    $action = $_POST["action"];
    switch ($action) {
        case "create":
            $project->createProject($title,$desc,$startDate,$endDate);
            break;
        case "adduser":
            $project->addUser($email,$permission,$projectid);
            break;
        case "getprojects":
            $project->getUserProjects();
            break;
        case "setprojectid":
            $project->setProjectSESSIONID($projectid);
            break;
        case "getprojectbyid":
            $project->getProjectBySESSIONID();
            break;
        case "getuserlist":
            $project->getUserList();
            break;
        case "removeuser":
            $project->removeUserByEmail($email);
            break;
        case "getalltasks":
            $project->getTasks();
            break;
        case "addtask":
            $project->addTask($parent_id,$title,$desc,$startDate,$endDate);
            break;
        case "deletetask":
            $project->deleteTask($taskID);
            break;
        case "addtaskuser":
            $project->addTaskUser($taskID,$userID);
            break;
        case "removetaskuser":
            $project->removeTaskUser($taskID,$userID);
            break;
        case "gettaskusers":
            $project->getTaskUsers($taskID);
            break;
        case "getprojectlog":
            $project->readProjectLog();
            break;
        case "settaskstatus":
            $project->setTaskStatus($taskID,$status);
            break;
        default:
            break;
    };
}

function appPage(){
    include_once 'database-connect.php';
}

class project{
    /** @var $db PDO  */
    private $db;

    function __construct($db_con)
    {
        $this->db = $db_con;
    }



    public function createProject($title,$desc,$startDate,$endDate){

        // Create new project
        // Get user email
        // Add to userProject as owner (permID  == 2)
        try{
            /** @var $statement PDOStatement */
            $statement = $this->db->prepare("INSERT INTO projects (title,description,startDate,endDate) 
                                              VALUES (:project_title, :project_desc, :project_startDate, :project_endDate)");
            $statement->bindParam(":project_title",$title);
            $statement->bindParam(":project_desc",$desc);
            $statement->bindParam(":project_startDate",$startDate);
            $statement->bindParam(":project_endDate",$endDate);
            $statement->execute();

            $_SESSION["new_project"]= $this->db->lastInsertId();
            $this->addToProjectLog("create"," created the project '".$title."'");

            $statement = $this->db->prepare("INSERT INTO userprojects (email, projectid, permissionID) 
                                              VALUES (:user_email,LAST_INSERT_ID(),2)");
            $statement->bindParam(":user_email",$_SESSION['user_session']);
            $statement->execute();
            $this->getUserProjects();
        } catch (PDOException $e){
            echo "error";
        }
    }

    public function setProjectSESSIONID($projectid){
        $_SESSION['project_id'] = $projectid;
        echo "success";
    }
    public function getProjectBySESSIONID(){
        try {
            $projectid = $_SESSION['project_id'];
            $statement = $this->db->prepare("SELECT projects.* FROM userprojects JOIN projects ON userprojects.projectid = projects.id WHERE projectid = :project_id");
            $statement->bindParam(":project_id",$projectid);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            $json = json_encode($result);
            echo '{"project":'.$json.'}';

        } catch (PDOException $e){
            echo $e->getMessage();
        }
    }

    public function getUserProjects(){
        try {
            $email = $_SESSION['user_session'];
            /** @var $statement PDOStatement */
            $statement = $this->db->prepare("SELECT projects.* FROM userprojects JOIN projects ON userprojects.projectid = projects.id WHERE email = :user_email");
            $statement->bindParam(":user_email",$email);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            $json = json_encode($result);
            echo '{"projects":';
            echo $json;
            echo '}';
        } catch (PDOException $e){
            echo $e->getMessage();
        }
    }

    public function addUser($email, $permission,$projectid){
        try {
            $statement = $this->db->prepare("SELECT email FROM users WHERE email=:user_email");
            $statement->bindParam(":user_email",$email);
            $statement->execute();
            $result = $statement->fetchAll();
            if (count($result) > 0){ //user found
                $statement = $this->db->prepare("SELECT * FROM userprojects WHERE email = :user_email AND projectid = :projectID" );
                $statement->bindParam(":user_email",$email);
                $statement->bindParam(":projectID",$_SESSION["project_id"]);
                $statement->execute();
                $result = $statement->fetchAll();
                if (count($result) > 0){
                    echo "already member";
                } else {
                    $statement = $this->db->prepare("INSERT INTO userprojects (email, projectid, permissionID) VALUES (:user_email,:project_id,:permission)");
                    $statement->bindParam(":user_email",$email);
                    $statement->bindParam(":project_id",$projectid);
                    $statement->bindParam(":permission",$permission);
                    $statement->execute();
                    echo "user added";

                    $this->addToProjectLog("addUser"," added '".$email."' to the project.");
                    return true;
                }
            } else {
                echo "no user";
                return false;
            }
        }catch (PDOException $e){
            echo $e->getMessage();
        }
    }

    public function getUserList(){
        try {
            $statement = $this->db->prepare("SELECT users.email, users.firstname, users.lastname,userprojects.permissionID FROM userprojects INNER JOIN users ON userprojects.email=users.email WHERE projectid = :projectID ");
            $statement->bindParam(":projectID",$_SESSION["project_id"]);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            $json = json_encode($result);
            echo $json;
        } catch (PDOException $e){
            echo $e->getMessage();
        }
    }

    public function removeUserByEmail($email){
        //check permission of user
        //Only manager and owner can remove
        //only owner can remove manager
        $currentUser = $_SESSION["user_session"];
        $statement = $this->db->prepare("SELECT permissionID from userprojects WHERE email=:email AND projectid = :project_id");
        $statement->bindParam(":email",$currentUser);
        $statement->bindParam(":project_id",$_SESSION["project_id"]);
        $statement->execute();
        $currentUserPermission = $statement->fetch(PDO::FETCH_ASSOC);
        $currentUserPermission = $currentUserPermission["permissionID"];

        //Check permission of to be removed
        $statement = $this->db->prepare("SELECT permissionID from userprojects WHERE email=:email AND projectid = :project_id");
        $statement->bindParam(":email",$email);
        $statement->bindParam(":project_id",$_SESSION["project_id"]);
        $statement->execute();
        $userToRemovePermission = $statement->fetch(PDO::FETCH_ASSOC);
        $userToRemovePermission =$userToRemovePermission["permissionID"];
        //OK - remove
        $removeOK = 0;
        if ($currentUserPermission == "2"){
            //Owner can remove any
            $removeOK = 1;
        } else if ($currentUserPermission == "3"){
            if ($userToRemovePermission=="4" || $userToRemovePermission=="5"){
            //Manager can remove User and Viewer
                $removeOK = 1;
            }
        }
        if ($removeOK ==1){
            $statement = $this->db->prepare("DELETE FROM userprojects WHERE email = :email AND projectid = :project_id");
            $statement->bindParam(":email",$email);
            $statement->bindParam(":project_id",$_SESSION["project_id"]);
            $statement->execute();
            echo "deleted";
            $this->addToProjectLog("delUser"," removed '".$email."' from the project.");
        } else {

            echo "not deleted";
        }
    }

    public function deleteProject(){

    }

    public function getTasks(){
        try {
            $statement =$this->db->prepare("SELECT task.*, users.firstname, users.lastname FROM task LEFT OUTER JOIN users ON users.email = task.creatorID WHERE projectID = :project_id");
            $statement->bindParam(":project_id",$_SESSION["project_id"]);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            $json = json_encode($result);
            echo '{"tasks":';
            echo $json;
            echo '}';
        }catch (PDOException $e){
            echo $e->getMessage();
        }
    }
    public function addTask($parent_id,$title,$desc,$startDate,$endDate){
        try{
            $current_user = $_SESSION["user_session"];
            $project_id = $_SESSION["project_id"];
            $statement =$this->db->prepare("INSERT INTO task (parentid,projectID,title,description,startdate,enddate,creatorID)
                                        VALUES (:parentid,:project_id,:title,:description,:startdate,:enddate,:creator_id)");
            $statement->bindParam(":project_id",$project_id);
            $statement->bindParam(":creator_id",$current_user);
            $statement->bindParam(":parentid",$parent_id);
            $statement->bindParam(":title",$title);
            $statement->bindParam(":description",$desc);
            $statement->bindParam(":startdate",$startDate);
            $statement->bindParam(":enddate",$endDate);
            $statement->execute();
            $this->addToProjectLog("addTask"," added the task '".$title."' to the project.");
        } catch (PDOException $e){
            echo $e->getMessage();
        }
    }

    public function deleteTask($taskID){
        try {
            $statement = $this->db->prepare("SELECT title FROM task WHERE id =:taskID");
            $statement->bindParam(":taskID",$taskID);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            $statement =$this->db->prepare("DELETE FROM task WHERE id = :taskID");
            $statement->bindParam(":taskID",$taskID);
            $statement->execute();

            $statement = $this->db->prepare("DELETE FROM userstask WHERE taskID = :taskID");
            $statement->bindParam(":taskID",$taskID);
            $statement->execute();

            $this->addToProjectLog("delTask"," deleted the task '".$result["title"]."' from the project.");
        } catch (PDOException $e){
            echo $e->getMessage();
        }
    }

    public function addTaskUser($taskID, $userID){
        try {
            //CHECK IF USER EXISTS PART OF PROJECT
            //CHECK IF USER HAS PERMISSION
            $statement = $this->db->prepare("INSERT INTO userstask (userID, taskID) VALUES (:userID, :taskID)");
            $statement->bindParam(":userID",$userID);
            $statement->bindParam(":taskID",$taskID);
            $statement->execute();

            $statement = $this->db->prepare("SELECT title FROM task WHERE id =:taskID");
            $statement->bindParam(":taskID",$taskID);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            $this->addToProjectLog("addUserTask"," added '".$userID."' to the task '".$result["title"]."'.");
        } catch (PDOException $e){
            echo $e->getMessage();
        }
    }

    public function removeTaskUser($taskID, $userID){
        try {
            $statement = $this->db->prepare("DELETE FROM userstask WHERE taskID = :taskID AND userID = :userID");
            $statement->bindParam(":userID",$userID);
            $statement->bindParam(":taskID",$taskID);
            $statement->execute();

            $statement = $this->db->prepare("SELECT title FROM task WHERE id =:taskID");
            $statement->bindParam(":taskID",$taskID);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            $this->addToProjectLog("addUserTask"," removed '".$userID."' from the task '".$result["title"]."'.");
        } catch (PDOException $e){
            echo $e->getMessage();
        }
    }
        
    public function getTaskUsers($taskID){
        try {
            $statement = $this->db->prepare("SELECT users.email, users.firstname, users.lastname FROM users INNER JOIN userstask ON userstask.userID = users.email WHERE userstask.taskID = :taskID");
            $statement->bindParam(":taskID",$taskID);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            $json = json_encode($result);
            echo $json;
        } catch (PDOException $e){
            echo $e->getMessage();
        }
    }

    public function setTaskStatus($taskID, $status){
        try{
            $statement = $this->db->prepare("UPDATE task SET completed = :status WHERE id = :taskID");
            $statement->bindParam(":taskID",$taskID);
            $statement->bindParam(":status",$status);
            $statement->execute();

            $statement = $this->db->prepare("SELECT title FROM task WHERE id =:taskID");
            $statement->bindParam(":taskID",$taskID);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            if ($status == 1){
                $statusTxt = "complete";
            } else {
                $statusTxt = "incomplete";
            }
            $this->addToProjectLog("addUserTask"," set '".$result["title"]."' to ".$statusTxt.".");
        }catch (PDOException $e){
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

    public function readProjectLog(){
        $projectID = $_SESSION["project_id"];
        echo file_get_contents("projectlogs/".$projectID.".txt");
    }
}