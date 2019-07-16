<?php
session_start();
$tid = $_REQUEST['tid'];
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

$cltdb = $_SESSION['s_cltdb'];

// check if member is a debtor or creditor of any companies
$db->query("select companies.coyname from companies,".$cltdb.".client_company_xref where companies.coyid = client_company_xref.company_id and client_company_xref.client_id = ".$tid);
$rows = $db->resultset();
$numrows = $db->rowCount();
if ($numrows > 0) {
	echo 'This member is a debtor or creditor of one or more of your companies. Please delete them from these companies first.';
	return;
} else {
	$db->query('delete from '.$cltdb.'.members where member_id = '.$tid);
	$db->execute();
}


$db->closeDB();
?>
