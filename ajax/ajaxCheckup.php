<?php
session_start();

require_once("../db.php");

$moduledb = $_SESSION['s_admindb'];
mysql_select_db($moduledb) or die(mysql_error());

$um = md5($_REQUEST['um']);

$query = "select uid from staff where username = '".$um."'";
$result = mysql_query($query) or die(mysql_error().$query);

$retnum = mysql_num_rows($result);

if ($retnum > 0) {
	$retval = 'Y';
} else {
	$retval = 'N';	
}
echo $retval;

?>
