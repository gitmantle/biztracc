<?php
session_start();
$ac = $_REQUEST['ac'];
$sb = $_REQUEST['sb'];
$cl = $_REQUEST['cname'];

$_SESSION['s_crac'] = $ac;
$_SESSION['s_crsb'] = $sb;
$_SESSION['s_crclientname'] = $cl;
?>
