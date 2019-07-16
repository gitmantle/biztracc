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

$q = "select ddate,amount,acc2dr,subdr,brdr from ".$table;
$result = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$q = "update fixassets set cost = ".$amount.", bought = '".$ddate."' where accountno = ".$acc2dr." and branch = '".$brdr."'";
$result = mysql_query($q) or die(mysql_error().' '.$q);

?>