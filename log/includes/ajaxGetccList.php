<?php
	session_start();
	
	$mailto = $_REQUEST['mailto'];

	require_once("../../db.php");

	$moduledb = $_SESSION['s_cltdb'];
	mysql_select_db($moduledb) or die(mysql_error());
	$query = "select concat(t1.firstname,' ',t1.lastname) as fname ,t2.comm, t3.member_id from members as t1, comms as t2, assoc_xref as t3 where t1.member_id = t2.member_id and t1.member_id = t3.member_id and t2.comms_type_id = 4 and t3.of_id = ".$mailto;
	$result = mysql_query($query) or die($query);
	$ccList = "<option value=\"\">Select Addressee</option>";
	while ($row = mysql_fetch_array($result)) {
		extract($row);
		$selected = '';
		$ccList .= '<option value="'.$comm.'"'.$selected.'>'.$fname.'</option>';
	}				
	echo $ccList;

?>