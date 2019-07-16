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
<script>

	window.onload = function() {
	  document.getElementById("userid").focus();
	};

	function verify() {
		var uname = document.getElementById("userid").value;
		if (uname == "") {
			alert("Please enter your user name");
			return false;
		}
		var pword = document.getElementById("password").value;
		if (pword == "") {
			alert("Please enter your password");
			return false;
		} else {
			document.getElementById('form1').submit();
		}
	}
	
	function submitenter(myfield,e)
	{
	var keycode;
	if (window.event) keycode = window.event.keyCode;
	else if (e) keycode = e.which;
	else return true;
	
	if (keycode == 13)
	   {
	   myfield.form.submit();
	   return false;
	   }
	else
	   return true;
	}


</script>
</head>
<body style="padding-top: 5px;">
<form name="form1" id="form1" method="post" action="verify.php">
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
        <li><a href="index_about.php">About Us</a></li>
        <li><a href="index_contact.php">Contact Us</a></li>
        <li class="active"><a href="index_login.php">Login</a></li>
        </li>
      </ul>
    </div>
    <!--/.nav-collapse -->
  </div>
</div>
  <div class="col-sm-6">
    <div class="panel panel-biztracc">
      <div class="panel-heading">Login Credentials</div>
      <div class="panel-body"> To use the demonstration software, login with the following credentials:-
        <ul>
          <li>Core System - User: demo, Password: demo</li>
          <li>Cmeds4U System - User: cmeds, Password: cmeds</li>
          <li>Logging Truck System - User: trux, Password: trux</li>
        </ul>
        <p>Otherwise log in with your subscribers credentials to access your live system</p>
      </div>
    </div>
  </div>
  <div class="col-sm-6">
    <div class="form-login">
      <h4>Welcome to bizTracc</h4>
      <input type="password" id="userid" name="userid" class="form-control input-sm chat-input" placeholder="username" />
      </br>
      <input type="password" id="password" name="password" class="form-control input-sm chat-input" placeholder="password"  onKeyPress="return submitenter(this,event)" />
      </br>
      <button class="btn btn-lg btn-primary btn-block" type="button" value="Submit" onClick="verify()">Login</button>
    </div>
  </div>
  </div>
</form>
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