<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subscriber = $row['subid'];
$user_id = $row['user_id'];

$table = 'ztmp'.$user_id.'_trading';

$findb = $_SESSION['s_findb'];

$db->query("select roundto from ".$findb.".globals");
$row = $db->single();
extract($row);
$rnd = $roundto;

$db->query("select sum(tot) as total from ".$findb.".".$table);
$row = $db->single();
extract($row);

$db->closeDB();

switch ($roundto) {
	case 0:
		$total = $total;
		break;
	case 5:
		$tot = round($total * 2, 1) / 2;
		$total = $tot;
		break;
	case 10:
		$tot = round($total,1);
		$total = $tot;
		break;
}

echo number_format($total,2);

?>
