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

	if ($force == 'Y') {
		$query = "select itemid,catid,itemcode,item from stkmast where groupid = 1";		
	} else {
		$query = "select itemid,catid,itemcode,item from stkmast where groupid = 1 and (lastupdated = '0000-00-00 00:00:00' or (now() - interval 30 day) < lastupdated)" ;
	}
	$result = mysql_query($query) or die(mysql_error());

	$items = array();
	if(mysql_num_rows($result)) {
		while($item = mysql_fetch_assoc($result)) {
		  $items[] = array('item'=>$item);
		}

		$q = "update stkmast set lastupdated = now() where lastupdated = '0000-00-00 00:00:00'";
		$r = mysql_query($q) or die(mysql_error());
		
		header('Content-type: application/json');
		echo json_encode(array('items'=>$items));
	} else {
		$mess[] = array('msg'=>'No records');
		header('Content-type: application/json');
		echo json_encode(array('message'=>$mess));
	}

?>
