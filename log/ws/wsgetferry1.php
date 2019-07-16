<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>

<?php

session_start();
/*
$sub_id = $_REQUEST['sid'];
$coy_id = $_REQUEST['cid'];
*/
$sub_id = 31;
$coy_id = 10;


$dbase = 'log'.$sub_id.'_'.$coy_id;

date_default_timezone_set($_SESSION['s_timezone']);
$ddate = date("Y-m-d");

//$server = "mysql3.webhost.co.nz";
$server = 'localhost';
$user = "logtracc9";
$pwd = "dun480can";
$connect = mysql_connect($server,$user,$pwd) or die("Check your database connection");

mysql_select_db($dbase) or die(mysql_error());

$json = '{"ferrys":[{"ferry":{"driverid":"24","truckno":"Truck AB 50","trailerno":"Trailer AB 51","truckbranch":"0002","trailerbranch":"0005","routeid":"21","adhoc":"","public":"","private":"","uid":"2","date":"2013-04-30"}},{"ferry":{"driverid":"24","truckno":"Truck AB 50","trailerno":"","truckbranch":"0002","trailerbranch":"","routeid":"1","adhoc":"another ad hoc test","public":"123","private":"24","uid":"3","date":"2013-04-30"}}]}';


$input = json_decode($json,true);

print_r($input);

if (is_array($input)) {

	foreach ($input as $dvalue) {
		//print_r($dvalue);
		foreach($dvalue as $tvalue) {
			//print_r($tvalue);
			foreach($tvalue as $value) {
				//print_r($value);
				$date = $value["date"];
				$truckno = $value["truckno"];
				$trailerno = $value["trailerno"];
				$truckbranch = $value["truckbranch"];
				$trailerbranch = $value["trailerbranch"];
				$driverid = $value["driverid"];
				$routeid = $value["routeid"];
				$adhoc = $value["adhoc"];
				$public = $value["public"];
				$private = $value["private"];
				
				if ($routeid == 1) {
					$rt = $adhoc;
				} else {
					$qr = "select route,public,private from routes where uid = ".$routeid;
					$rr = mysql_query($qr) or die(mysql_error()." ".$qr);
					$row = mysql_fetch_array($rr);
					extract($row);
					$rt = $route;
				}
				
				$q = "insert into ferry (date,truck,trailer,truckbranch,trailerbranch,route,routeid,operator,public,private) values (";
				$q .= "'".$date."',";
				$q .= "'".$truckno."',";
				$q .= "'".$trailerno."',";
				$q .= "'".$truckbranch."',";
				$q .= "'".$trailerbranch."',";
				$q .= "'".$rt."',";
				$q .= $routeid.",";
				$q .= $driverid.",";
				$q .= $public.",";
				$q .= $private.")";	
				
				$r = mysql_query($q) or die(mysql_error()." ".$q);
				
				$ferryid = mysql_insert_id();
				
				// update ruckms with milage details for truck
				$qtruck = "select max(hubodometer) as kms from driverlog where truckno = '".$truckno."'";
				$rtruck = mysql_query($qtruck) or die(mysql_error()." ".$qtruck);
				$row = mysql_fetch_array($rtruck);
				extract($row);
				if ($kms != NULL) {
					// get the relevant ruc licence number
					$qlic = "select ruclicence,regno from vehicles where vehicleno = '".$truck."' and fromkms < ".$kms." and ruckms > ".$kms;
					$rlic = mysql_query($qlic) or die(mysql_error()." ".$qlic);
					$numrecs = mysql_num_rows($rlic);
					if ($numrecs == 1) {
						$row = mysql_fetch_array($rlic);
						extract($row);
						$rlic = $ruclicence;
						$rno = $regno;
						
						// get the private milage for this route
						$qpvt = "select sum(routes.private)*2 as dprivate from routes where uid = ".$routid;
						$rpvt = mysql_query($qpvt) or die(mysql_error()." ".$qpvt);
						$row = mysql_fetch_array($rpvt);
						extract($row);
						$pvtkms = $dprivate;
						
						// insert record into ruckms
						$qir = "insert into ruckms (ddate,vehicle,regno,ruclicence,ferry_id,routeid,private) values (";
						$qir .= "'".$date."',";
						$qir .= "'".$truck."',";
						$qir .= "'".$rno."',";
						$qir .= "'".$rlic."',";
						$qir .= $ferryid.",";
						$qir .= $routid.",";
						$qir .= $pvtkms.")";
						$rir = mysql_query($qir) or die(mysql_error()." ".$qir);
					}
				}
				
				// update ruckms with milage details for trailer
				$qtrailer = "select max(hubodometer) as kms from driverlog where truckno = '".$trailerno."'";
				$rtrailer = mysql_query($qtrailer) or die(mysql_error()." ".$qtrailer);
				$row = mysql_fetch_array($rtrailer);
				extract($row);
				if ($kms != NULL) {
					// get the relevant ruc licence number
					$qlic = "select ruclicence,regno from vehicles where vehicleno = '".$trailer."' and fromkms < ".$kms." and ruckms > ".$kms;
					$rlic = mysql_query($qlic) or die(mysql_error()." ".$qlic);
					$numrecs = mysql_num_rows($rlic);
					if ($numrecs == 1) {
						$row = mysql_fetch_array($rlic);
						extract($row);
						$rlic = $ruclicence;
						$rno = $regno;
						
						// get the private milage for this route
						$qpvt = "select sum(routes.private) as dprivate from routes where uid = ".$routid;
						$rpvt = mysql_query($qpvt) or die(mysql_error()." ".$qpvt);
						$row = mysql_fetch_array($rpvt);
						extract($row);
						$pvtkms = $dprivate;
						
						// insert record into ruckms
						$qir = "insert into ruckms (ddate,vehicle,regno,ruclicence,ferry_id,routeid,private) values (";
						$qir .= "'".$date."',";
						$qir .= "'".$trailer."',";
						$qir .= "'".$rno."',";
						$qir .= "'".$rlic."',";
						$qir .= $ferryid.",";
						$qir .= $routid.",";
						$qir .= $pvtkms.")";
						$rir = mysql_query($qir) or die(mysql_error()." ".$qir);
					}
				}
				
			}
		}
		echo 2;
	}
} else {
	
echo 5;

}



?>









</body>
</html>

