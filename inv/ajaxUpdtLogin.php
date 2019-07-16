<?php
session_start();
$u = $_REQUEST['u'];
$p = $_REQUEST['p'];

$_SESSION['s_u'] = md5($u);
$_SESSION['s_p'] = md5($p);

?>
