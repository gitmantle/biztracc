<?php
session_start();
$grp = $_REQUEST['grp'];
$cat = $_REQUEST['cat'];

$_SESSION['s_stkgrp'] = $grp;
$_SESSION['s_stkcat'] = $cat;
?>
