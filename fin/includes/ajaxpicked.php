<?php
session_start();
$rid = $_REQUEST['id'];
$picked = $_REQUEST['quantity'];
$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
require("../../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$tradetable = 'ztmp'.$user_id.'_trading';

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

// get original quantity
$q = "select quantity from ".$tradetable." where uid = ".$rid;
$r = mysql_query($q) or die(mysql_error().' '.$q);
$row = mysql_fetch_array($r);
extract($row);
$origqty = $quantity;

if ($picked > $origqty) {
	echo "You may not return more than you received";
} else {
	$q = "update ".$tradetable." set quantity = ".$picked.", value = ".$picked." * price, tax = ".$picked." * price * taxpcent / 100 where uid = ".$rid;
	$r = mysql_query($q) or die(mysql_error().' '.$q);
	$q = "update ".$tradetable." set tot = value + tax where uid = ".$rid;
	$r = mysql_query($q) or die(mysql_error().' '.$q);
	echo '1';
}

?>
