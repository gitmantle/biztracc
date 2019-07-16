<?php
	session_start();
	
	$coyidno = $_SESSION['s_coyid'];
	$wshop = $_REQUEST['id'];

	require_once("../../db.php");

	$moduledb = $_SESSION['s_admindb'];
	mysql_select_db($moduledb) or die(mysql_error());
	
	$query = "select * from sessions where session = '".$usersession."'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	$subscriber = $subid;
	
	$query = "select uid,concat_ws(' ',ufname,ulname) as fname from users where sub_id = ".$subscriber." and workshop_id = ".$wshop;
	$result = mysql_query($query) or die($query);
	$WSstaffList = "<option value=\"\">Select Workshop Staff Member</option>";
	while ($row = mysql_fetch_array($result)) {
		extract($row);
		$WSstaffList .= '<option value="'.$fname.'">'.$fname.'</option>';
		$i = $i + 1;
	}				

	echo $WSstaffList;

?>