<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Rebuild ruckms</title>
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
	
$server = 'localhost';
//$server = "mysql3.webhost.co.nz";
$user = "logtracc9";
$pwd = "dun480can";
$connect = mysql_connect($server,$user,$pwd) or die("Check your database connection");

mysql_select_db($dbase) or die(mysql_error());


$dbase = 'log'.$sub_id.'_'.$coy_id;
mysql_select_db($dbase) or die(mysql_error());

$qdr = "delete from ruckms";
$rdr = mysql_query($qdr) or die (mysql_error());

$qd = "select * from dockets";
$rd = mysql_query($qd) or die (mysql_error());
while ($row = mysql_fetch_array($rd)) {
	extract($row);
	$date = $ddate;
	$docketno = $docket_no;	
	$truck = $truck;
	$trailer = $trailer;
	$truckbranch = $truckbranch;
	$trailerbranch = $trailerbranch;
	$routid = $routeid;
	
					
					// update ruckms with milage details for truck
					$ok = 'Y';
					// get the relevant ruc licence number
					$qlic = "select ruclicence from rucs where date_issued >= '".$date."' and date_issued <= '".$date."' and vehicleno = '".$truck."'";
					$rlic = mysql_query($qlic) or die(mysql_error()." ".$qlic);
					$numrecs = mysql_num_rows($rlic);
					if ($numrecs == 1) {
						$row = mysql_fetch_array($rlic);
						extract($row);
						
					} else {
						$qrt = "select ruclicence from rucs where uid=(select max(uid) FROM rucs where vehicleno = '".$truck."')";	
						$rrt = mysql_query($qrt) or die(mysql_error()." ".$qrt);
						$numrows = mysql_num_rows($rrt);
						if ($numrows > 0) {
							$row = mysql_fetch_array($rrt);
							$ok = 'Y';
						} else {
							$ok = 'N';
						}
						extract($row);
					}
					if ($ok == 'Y') {
					$rlic = $ruclicence;
					
					// get reg number of vehicle
					$qreg = "select regno from vehicles where vehicleno = '".$truck."'";
					$rreg = mysql_query($qreg) or die(mysql_error()." ".$qreg);
					$row = mysql_fetch_array($rreg);
					extract($row);
					$rno = $regno;
					
							
					// get the private milage for this route
					$qpvt = "select sum(routes.private)*2 as dprivate from routes where uid = ".$routid;
					$rpvt = mysql_query($qpvt) or die(mysql_error()." ".$qpvt);
					$row = mysql_fetch_array($rpvt);
					extract($row);
					$pvtkms = $dprivate;
							
					// insert record into ruckms
					$qir = "insert into ruckms (ddate,vehicle,regno,ruclicence,docket_no,routeid,private) values (";
					$qir .= "'".$date."',";
					$qir .= "'".$truck."',";
					$qir .= "'".$rno."',";
					$qir .= "'".$rlic."',";
					$qir .= $docketno.",";
					$qir .= $routid.",";
					$qir .= $pvtkms.")";
					$rir = mysql_query($qir) or die(mysql_error()." ".$qir);
					}
					
					// update ruckms with milage details for trailer
					$ok = 'Y';
					// get the relevant ruc licence number
					$qlic = "select ruclicence from rucs where date_issued >= '".$date."' and date_issued <= '".$date."' and vehicleno = '".$trailer."'";
					$rlic = mysql_query($qlic) or die(mysql_error()." ".$qlic);
					$numrecs = mysql_num_rows($rlic);
					if ($numrecs == 1) {
						$row = mysql_fetch_array($rlic);
						extract($row);
						
					} else {
						$qrt = "select ruclicence from rucs where uid=(select max(uid) FROM rucs where vehicleno = '".$trailer."')";	
						$rrt = mysql_query($qrt) or die(mysql_error()." ".$qrt);
						$numrows = mysql_num_rows($rrt);
						if ($numrows > 0) {
							$row = mysql_fetch_array($rrt);
							$ok = 'Y';
						} else {
							$ok = 'N';
						}
						extract($row);
					}
					if ($ok == 'Y') {
					$rlic = $ruclicence;
					
					// get reg number of vehicle
					$qreg = "select regno from vehicles where vehicleno = '".$trailer."'";
					$rreg = mysql_query($qreg) or die(mysql_error()." ".$qreg);
					$row = mysql_fetch_array($rreg);
					extract($row);
					$rno = $regno;
					
							
					// get the private milage for this route
					$qpvt = "select sum(routes.private) as dprivate from routes where uid = ".$routid;
					$rpvt = mysql_query($qpvt) or die(mysql_error()." ".$qpvt);
					$row = mysql_fetch_array($rpvt);
					extract($row);
					$pvtkms = $dprivate;
							
					// insert record into ruckms
					$qir = "insert into ruckms (ddate,vehicle,regno,ruclicence,docket_no,routeid,private) values (";
					$qir .= "'".$date."',";
					$qir .= "'".$trailer."',";
					$qir .= "'".$rno."',";
					$qir .= "'".$rlic."',";
					$qir .= $docketno.",";
					$qir .= $routid.",";
					$qir .= $pvtkms.")";
					$rir = mysql_query($qir) or die(mysql_error()." ".$qir);
					}
}


?>









</body>
</html>