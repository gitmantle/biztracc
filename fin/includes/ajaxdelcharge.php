<?php
session_start();
$usersession = $_SESSION['usersession'];
$tid = $_REQUEST['tid'];

$findb = $_SESSION['s_findb'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$chargefile = 'ztmp'.$user_id.'_charges';

$db->query('delete from '.$findb.'.'.$chargefile.' where uid = '.$tid);
$db->execute();

$db->closeDB();

?>
