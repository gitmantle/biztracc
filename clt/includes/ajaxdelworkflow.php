<?php
session_start();
$tid = $_REQUEST['tid'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

$cltdb = $_SESSION['s_cltdb'];

$db->query('delete from '.$cltdb.'.workflow where process_id = '.$tid);
$db->execute();

$db->closeDB();
?>
