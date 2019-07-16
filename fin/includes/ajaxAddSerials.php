<?php
session_start();
//ini_set('display_errors', true);

//itemcode:itemcode,itemname:itemname,serials:serials,loc:loc,refno:refno

$itemcode = $_REQUEST['itemcode'];
$itemname = $_REQUEST['itemname'];
$serials = $_REQUEST['serials'];
$loc = $_REQUEST['loc'];
$refno = $_REQUEST['refno'];

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

$serialtable = 'ztmp'.$user_id.'_serialnos';

$findb = $_SESSION['s_findb'];

$dupserials = array();

$s = explode(",",$serials);
foreach($s as $value) {

	// check the serial number for this item does not already exist.
	$db->query("select distinct serialno,itemcode from ".$findb.".stkserials where serialno = :serialno and itemcode = :itemcode");
	$db->bind(':itemcode', $itemcode);
	$db->bind(':serialno', $value);
	
	$row = $db->single();
	$numrows = $db->rowCount();
	
	if ($numrows > 0) {
		extract($row);
		$dsno = $serialno;
		$dupserials[] = $dsno;
	}
}
	
if (count($dupserials) == 0)	{
	$s = explode(",",$serials);
	foreach($s as $value) {
		$db->query("insert into ".$findb.".".$serialtable." (itemcode,item,serialno,locationid,ref_no) values (:itemcode,:item,:serialno,:locationid,:ref_no)");
		$db->bind(':itemcode', $itemcode);
		$db->bind(':item', $itemname);
		$db->bind(':serialno', $value);
		$db->bind(':locationid', $loc);
		$db->bind(':ref_no', $refno);
		$db->execute();
	}
}

$db->closeDB();

echo implode(',',$dupserials);

?>

