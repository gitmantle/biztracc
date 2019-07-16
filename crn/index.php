<?php
session_start();
date_default_timezone_set($_SESSION['s_timezone']);

$usersession = $_SESSION['usersession'];
$coyid = $_SESSION['s_coyid'];
$_SESSION['s_module'] = 'crn';

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$moduledb = 'infinint_sub'.$subid;
$_SESSION['s_cltdb'] = $moduledb;

$moduledb = 'infinint_fin'.$subid.'_'.$coyid;
$_SESSION['s_findb'] = $moduledb;

$moduledb = 'infinint_crn'.$subid.'_'.$coyid;
$_SESSION['s_crndb'] = $moduledb;


$db->closeDB();

$thisyear = date('Y');

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="bizTracc Crane">
    <meta name="author" content="Murray Russell">
    <title>bizTracc Crane</title>
    <link href="../includes/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../includes/bootstrap/css/custom.css" rel="stylesheet">

  </head>
  <body>
    <!-- Fixed navbar -->
    <div class="navbar navbar-default navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li class="active"><a href="updtjobs.php">Jobs</a></li>
            <li><a href="calendar/jobcalendar.php" target="_blank">Schedule</a></li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Reports<span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li>&nbsp;</li>
                <li>&nbsp;</li>
              </ul>
            </li> 
            <li><a href="index_incidents.php">Incidents</a></li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Workshop<span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="servemaint.php">Service and Mainteneance</a></li>
                <li><a href="workshops.php">Update Workshops</a></li>
             </ul>
            </li> 
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Administration<span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="updtfleet.php">Fleet Management</a></li>
                <li><a href="updtoperators.php">Drivers/Operators</a></li>
             </ul>
            </li> 
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Housekeeping<span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="backup.php">Backup</a></li>
                <li><a href="rbkup.php">Restore</a></li>
              </ul>
            </li> 
            <li><a href="../main.php">bizTracc Home</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>
 
    
    
 <div class="container">
		<div class="jumbotron">
			<h2>bizTracc Crane</h2>
		</div>
		<div class="row myBackground">
			<div class="col-sm-12">
 &nbsp;
			</div>
		</div>
	</div>	   
    
    
    
     <div class="navbar navbar-inverse navbar-fixed-bottom" role="navigation">
    	<div class="container">
    		<div class="navbar-text pull-left">
    			<p>&copy; Murray Russell 2016 - <?php echo $thisyear; ?></p>
    		</div>
    	</div>
    </div>   
    
    
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="../includes/js/bootstrap.min.js"></script>
  </body>
  
</html>