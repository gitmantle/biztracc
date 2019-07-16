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

$drfile = 'ztmp'.$user_id.'_statements';

$tid = $_REQUEST['id'];

$findb = $_SESSION['s_findb'];

$db->query('delete from '.$findb.'.'.$drfile.' where uid = '.$tid);
$db->execute();

$db->closeDB();

?>
