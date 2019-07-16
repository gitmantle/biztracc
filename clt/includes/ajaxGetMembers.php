<?php
session_start();
$oname = strtoupper($_REQUEST['oname']);
require("../../db.php");

$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());
$query = "select member_id,firstname,lastname from members where lastname like '".$oname."%' order by lastname,firstname";
$result = mysql_query($query) or die($query);
$owner_options = "<option value=\"0\">Select</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	$owner_options .= '<option value="'.$member_id.'">'.$firstname.' '.$lastname.'</option>';
}

echo $owner_options;


?>

