<?php
session_start();
$dbase = $_SESSION['s_admindb'];


require("../db.php");
mysql_select_db($dbase) or die(mysql_error());

$cmuid = $_REQUEST['uid'];

	include_once("../includes/mantleadmin.php");
	$oIn = new mantleadmin;	
	
	$oIn->uid = $cmuid;

	$oIn->DelLink();
	
?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Delete Link</title>
</head>

<body> 



	<script>
		window.open("","updtlinks").jQuery("#updtlinklist").trigger("reloadGrid");
		this.close();
	</script>

</body>
</html>
