<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$rtfile = $_SESSION['s_rtfile'];

switch($rtfile) {
	case "rt1":
		$fl = "z_1rec";
		break;
	case "rt2":
		$fl = "z_2rec";
		break;
	case "rt3":
		$fl = "z_3rec";
		break;
	case "rt4":
		$fl = "z_4rec";
		break;
	case "rt5":
		$fl = "z_5rec";
		break;
	case "rt6":
		$fl = "z_6rec";
		break;
}

$tid = $_REQUEST['tid'];

$findb = $_SESSION['s_findb'];

$db->query('delete from '.$findb.'.'.$fl.' where uid = '.$tid);
$db->execute();
$db-<closeDB();

?>
