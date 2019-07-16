<?php
session_start();
//ini_set('display_errors', true);
require("../../db.php");

$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$table = 'ztmp'.$user_id.'_wages';

$op = $_REQUEST['op'];
$trkno = $_REQUEST['trkno'];
$trkbr = $_REQUEST['trkbr'];
$trlno = $_REQUEST['trlno'];
$trlbr = $_REQUEST['trlbr'];
$amt = $_REQUEST['amt'];
$trkpcent = $_REQUEST['trkpcent'];
$trlpcent = $_REQUEST['trlpcent'];
if ($trlno == '') {
	$trkamount = $amt;
	$trlamount = 0;
} else {
	$trkamount = $amt * $trkpcent / 100;
	$trlamount = $amt - $trkamount;
}

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

$sql = "insert into ".$table." (operator,truckno,truckbranch,truckamt,trailerno,trailerbranch,traileramt,total) values (";
$sql .= "'".$op."',";
$sql .= "'".$trkno."',";
$sql .= "'".$trkbr."',";
$sql .= $trkamount.",";
$sql .= "'".$trlno."',";
$sql .= "'".$trlbr."',";
$sql .= $trlamount.",";
$sql .= $amt.")";

$result = mysql_query($sql) or die(mysql_error().' - '.$sql);



?>
