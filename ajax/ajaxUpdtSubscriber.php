<?php
session_start();
$sid = $_REQUEST['subid'];

$_SESSION['s_subid'] = $sid;

?>
