<?php
session_start();

$todate = $_REQUEST['tdate'];
$fromdate = $_REQUEST['fdate'];

date_default_timezone_set($_SESSION['s_timezone']);

include_once("../includes/DBClass.php");
$db = new DBClass();

$findb = $_SESSION['s_findb'];

$db->query("update ".$findb.".trmain set gstrecon = 'Y' where accountno = 870 and ddate >= '".$fromdate."' and ddate <= '".$todate."'");
$db->execute();
$db->query("update ".$findb.".globals set gstfiled = '".$todate."'");
$db->execute();

echo  $_SESSION['s_tradtax']." commited";

$db->closeDB();

?>