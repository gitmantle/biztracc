<?php
	session_start();
	
	$branchcode = $_REQUEST['branchcode'];

	require_once("../../db.php");
	$moduledb = $_SESSION['s_findb'];
	mysql_select_db($moduledb) or die(mysql_error());
	
	$query = "select accountno,branch,sub,account from glmast where branch = '".$branchcode."' order by accountno,branch,sub";
	$result = mysql_query($query) or die($query);
	$accountsList = "<option value=\"\">Select Account</option>";
	while ($row = mysql_fetch_array($result)) {
		extract($row);
		$accountsList .= "<option value=\"".$accountno."-".$branch."-".$sub."\">".$accountno."-".$branch."-".$sub."  ".$account."</option>";
	}				

	echo $accountsList;


?>