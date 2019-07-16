<?php
session_start();
//ini_set('display_errors', true);

$coyno = $_SESSION['s_coyid'];
$cluid = $_SESSION["s_memberid"];
$coyname = $_SESSION['s_coyname'];
$act = $_REQUEST['act'];

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

$table = 'ztmp'.$user_id.'_quote';
date_default_timezone_set($_SESSION['s_timezone']);

$ddate = $_REQUEST['ddate'];
$descript1 = $_REQUEST['descript'];
$reference = strtoupper($_REQUEST['ref']);
$invno = substr($reference,3);
$acc = $_REQUEST['acc'];
$asb = $_REQUEST['asb'];
$loc = $_REQUEST['loc'];
$xref = '';
if (isset($_REQUEST['postaladdress'])) {
	$postaladdress = $_REQUEST['postaladdress'];
} else {
	$postaladdress = '';
}
if (isset($_REQUEST['deliveryaddress'])) {
	$deliveryaddress = $_REQUEST['deliveryaddress'];
} else {
	$deliveryaddress = '';
}
$client = $_REQUEST['clt'];
$paymentmethod = $_REQUEST['paymethod'];
if (isset($_REQUEST['qnote'])) {
	$qnote = $_REQUEST['qnote'];
} else {
	$qnote = '';
}

if (isset($_REQUEST['forex'])) {
	$f = explode('~',$_REQUEST['forex']);
	$fxcode = $f[0];
	$fxrate = $f[1];
} else {
	$fxcode = $_REQUEST['fxcode'];
	$fxrate = $_REQUEST['fxrate'];	
}
$localcurrency = $_SESSION['s_localcurrency'];

$a2dr = $acc;
$b2dr = '';
$s2dr = $asb;
$a2cr = 0;
$b2cr = '';
$s2cr = 0;

$findb = $_SESSION['s_findb'];

// get branch from stklocs
$db->query("select branch from ".$findb.".stklocs where uid = ".$loc);
$row = $db->single();
extract($row);
$defbranch = $branch;

$cltdb = $_SESSION['s_cltdb'];

// if all quotelines have been deleted, delete quote
$db->query("select * from ".$cltdb.".".$table);
$rows = $db->resultset();
if (count($rows) == 0) {
	$db->query("delete from ".$cltdb.".quotes where ref_no = '".$reference."' and company_id = ".$coyno);
	$db->execute();
	return;
}

$db->query("select sum(tax) as totaltax, sum(value) as totalamount from ".$cltdb.".".$table);
$row = $db->single();
extract($row);
$totalvalue = $totalamount + $totaltax;

$cash = 0;
$cheque = 0;
$ccard = 0;
$eftpos = 0;

if (empty($b2dr)) {$br2dr = '1000';}
if (empty($s2dr)) {$sub2dr = 0;}
if (empty($b2cr)) {$br2cr = '1000';}
if (empty($s2cr)) {$sub2cr = 0;}	
	
$ano = $a2dr;
$bno = $b2dr;
$sno = $s2dr;
$taxdrcr = 'cr';

if ($act == 'edit') {
	$db->query("select uid from ".$cltdb.".quotes where ref_no = '".$reference."' and coy_id = ".$coyno);
	$row = $db->single();
	extract($row);
	$qid = $uid;	
	
	$db->query("delete from ".$cltdb.".quotes where ref_no = '".$reference."'");
	$db->execute();
	$db->query("delete from ".$cltdb.".quotelines where ref_no = '".$reference."'");
	$db->execute();
}

//convert to local currency
$totalamount = $totalamount / $fxrate;
$totaltax = $totaltax / $fxrate;
$totalvalue = $totalamount + $totaltax;

// insert quote record
$db->query("insert into ".$cltdb.".quotes (ddate,member_id,coy_id,accountno,branch,sub,gldesc,invno,ref_no,xref,totvalue,tax,cash,cheque,eftpos,ccard,staff,postaladdress,deliveryaddress,client,note,coyname,currency,rate) values (:ddate,:member_id,:coy_id,:accountno,:branch,:sub,:gldesc,:invno,:ref_no,:xref,:totvalue,:tax,:cash,:cheque,:eftpos,:ccard,:staff,:postaladdress,:deliveryaddress,:client,:note,:coyname,:currency,:rate)");
$db->bind(':ddate', $ddate);
$db->bind(':member_id', $cluid);
$db->bind(':coy_id', $coyno);
$db->bind(':accountno', $ano);
$db->bind(':branch', $bno);
$db->bind(':sub', $sno);
$db->bind(':gldesc', $descript1);
$db->bind(':invno', $invno);
$db->bind(':ref_no', $reference);
$db->bind(':xref', $xref);
$db->bind(':totvalue', $totalamount);
$db->bind(':tax', $totaltax);
$db->bind(':cash', $cash);
$db->bind(':cheque', $cheque);
$db->bind(':eftpos', $eftpos);
$db->bind(':ccard', $ccard);
$db->bind(':staff', $sname);
$db->bind(':postaladdress', $postaladdress);
$db->bind(':deliveryaddress', $deliveryaddress);
$db->bind(':client', $client);
$db->bind(':note', $qnote);
$db->bind(':coyname', $coyname);
$db->bind(':currency', $fxcode);
$db->bind(':rate', $fxrate);

$db->execute();

$quote_id = $db->lastInsertId();

$_SESSION['s_quoteid'] = $quote_id;
	
$db->query("select * from ".$cltdb.".".$table);
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	
	//convert to local currency
	$price = $price / $fxrate;
	$value = $value / $fxrate;
	$tax = $tax / $fxrate;
	$discount = $discount / $fxrate;	
	
	// insert records into quotelines
	
	$db->query("insert into ".$cltdb.".quotelines (quote_id,itemcode,item,price,quantity,unit,taxindex,taxtype,taxpcent,tax,ref_no,value,discount,disc_type,grnlineno,note,currency,rate) values (:quote_id,:itemcode,:item,:price,:quantity,:unit,:taxindex,:taxtype,:taxpcent,:tax,:ref_no,:value,:discount,:disc_type,:grnlineno,:note,:currency,:rate)");
	$db->bind(':quote_id', $quote_id);
	$db->bind(':itemcode', $itemcode);
	$db->bind(':item', $item);
	$db->bind(':price', $price);
	$db->bind(':quantity', $quantity);
	$db->bind(':unit', $unit);
	$db->bind(':taxindex', $taxindex);
	$db->bind(':taxtype', $taxtype);
	$db->bind(':taxpcent', $taxpcent);
	$db->bind(':tax', $tax);
	$db->bind(':ref_no', $reference);
	$db->bind(':value', $value);
	$db->bind(':discount', $discount);
	$db->bind(':disc_type', $disctype);
	$db->bind(':grnlineno', $uid);
	$db->bind(':note', $note);
	$db->bind(':currency', $fxcode);
	$db->bind(':rate', $fxrate);
	
	$db->execute();
	
}

$db->query("delete from ".$cltdb.".".$table);
$db->execute();
	
$db->closeDB();

?>

