<?php
session_start();
$sub_id = $_REQUEST['sid'];
$coy_id = $_REQUEST['cid'];
$truck = $_REQUEST['truck'];
$dbase = 'log'.$sub_id.'_'.$coy_id;

date_default_timezone_set($_SESSION['s_timezone']);
$ddate = date("Y-m-d");
	
	$server = "mysql3.webhost.co.nz";
	$user = "logtracc9";
	$pwd = "dun480can";
	$connect = mysql_connect($server,$user,$pwd) or die("Check your database connection");

	mysql_select_db($dbase) or die(mysql_error());

	$query = "select ruckms from vehicles where cost_centre = '".$truck."'";		
	$result = mysql_query($query) or die(mysql_error());

	$ruc = array();
	if(mysql_num_rows($result)) {
		while($ruckms = mysql_fetch_assoc($result)) {
		  $ruc[] = array('ruckms'=>$ruckms);
		}
		
		header('Content-type: application/json');
		echo json_encode(array('ruc'=>$ruc));
	} else {
		$mess[] = array('msg'=>'No records');
		header('Content-type: application/json');
		echo json_encode(array('message'=>$mess));
	}
	

?>
