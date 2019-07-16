<?php
session_start();
$sub_id = $_REQUEST['sid'];
$coy_id = $_REQUEST['cid'];
$dbase = 'fin'.$sub_id.'_'.$coy_id;

date_default_timezone_set($_SESSION['s_timezone']);
$ddate = date("Y-m-d");
	
	$server = "mysql3.webhost.co.nz";
	$user = "logtracc9";
	$pwd = "dun480can";
	$connect = mysql_connect($server,$user,$pwd) or die("Check your database connection");

	mysql_select_db($dbase) or die(mysql_error());

	$query = "select taxpcent as gstpcent,5 as oninterval,60 as offinterval from taxtypes where uid = 1";		
	$result = mysql_query($query) or die(mysql_error());

	$gst = array();
	if(mysql_num_rows($result)) {
		while($gstpcent = mysql_fetch_assoc($result)) {
		  $gst[] = array('gstpcent'=>$gstpcent);
		}
		
		header('Content-type: application/json');
		echo json_encode(array('gst'=>$gst));
	} else {
		$mess[] = array('msg'=>'No records');
		header('Content-type: application/json');
		echo json_encode(array('message'=>$mess));
	}
	

?>
