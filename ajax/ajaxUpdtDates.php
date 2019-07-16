<?php
session_start();
$fd = $_REQUEST['fdt'];
$ed = $_REQUEST['edt'];
$ob = $_REQUEST['ob'];

$_SESSION['s_fromdate'] = $fd;
$_SESSION['s_todate'] = $ed;
$_SESSION['s_sob'] = $ob;

?>
