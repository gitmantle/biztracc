<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$user_id = $row['user_id'];

$findb = $_SESSION['s_findb'];

$table = 'ztmp'.$user_id.'_trading';
$stkcode = $_REQUEST['stkid'];
$stkitem = $_REQUEST['stkitem'];
$stkprice = $_REQUEST['stkprice'];
$stkunit = $_REQUEST['stkunit'];
$stkqty = $_REQUEST['stkqty'];
$tpcent = $_REQUEST['tpcent'];
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
if (isset($_REQUEST['priceband'])) {
	if ($_REQUEST['priceband'] == 0) {
		$pband = 1;
	} else {
		$pband = $_REQUEST['priceband'];
	}
} else {
	$pband = 1;
}
$disc = $_REQUEST['discount'];
$disctype = $_REQUEST['disctype'];
$setsell = $_REQUEST['setsell'];
$loc = $_REQUEST['loc'];
$stock = $_REQUEST['stock'];
$avcost = $_REQUEST['avcost'];



if (isset($_REQUEST['stock'])) {
	$stock = $_REQUEST['stock'];				
} else {
	$stock = 'No';
}
if (isset($_REQUEST['avcost'])) {
	$avcost = $_REQUEST['avcost'];				
} else {
	$avcost = 0;
}
$db->query("select pcent from ".$findb.".stkpricepcent where uid = ".$pband);
$row = $db->single();
extract($row);

$value = round($stkprice * $stkqty,2);

$discount = 0;
if($disctype == '$') {
	$discount = $disc;
}
if($disc <> 0 && $disctype == '%') {
	$discount = round($value * $disc /100,2);
}
$value = $value - $discount;
$tax = round($value*$tpcent/100,2);
$tot = $value + $tax;

if (isset($_REQUEST['fxcode'])) {
	$fxcode = $_REQUEST['fxcode'];
	$fxrate = $_REQUEST['fxrate'];
	$fxamt = $_REQUEST['fxamt'];
} else {
	$db->query("select currency from ".$findb.".forex where def_forex = 'Yes'");
	$row = $db->single();
	extract($row);
	$fxcode = $currency;
	$fxrate = 1;
	$fxamt = $value;
}


$db->query("insert into ".$findb.".".$table." (itemcode,item,price,unit,quantity,tax,value,discount,disctype,discamount,tot,taxindex,taxtype,taxpcent,sellacc,sellbr,sellsub,purchacc,purchbr,purchsub,groupid,catid,loc,stock,avcost,forex,currency,rate) values (:itemcode,:item,:price,:unit,:quantity,:tax,:value,:discount,:disctype,:discamount,:tot,:taxindex,:taxtype,:taxpcent,:sellacc,:sellbr,:sellsub,:purchacc,:purchbr,:purchsub,:groupid,:catid,:loc,:stock,:avcost,:forex,:currency,:rate)");
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
$db->bind(':loc', $loc);
$db->bind(':stock', $stock);
$db->bind(':avcost', $avcost);
$db->bind(':forex', $fxamt);
$db->bind(':currency', $fxcode);
$db->bind(':rate', $fxrate);

$db->execute();

$db->closeDB();

?>
