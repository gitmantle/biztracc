<?php
session_start();
$usersession = $_SESSION['usersession'];
$tid = $_REQUEST['tid'];

include_once("DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$moduledb = 'infinint_sub'.$subid;

$db->query('update '.$moduledb.'.todo set done = "Yes" where todo_id = '.$tid);
$db->execute();

$db->closeDB();

?>
