
<?php
session_start();

$findb = $_SESSION['s_findb'];

$fbranch = $_REQUEST['tobranch'];
$facc = explode('-',$_REQUEST['fromacc']);
$fac = $facc[0];
$lacc = explode('-',$_REQUEST['toacc']);
$lac = $lacc[0];
$frombranch = $_REQUEST['frombranch'];
	
include_once("../includes/DBClass.php");
$db = new DBClass();

// get array of non-system accounts
$db->query("select account,accountno,sub,blocked,grp,paygst from ".$findb.".glmast where accountno >= ".$fac." and accountno <= ".$lac." and branch = '".$frombranch."' and system = 'N'");
$rows = $db->resultset();

foreach ($rows as $row) {
	extract($row);
	$mname = $account;
	$macno = $accountno;
	$msub = $sub;
	$mblocked = $blocked;
	$mgrp = $grp;
	$mpaygst = $paygst;
	$db->query("insert into ".$findb.".glmast (account,accountno,branch,sub,blocked,grp,paygst,ctrlacc) values (:account,:accountno,:branch,:sub,:blocked,:grp,:paygst,:ctrlacc)");
	$db->bind(':account', $mname);
	$db->bind(':accountno', $macno);
	$db->bind(':branch', $fbranch);
	$db->bind(':sub', $msub);
	$db->bind(':blocked', $mblocked);
	$db->bind(':grp', $mgrp);
	$db->bind(':paygst', $mpaygst);
	$db->bind(':ctrlacc', 'N');
	
	$db->execute();
}	
	
$db->closeDB();
?>
 


