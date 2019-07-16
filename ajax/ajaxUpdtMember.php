<?php
session_start();
$memberid = $_REQUEST['memberid'];

$_SESSION['s_memberid'] = $memberid;
?>
