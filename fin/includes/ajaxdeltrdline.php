<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subscriber = $row['subid'];
$user_id = $row['user_id'];
$table = 'ztmp'.$user_id.'_trading';

$tid = $_REQUEST['tid'];

$findb = $_SESSION['s_findb'];


$db->query('delete from '.$findb.'.'.$table.' where uid = :tid');
$db->bind(':tid', $tid);
$db->execute();

$db->closeDB();
?>
