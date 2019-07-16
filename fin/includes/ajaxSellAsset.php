<?php
session_start();
//ini_set('display_errors', true);

if ($_SESSION['s_server'] == 'localhost') {
	$root = $_SERVER['DOCUMENT_ROOT'];
	$pathdb = $root.'logtracc/db.php';
	require($pathdb);
} else {
	$root = $_SERVER['DOCUMENT_ROOT'];
	$pathdb = $root.'/db.php';
	require($pathdb);
}


$coyno = $_SESSION['s_coyid'];

$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$uip = $userip;
$unm = $uname;

$table = 'ztmp'.$user_id.'_trans';
date_default_timezone_set($_SESSION['s_timezone']);

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select acc2cr,subcr,brcr from ".$table." where uid = 1";
$result = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$a2c = $acc2cr;
$b2c = $brcr;

$q = "select amount from ".$table." where uid = 3";
$result = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$q = "update fixassets set cost = cost - ".$amount." where accountno = ".$a2c." and branch = '".$b2c."'";
$result = mysql_query($q) or die(mysql_error().' '.$q);


?>