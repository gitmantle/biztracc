<?php
$dbs = $_SESSION['s_cltdb'];

require("../db.php");
mysql_select_db($dbs) or die(mysql_error());

$cmuid = $_REQUEST['tid'];

$query = "delete from links where link_id = ".$cmuid;
$result = mysql_query($query) or die(mysql_error().' '.$query);

$dbs = $_SESSION['s_admindb'];
mysql_select_db($dbs) or die(mysql_error());
?>

