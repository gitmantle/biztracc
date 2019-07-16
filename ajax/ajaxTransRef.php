<?php
session_start();
$reference = $_REQUEST['tref'];

$_SESSION['s_transref'] = $reference;
?>
