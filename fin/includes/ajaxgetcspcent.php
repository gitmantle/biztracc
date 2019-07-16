<?php
	session_start();
	
	include_once("../../includes/DBClass.php");
	$db = new DBClass();

	$findb = $_SESSION['s_findb'];

	$db->query("select c_s_markup from ".$findb.".globals");
	$row = $db->single();
	extract($row);
	
	$db->closeDB();
	
	echo $c_s_markup;


?>