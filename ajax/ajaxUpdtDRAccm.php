<?php
session_start();
$onetwo = $_REQUEST['onetwo'];

if ($onetwo == 1) {
	$ac = $_REQUEST['ac'];
	$sb = $_REQUEST['sb'];
	$acname = $_REQUEST['acname'];
	$_SESSION['s_viewdr1'] = $ac.'~'.$sb.'~'.$acname;
} else {
	$ac2 = $_REQUEST['ac2'];
	$sb2 = $_REQUEST['sb2'];
	$acname2 = $_REQUEST['acname2'];
	$_SESSION['s_viewdr2'] = $ac2.'~'.$sb2.'~'.$acname2;
}
?>
