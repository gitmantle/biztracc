<?php
session_start();
$tid = $_REQUEST['tid'];

$cltdb = $_SESSION['s_cltdb'];
$findb = $_SESSION['s_findb'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

// check if the account has sub accounts
if ($sub = 0) {
	$db->query("select crno,crsub from ".$cltdb.".client_company_xref where (uid = ".$tid." and crsub > 0)");
	$rows = $db->resultset();
		
	if (count($rows) > 0) {
		echo 'This account has sub accounts, delete them first. Do not tick the box below otherwise you will not get any more warning messages on this page.';
		return;
	}
}

// check if the account has transactions
$db->query("select crno,crsub from ".$cltdb.".client_company_xref where uid = ".$tid);
$row = $db->single();
extract($row);
$crn = $crno;
$crs = $crsub;

$db->query("select accountno from ".$findb.".trmain where (accountno = ".$crn." and sub = ".$crs.")");
$rows = $db->resultset();
	
if (count($rows) > 0) {
	echo 'This account has transactions. Do not tick the box below otherwise you will not get any more warning messages on this page.';
	return;
}


$tot = 0;
foreach ($rows as $row) {
	extract($row);
	$tot = $tot + $debit-$credit;
}
if ($tot > 0) {
	echo 'This account has a non-zero balance. Do not tick the box below otherwise you will not get any more warning messages on this page.';
	return;
}

echo 'Deleting account.  Do not tick the box below otherwise you will not get any more warning messages on this page.';
$db->query('delete from '.$cltdb.'.client_company_xref where uid = '.$tid);
$db->execute();

$db->closeDB();
?>
