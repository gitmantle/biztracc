<?php
session_start();
$sub_id = $_REQUEST['sid'];
$force = $_REQUEST['force'];

date_default_timezone_set($_SESSION['s_timezone']);
$ddate = date("Y-m-d");

	$server = "mysql3.webhost.co.nz";
	$user = "logtracc9";
	$pwd = "dun480can";
	$connect = mysql_connect($server,$user,$pwd) or die("Check your database connection");

	mysql_select_db('logtracc') or die(mysql_error());

	if ($force == 'Y') {
		$query = "select uid,ufname,ulname,logsuser,logspwd,uadmin from users where sub_id = ".$sub_id;
	} else {
		$query = "select uid,ufname,ulname,logsuser,logspwd,uadmin from users where sub_id = ".$sub_id." and (lastupdated = '0000-00-00 00:00:00' or (now() - interval 30 day) < lastupdated)" ;
	}
	$result = mysql_query($query) or die(mysql_error());

	$users = array();
	if(mysql_num_rows($result)) {
		while($user = mysql_fetch_assoc($result)) {
		  $users[] = array('driver'=>$user);
		}
		$q = "update users set lastupdated = now() where lastupdated = '0000-00-00 00:00:00'";
		$r = mysql_query($q) or die(mysql_error());
		
		header('Content-type: application/json');
		echo json_encode(array('drivers'=>$users));
	} else {
		$mess[] = array('msg'=>'No records');
		header('Content-type: application/json');
		echo json_encode(array('message'=>$mess));
	}
	

?>
