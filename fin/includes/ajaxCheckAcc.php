<?php
session_start();

$ac = $_REQUEST['ac'];
$br = $_REQUEST['br'];
$sb = $_REQUEST['sb'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$findb = $_SESSION['s_findb'];

$db->query("select uid from ".$findb.".glmast where accountno = ".$ac." and branch = '".$br."' and sub = ".$sb);
$row = $db->resultset();
$numrows = $db->rowCount();
if ($numrows > 0) {
	echo 'duplicate';
} else {
	echo 'N';
}
$db->closeDB();
return;


?>