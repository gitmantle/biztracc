<?php
session_start();

$todate = $_REQUEST["todate"];

require("../../db.php");

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "update globals set lstatdt = '".$todate."'";
$r = mysql_query($q) or die(mysql_error().' '.$q);

?>
