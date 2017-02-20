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


    <script type="text/javascript">
        var userRow = [];
        var i = <?php echo count($userRow) ?>;
        <?php for ($i = 0;  $i<count($userRow); $i++){ ?>
        userRow.push("<?php echo current($userRow );
            next($userRow)?>");
        <?php } ?>

        var project = JSON.parse('<?php $project->getUserProjects()?>');

    </script>
    <script src="js/loggedIn.js"></script>


    <meta charset="UTF-8">
    <title>My Projects</title>
</head>
<body>

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
<!--                <li><a href="dashboard.php">Dashboard</a></li>-->
                <li class="active"><a href="#">Projects</a></li>
<!--                <li><a href="#">People</a></li>-->
<!--                <li><a href="#">Calander</a></li>-->
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

<div class="container nav-bar-pushdown">

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading blue-bg">
                    <h3 class="panel-title">My Projects</h3>
                </div>
                <div class="panel-body">
                    <p><a class=" btn btn-default" id="new-project-button" >New Project</a><span id="new-project-success"></span></p><br/>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="well" id="new-project-div" style="display: none;">
                                <form class="new-project">
                                    <!--<label for="new-project-title"></label>-->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <input id="new-project-title" type="text" placeholder="Title"/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <textarea rows="4" id="new-project-description" placeholder="Project description"></textarea>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="new-project-startdate">Start date</label>
                                            <input id="new-project-startdate" type="date"/>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="new-project-enddate">Completion date</label>
                                            <input id="new-project-enddate" type="date"/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <a class="btn btn-success" id="new-project-save">Save</a>
                                            <a class="btn btn-danger" id="new-project-clear">Clear</a>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4"><p id="new-project-error"></p></div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div id="current-user-projects">

                    </div>


<!--                    <div class="row">-->
<!--                        <p><a class="btn btn-default view-more-btn" href="#" role="button">View More &raquo;</a></p>-->
<!--                    </div>-->

                </div>
            </div>
        </div>
    </div>

</div>

</body>
<script src="js/projects.js"></script>

</html>