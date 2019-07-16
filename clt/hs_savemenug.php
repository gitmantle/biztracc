<?php
session_start();

$cltdb = $_SESSION['s_cltdb'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$field = $_REQUEST['field'];
$uid = $_REQUEST['uid'];
$value = $_REQUEST['value'];

if ($value == 'true') {
	$db->query('update '.$cltdb.'.c_menu set '.$field.' = "Y" where menu_id = '.$uid);
} else {
	$db->query('update '.$cltdb.'.c_menu set '.$field.' = "N" where menu_id = '.$uid);
}
$db->execute();
$db->closeDB();

echo "Saved";
	
?>
