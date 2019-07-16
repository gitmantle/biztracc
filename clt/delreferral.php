<?php
session_start();
$dbase = $_SESSION['s_admindb'];


require("../db.php");
mysql_select_db($dbase) or die(mysql_error());

$refid = $_REQUEST['uid'];

include_once("includes/cltadmin.php");
$oCm = new cltadmin;	

$oCm->referralid = $refid;

$oCm->DelReferral();

$query = "select * from sessions where session_id = ".$usersession;
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
date_default_timezone_set($_SESSION['s_timezone']);

$hdate = date('Y-m-d');
$ttime = strftime("%H:%M", time());

$query = "insert into audit (ddate,ttime,user_id,uname,sub_id,member_id,action) values ";
$query .= "('".$hdate."',";
$query .= "'".$ttime."',";
$query .= $user_id.",";
$query .= "'".$uname."',";
$query .= $sub_id.",";
$query .= $refid.",";
$query .= '"Deleted referral id '.$refid.'")';
	  
$result = mysql_query($query) or die(mysql_error().$query);		

	
	
?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Delete Referral</title>
</head>

<body> 

<?php
	echo '<script>';
	echo 'window.open("","updtreferrals").jQuery("#referrallist").trigger("reloadGrid");';
	echo 'this.close();';			
	echo '</script>';
?>

</body>
</html>
