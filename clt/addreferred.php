<?php
session_start();
$usersession = $_COOKIE['usersession'];
$dbase = $_SESSION['s_admindb'];

require("../db.php");
mysql_select_db($dbase) or die(mysql_error());
$query = "select * from sessions where session_id = ".$usersession;
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$newref = $_REQUEST['newref'];

if ($newref != '') {
	mysql_select_db($dbase) or die(mysql_error());
	
	include_once("../includes/mantleadmin.php");
	$oIn = new mantleadmin;	
	
	$oIn->referred = $newref;
	$oIn->sub_id = $sub_id;
	
	$newref = $oIn->AddReferred();
	
	echo $newref;
	
}

?>
