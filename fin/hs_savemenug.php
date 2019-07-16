<?php
session_start();

$findb = $_SESSION['s_findb'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$field = $_REQUEST['field'];
$uid = $_REQUEST['uid'];
$value = $_REQUEST['value'];

if ($value == 'true') {
	$db->query('update '.$findb.'.c_menu set '.$field.' = "Y" where c_menu_id = '.$uid);
} else {
	$db->query('update '.$findb.'.c_menu set '.$field.' = "N" where c_menu_id = '.$uid);
}
$db->execute();
$db->closeDB();

echo "Saved";
	
?>
