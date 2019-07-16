<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Load RUCs</title>
</head>

<body>

<?php

session_start();
$server = "mysql3.webhost.co.nz";
$user = "logtracc9";
$pwd = "dun480can";
$connect = mysql_connect($server,$user,$pwd) or die("Check your database connection - db1"." SERVER ".$server);

$sub_id = 31;
$coy_id = 10;

$dbase = 'log'.$sub_id.'_'.$coy_id;
mysql_select_db($dbase) or die(mysql_error());

$q = "select ddate,docket_no,truck,trailer,truckbranch,trailerbranch,routeid from dockets";
$r = mysql_query($q) or die(mysql_error().' '.$q);
while ($row = mysql_fetch_array($r)) {
	extract($row);
				// update ruckms with milage details for truck
					// get the relevant ruc licence number
					$qlic = "select ruclicence,regno from vehicles where vehicleno = '".$truck."'";
					$rlic = mysql_query($qlic) or die(mysql_error()." ".$qlic);
					$numrecs = mysql_num_rows($rlic);
					if ($numrecs == 1) {
						$row = mysql_fetch_array($rlic);
						extract($row);
						$rlic = $ruclicence;
						$rno = $regno;
						
						// get the private milage for this route
						$qpvt = "select sum(routes.private)*2 as dprivate from routes where uid = ".$routeid;
						$rpvt = mysql_query($qpvt) or die(mysql_error()." ".$qpvt);
						$row = mysql_fetch_array($rpvt);
						extract($row);
						$pvtkms = $dprivate;
						
						// insert record into ruckms
						$qir = "insert into ruckms (ddate,vehicle,regno,ruclicence,docket_no,routeid,private) values (";
						$qir .= "'".$ddate."',";
						$qir .= "'".$truck."',";
						$qir .= "'".$rno."',";
						$qir .= "'".$rlic."',";
						$qir .= $docket_no.",";
						$qir .= $routeid.",";
						$qir .= $pvtkms.")";
						$rir = mysql_query($qir) or die(mysql_error()." ".$qir);
					}
				
				// update ruckms with milage details for trailer
					// get the relevant ruc licence number
					$qlic = "select ruclicence,regno from vehicles where vehicleno = '".$trailer."'";
					$rlic = mysql_query($qlic) or die(mysql_error()." ".$qlic);
					$numrecs = mysql_num_rows($rlic);
					if ($numrecs == 1) {
						$row = mysql_fetch_array($rlic);
						extract($row);
						$rlic = $ruclicence;
						$rno = $regno;
						
						// get the private milage for this route
						$qpvt = "select sum(routes.private) as dprivate from routes where uid = ".$routeid;
						$rpvt = mysql_query($qpvt) or die(mysql_error()." ".$qpvt);
						$row = mysql_fetch_array($rpvt);
						extract($row);
						$pvtkms = $dprivate;
						
						// insert record into ruckms
						$qir = "insert into ruckms (ddate,vehicle,regno,ruclicence,docket_no,routeid,private) values (";
						$qir .= "'".$ddate."',";
						$qir .= "'".$trailer."',";
						$qir .= "'".$rno."',";
						$qir .= "'".$rlic."',";
						$qir .= $docket_no.",";
						$qir .= $routeid.",";
						$qir .= $pvtkms.")";
						$rir = mysql_query($qir) or die(mysql_error()." ".$qir);
					}
}


?>


</body>
</html>