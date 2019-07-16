<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

$table = 'ztmp'.$user_id.'_quote';

$tid = $_REQUEST['tid'];

$cltdb = $_SESSION['s_cltdb'];


$db->query('delete from '.$cltdb.'.'.$table.' where uid = '.$tid);
$db->execute();

$db->query('delete from '.$cltdb.'.quotelines where uid = '.$tid);
$db->execute();

$db->closeDB();
?>
