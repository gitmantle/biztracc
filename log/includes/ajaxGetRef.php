<?php
	session_start();
	
	$ref = $_REQUEST['ref'];

	require_once("../../db.php");

	$moduledb = $_SESSION['s_findb'];
	mysql_select_db($moduledb) or die(mysql_error());
	$ref = $_REQUEST['ref'];

	$query = "lock tables numbers write";
	$result = mysql_query($query) or die($query);
	$query = "select ".$ref." from numbers";
	$result = mysql_query($query) or die($query);
	$row = mysql_fetch_array($result);
	extract($row);
	$refno = $$ref + 1;
	$query = "update numbers set ".$ref." = ".$refno;
	$result = mysql_query($query) or die($query);
	$query = "unlock tables";
	$result = mysql_query($query) or die($query);
	
	echo $refno;


?>