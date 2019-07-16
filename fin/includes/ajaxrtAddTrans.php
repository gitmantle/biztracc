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
$acc2dr = $_REQUEST['acc2dr'];
$subdr = $_REQUEST['subdr'];
$brdr = $_REQUEST['brdr'];
$acc2cr = $_REQUEST['acc2cr'];
$subcr = $_REQUEST['subcr'];
$brcr = $_REQUEST['brcr'];
$ddate = $_REQUEST['ddate'];
$descript1 = $_REQUEST['descript1'];
$reference = $_REQUEST['reference'];
$amount = $_REQUEST['amount'];
$tax = $_REQUEST['tax'];
$taxtype = $_REQUEST['taxtype'];
$taxpcent = $_REQUEST['taxpcent'];
$total = $_REQUEST['total'];
$refindex = $_REQUEST['refindex'];
$taxindex = $_REQUEST['taxindex'];
$a2d = $_REQUEST['a2d'];
$a2c = $_REQUEST['a2c'];
$drgst = $_REQUEST['drgst'];
$crgst = $_REQUEST['crgst'];

$findb = $_SESSION['s_findb'];

$db->query("insert into ".$findb.".".$fl." (acc2dr,subdr,brdr,acc2cr,subcr,brcr,ddate,descript1,reference,amount,tax,taxtype,taxpcent,total,refindex,taxindex,a2d,a2c,drgst,crgst) values (:acc2dr,:subdr,:brdr,:acc2cr,:subcr,:brcr,:ddate,:descript1,:reference,:amount,:tax,:taxtype,:taxpcent,:total,:refindex,:taxindex,:a2d,:a2c,:drgst,:crgst)");
$db->bind(':acc2dr', $acc2dr);
$db->bind(':subdr', $subdr);
$db->bind(':brdr', $brdr);
$db->bind(':acc2cr', $acc2cr);
$db->bind(':subcr', $subcr);
$db->bind(':brcr', $brcr."',";
$db->bind(':ddate', $ddate);
$db->bind(':descript1', $descript1);
$db->bind(':reference', $reference);
$db->bind(':amount', $amount);
$db->bind(':tax', $tax);
$db->bind(':taxtype', $taxtype);
$db->bind(':taxpcent', $taxpcent);
$db->bind(':total', $total);
$db->bind(':refindex', $refindex);
$db->bind(':taxindex', $taxindex);
$db->bind(':a2d', $a2d);
$db->bind(':a2c', $a2c);
$db->bind(':drgst', $drgst);
$db->bind(':crgst', $crgst);

$db->execute();
$db->closeDB();

?>
