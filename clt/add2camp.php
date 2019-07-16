<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];
date_default_timezone_set($_SESSION['s_timezone']);

include_once("../includes/DBClass.php");
$dba = new DBClass();

$dba->query("select * from sessions where session = :vusersession");
$dba->bind(':vusersession', $usersession);
$row = $dba->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];
$usergroup = $row['usergroup'];

$cltdb = $_SESSION['s_cltdb'];

$filterfile = "ztmp".$user_id."_filterlist";
$campid = $_REQUEST['camp_id'];

$dba->query("select member_id as memid,lastname,firstname,preferredname,suburb,staff from ".$cltdb.".".$filterfile." where (status != 'In progress' or status != 'Non-reviewable') and selected = 'Y'");
$rows = $dba->resultset();
if (count($rows) > 0) {
	foreach ($rows as $row) {
		extract($row);
			$dba->query('insert into '.$cltdb.'.candidates (member_id,campaign_id,lastname,firstname,preferred,suburb,staff) values (:member_id,:campaign_id,:lastname,:firstname,:preferred,:suburb,:staff)');
			$dba->bind(':member_id', $memid);									 
			$dba->bind(':campaign_id', $campid);									 
			$dba->bind(':lastname', $lastname);									 
			$dba->bind(':firstname', $firstname);									 
			$dba->bind(':preferred', $preferredname);									 
			$dba->bind(':suburb', $suburb);									 
			$dba->bind(':staff', $staff);									 

			$dba->execute();
	}
}

$dba->closeDB();
?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Add to Campaign</title>
</head>

<body> 



	<script>
		this.close();
	</script>

</body>
</html>
