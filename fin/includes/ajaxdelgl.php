<?php
session_start();
$tid = $_REQUEST['tid'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$findb = $_SESSION['s_findb'];

$db->query("select account,accountno,branch,sub,ctrlacc from ".$findb.".glmast where uid = ".$tid);
$row = $db->single();
extract($row);

if ($ctrlacc == 'Y') {
	echo 'alert("This is a system account. Do not tick the box below otherwise you will not get any more warning messages on this page.';
	return;
}

// check if the account has sub accounts
if ($sub = 0) {
	$db->query("select accountno from ".$findb.".glmast where (accountno = ".$accountno." and branch = '".$branch."')");
	$rows = $db->resultset();
		
	if (count($rows) > 0) {
		echo 'This account has sub accounts, delete them first. Do not tick the box below otherwise you will not get any more warning messages on this page.';
		return;
	}
}

// check if the account has transactions
$db->query("select accountno from ".$findb.".trmain where (accountno = ".$accountno." and branch = '".$branch."' and sub = ".$sub.")");
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
$db->query('delete from '.$findb.'.glmast where uid = '.$tid);
$db->execute();

$db->closeDB();

?>
