<?php
session_start();
$fno = $_REQUEST['fno'];
$tno = $_REQUEST['tno'];
$dt = $_REQUEST['dt'];

$_SESSION['s_fno'] = $fno;
$_SESSION['s_tno'] = $tno;
$_SESSION['s_dt'] = $dt;
?>
