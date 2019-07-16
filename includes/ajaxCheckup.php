<?php
session_start();
$um = md5($_REQUEST['um']);
$pd = md5($_REQUEST['pd']);

$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());

$query = "select uid from users where username = '".$um."' and upwd = '".$pd."'";
$result = mysql_query($query) or die(mysql_error().$query);

$retnum = mysql_num_rows($result);

if ($retnum > 0) {
	$retval = 'Y';
} else {
	$retval = 'N';	
}
echo $retval;

?>
