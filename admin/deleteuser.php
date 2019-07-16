<?php
$dbs = $_SESSION['s_admindb'];

require("../db.php");
mysql_select_db($dbs) or die(mysql_error());

$cmuid = $_REQUEST['uid'];

$query = "delete from access where staff_id = ".$cmuid;
$result = mysql_query($query) or die(mysql_error().' '.$query);

$query = "delete from staff where staff_id = ".$cmuid;
$result = mysql_query($query) or die(mysql_error().' '.$query);

	
?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Delete Staff Member</title>
</head>

<body> 

<?php
	echo '<script>';
	echo 'window.open("","updtusers").jQuery("#stafflist2").trigger("reloadGrid");';
	echo 'this.close();';			
	echo '</script>';
?>

</body>
</html>
