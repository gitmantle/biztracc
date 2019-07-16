<?php
session_start();

$dc = $_SESSION['s_select'];
$d = explode('~',$dc);
$drcr = $d[0];

echo $drcr;
?>
