<?php
session_start();
//ini_set('display_errors', true);

$coyno = $_SESSION['s_coyid'];

$usersession = $_SESSION['usersession'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

//$db->beginTransaction();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
extract($row);
$uip = $row['userip'];
$user_id = $row['user_id'];
$unm = $row['uname'];

$table = 'ztmp'.$user_id.'_trading';
date_default_timezone_set($_SESSION['s_timezone']);
$serialtable = 'ztmp'.$user_id.'_serialnos';

$transtype = strtoupper($_REQUEST['type']);
$ddate = $_REQUEST['ddate'];
$descript1 = $_REQUEST['descript'];
$reference = strtoupper($_REQUEST['ref']);
$reference = strtr($reference,chr(13),''); // replace carriage return blank
$reference = strtr($reference,chr(10),''); // replace line feed with blank
$reference = trim($reference);
if (isset($_REQUEST['staffmember'])) {
	$staffmember = $_REQUEST['staffmember'];
} else {
	$staffmember = '';
}

$acc = $_REQUEST['acc'];
$asb = $_REQUEST['asb'];
$loc = $_REQUEST['loc'];
$xref = '';
$postaladdress = '';
$deliveryaddress = '';
$client = $_REQUEST['clt'];
$transref = "";

$findb = $_SESSION['s_findb'];
$cltdb = $_SESSION['s_cltdb'];

$rno = '';

// get branch from stklocs
$db->query("select branch from ".$findb.".stklocs where uid = :loc");
$db->bind(':loc', $loc);
$row = $db->single();
extract($row);
$defbranch = $branch;

	// insert p_ohead record
	$db->query("insert into ".$findb.".p_ohead (ddate,accountno,branch,sub,gldesc,ref_no,xref,staff,postaladdress,deliveryaddress,client) values (:ddate,:accountno,:branch,:sub,:gldesc,:ref_no,:xref,:staff,:postaladdress,:deliveryaddress,:client)");
	$db->bind(':ddate', $ddate);
	$db->bind(':accountno', $acc);
	$db->bind(':branch', $defbranch);
	$db->bind(':sub', $asb);
	$db->bind(':gldesc', $descript1);
	$db->bind(':ref_no', $reference);
	$db->bind(':xref', $rno);
	$db->bind(':staff', $staffmember);
	$db->bind(':postaladdress', $postaladdress);
	$db->bind(':deliveryaddress', $deliveryaddress);
	$db->bind(':client', $client);
	
	$db->execute();	
	
	$db->query("select * from ".$findb.".".$table);
	$rows = $db->resultset();
	foreach ($rows as $row) {
		extract($row);
		$rid = $uid;
		$qty = $quantity;
		$stk = $stock;
		$cst = $avcost;
		
		// insert records into p_olines
		
		$db->query("insert into ".$findb.".p_olines (itemcode,item,quantity,unit,ref_no,p_olineno,supplier,sub) values (:itemcode,:item,:quantity,:unit,:ref_no,:p_olineno,:supplier,:sub)");
		$db->bind(':itemcode', $itemcode);
		$db->bind(':item', $item);
		$db->bind(':quantity', $quantity);
		$db->bind(':unit', $unit);
		$db->bind(':ref_no', $reference);
		$db->bind(':p_olineno', $rid);
		$db->bind(':supplier', $acc);
		$db->bind(':sub', $asb);
			
		$db->execute();	

	}


$db->query("delete from ".$findb.".".$table);
$db->execute();		
$db->query("delete from ".$findb.".".$serialtable);
$db->execute();		

/*
if ($err ==  ) {
	$db->cancelTransaction();
} else {
	$db->endTransaction();	
}
*/

$db->closeDB();

?>

