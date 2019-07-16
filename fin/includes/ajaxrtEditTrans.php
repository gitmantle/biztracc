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
$ln = $_REQUEST['ln'];

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
$descript1 = $_REQUEST['descript1'];
$amount = $_REQUEST['amount'];
$tax = $_REQUEST['tax'];
$taxtype = $_REQUEST['taxtype'];
$taxpcent = $_REQUEST['taxpcent'];
$total = $_REQUEST['total'];

$findb = $_SESSION['s_findb'];

$db->query("update ".$findb.".".$fl." set descript1 = :descript1, taxtype = :taxtype, amount = :amount, tax = :tax, total = :total where uid = :uid");
$db->bind(':descript1', $descript1);
$db->bind(':taxtype',$taxtype);
$db->bind(':amount', $amount);
$db->bind(':tax', $tax);
$db->bind(':total', $total);
$db->bind(':uid', $ln);

$db->execute();
$db->closeDB();

?>
