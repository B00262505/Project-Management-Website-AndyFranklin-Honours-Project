<?php
include_once 'database-connect.php';
if(!$user->is_loggedin())
{
    header('Location: index.html');
}
$user_email = $_SESSION['user_session'];
$stmt = $DB_con->prepare("SELECT email,firstname,lastname FROM users WHERE email=:user_email");
$stmt->execute(array(":user_email"=>$user_email));
$userRow=$stmt->fetch(PDO::FETCH_ASSOC);


if (!isset($_SESSION['project_id'])){
    header('Location: index.html');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href='//fonts.googleapis.com/css?family=Raleway:400,300,600' rel='stylesheet' type='text/css'>

    <!--JQuery-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

    <!--bootstrap-->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="css/styles.css"/>
    <meta charset="UTF-8">
    <!--    Add project tile here -->
    <title>Project<?php ?></title>

    <script type="text/javascript">
        var userRow = [];
        var i = <?php echo count($userRow) ?>;
        <?php for ($i = 0;  $i<count($userRow); $i++){ ?>
        userRow.push("<?php echo current($userRow );
            next($userRow)?>");
        <?php } ?>
    </script>
    <link rel="stylesheet" type="text/css" href="css/lightbox.min.css">
    <script src="js/loggedIn.js"></script>
    <script src="js/project.js"></script>
    <script src="js/aproject-noticeboard.js"></script>
    <script src="js/aproject-sidebar.js"></script>
    <script src="js/lightbox.min.js"></script>



</head>
<body>

<!--Signed in navigation bar-->
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Project Pal</a>
        </div>

        <div class="collapse navbar-collapse" id="navbar-collapse-1">
            <ul class="nav navbar-nav">

                <li class="active"><a href="projects.php">Projects</a></li>

            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><p class="navbar-text">Signed in as <a id="logged-in-user" href="profile.php">&nbsp;</a> </p></li>
                <li><a id="logout-button"><span class="glyphicon glyphicon glyphicon-log-out"></span> Logout</a></li>
                <!--<li><a href="#"><span class="glyphicon glyphicon-user"></span> Sign Up</a></li>-->
                <!--<li><a href="#"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>-->
            </ul>
        </div>
    </div>
</nav>
<div class="sub-bar blue-bg"></div>

<div class="container-fluid">

    <div class="row fluid-content-aproject">
        <div class="col-md-3 sidebar">
            <ul>
                <li><a href="aproject.php" class="sidebar-menu-1">Overview<span class="glyphicon glyphicon-home pull-right"></span></a></li>
                <li><a href="aproject-tasks.php" class="sidebar-menu-1">Tasks<span class="glyphicon glyphicon-tasks pull-right"></span></a></li>
                <li><a href="aproject-graphs.php" class="sidebar-menu-1">Graphs<span class="glyphicon glyphicon-stats pull-right"></span></a></li>
                <li><a href="aproject-updates.php" class="sidebar-menu-1">Recent Updates<span class="glyphicon glyphicon-time pull-right"></span></a></li>
                <li class="project-active-sidebar list-group" class="sidebar-menu-1">Noticeboard<span class="glyphicon glyphicon-envelope pull-right"></span></li>
            </ul>
            <hr/>

            &nbsp;<span class="profile-error" id="sidebar-error"></span>

            <div class="panel list-group">
                <a href="#" class="list-group-item" data-toggle="collapse" data-target="#user-submenu1" data-parent="#menu">Project Members<span class="glyphicon glyphicon-collapse-down pull-right"></span></a>
                <div id="user-submenu1" class="sublinks collapse">
                    <a href="#" class="list-group-item" data-toggle="collapse" data-target="#user-addnewmenu" data-parent="#menu">&nbsp;&nbsp;Add New Member<span class="glyphicon glyphicon-collapse-down pull-right"></span></a>
                    <div id="user-addnewmenu" class="sublinks collapse">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <select class="selectpicker show-tick" style="width: 80px;" id="user-add-user-select">
                                    <option value="5">User</option>
                                    <option value="3">Manager</option>
                                    <option value="4">Viewer</option>
                                </select>
                            </div>
                            <input class="form-control" placeholder="Email address" id="sidebar-new-user-email">
                        </div>
                        <a id="user-add-user" class="list-group-item sidebar-menu-button"><span class="glyphicon glyphicon-plus"></span>&nbsp;Add</a>
                    </div>
                    <a href="#" class="list-group-item" data-toggle="collapse" data-target="#user-view-members-menu" data-parent="#menu">&nbsp;&nbsp;Remove Group Members<span class="glyphicon glyphicon-collapse-down pull-right"></span></a>
                    <div id="user-view-members-menu" class="sublinks collapse">
                        <a href="#" class="list-group-item" data-toggle="collapse" data-target="#user-view-members-manager-menu" data-parent="#menu">&nbsp;&nbsp;&nbsp;&nbsp;Managers<span class="glyphicon glyphicon-collapse-down pull-right"></span></a>
                        <div id="user-view-members-manager-menu" class="sublinks collapse">

                            <div class="input-group">
                                <div class="input-group" id="sidebar-managers">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox"> <span class="managerName" id="THEIR USER ID">Andy Franklin</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <a id="user-remove-manager" class="list-group-item sidebar-menu-button"><span class="glyphicon glyphicon-minus"></span>&nbsp;Remove Selected</a>
                        </div>
                        <a href="#" class="list-group-item" data-toggle="collapse" data-target="#user-view-members-user-menu" data-parent="#menu">&nbsp;&nbsp;&nbsp;&nbsp;Users<span class="glyphicon glyphicon-collapse-down pull-right"></span></a>
                        <div id="user-view-members-user-menu" class="sublinks collapse">

                            <div class="input-group">
                                <div class="input-group">
                                    <div class="input-group" id="sidebar-users">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox"> <span class="managerName" id="THEIR USER ID">Andy Franklin</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a id="user-remove-user" class="list-group-item sidebar-menu-button"><span class="glyphicon glyphicon-minus"></span>&nbsp;Remove Selected</a>
                        </div>
                        <a href="#" class="list-group-item" data-toggle="collapse" data-target="#user-view-members-viewer-menu" data-parent="#menu">&nbsp;&nbsp;&nbsp;&nbsp;Viewers<span class="glyphicon glyphicon-collapse-down pull-right"></span></a>
                        <div id="user-view-members-viewer-menu" class="sublinks collapse">

                            <div class="input-group">
                                <div class="input-group">
                                    <div class="input-group" id="sidebar-viewers">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox"> <span class="managerName" id="THEIR USER ID">Andy Franklin</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a id="user-remove-viewer" class="list-group-item sidebar-menu-button"><span class="glyphicon glyphicon-minus"></span>&nbsp;Remove Selected</a>

                        </div>
                    </div>
                </div>



            </div>
            <!--            <input type="text" placeholder="User email"/> <a class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>  </a>-->

        </div>
        
        <div class="col-md-9 col-md-offset-3 project-area">
            <span class="glyphicon glyphicon-circle-arrow-left" id="expand-sidebar-button"></span>
            <p id="new-noticeboard-message-paragraph"><a class=" btn btn-default" id="new-noticeboard-message-button">New Notice</a><span id="new-project-success"></span></p>
            <div id="new-noticeboard-post" style="display: none;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="well" style="  margin-left: 25px">
                            <form class="new-project" id="new-noticeboard-message-form">
                                <input type="hidden" name="action" value="newnotice">
                                <!--<label for="new-project-title"></label>-->
                                <div class="row">
                                    <div class="col-md-12">
                                        <input id="new-noticeboard-message-title" type="text" placeholder="Title" name="title"/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <textarea rows="4" id="new-noticeboard-message-description" placeholder="Message Body" name="body"></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <label class="input-group-btn">
                                                <span class="btn btn-primary">
                                                    Image upload: Browse<input name="image" id="new-noticeboard-message-file-input" accept="image/*" type="file" style="display: none;">
                                                </span>
                                            </label>
                                            <input type="text" class="form-control" readonly="" id="new-noticeboard-message-file-text">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <a class="btn btn-success" id="new-noticeboard-messsage-submit">Submit</a>
                                        <a class="btn btn-danger" id="new-noticeboard-clear">Clear</a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12"><p id="new-noticeboard-message-error"></p></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div id="noticeboard-message-container"  >

            </div>
        </div>
    </div>


</div>

</body>
</html>


