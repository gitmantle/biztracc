<?php
session_start();

$server = "mysql3.webhost.co.nz";
$user = "logtracc9";
$pwd = "dun480can";
$connect = mysql_connect($server,$user,$pwd) or die("Check your database connection");	mysql_select_db($dbase) or die(mysql_error());

$edt = $_REQUEST['edt'];
$efm = $_REQUEST['efm'];
$esb = $_REQUEST['esb'];
$ems = $_REQUEST['ems'];
$output = str_replace("\n","[NEWLINE]",$ems);
$output = preg_replace('/[^(\x20-\x7F)]*/','', $output);
$ems = str_replace("[NEWLINE]","\n",$output);
$mid = $_REQUEST['mid'];
$sub = $_REQUEST['sub'];

$retval = '20-Success';

$dbase = 'sub30';
mysql_select_db($dbase) or die(mysql_error());

if (!$connect) {
  $retval = '12-Failed connection';
}

if ($retval == '20-Success') {

	$q = 'insert into emails (member_id,sub_id,email_date,email_from,email_subject,email_message) values ('.$mid.','.$sub.',"'.$edt.'","'.$efm.'","'.$esb.'","'.$ems.'")';
	$r = mysql_query($q) or die (mysql_error());
	if (!$r) {
		$retval = '11-Failed insert';
	}
}
echo $retval;

?>

