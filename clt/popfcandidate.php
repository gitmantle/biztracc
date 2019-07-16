<?php
session_start();
$usersession = $_SESSION['usersession'];

$candid = $_REQUEST['candid'];
$memid = $_REQUEST['memid'];

$cltdb = $_SESSION['s_cltdb'];

include_once("../includes/DBClass.php");
$db = new DBClass();

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

$db->closeDB();

?>

