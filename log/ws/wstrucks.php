<?php
session_start();
$sub_id = $_REQUEST['sid'];
$coy_id = $_REQUEST['cid'];
$force = $_REQUEST['force'];
$dbase = 'fin'.$sub_id.'_'.$coy_id;

date_default_timezone_set($_SESSION['s_timezone']);
$ddate = date("Y-m-d");
	
	$server = "mysql3.webhost.co.nz";
	$user = "logtracc9";
	$pwd = "dun480can";
	$connect = mysql_connect($server,$user,$pwd) or die("Check your database connection");

	mysql_select_db($dbase) or die(mysql_error());

	$query = "select branch,branchname as truckname from branch where branchname like 'Truck%'";		
	$result = mysql_query($query) or die(mysql_error());

	$trucks = array();
	if(mysql_num_rows($result)) {
		while($truck = mysql_fetch_assoc($result)) {
		  $trucks[] = array('truck'=>$truck);
		}
		
		header('Content-type: application/json');
		echo json_encode(array('trucks'=>$trucks));
	} else {
		$mess[] = array('msg'=>'No records');
		header('Content-type: application/json');
		echo json_encode(array('message'=>$mess));
	}
	

?>