<?php
session_start();
$ac = $_REQUEST['vac'];
$br = $_REQUEST['vbr'];
$sb = $_REQUEST['vsb'];
$fd = $_REQUEST['fdt'];
$ed = $_REQUEST['edt'];
$ob = $_REQUEST['ob'];

$_SESSION['s_viewac'] = $ac.'~'.$br.'~'.$sb;
$_SESSION['s_fromdate'] = $fd;
$_SESSION['s_todate'] = $ed;
$_SESSION['s_sob'] = $ob;

?>
