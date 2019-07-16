<?php
session_start();

$thisyear = date('Y');

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="bizTracc">
<meta name="author" content="Murray Russell">
<title>bizTracc Home</title>
<link href="includes/css/bootstrap.min.css" rel="stylesheet">
<link href="includes/css/custom.css" rel="stylesheet">
</head>
<body>
<!-- Fixed navbar -->
<div class="navbar navbar-default navbar-fixed-top" role="navigation">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
      <a class="navbar-brand"  href="#" title="Brand" style="padding-top: 5px; padding-bottom: 5px"> <img style="height: 40px;"src="images/bizTracc_logo.png"> </a> </div>
    <div class="navbar-collapse collapse">
      <ul class="nav navbar-nav navbar-right">
        <li><a href="index.php">Home</a></li>
        <li><a href="index_core.php">Core Features</a></li>
        <li  class="active" class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Processes <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="index_logs.php">Logging Truck Operations</a></li>
            <li><a href="index_cmeds.php">Chronic Medicine Distribution</a></li>
          </ul>
        </li>
        <li><a href="index_about.php">About Us</a></li>
        <li><a href="index_contact.php">Contact Us</a></li>
        <li><a href="index_login.php">Login</a></li>

      </ul>
    </div>
    <!--/.nav-collapse -->
  </div>
</div>
<div class="container">
  <div class="jumbotron">
    <h2>bizTracc - Integrated Business System</h2>
  </div>
  <div class="row myBackground">
    <div class="col-sm-12">
        <p>bizTracc integrated business systems comprise of software components catering for client management, accounting, stock control, fixed asset management and facilities specific to logging truck operations.</p>
         <p>These systems provide data capture from tablets mounted in the truck cab that feed to a back end web based system where administrators can correlate, analyse and obtain reports on all aspects of their operations. These inlcude:-</p>
        <ul>
        <li>Full accounting reports plus profit and loss per vehicle</li>
        <li>RUC refund forms automatically filled in</li>
        <li>Driver log data</li>
        <li>Location tracking of vehicle movements</li>
        <li>Incident reporting directly from incident site, including pictures, uploaded to office</li>
        <li>Vehicle and Plant service and maintenance reords</li>
        </ul>
    </div>
  </div>
</div>

      <div class="col-sm-12">
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      </div>
    


<div class="navbar navbar-inverse navbar-fixed-bottom" role="navigation">
  <div class="container">
    <div class="navbar-text pull-left">
      <p>&copy; Murray Russell 2000 - <?php echo $thisyear; ?></p>
    </div>
  </div>
</div>
<!-- Bootstrap core JavaScript
    ================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="includes/js/bootstrap.min.js"></script>
</body>
</html>