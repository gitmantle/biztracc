<?php
session_start();
//ini_set('display_errors', true);
require("../../db.php");

$bcode = $_REQUEST['bcode'];

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select * from stkmast where barno = ".$bcode;
$r = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($r);
extract($row);
$stk = $item.'~'.$itemcode.'~'.$unit.'~'.$deftax.'~'.$sellacc.'~'.$sellbr.'~'.$sellsub.'~'.$purchacc.'~'.$purchbr.'~'.$purchsub.'~'.$groupid.'~'.$catid.'~'.$avgcost.'~'.$setsell.'~'.$trackserial.'~'.$taxpcent;

echo $stk;

?>
