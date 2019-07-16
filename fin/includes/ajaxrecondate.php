<?php
session_start();

$ac = $_REQUEST['ac'];
$br = $_REQUEST['br'];
$sb = $_REQUEST['sb'];

$_SESSION['s_bankac'] = $ac;
$_SESSION['s_bankbr'] = $br;
$_SESSION['s_banksb'] = $sb;

$findb = $_SESSION['s_findb'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select recondate from ".$findb.".glmast where accountno = ".$ac." and branch = '".$br."' and sub = ".$sb);
$row = $db->single();
extract($row);

$dt = explode('-',$recondate);
$y = $dt[0];
$m = $dt[1];
$d = $dt[2];

$recdate = $d.'/'.$m.'/'.$y;

echo $recdate;

$db->closeDB();

return;

?>