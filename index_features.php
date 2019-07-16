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
        <li class="active"><a href="index_features.php">Features</a></li>
        <li><a href="index_about.php">About Us</a></li>
        <li><a href="index_contact.php">Contact Us</a></li>
        <li><a href="index_pricing.php">Pricing</a></li>
        <li><a href="index_register.php">Register</a></li>
        <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Login <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="order.php">Order</a></li>
            <li><a href="index_manual.php">Manual</a></li>
            <li><a href="index_account.php">My Account</a></li>
            <li><a href="index_login.php">Online Tests</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="admin/index.php">Administration</a></li>
          </ul>
        </li>
        <li><a href="index_help.php">Help</a></li>
      </ul>
    </div>
    <!--/.nav-collapse -->
  </div>
</div>
<div class="container">
<div class="jumbotron">
  <h2>bizTracc - Features & Business Model</h2>
</div>
<div class="row myBackground">
  <div class="col-sm-12">
    <h2>Features</h2>
    <ul>
      <li>Fully integrated accounting, stock control, fixed assets, client relationship management and business processes</li>
      <li>Web based, so accessible from anywhere legitimate user has broadband internet access</li>
      <li>Any relevant data only ever entered once</li>
      <li>Clients for all associated companies held once. Update from one company and record updated for all</li>
      <li>Confine user access to only those facilities of each company to which they are entitled</li>
      <li>You can give your accountant access thereby eliminating correcting journal entries each year end</li>
      <li>We can provide a complete bookkeeping service and do your accounts for you</li>
    </ul>
    <h2>Pricing</h2>
    <ul>
      <li>Fixed monthly fee for use of the core system</li>
      <li>Negotiated fixed monthly fee for bookkeeping services based on average number of transactions</li>
      <li>Set monthly fee for use of specific business processes</li>
      <li>This adds up to a total fixed monthly fee with no surprises on a renewable two year contract</li>
    </ul>
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