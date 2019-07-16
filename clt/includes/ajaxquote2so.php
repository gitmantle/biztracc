<?php
session_start();
//ini_set('display_errors', true);

$ref = $_REQUEST['ref'];

$usersession = $_SESSION['usersession'];
date_default_timezone_set($_SESSION['s_timezone']);

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];
$uip = $row['userip'];
$unm = $sname;

$cltdb = $_SESSION['s_cltdb'];

$today = date('Y-m-d');

$db->query("select uid,member_id,coy_id,accountno,branch,sub,gldesc,invno,ref_no,totvalue,tax,staff,postaladdress,deliveryaddress,client,currency,rate from ".$cltdb.".quotes where uid = ".$ref);
$row = $db->single();
extract ($row);
$r = substr($ref_no,3);
$soref = 'S_O'.$r;
$quoteid = $uid;

$ln = explode(" ",$client);
$lastname = $ln[0];

// member is not a debtor of the relevant company, make them so
if ($accountno == 0) {
	$accountno = 30000000 + $member_id;
	
	$db->query("insert into ".$cltdb.".client_company_xref (client_id,company_id,drno,sortcode,member) values (:client_id,:company_id,:drno,:sortcode,:member)");
	$db->bind(':client_id', $member_id);
	$db->bind(':company_id', $coy_id);
	$db->bind(':drno', $accountno);
	$db->bind(':sortcode', $lastname.$accountno.'-0');
	$db->bind(':member', $client);	
	$db->execute();	
	
	$db->query("update ".$cltdb.".quotes set accountno = :accountno where uid = :uid");
	$db->bind(':accountno', $accountno);
	$db->bind(':uid', $ref);
	$db->execute();
}

$_SESSION['s_findb'] = 'infinint_fin'.$subid.'_'.$coy_id;
$findb = $_SESSION['s_findb'];

// insert into invhead
$db->query("insert into ".$findb.".invhead (ddate,accountno,branch,sub,gldesc,transtype,ref_no,xref,totvalue,tax,cash,cheque,eftpos,ccard,staff,postaladdress,deliveryaddress,client,signature,currency,rate) values (:ddate,:accountno,:branch,:sub,:gldesc,:transtype,:ref_no,:xref,:totvalue,:tax,:cash,:cheque,:eftpos,:ccard,:staff,:postaladdress,:deliveryaddress,:client,:signature,:currency,:rate)");
$db->bind(':ddate', $today);
$db->bind(':accountno', $accountno);
$db->bind(':branch', $branch);
$db->bind(':sub', $sub);
$db->bind(':gldesc', $gldesc);
$db->bind(':transtype', 'S_O');
$db->bind(':ref_no', $soref);
$db->bind(':xref', '');
$db->bind(':totvalue', $totvalue);
$db->bind(':tax', $tax);
$db->bind(':cash', 0);
$db->bind(':cheque', 0);
$db->bind(':eftpos', 0);
$db->bind(':ccard', 0);
$db->bind(':staff', $staff);
$db->bind(':postaladdress', $postaladdress);
$db->bind(':deliveryaddress', $deliveryaddress);
$db->bind(':client', $client);
$db->bind(':signature', '');
$db->bind(':currency', $currency);
$db->bind(':rate', $rate);

$db->execute();	

$db->query("select itemcode,item,quantity,price,taxtype,taxpcent,tax,value,unit,ref_no,grnlineno,discount,disc_type,currency,rate from ".$cltdb.".quotelines where quote_id = ".$quoteid);
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	
	// insert records into invtrans
	
	$db->query("insert into ".$findb.".invtrans (itemcode,item,price,quantity,unit,taxtype,taxpcent,tax,ref_no,value,discount,disc_type,grnlineno,currency,rate) values (:itemcode,:item,:price,:quantity,:unit,:taxtype,:taxpcent,:tax,:ref_no,:value,:discount,:disc_type,:grnlineno,:currency,:rate)");
	$db->bind(':itemcode', $itemcode);
	$db->bind(':item', $item);
	$db->bind(':price', $price);
	$db->bind(':quantity', $quantity);
	$db->bind(':unit', $unit);
	$db->bind(':taxtype', $taxtype);
	$db->bind(':taxpcent', $taxpcent);
	$db->bind(':tax', $tax);
	$db->bind(':ref_no', $soref);
	$db->bind(':value', $value);
	$db->bind(':discount', $discount);
	$db->bind(':disc_type', $disc_type);
	$db->bind(':grnlineno', $grnlineno);
	$db->bind(':currency', $currency);
	$db->bind(':rate', $rate);
		
	$db->execute();	
	
}

$db->query("update ".$cltdb.".quotes set xref = '".$soref."' where ref_no = '".$ref_no."'");
$db->execute();	

$cid = $_SESSION['s_memberid'];
$db->query("select status from ".$cltdb.".members where member_id = ".$cid);
$row = $db->single();
extract($row);

if ($status == 'Lead' || $status == 'Prospect') {
	$db->query("update ".$cltdb.".members set status = 'Client' where member_id = ".$cid);
	$db->execute();
}

$db->closeDB();

?>

