<?php
session_start();
$usersession = $_SESSION['usersession'];

$arrdel = $_REQUEST['astring'];
$from = $_REQUEST['from'];
$cltdb = $_SESSION['s_cltdb'];

include_once("../../includes/DBClass.php");
$dbd = new DBClass();

$dbd->query("select * from sessions where session = :vusersession");
$dbd->bind(':vusersession', $usersession);
$row = $dbd->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];
$usergroup = $row['usergroup'];

if ($from == 'p') {
	$filterfile = "ztmp".$user_id."_filter";
} else {
	$filterfile = "ztmp".$user_id."_efilter";
}

$adel = explode(',',$arrdel);

foreach ($adel as $value) {
  $dbd->query('delete from '.$cltdb.'.'.$filterfile.' where member_id = '.$value);
  $dbd->execute();
}

$dbd->closeDB();
?>
