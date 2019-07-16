<?php
session_start();
$source = $_REQUEST['source'];

$_SESSION['s_source'] = $source;
?>
