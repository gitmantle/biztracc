<?php
session_start();
$ac = $_REQUEST['ac'];
$sb = $_REQUEST['sb'];
if (isset($_REQUEST['cname'])) {
	$cl = $_REQUEST['cname'];
} else {
	$cl = "";
}
if (isset($_REQUEST['br'])) {
	$br = $_REQUEST['br'];
} else {
	$br = "";
}


$_SESSION['s_drac'] = $ac;
$_SESSION['s_drsb'] = $sb;
$_SESSION['s_clientname'] = $cl;
$_SESSION['s_payrecbranch'] = $br;
?>
