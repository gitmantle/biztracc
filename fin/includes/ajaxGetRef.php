<?php
	session_start();
	
	$ref = $_REQUEST['ref'];
	
	$findb = $_SESSION['s_findb'];
	
	include_once("../../includes/DBClass.php");
	$db = new DBClass();

	$db->query("select ".$ref." from ".$findb.".numbers");
	$row = $db->single();
	extract($row);
	$refno = $$ref + 1;
	$db->query("update ".$findb.".numbers set ".$ref." = :refno");
	$db->bind(':refno', $refno);
	$db->execute();
	
	echo $refno;

	$db->closeDB();

?>