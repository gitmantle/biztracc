<?php
session_start();
$purchref = strtoupper($_REQUEST['purchref']);

$_SESSION['s_purchref'] = $purchref;

$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$tradetable = 'ztmp'.$user_id.'_trading';

$findb = $_SESSION['s_findb'];

$db->query("delete from ".$findb.".".$tradetable);
$db->execute();

$db->query("insert into ".$findb.".".$tradetable." select invtrans.uid,invtrans.itemcode,invtrans.item,invtrans.price,invtrans.unit,(invtrans.quantity - invtrans.returns) as qty,invtrans.tax,invtrans.value,invtrans.discount,invtrans.disc_type,0,(invtrans.value+invtrans.tax) as tot,0,invtrans.taxtype,invtrans.taxpcent,stkmast.sellacc,'',stkmast.sellsub,stkmast.purchacc,'',stkmast.purchsub,stkmast.groupid,stkmast.catid,stkmast.trackserial,stktrans.locid,'Y',invtrans.ref_no,stkmast.stock,stkmast.avgcost,(invtrans.quantity - invtrans.returns) as oqty,invtrans.value,invtrans.currency,invtrans.rate,'' from ".$findb.".invtrans,".$findb.".stkmast,".$findb.".stktrans,".$findb.".invhead where (invtrans.itemcode = stkmast.itemcode) and (stktrans.ref_no = invtrans.ref_no) and (stktrans.itemcode = invtrans.itemcode) and (invhead.ref_no = invtrans.ref_no) and invtrans.ref_no = '".$purchref."'");
$db->execute();

//***************************************************
// check if serial numbered and how many not sold
//***************************************************
$pref = substr($purchref,0,3);
if ($pref == 'GRN' || $pref == 'C_P') {
	$db->query("select uid, itemcode,trackserial from ".$findb.".".$tradetable);
	$rows = $db->resultset();
	foreach ($rows as $row) {
		extract($row);
		$id = $uid;
		if ($trackserial == 'Yes') {
			$db->query("select count(sold) as available from ".$findb.".stkserials where sold = '' and ref_no = '".$purchref."'");
			$row = $db->single();
			extract($row);
			
			$db->query("update ".$findb.".".$tradetable." set quantity = ".$available." where uid = ".$id);
			$db->execute();
		}
	}
	
}
//***************************************************

$sql = "delete from ".$findb.".".$tradetable." where quantity = 0";
$db->query($sql);
$db->execute();

$db->query("select client,accountno,sub,cash,cheque,eftpos,ccard,postaladdress from ".$findb.".invhead where ref_no = '".$purchref."'");
$row = $db->single();
extract($row);
$paymethod = '';
if ($cheque > 0) {
	$paymethod = 'chq';
}
if ($cash > 0) {
	$paymethod = 'csh';
}
if ($eftpos > 0) {
	$paymethod = 'eft';
}
if ($ccard > 0) {
	$paymethod = 'crd';
}

$db->query("select uid from ".$findb.".".$tradetable);
$rows = $db->resultset();

$numrows = $db->rowCount();

if ($numrows == 0) {
	$pac = 'N';
} else {
	$pac = $client.'~'.$accountno.'~'.$sub.'~'.$paymethod.'~'.$postaladdress;
}

$db->closeDB();


echo $pac;

?>
