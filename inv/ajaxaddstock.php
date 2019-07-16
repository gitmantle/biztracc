<?php
session_start();
$stkitem = trim($_REQUEST['stitem']);

$coyid = $_SESSION['s_coyid'];

ini_set('display_errors', true);
require("../db.php");

$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$cltdb = $_SESSION['s_findb'];
mysql_select_db($cltdb) or die(mysql_error());

// look for stock item

$q = "select itemid, itemcode, deftax from stkmast where item = '".$stkitem."'";
$r = mysql_query($q) or die(mysql_error().' '.$q);
$numrows = mysql_num_rows($r);
if ($numrows > 0) {
	$row = mysql_fetch_array($r);
	extract($row);
	$sid = $itemid;
	$stcode = $itemcode;
	
	$q = "select taxpcent from taxtypes where uid = ".$deftax;
	$r = mysql_query($q) or die(mysql_error());
	$row = mysql_fetch_array($r);
	extract($row);
	$tpcent = $taxpcent;
	
	$tpcent = $taxpcent;
	
	
} else { // add to stkmast 

	$q = "select taxpcent from taxtypes where uid = 1";
	$r = mysql_query($q) or die(mysql_error());
	$row = mysql_fetch_array($r);
	extract($row);
	$tpcent = $taxpcent;
	

	$q = "insert into stkmast (groupid,catid,item,unit,sellacc,sellsub,purchacc,purchsub,stock) values (1,1,'".$stkitem."','Each',1,0,101,0,'No')";
	$r = mysql_query($q) or die(mysql_error().' '.$q);
	$sid = mysql_insert_id();
	
	$stkcode = substr($stkitem,0,2).'-'.$sid;
	
	$qs = "update stkmast set itemcode = '".$stkcode."' where itemid = ".$sid;
	$rs = mysql_query($qs) or die(mysql_error());

}

$stk = $stkcode.'~'.$stkitem.'~Each~1~101~ ~0~101~ ~0~1~1~0~0~No~'.$tpcent;

echo $stk;

?>
