<?php
session_start();
$uid = $_REQUEST['uid'];

$_SESSION['s_userid'] = $uid;

?>
