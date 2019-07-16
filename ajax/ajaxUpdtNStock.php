<?php
session_start();
$nstk = $_REQUEST['nstk'];
$bdt = $_REQUEST['bdt'];
$edt = $_REQUEST['edt'];

$_SESSION['s_nstk'] = $nstk;
$_SESSION['s_bdt'] = $bdt;
$_SESSION['s_edt'] = $edt;
?>
