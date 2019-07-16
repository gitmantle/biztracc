<?php
session_start();
$tid = $_REQUEST['tid'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$cltdb = $_SESSION['s_cltdb'];

$db->query('delete from '.$cltdb.'.quotelines where ref_no = "'.$tid.'"');
$db->execute();
$db->query('delete from '.$cltdb.'.quotes where ref_no = "'.$tid.'"');
$db->execute();

$db->closeDB();

?>

