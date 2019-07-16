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
<title>bizTracc About</title>
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
        <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Processes <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="index_logs.php">Logging Truck Operations</a></li>
            <li><a href="index_cmeds.php">Chronic Medicine Distribution</a></li>
          </ul>
        </li>
        <li class="active"><a href="index_about.php">About Us</a></li>
        <li><a href="index_contact.php">Contact Us</a></li>
        <li><a href="index_login.php">Login</a></li>
        </li>
      </ul>
    </div>
    <!--/.nav-collapse -->
  </div>
</div>
<div class="container">
<div class="jumbotron">
  <h2>bizTracc - About Us</h2>
</div>
<div class="col-sm-12">
  <div class="panel panel-biztracc">
    <div class="panel-heading">Murray Russell</div>
    <div class="panel-body"> 
    <p>30 Years in the IT business writing business applications, accounting systems and other business related processes that feed into accounts or obtain data from accounts. These include systems for retail and manufacturing businesses, legal and medical practices, log haulage and service industries.</p>
      <p>I enjoy the practical application of the creative process; ascertaining business requirements and producing easy to use, intuitive systems, either desktop based or now more pertinently, web based applicatons.</p>
      <p>All areas of of industry and business happily considered. Whatever your vision, I'd be delighted to assist.</p>
      <p>Please feel free to access the demonstration software through the Login screen.</p>
      <p>
  
<a href="https://au.linkedin.com/pub/murray-russell/83/3a9/881">
          <img src="https://static.licdn.com/scds/common/u/img/webpromo/btn_myprofile_160x33.png" width="160" height="33" border="0" alt="View Murray Russell's profile on LinkedIn">
    </a>

 </p>
    </div>
  </div>
</div>
<div class="col-sm-12">
  <div class="panel panel-biztracc">
    <div class="panel-heading">Robyn Mills</div>
    <div class="panel-body"> 
      <p>27 years employed as a bookkeeper in legal, architectural and physiotherapy practices</p>
      <p>2 years assessing and testing business related software.</p>
      <p>
      
<a href="https://au.linkedin.com/pub/robyn-mills/a9/ab1/675">
          <img src="https://static.licdn.com/scds/common/u/img/webpromo/btn_myprofile_160x33.png" width="160" height="33" border="0" alt="View Robyn Mill's profile on LinkedIn">
    </a>      
      
      </p>
      
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