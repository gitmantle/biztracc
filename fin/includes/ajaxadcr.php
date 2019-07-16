<?php
session_start();
$tid = $_REQUEST['tid'];
$coyidno = $_SESSION['s_coyid'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$cltdb = $_SESSION['s_cltdb'];

$cracno = 20000000 + $tid;

$db->query("select uid from ".$cltdb.".client_company_xref where company_id = ".$coyidno." and crno = ".$cracno);
$rows = $db->resultset();

if (count($rows) > 0) {
	echo 'This client already exists as a Creditor in this Company. Do not tick the box below otherwise you will not get any more warning messages on this page.';
	return;
}	

$db->query("select lastname from ".$cltdb.".members where member_id = ".$tid);
$row = $db->single();
extract($row);
	
$db->query("insert into ".$cltdb.".client_company_xref (client_id,company_id,crno,sortcode,member) values (:client_id,:company_id,:crno,:sortcode,:member)");
$db->bind(':client_id', $tid);
$db->bind(':company_id', $coyidno);
$db->bind(':crno', $cracno);
$db->bind(':sortcode', $lastname.$cracno."-0");
$db->bind(':member', $lastname);

$db->execute();
$db->closeDB();

echo 'Creditor added.  Do not tick the box below otherwise you will not get any more warning messages on this page.';

?>
