<?php
session_start();
require_once("../db.php");

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());


	$field = $_REQUEST['field'];
	$uid = $_REQUEST['uid'];
	$value = $_REQUEST['value'];
	
	if ($value == 'true') {
		$updt = 'update c_menu set '.$field.' = "Y" where menu_id = '.$uid;
	} else {
		$updt = 'update c_menu set '.$field.' = "N" where menu_id = '.$uid;
	}
	$result = mysql_query($updt) or die("Invalid Query: ".mysql_error().' '.$updt);
	
	echo "Saved";
	
?>
