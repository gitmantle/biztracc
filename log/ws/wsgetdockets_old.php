<?php

session_start();
$sub_id = $_REQUEST['sid'];
$coy_id = $_REQUEST['cid'];


$json = file_get_contents('php://input');

$File = "logdockets.txt"; 
 $Handle = fopen($File, 'a');
 $Data.=$json;
 fwrite($Handle, $Data);  
 fclose($Handle); 

$input = json_decode($json,true);

date_default_timezone_set($_SESSION['s_timezone']);
$ddate = date("Y-m-d");
	
$server = "mysql3.webhost.co.nz";
$user = "logtracc9";
$pwd = "dun480can";
$connect = mysql_connect($server,$user,$pwd) or die("Check your database connection");

$dbase = 'logtracc';
mysql_select_db($dbase) or die(mysql_error());
$q = "select subcontract_to,coysubid from companies where coyid = ".$coy_id;
$r = mysql_query($q) or die (mysql_error());
$row = mysql_fetch_array($r);
extract($row);
$s_contract = $subcontract_to;
$c_subid = $coysubid;

$dbase = 'log'.$sub_id.'_'.$coy_id;
mysql_select_db($dbase) or die(mysql_error());

if (is_array($input)) {
	foreach ($input as $dvalue) {
		foreach($dvalue as $tvalue) {
			foreach($tvalue as $value) {
				$date = $value['date'];
				$docketno = $value['docketno'];	
				$std = $value['stand'];
				$stand = (int) $std;
				$length = $value['length'];
				$peel = $value['peel'];
				$saw = $value['saw'];
				$pulp = $value['pulp'];
				$other = $value['other'];
				$pieces = $value['pieces'];
				$species = $value['species'];
				$harvest = $value['harvest'];	
				$customer = $value['customer'];
				$destination = $value['destination'];
				$cont = $value['contractor'];
				$cr = $value['crew'];
				$cartage = $value['cartage'];
				$truck = $value['truck'];
				$trailer = $value['trailer'];
				$truckbranch = $value['truckbranch'];
				$trailerbranch = $value['trailerbranch'];
				$gross = $value['gross'];
				$tare = $value['tare'];	
				$net = $value['net'];
				$debtor = $value['debtor'];
				$operator = $value['operator'];
				$routid = $value['routeid'];
				$latstart = $value['latstart'];
				$latend = $value['latend'];
				$longstart = $value['longstart'];
				$longend = $value['longend'];


				$qd = "SELECT docket_no as dno FROM dockets WHERE docket_no = ".$docketno;
				$result = mysql_query($qd) or die (mysql_error().' '.$qd);
				$numrows = mysql_num_rows($result);
				if ($numrows > 0)	{
					// not added
				} else {


				// calculate the $ amount
				$qr = "select rate,compartment,forest from routes where uid = ".$routid;
				$rr = mysql_query($qr) or die (mysql_error());
				$row = mysql_fetch_array($rr);
				extract($row);
				$amt = $net * $rate;
				$cpt = $compartment;
				$frt = $forest;
				
				// get contractor and crew
				$qr = "select contractor,crew from contractors where uid = ".$cont;
				$rr = mysql_query($qr) or die (mysql_error());
				$row = mysql_fetch_array($rr);
				extract($row);
				$ctor = $contractor;
				$crw = $crew;
				
				if (trim($truck) == '') {
					$tkbr = '';
				} else {
					$tkbr = $truckbranch;
				}
				if (trim($trailer) == '') {
					$tlbr = '';
				} else {
					$tlbr = $trailerbranch;
				}
				
				
				
				$q = "insert into dockets (ddate,docket_no,forest,cpt,skid,length,peel,saw,pulp,other,pieces,species,harvest,customer,destination,contractor,crew,cartage,truck,trailer,truckbranch,trailerbranch,gross,tare,net,routeid,debtor,operator,latstart,latend,longstart,longend,amount) values (";
																																																																											                $q .= "'".$date."',";
				$q .= $docketno.",";	
				$q .= "'".$frt."',";
				$q .= "'".$cpt."',";
				$q .= "'".$stand."',";
				$q .= $length.",";
				$q .= "'".$peel."',";
				$q .= "'".$saw."',";
				$q .= "'".$pulp."',";
				$q .= "'".$other."',";
				$q .= "'".$pieces."',";
				$q .= "'".$species."',";
				$q .= "'".$harvest."',";
				$q .= "'".$customer."',";
				$q .= "'".$destination."',";
				$q .= "'".$ctor."',";
				$q .= "'".$crw."',";
				$q .= "'".$cartage."',";
				$q .= "'".$truck."',";
				$q .= "'".$trailer."',";
				$q .= "'".$tkbr."',";
				$q .= "'".$tlbr."',";
				$q .= $gross.",";
				$q .= $tare.",";	
				$q .= $net.",";
				$q .= $routid.",";
				$q .= "'".$debtor."',";
				$q .= $operator.",";
				$q .= "'".$latstart."',";
				$q .= "'".$latend."',";
				$q .= "'".$longstart."',";
				$q .= "'".$longend."',";
				$q .= $amt.")";
	
				$r = mysql_query($q) or die(mysql_error()." ".$q);
				
				
				if ($s_contract > 0) {
					$dbase = 'log'.$c_subid.'_'.$s_contract;
					mysql_select_db($dbase) or die(mysql_error());

					// calculate the $ amount
					$qr = "select rate,compartment,forest from routes where uid = ".$routid;
					$rr = mysql_query($qr) or die (mysql_error());
					$row = mysql_fetch_array($rr);
					extract($row);
					$amt = $net * $rate;
					$cpt = $compartment;
					$frt = $forest;

					$q = "insert into dockets (ddate,docket_no,forest,cpt,skid,length,peel,saw,pulp,other,pieces,species,harvest,customer,destination,contractor,crew,cartage,truck,trailer,truckbranch,trailerbranch,gross,tare,net,routeid,debtor,operator,latstart,latend,longstart,longend,amount) values (";
																																																																																					                    $q .= "'".$date."',";
					$q .= $docketno.",";	
					$q .= "'".$frt."',";
					$q .= "'".$cpt."',";
					$q .= "'".$stand."',";
					$q .= $length.",";
					$q .= "'".$peel."',";
					$q .= "'".$saw."',";
					$q .= "'".$pulp."',";
					$q .= "'".$other."',";
					$q .= "'".$pieces."',";
					$q .= "'".$species."',";
					$q .= "'".$harvest."',";
					$q .= "'".$customer."',";
					$q .= "'".$destination."',";
					$q .= "'".$ctor."',";
					$q .= "'".$crw."',";
					$q .= "'".$cartage."',";
					$q .= "'".$truck."',";
					$q .= "'".$trailer."',";
					$q .= "'".$tkbr."',";
					$q .= "'".$tlbr."',";
					$q .= $gross.",";
					$q .= $tare.",";	
					$q .= $net.",";
					$q .= $routid.",";
					$q .= "'".$debtor."',";
					$q .= $operator.",";
					$q .= "'".$latstart."',";
					$q .= "'".$latend."',";
					$q .= "'".$longstart."',";
					$q .= "'".$longend."',";
					$q .= $amt.")";
		
					$r = mysql_query($q) or die(mysql_error()." ".$q);
				
					$dbase = 'log'.$sub_id.'_'.$coy_id;
					mysql_select_db($dbase) or die(mysql_error());
				}
				
				// update ruckms with milage details for truck
				$qtruck = "select max(hubodometer) as kms from driverlog where truckno = '".$truck."'";
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
				}
				
				// update ruckms with milage details for trailer
				$qtrailer = "select max(hubodometer) as kms from driverlog where truckno = '".$trailer."'";
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
			}
			}
		}
	echo 2;
	}
} else {
	
echo 5;

}



?>

