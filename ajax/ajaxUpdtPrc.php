<?php
session_start();
$prc = $_REQUEST['prc'];
$sid = $_SESSION['subscriber'];
$coyid = $_SESSION['s_coyid'];

$_SESSION['s_prcdb'] = 'infinint_'.$prc.$sid.'_'.$coyid;
?>
