<?php
session_start();
$usersession = $_SESSION['usersession'];
$tid = $_REQUEST['tid'];

$findb = $_SESSION['s_findb'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query('delete from '.$findb.'.stkpricepcent where uid = '.$tid);
$r = $db->execute();

$db->closeDB();

?>
