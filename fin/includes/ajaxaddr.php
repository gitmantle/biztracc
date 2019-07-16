<?php
session_start();
$tid = $_REQUEST['tid'];
$coyidno = $_SESSION['s_coyid'];

$usersession = $_SESSION['usersession'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$cltdb = $_SESSION['s_cltdb'];

$dracno = 30000000 + $tid;

$db->query("select uid from ".$cltdb.".client_company_xref where company_id = ".$coyidno." and drno = ".$dracno);
$rows = $db->resultset();

if (count($rows) > 0) {
	echo 'This client already exists as a Debtor in this Company. Do not tick the box below otherwise you will not get any more warning messages on this page.';
	return;
}	

$db->query("select lastname from ".$cltdb.".members where member_id = ".$tid);
$row = $db->single();
extract($row);
	
$db->query("insert into ".$cltdb.".client_company_xref (client_id,company_id,drno,sortcode,member) values (:client_id,:company_id,:drno,:sortcode,:member)");
$db->bind(':client_id', $tid);
$db->bind(':company_id', $coyidno);
$db->bind(':drno', $dracno);
$db->bind(':sortcode', $lastname.$dracno.'-0');
$db->bind(':member', $lastname);

$db->execute();

$db->closeDB();

echo 'Debtor added.  Do not tick the box below otherwise you will not get any more warning messages on this page.';

?>
