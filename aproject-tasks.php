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
    <script src="js/aproject-sidebar.js"></script>

    <script>var page = "tasks"</script>
    <script src="js/d3.min.js"></script>
    <script src="http://d3js.org/d3.v3.min.js"></script>
    <script src="js/aproject-tasks.js"></script>



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
                <li class="project-active-sidebar sidebar-menu-1">Tasks<span class="glyphicon glyphicon-tasks pull-right"></span></li>
                <li><a href="aproject-graphs.php" class="sidebar-menu-1">Graphs<span class="glyphicon glyphicon-stats pull-right"></span></a></li>
                <li><a href="aproject-updates.php" class="sidebar-menu-1">Recent Updates<span class="glyphicon glyphicon-time pull-right"></span></a></li>
                <li><a href="aproject-noticeboard.php" class="sidebar-menu-1">Noticeboard<span class="glyphicon glyphicon-envelope pull-right"></span></a></li>
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
        </div>

        <div class="col-md-9 col-md-offset-3 project-area">
            <span class="glyphicon glyphicon-circle-arrow-left" id="expand-sidebar-button"></span>

            <p id="new-task-paragraph"><a class=" btn btn-default" id="new-task-button">New Task</a><span id="new-task-success"></span></p>
            <div class="well" id="new-task-div" style="display: none;">
                <form class="new-project">
                    <div class="row">
                        <div class="col-md-12">
                            <input id="new-task-title" type="text" placeholder="Title"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <textarea rows="4" id="new-task-description" placeholder="Task description"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="new-task-startdate">Start date</label>
                            <input id="new-task-startdate" type="date"/>
                        </div>
                        <div class="col-md-4">
                            <label for="new-task-enddate">Completion date</label>
                            <input id="new-task-enddate" type="date"/>
                        </div>
                    </div>
                    <div class="row">
                        <label for="new-task-parentID" style="padding-left: 15px">Parent Task</label>
                        <select id="new-task-parentID">
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <a class="btn btn-default" id="new-task-save">Save</a>
                            <a class="btn btn-default">Clear</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><p id="new-task-error"></p></div>
                    </div>
                </form>
            </div>

            <div class="table-responsive" id="tasks-table">
                <table class="table" >
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Task</th>
                        <th>Start Date</th>
                        <th>Due Date</th>
                        <th>Remaining</th>
                        <th>Created By</th>
                        <th>Worked By</th>
                        <th>Completed</th>
                        <th>View More</th>
                    </tr>
                    </thead>

                    <tbody id="tasks-table-body">
                    </tbody>
                </table>
            </div>
            <div id="tree-chart">

            </div>
            <div id="single-task-view" style="display: none">
                <p id="task-back-toTable"><a class=" btn btn-default" id="back-to-table">Table View</a></p>
                <div class="panel panel-default">
                    <div class="panel-heading blue-bg">
                        <h3 class="panel-title" id="current-task-title"></h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <p id="current-task-dates" class="current-project-description"></p>
                            <p id="current-task-description" class="current-project-description"></p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row task-controls">

                            <a class="btn btn-primary" id="task-control-add-user-menu">Assign User</a>
                            <a class="btn btn-warning" id="task-control-remove-user-menu">Remove User</a>
                            <a class="btn btn-success" id="task-control-add-sub-task-menu">Add Sub Task</a>
                            <a class="btn btn-info" id="task-control-complete-menu">Mark Complete</a>
                            <a class="btn btn-danger" id="task-control-delete-menu">Delete Task</a>
<!--                            <a class="btn btn-default" id="task-control-delete">Delete Task</a>-->
                        </div>

                        <div class="row task-controls" id="task-form-delete" style="display: none">
                            <br/>
                            <div class="well">
                                <p>Deleting this task will delete any sub-tasks.<br/><br/>Are you sure?<br/></p>
                                <div class="checkbox">
                                    <label><input id="delete-task-checkbox" type="checkbox" value=""><b>Confirm</b></label>
                                </div>
                                <a class="btn btn-danger" id="task-control-delete">Delete</a>
                                <p id="delete-task-error"></p>
                            </div>
                        </div>
                        <div class="row task-controls" id="task-form-add-user" style="display: none">
                            <br/>
                            <div class="well">
                                <p>Add a user to work this task.</p>
                                <label for="task-select-users-add">Select a user.</label>
                                <select id="task-select-users-add">
                                </select><br/>
                                <br/><a class="btn btn-primary" id="task-control-add-user">Add User</a>
                            </div>
                            <div class="row">
                                <div class="col-md-12"><p id="add-user-task-error" class="error"></p></div>
                            </div>
                        </div>


                        <div class="row task-controls" id="task-form-mark-complete" style="display: none">
                            <br/>
                            <div class="well">
                                <p>Change whether this task is marked as complete or incomplete.</p>
                                <p>Marking this task as complete will complete all sub tasks.</p>
                                <label><input id="complete-task-checkbox" type="checkbox" value=""><b>&nbsp; Completed</b></label>
                                <br/>
                                <br/><a class="btn btn-info" id="task-control-mark-complete">Save Changes</a>
                            </div>
                            <div class="row">
                                <div class="col-md-12"><p id="mark-complete-task-error" class="error"></p></div>
                            </div>
                        </div>


                        <div class="row task-controls" id="task-form-remove-user" style="display: none">
                            <br/>
                            <div class="well">
                                <p>Remove a user from working this task.</p>
                                <label for="task-select-users-remove">Select a user.</label>
                                <select id="task-select-users-remove">
                                </select><br/>
                                <br/><a class="btn btn-warning" id="task-control-remove-user">Remove User</a>
                            </div>
                            <div class="row">
                                <div class="col-md-12"><p id="remove-user-task-error" class="error"></p></div>
                            </div>
                        </div>
                        <div class="row task-controls" id="task-form-add-sub-task" style="display: none">
                            <br/>
                            <div class="well new-project">
                                <div class="row">
                                    <div class="col-md-12">
                                        <input id="new-task-title2" type="text" placeholder="Title"/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <textarea rows="4" id="new-task-description2" placeholder="Task description"></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="new-task-startdate2">Start date</label>
                                        <input id="new-task-startdate2" type="date"/>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="new-task-enddate2">Completion date</label>
                                        <input id="new-task-enddate2" type="date"/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <a class="btn btn-success" id="new-task-save2">Save</a>
                                        <a class="btn btn-default">Clear</a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4"><p id="new-task-error2"></p></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


</body>
</html>
