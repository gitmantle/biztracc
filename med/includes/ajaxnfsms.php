<?php
session_start();

$admindb = $_SESSION['s_admindb'];
$usersession = $_SESSION['usersession'];

require_once('../../db.php');
mysql_select_db($admindb) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$subscriber = $subid;

$nftable = 'ztmp'.$user_id.'_nofunds';

$q = "select email as coyemail from globals";
$r = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($r);
extract($row);

$q = "select * from ".$nftable." where mobile <> ''";
$r = mysql_query($q) or die(mysql_error().' '.$q);
while ($row = mysql_fetch_array($r)) {
	extract($row);
	
		

}

		echo "This will need to be set up with an SMS gateway for which charges apply";


?>