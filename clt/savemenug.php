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

$table = $sub_id.'_menu';


	$field = $_REQUEST['field'];
	$uid = $_REQUEST['uid'];
	$value = $_REQUEST['value'];
	
	if ($value == 'true') {
		$updt = 'update '.$table.' set '.$field.' = "Y" where menu_id = '.$uid;
	} else {
		$updt = 'update '.$table.' set '.$field.' = "N" where menu_id = '.$uid;
	}
	$result = mysql_query($updt) or die("Invalid Query: ".mysql_error());
	
	echo "Saved";
	
?>
