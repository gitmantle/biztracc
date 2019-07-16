<?php
session_start();
$jobid = $_REQUEST['jid'];
$_SESSION['s_jobid'] = $jobid;
?>
