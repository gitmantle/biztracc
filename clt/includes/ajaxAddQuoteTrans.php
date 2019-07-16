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
$subscriber = $subid;
$sname = $row['uname'];

$table = 'ztmp'.$user_id.'_quote';

$stkcode = $_REQUEST['stkid'];
$stkitem = $_REQUEST['stkitem'];
$stkprice = $_REQUEST['stkprice'];
$stkunit = $_REQUEST['stkunit'];
$stkqty = $_REQUEST['stkqty'];
$taxindex = $_REQUEST['taxindex'];
$taxtype = $_REQUEST['taxtype'];
$taxpcent = $_REQUEST['tpcent'];
$sac = $_REQUEST['sac'];
$sbr = $_REQUEST['sbr'];
$ssb = $_REQUEST['ssb'];
$pac = $_REQUEST['pac'];
$pbr = $_REQUEST['pbr'];
$psb = $_REQUEST['psb'];
$grp = $_REQUEST['grp'];
$cat = $_REQUEST['cat'];
$priceband = $_REQUEST['priceband'];
$disc = $_REQUEST['discount'];
$disctype = $_REQUEST['disctype'];
$setsell = $_REQUEST['setsell'];
if (isset($_REQUEST['lnote'])) {
	$lnote = $_REQUEST['lnote'];
} else {
	$lnote = '';
}

$fxcode = $_REQUEST['fxcode'];
$fxrate = $_REQUEST['fxrate'];
//$fxamt = $_REQUEST['fxamt'];


$value = round($stkprice * $stkqty,2);

if (strlen(trim($sbr)) != 4) {
	$sbr = '1000';
}
if (strlen(trim($pbr)) != 4) {
	$pbr = '1000';
}

$discount = 0;
if($disctype == '$') {
	$discount = $disc;
}
if($disc <> 0 && $disctype == '%') {
	$discount = round($value * $disc /100,2);
}
$value = $value - $discount;
$tax = round($value*$taxpcent/100,2);
$tot = $value + $tax;

// forex conversion
if ($_SESSION['s_localcurrency'] != $fxcode) {
	$stkprice = $fxamt;
	$value = $fxamt * $stkqty;
	if($disctype == '$') {
		$discount = $disc;
	}
	if($disc <> 0 && $disctype == '%') {
		$discount = round($value * $disc /100,2);
	}	
	$disc = $discount;
	$value = $value - $discount;
	$tax = $value * $taxpcent/100;
	$tot = $value + $tax;
	$fxamt = $value;
	
}


$cltdb = $_SESSION['s_cltdb'];

$db->query("insert into ".$cltdb.".".$table." (itemcode,item,price,unit,quantity,tax,value,discount,disctype,discamount,tot,taxindex,taxtype,taxpcent,sellacc,sellbr,sellsub,purchacc,purchbr,purchsub,groupid,catid,note,currency,rate) values (:itemcode,:item,:price,:unit,:quantity,:tax,:value,:discount,:disctype,:discamount,:tot,:taxindex,:taxtype,:taxpcent,:sellacc,:sellbr,:sellsub,:purchacc,:purchbr,:purchsub,:groupid,:catid,:note,:currency,:rate)");
$db->bind(':itemcode', $stkcode);
$db->bind(':item', $stkitem);
$db->bind(':price', $stkprice);
$db->bind(':unit', $stkunit);
$db->bind(':quantity', $stkqty);
$db->bind(':tax', $tax);
$db->bind(':value', $value);
$db->bind(':discount', $discount);
$db->bind(':disctype', $disctype);
$db->bind(':discamount', $disc);
$db->bind(':tot', $tot);
$db->bind(':taxindex', $taxindex);
$db->bind(':taxtype', $taxtype);
$db->bind(':taxpcent', $taxpcent);
$db->bind(':sellacc', $sac);
$db->bind(':sellbr', $sbr);
$db->bind(':sellsub', $ssb);
$db->bind(':purchacc', $pac);
$db->bind(':purchbr', $pbr);
$db->bind(':purchsub', $psb);
$db->bind(':groupid', $grp);
$db->bind(':catid', $cat);
$db->bind(':note', $lnote);
$db->bind(':currency', $fxcode);
$db->bind(':rate', $fxrate);

$db->execute();

$db->closeDB();

?>
