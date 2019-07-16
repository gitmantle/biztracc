<?php
session_start();
$sub_id = $_REQUEST['sid'];
$coy_id = $_REQUEST['cid'];

$dbase = 'logtracc';

date_default_timezone_set($_SESSION['s_timezone']);
$ddate = date("Y-m-d");
	
	$server = "mysql3.webhost.co.nz";
	$user = "logtracc9";
	$pwd = "dun480can";
	$connect = mysql_connect($server,$user,$pwd) or die("Check your database connection");

	mysql_select_db($dbase) or die(mysql_error());

	$query = "select uid,incterrain from incterrain order by incterrain";		
	$result = mysql_query($query) or die(mysql_error());

	$terrains = array();
	if(mysql_num_rows($result)) {
		while($terrain = mysql_fetch_assoc($result)) {
		  $terrains[] = array('terrain'=>$terrain);
		}
		
		header('Content-type: application/json');
		echo json_encode(array('terrains'=>$terrains));
	} else {
		$mess[] = array('msg'=>'No records');
		header('Content-type: application/json');
		echo json_encode(array('message'=>$mess));
	}
	

?>
