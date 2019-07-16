<?php
error_reporting (E_ALL ^ E_NOTICE ^ E_WARNING);
session_start();

$userid = md5(trim($_REQUEST['userid']));
$password = md5(trim($_REQUEST['password']));
//$_SESSION['s_server'] = "mysql3.webhost.co.nz";
$_SESSION['s_server'] = "localhost";
$_SESSION['s_admindb'] = "logtracc";
$dbase = $_SESSION['s_admindb'];

$_SESSION['s_timezone'] = 'Pacific/Auckland';
date_default_timezone_set($_SESSION['s_timezone']);


$uip = trim(str_replace('.','x',$_SERVER['REMOTE_ADDR']));
$dt = date('Y-m-d H:i:s',time());

// check for browser 

$useragent = $_SERVER['HTTP_USER_AGENT'];

if (preg_match('|MSIE ([0-9].[0-9]{1,2})|',$useragent,$matched)) {
	$browser_version=$matched[1];
	$browser = 'IE';
} elseif (preg_match( '|Opera/([0-9].[0-9]{1,2})|',$useragent,$matched)) {
	$browser_version=$matched[1];
	$browser = 'Opera';
} elseif(preg_match('|Firefox/([0-9\.]+)|',$useragent,$matched)) {
		$browser_version=$matched[1];
		$browser = 'Firefox';
} elseif(preg_match('|Safari/([0-9\.]+)|',$useragent,$matched)) {
		$browser_version=$matched[1];
		$browser = 'Safari';
} else {
		// browser not recognized!
	$browser_version = 0;
	$browser= 'other';
}

require('../db1.php');
mysql_select_db($dbase) or die(mysql_error());
$q1 = "select * from users where username = '".$userid."' and upwd = '".$password."' and uberadmin = 'Y'";
$r1 = mysql_query($q1) or die(mysql_error().' '.$q1);
if (mysql_num_rows($r1) > 0 ) {
	$row = mysql_fetch_array($r1);
	extract($row);

	$query = "insert into sessions (timestamp,user_id,browser,userip,admin,uberadmin,uname,subid) values ";
	$query .= "('".$dt."',";
	$query .= $row[0].",";
	$query .= "'".$browser."',";
	$query .= "'".$uip."',";
	$query .= "'".$row[6]."',";
	$query .= "'".$row[12]."',";
	$query .= "'".trim($row[2]).' '.trim($row[3])."',";
	$query .= $row[13].")";

	$result = mysql_query($query) or die(mysql_error().$query);
	$newid = mysql_insert_id();	
	
	$q = 'update sessions set session = "'.md5($newid).'" where session_id = '.$newid;
	$r = mysql_query($q) or die(mysql_error().' '.$q);
	
	$_SESSION['usersession'] = md5($newid);
	$_SESSION['s_subscriber'] = $row[13];

	date_default_timezone_set($_SESSION['s_timezone']);
	$hdate = date('Y-m-d');
	$ttime = strftime("%H:%M", mktime());
	$format = 'Y-m-j G:i:s'; 
	$oneWeekAgo = strtotime ( $format, strtotime ( '-7 day' . $hdate )) ; 
												   
	$q = "delete from sessions where timestamp < '".$oneWeekAgo."'";
	$r = mysql_query($q) or die(mysql_error().' '.$q); 

	$query = "insert into audit (ddate,ttime,user_id,userip,uname,coyid,action) values ";
	$query .= "('".$hdate."',";
	$query .= "'".$ttime."',";
	$query .= $row[0].",";
	$query .= "'".$uip."',";
	$query .= '"'.trim($row[2]).' '.trim($row[3]).'",';
	$query .= $row[13].",";
	$query .= "'"."Login Uberadmin"."')";

	$result = mysql_query($query) or die(mysql_error().$query);

		header("Location: admin.php");

} else {

	header("Location: index.php");

}

?>

