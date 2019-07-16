<?php
session_start();
$usersession = $_SESSION['usersession'];
$startclockuid = $_SESSION['startclockuid'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$stop = date("Y-m-d H:i:s");

$findb = 'infinint_fin45_27';

$db->query("update ".$findb.".wip set finish = :finish where uid = :uid");
$db->bind(':finish', $stop);
$db->bind(':uid', $startclockuid);
$db->execute();

$db->query("select time_to_sec(timediff(finish,start))/3600 as hours from ".$findb.".wip where uid = :uid");
$db->bind(':uid', $startclockuid);
$row = $db->single();
$hours = $row['hours'];
$db->execute();

$db->query("update ".$findb.".wip set hours = :hours where uid = :uid");
$db->bind(':hours', $hours);
$db->bind(':uid', $startclockuid);
$db->execute();

$_SESSION['startclockuid'] = 0;

$db->closeDB();
?>
