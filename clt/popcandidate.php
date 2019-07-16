<?php
session_start();
ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];

$cltdb = $_SESSION['s_cltdb'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$candid = $_REQUEST['candid'];
$memid = $_REQUEST['memid'];

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

$db->query("select candstatus from ".$cltdb.".candidates where candidate_id = ".$candid);
$row = $db->single();
extract($row);

$db->query("update ".$cltdb.".candidates set candstatus = 'Available', staff_id = 0 where candstatus = 'Busy' and staff_id = ".$user_id);
$db->execute(); 

if ($candstatus == 'Available') {
	$db->query("update ".$cltdb.".candidates set candstatus = 'Busy', staff_id = ".$user_id." where candidate_id = ".$candid);
	$db->execute();
	
	$db->query("select lastname,firstname,dob from ".$cltdb.".members where member_id = ".$memid);
	$row = $db->single();
	extract($row);
	$memname = trim($firstname.' '.$lastname);
	if ($dob == "0000-00-00") {
		$age = 0;
	} else {
		$age = floor((time() - strtotime($dob))/31556926);
	}
	
	echo $memname.'#'.$age;
} 


if ($candstatus == 'Busy') {
	echo 'B';
}

if ($candstatus == 'Complete') {
	echo 'C';
}

$db->closeDB();

?>

