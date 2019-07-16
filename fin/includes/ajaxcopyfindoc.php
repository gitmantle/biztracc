<?php
	session_start();
	$usersession = $_SESSION['usersession'];

	$copyfrom = $_REQUEST['copyfrom'];
	$copyto = $_REQUEST['copyto'];

	$findb = $_SESSION['s_findb'];
	
	$templatefrom = $findb.".".$copyfrom."template";
	$templateto = $findb.".".$copyto."template";

	include_once("../../includes/DBClass.php");
	$db = new DBClass();
	
	//drop old table
	$db->query("drop table ".$templateto);
	$db->execute();
	
	// recreate by copying structure
	$db->query("create table ".$templateto." like ".$templatefrom);
	$db->execute();
	
	// copy all records from from table
	$db->query("insert into ".$templateto." select * from ".$templatefrom);
	$db->execute();

	$db->closeDB();
	
	echo '<script>';
	echo 'this.close();';
	echo '</script>';

?>
