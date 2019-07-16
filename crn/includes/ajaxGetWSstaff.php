<?php
	session_start();
	
	$coyidno = $_SESSION['s_coyid'];
	$wshop = $_REQUEST['id'];

	include_once("../includes/DBClass.php");
	$db = new DBClass();

	$crndb = $_SESSION['s_crndb'];
	
	$db->query("select * from sessions where session = :vusersession");
	$db->bind(':vusersession', $usersession);
	$row = $db->single();
	$subscriber = $row['subid'];
	$user_id = $row['user_id'];	
	
	
	$db->query("select uid,concat_ws(' ',ufname,ulname) as fname from users where sub_id = ".$subscriber." and workshop_id = ".$wshop);
	$rows = $db->resultset();
	$WSstaffList = "<option value=\"\">Select Workshop Staff Member</option>";
	foreach ($rows as $row) {
		extract($row);
		$WSstaffList .= '<option value="'.$fname.'">'.$fname.'</option>';
		$i = $i + 1;
	}				

	$db->closeDB();
	
	echo $WSstaffList;

?>