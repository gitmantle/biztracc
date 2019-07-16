<?php
session_start();
$sub_id = $_REQUEST['sid'];
$coy_id = $_REQUEST['cid'];
$force = $_REQUEST['force'];
$dbase = 'log'.$sub_id.'_'.$coy_id;

date_default_timezone_set($_SESSION['s_timezone']);
$ddate = date("Y-m-d");
	
	$server = "mysql3.webhost.co.nz";
	$user = "logtracc9";
	$pwd = "dun480can";
	$connect = mysql_connect($server,$user,$pwd) or die("Check your database connection");

	mysql_select_db($dbase) or die(mysql_error());

	if ($force == 'Y') {
		$query = "select uid,route,compartment from routes order by route,compartment";		
	} else {
		$query = "select uid,route,compartment from routes where lastupdated = '0000-00-00 00:00:00' or (now() - interval 30 day) < lastupdated order by route,compartment" ;
	}
	$result = mysql_query($query) or die(mysql_error());

	$routes = array();
	if(mysql_num_rows($result)) {
		while($route = mysql_fetch_assoc($result)) {
		  $routes[] = array('route'=>$route);
		}
		$q = "update routes set lastupdated = now() where lastupdated = '0000-00-00 00:00:00'";
		$r = mysql_query($q) or die(mysql_error());
		
		header('Content-type: application/json');
		echo json_encode(array('routes'=>$routes));
	} else {
		$mess[] = array('msg'=>'No records');
		header('Content-type: application/json');
		echo json_encode(array('message'=>$mess));
	}
	

?>
