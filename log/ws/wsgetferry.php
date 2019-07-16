<?php

session_start();
$sub_id = $_REQUEST['sid'];
$coy_id = $_REQUEST['cid'];

$json = file_get_contents('php://input');

$File = "logferry.txt"; 
 $Handle = fopen($File, 'a');
 $Data.=$json;
 fwrite($Handle, $Data);  
 fclose($Handle); 

$dbase = 'log'.$sub_id.'_'.$coy_id;

date_default_timezone_set($_SESSION['s_timezone']);
$ddate = date("Y-m-d");
	
$server = "mysql3.webhost.co.nz";
$user = "logtracc9";
$pwd = "dun480can";
$connect = mysql_connect($server,$user,$pwd) or die("Check your database connection");

mysql_select_db($dbase) or die(mysql_error());

$input = json_decode($json,true);

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
				$routid = $value["routeid"];
				$adhoc = $value["adhoc"];
				$public = $value["public"];
				$private = $value["private"];
				
				if ($routid == 1) {
					$rt = $adhoc;
				} else {
					$qr = "select route,public,private from routes where uid = ".$routid;
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
				$q .= $routid.",";
				$q .= $driverid.",";
				$q .= $public.",";
				$q .= $private.")";	
				
				$r = mysql_query($q) or die(mysql_error()." ".$q);
				
				$ferryid = mysql_insert_id();
				
				// update ruckms with milage details for truck
				$qtruck = "select odometer as kms from vehicles where vehicleno = '".$truckno."'";
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
				$qtrailer = "select odometer as kms from vehicles where vehicleno = '".$trailerno."'";
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

