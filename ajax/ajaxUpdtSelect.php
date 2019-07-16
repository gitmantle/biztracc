<?php
session_start();
$drcr = $_REQUEST['drcr'];
$cat = $_REQUEST['cat'];
$ledger = $_REQUEST['ledger'];

$_SESSION['s_select'] = $drcr.'~'.$cat.'~'.$ledger;
?>
