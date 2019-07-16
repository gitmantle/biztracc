<?php
session_start();
$cid = $_REQUEST['coyid'];
$c = explode('~',$cid);
$coyid = $c[0];
$coytaxyear = $c[1];

$_SESSION['s_coyid'] = $coyid;
$_SESSION['s_coytaxyear'] = $coytaxyear;

?>
