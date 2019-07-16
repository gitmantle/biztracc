<?php
session_start();

$rtfile = $_REQUEST["rtfile"];
$rtd = $_REQUEST["rtd"];


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


$findb = $_SESSION['s_findb'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$db->query("update ".$findb.".".$fl." set ddate = '".$rtd."'");
$db->execute();
$db->closeDB();

?>
