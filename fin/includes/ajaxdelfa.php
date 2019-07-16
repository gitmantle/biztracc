<?php
session_start();
$tid = $_REQUEST['tid'];

$findb = $_SESSION['s_findb'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select hcode,branch,accountno from ".$findb.".fixassets where uid = ".$tid);
$row = $db->single();
extract($row);


// check if the account has transactions
$db->query("select accountno from ".$findb.".trmain where (accountno = ".$accountno." and branch = '".$branch."')");
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
$db->query('delete from ".$findb.".fixassets where uid = '.$tid);
$db->execute();
$db->clsoeDB();
?>
