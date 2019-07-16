<?php
session_start();

error_reporting (E_ALL ^ E_NOTICE ^ E_WARNING);
session_start();

//$_SESSION['s_server'] = "mysql3.webhost.co.nz";
$_SESSION['s_server'] = "localhost";
$_SESSION['s_admindb'] = "logtracc";

$userid = $_SESSION['s_u'];
$password = $_SESSION['s_p'];
$dbase = $_SESSION['s_admindb'];

require_once('../db1.php');
mysql_select_db($dbase) or die(mysql_error());


$query = "select * from users where username = '".$userid."' and upwd = '".$password."'";
$result = mysql_query($query) or die(mysql_error());

if (mysql_num_rows($result) > 0 ) {
	$row = mysql_fetch_array($result);
	extract($row);
	$uid = $row[0];
	$uadmin = $row[6];
	$ufname = $row[2];
	$ulname = $row[3];
	$sub_id = $row[13];
	$ubadmin = $row[12];
	$subscriber = $row[13];
	$staffid = $uid;
	$_SESSION['deftheme'] = $row[14];
	
	$q = "select coyid from access where staff_id = ".$staffid." and module = 'trd'";
	$r = mysql_query($q) or die(mysql_error());
	$row = mysql_fetch_array($r);
	extract($row);
	$_SESSION['s_findb'] = 'fin'.$sub_id.'_'.$coyid;
	$_SESSION['s_cltdb'] = 'sub'.$sub_id;
	$_SESSION['s_coyid'] = $coyid;
	
	$q = "select coyname from companies where coyid = ".$coyid;
	$r = mysql_query($q) or die(mysql_error());
	$row = mysql_fetch_array($r);
	extract($row);
	$_SESSION['s_coyname'] = $coyname;
	
	$query = "insert into sessions (timestamp,user_id,browser,userip,admin,uname,subid,uberadmin) values ";
	$query .= "('".$dt."',";
	$query .= $uid.",";
	$query .= "'".$browser."',";
	$query .= "'".$uip."',";
	$query .= "'".$uadmin."',";
	$query .= "'".trim($ufname).' '.trim($ulname)."',";
	$query .= $sub_id.",";
	$query .= "'".$ubadmin."')";
	
	$result = mysql_query($query) or die(mysql_error().$query);
	$newid = mysql_insert_id();	
	
	$q = 'update sessions set session = "'.md5($newid).'" where session_id = '.$newid;
	$r = mysql_query($q) or die(mysql_error().' '.$q);
	
	$_SESSION['usersession'] = md5($newid);
	
	$q = "select subname,logo,clt,fin,hrs,prc,man,timezone from subscribers where subid = ".$sub_id;
	$r = mysql_query($q) or die(mysql_error());
	$qrow = mysql_fetch_array($r);
	extract($qrow);
	$_SESSION['subscriber_name'] = $qrow[1];
	$_SESSION['logo'] = $logo;
	$_SESSION['clt'] = $clt;
	$_SESSION['fin'] = $fin;
	$_SESSION['hrs'] = $hrs;
	$_SESSION['prc'] = $prc;
	$_SESSION['man'] = $man;
	$_SESSION['subscriber'] = $sub_id;
	$_SESSION['s_staffid'] = $staffid;
	$_SESSION['s_admindb'] = "logtracc";
	$_SESSION['s_timezone'] = $timezone;
	//date_default_timezone_set($_SESSION['s_timezone']);


	$hdate = date('Y-m-d');
	$ttime = strftime("%H:%M", time());

	$query = "insert into audit (ddate,ttime,user_id,userip,uname,sub_id,action) values ";
	$query .= "('".$hdate."',";
	$query .= "'".$ttime."',";
	$query .= $uid.",";
	$query .= "'".$uip."',";
	$query .= "'".trim($ufname).' '.trim($ulname)."',";
	$query .= $sub_id.",";
	$query .= "'"."Login"."')";
	
	$result = mysql_query($query) or die(mysql_error().$query);

		
	header("Location: invoice.php");

} else {
	header("Location: index.php");
}

?>
