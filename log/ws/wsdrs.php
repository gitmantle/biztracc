<?php
session_start();
$sub_id = $_REQUEST['sid'];
$coy_id = $_REQUEST['cid'];
$force = $_REQUEST['force'];
$dbase = 'sub'.$sub_id;

date_default_timezone_set($_SESSION['s_timezone']);
$ddate = date("Y-m-d");
	
	$server = "mysql3.webhost.co.nz";
	$user = "logtracc9";
	$pwd = "dun480can";
	$connect = mysql_connect($server,$user,$pwd) or die("Check your database connection");

	mysql_select_db($dbase) or die(mysql_error());

	if ($force == 'Y') {
		$query = "select concat(members.lastname,' ',members.firstname,' ',client_company_xref.subname) as account,client_company_xref.drno as accountno,client_company_xref.drsub as sub from members left join client_company_xref on members.member_id = client_company_xref.client_id where client_company_xref.company_id = ".$coy_id." and client_company_xref.drno != 0 and client_company_xref.blocked = 'No' ";		
	} else {
		$query = "select concat(members.lastname,' ',members.firstname,' ',client_company_xref.subname) as account,client_company_xref.drno as accountno,client_company_xref.drsub as sub from members left join client_company_xref on members.member_id = client_company_xref.client_id where client_company_xref.company_id = ".$coy_id." and client_company_xref.drno != 0 and client_company_xref.blocked = 'No'  and (lastupdated = '0000-00-00 00:00:00' or (now() - interval 30 day) < lastupdated)" ;
	}
	$result = mysql_query($query) or die(mysql_error());

	$customers = array();
	if(mysql_num_rows($result)) {
		while($customer = mysql_fetch_assoc($result)) {
		  $customers[] = array('customer'=>$customer);
		}
		$q = "update client_company_xref set lastupdated = now() where lastupdated = '0000-00-00 00:00:00' and client_company_xref.company_id = ".$coy_id." and client_company_xref.drno != ''";
		$r = mysql_query($q) or die(mysql_error());
		
		header('Content-type: application/json');
		echo json_encode(array('customers'=>$customers));
	} else {
		$mess[] = array('msg'=>'No records');
		header('Content-type: application/json');
		echo json_encode(array('message'=>$mess));
	}

?>
