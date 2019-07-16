<?php
session_start();
$ac = $_REQUEST['vac'];
$br = $_REQUEST['vbr'];
$sb = $_REQUEST['vsb'];
$ac2 = $_REQUEST['vac2'];
$br2 = $_REQUEST['vbr2'];
$sb2 = $_REQUEST['vsb2'];

$_SESSION['s_viewac'] = $ac.'~'.$br.'~'.$sb;
$_SESSION['s_viewac2'] = $ac2.'~'.$br2.'~'.$sb2;
?>
