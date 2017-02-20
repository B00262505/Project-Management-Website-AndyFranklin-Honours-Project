
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
    <meta charset="UTF-8">
    <title>My Dashboard</title>

    <script type="text/javascript">
        var userRow = [];
        var i = <?php echo count($userRow) ?>;
        <?php for ($i = 0;  $i<count($userRow); $i++){ ?>
            userRow.push("<?php echo current($userRow );
                next($userRow)?>");
        <?php } ?>
        
    </script>
    <script src="js/loggedIn.js"></script>
    <script src="js/dashboard.js"></script>
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
                <li class="active"><a href="#">Dashboard</a></li>
                <li><a href="projects.php">Projects</a></li>
                <li><a href="#">People</a></li>
                <li><a href="#">Calander</a></li>
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
                    <h3 class="panel-title">My Deadlines</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="well">
                                <p>Honours Project</p>
                                <p></p>
                                <p><span>1</span> Weeks, <span>5</span> Days, <span>2</span> Hours, <span>20</span> Minutes, <span>50</span> Seconds.</p>
                                <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>

                                <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="well">
                                <p>Honours Project</p>
                                <p></p>
                                <p><span>1</span> Weeks, <span>5</span> Days, <span>2</span> Hours, <span>20</span> Minutes, <span>50</span> Seconds.</p>
                                <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>

                                <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="well">
                                <p>Honours Project</p>
                                <p></p>
                                <p><span>1</span> Weeks, <span>5</span> Days, <span>2</span> Hours, <span>20</span> Minutes, <span>50</span> Seconds.</p>
                                <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>

                                <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <p><a class="btn btn-default view-more-btn" href="#" role="button">View More &raquo;</a></p>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading blue-bg">
                    <h3 class="panel-title">My Updates</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="well">
                                <p>Honours Project</p>
                                <p></p>
                                <p><span>1</span> Weeks, <span>5</span> Days, <span>2</span> Hours, <span>20</span> Minutes, <span>50</span> Seconds.</p>
                                <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>

                                <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="well">
                                <p>Honours Project</p>
                                <p></p>
                                <p><span>1</span> Weeks, <span>5</span> Days, <span>2</span> Hours, <span>20</span> Minutes, <span>50</span> Seconds.</p>
                                <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>

                                <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="well">
                                <p>Honours Project</p>
                                <p></p>
                                <p><span>1</span> Weeks, <span>5</span> Days, <span>2</span> Hours, <span>20</span> Minutes, <span>50</span> Seconds.</p>
                                <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>

                                <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <p><a class="btn btn-default view-more-btn" href="#" role="button">View More &raquo;</a></p>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>





</body>
</html>