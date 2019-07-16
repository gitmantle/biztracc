<?php
session_start();

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "update incidents set include = 'N'";
$r = mysql_query($q) or die(mysql_error().' '.$q);

?>
