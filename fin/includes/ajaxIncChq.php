<?php
	session_start();
	
	$findb = $_SESSION['s_findb'];
	
	include_once("../../includes/DBClass.php");
	$db = new DBClass();

	$db->query("select chq from ".$findb.".numbers");
	$row = $db->single();
	extract($row);
	$refno = $chq + 1;
	$db->query("update ".$findb.".numbers set chq = :refno");
	$db->bind(':refno', $refno);
	$db->execute();

	$db->closeDB();

?>