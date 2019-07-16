<?php 
session_start();
$usersession = $_SESSION['usersession'];

$selected = $_REQUEST['selrows'];
$sellist = implode(',',$selected);

$cltdb = $_SESSION['s_cltdb'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$fltfile = "ztmp".$user_id."_filterlist";

$db->query("update ".$cltdb.".".$fltfile." set selected = 'N'");
$db->execute();

foreach($selected as $sel) {
	$db->query("update ".$cltdb.".".$fltfile." set selected = 'Y' where member_id = ".$sel);
	$db->execute();
}

$campid = $_REQUEST['camp_id'];

$db->query("select member_id as memid,lastname,firstname,preferredname,suburb,staff from ".$cltdb.".".$fltfile." where (status != 'In progress' or status != 'Non-reviewable') and selected = 'Y'");
$rows = $db->resultset();
if (count($rows) > 0) {
	foreach ($rows as $row) {
		extract($row);
			$db->query('insert into '.$cltdb.'.candidates (member_id,campaign_id,lastname,firstname,preferred,suburb,staff) values (:member_id,:campaign_id,:lastname,:firstname,:preferred,:suburb,:staff)');
			$db->bind(':member_id', $memid);									 
			$db->bind(':campaign_id', $campid);									 
			$db->bind(':lastname', $lastname);									 
			$db->bind(':firstname', $firstname);									 
			$db->bind(':preferred', $preferredname);									 
			$db->bind(':suburb', $suburb);									 
			$db->bind(':staff', $staff);									 

			$db->execute();
	}
}

$db->closeDB();
?>