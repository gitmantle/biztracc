<?php

session_start();
$sub_id = $_REQUEST['sid'];
$coy_id = $_REQUEST['cid'];

$json = file_get_contents('php://input');

$File = "logincidents.txt"; 
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

	foreach ($input as $avalue) {
		//print_r($avalue);
	
		foreach($avalue as $bvalue) {
			//print_r($bvalue);
	
			foreach($bvalue as $cvalue) {
				//print_r($cvalue);
				foreach($cvalue as $dvalue) {
					//print_r($dvalue);
					
					$akey = key($dvalue);
					if ($akey == "header") {
						foreach($dvalue as $value) {
							//print_r($value);
							
							$subid = $value["subid"];
							$coyid = $value["coyid"];
							$truck = $value["truckno"];
							$truckbranch = $value["truckbranch"];
							$trailer = $value["trailerno"];
							$trailerbranch = $value["trailerbranch"];
							$driverid = $value["driverid"];
							$date = $value["date"];
							$time = $value["time_incident"];
							$lat = $value["latitude"];
							$long = $value["longitude"];
							$lti = $value["LTI"];
							$cl = explode('~',$value["client"]);
							$ac = $cl[0];
							$sb = $cl[1];
							$subcontractor = $value["sub_contractor"];
							$crew = $value["crew"];
							$incidenttype = $value["incident_type"];
							$road = $value["road"];
							$route = $value["route"];
							$detail = $value["details"];
							$harm = $value["harm_people"];
							$damage = $value["damage_property"];
							$occur = $value["reoccur"];
							$terrain = $value["terrain"];
							$weather = $value["weather"];
							$temperature = $value["temperature"];
							$wind = $value["wind"];
							$basic1 = $value["basic1"];
							$basic2 = $value["basic2"];
							$basic3 = $value["basic3"];
							$basic4 = $value["basic4"];
							$basic5 = $value["basic5"];
							$basic6 = $value["basic6"];
							$basic7 = $value["basic7"];
							$basic8 = $value["basic8"];
							$immediate = $value["immediate"];
							$hazards = $value["hazards"];
							$incidentid = $value["incident_id"];
							
							$hdate = date("Y-m-d");

							$qd = "SELECT uid as id FROM incidents WHERE tabletid = '".$incidentid."'";
							$result = mysql_query($qd) or die (mysql_error().' '.$qd);
							$numrows = mysql_num_rows($result);
							if ($numrows > 0)	{
								// not added
								$new = 'N';
							} else {
								$new = 'Y';
								
								$moduledb = 'sub'.$sub_id;

								mysql_select_db($moduledb) or die(mysql_error());								
								// get client name
								$qc = "select concat(members.firstname,' ',members.lastname) as fname, client_company_xref.hs_contractor from members, client_company_xref where members.member_id = client_company_xref.client_id and client_company_xref.drno = ".$ac." and client_company_xref.drsub = ".$sb;
								$rc = mysql_query($qc) or die (mysql_error().' '.$qc);
								$row = mysql_fetch_array($rc);
								extract($row);
								$clientname = $fname;
								$hsc = $hs_contractor;
								
								$moduledb = 'logtracc';
								mysql_select_db($moduledb) or die(mysql_error());
								// get driver name
								$qdr = "select concat(users.ufname,' ',users.ulname) as uname from users where uid = ".$driverid;
								$rdr = mysql_query($qdr) or die (mysql_error().' '.$qdr);
								$row = mysql_fetch_array($rdr);
								extract($row);
								$drivername = $uname;
								
								$moduledb = 'log'.$sub_id.'_'.$coy_id;
								mysql_select_db($moduledb) or die(mysql_error());
								
								$qf = "select forest,compartment from routes where uid = ".$route;
								$rf = mysql_query($qf);
								$row = mysql_fetch_array($rf) or die(mysql_error());
								extract($row);
								$frt = $forest;
								$cpt = $compartment;
								
								
								$q = "insert into incidents (date_entered,date_incident,time_incident,latitude,longitude,LTI,client,accountno,sub,hs_contractor,sub_contractor,crew,compiler,incident_type,route_id,forest,compartment,road,details,truckno,trailerno,harm_people,damage_property,reocurr,terrain,weather,temperature,wind,basic1,basic2,basic3,basic4,basic5,basic6,basic7,basic8,immediate,hazards,tabletid) values (";
								$q .= '"'.$hdate.'",';
								$q .= '"'.$date.'",';
								$q .= '"'.$time.'",';
								$q .= '"'.$lat.'",';
								$q .= '"'.$long.'",';
								$q .= '"'.$lti.'",';
								$q .= '"'.$clientname.'",';
								$q .= $ac.',';
								$q .= $sb.',';
								$q .= $hsc.',';
								$q .= '"'.$subcontractor.'",';
								$q .= '"'.$crew.'",';
								$q .= '"'.$drivername.'",';
								$q .= '"'.$incidenttype.'",';
								$q .= $route.',';
								$q .= '"'.$forest.'",';
								$q .= '"'.$cpt.'",';
								$q .= '"'.$road.'",';
								$q .= '"'.$detail.'",';
								$q .= '"'.$truck.'",';
								$q .= '"'.$trailer.'",';
								$q .= '"'.$harm.'",';
								$q .= '"'.$damage.'",';
								$q .= '"'.$occur.'",';
								$q .= '"'.$terrain.'",';
								$q .= '"'.$weather.'",';
								$q .= '"'.$temperature.'",';
								$q .= '"'.$wind.'",';
								$q .= '"'.$basic1.'",';
								$q .= '"'.$basic2.'",';
								$q .= '"'.$basic3.'",';
								$q .= '"'.$basic4.'",';
								$q .= '"'.$basic5.'",';
								$q .= '"'.$basic6.'",';
								$q .= '"'.$basic7.'",';
								$q .= '"'.$basic8.'",';
								$q .= '"'.$immediate.'",';
								$q .= '"'.$hazards.'",';
								$q .= '"'.$incidentid.'")';
							
								$r = mysql_query($q) or die(mysql_error().$q);
								$incid = mysql_insert_id();
								
							}
						}
									
					} elseif ($akey == "person" && $new == 'Y') {
						foreach($dvalue as $value) {
							//print_r($value);
			
							$name = $value['name'];
							$shift = $value['shift'];
							$involve = $value['involvement'];
							$stime = $value['start'];
							$operation = $value['operation'];
							$quals = $value['qualifications'];
							if ($value['exp_industry_y'] == "") {
								$indy = 0;
							}else {
								$indy = $value['exp_industry_y'];
							}
							if ($value['exp_industry_m'] == "") {
								$indm = 0;
							}else {
								$indm = $value['exp_industry_m'];
							}
							if ($value['exp_industry_d'] == "") {
								$indd = 0;
							}else {
								$indd = $value['exp_industry_d'];
							}
							if ($value['exp_job_y'] == "") {
								$joby = 0;
							}else {
								$joby = $value['exp_job_y'];
							}
							if ($value['exp_job_m'] == "") {
								$jobm = 0;
							}else {
								$jobm = $value['exp_job_m'];
							}
							if ($value['exp_job_d'] == "") {
								$jobd = 0;
							}else {
								$jobd = $value['exp_job_d'];
							}
												
					
							$q = "insert into incpeople (incident_id,name,involvment,shift,starttime,operation,qualifications,indexpy,indexpm,indexpd,jobexpy,jobexpm,jobexpd) values (";
							$q .= $incid.',';
							$q .= '"'.$name.'",';
							$q .= '"'.$involve.'",';
							$q .= '"'.$shift.'",';
							$q .= '"'.$stime.'",';
							$q .= '"'.$operation.'",';
							$q .= '"'.$quals.'",';
							$q .= $indy.',';
							$q .= $indm.',';
							$q .= $indd.',';
							$q .= $joby.',';
							$q .= $jobm.',';
							$q .= $jobd.')';
					
							$r = mysql_query($q) or die(mysql_error().$q);			
			
						}
									
					} elseif ($akey == "injured" && $new == 'Y') {
						foreach($dvalue as $value) {
							//print_r($value);
							
								
							$name = $value['name'];
							$injury1 = $value['injury1'];
							$body1 = $value['bodypart1'];
							$injury2 = $value['injury2'];
							$body2 = $value['bodypart2'];
							$injury3 = $value['injury3'];
							$body3 = $value['bodypart3'];
							$injury4 = $value['injury4'];
							$body4 = $value['bodypart4'];
							$injury5 = $value['injury5'];
							$body5 = $value['bodypart5'];
							$injury6 = $value['injury6'];
							$body6 = $value['bodypart6'];
							$treatment = $value['treatment'];
							$severity = $value['severity'];
							$legal = $value['legal'];
							if ($value['days_lost'] == "") {
								$dayslost = 0;
							}else {
								$dayslost = $value['days_lost'];
							}
					
							$q = "insert into incinjuries (incident_id,name,injury1,body1,injury2,body2,injury3,body3,injury4,body4,injury5,body5,injury6,body6,treatment,severity,legal,dayslost) values (";
							$q .= $incid.',';
							$q .= '"'.$name.'",';
							$q .= '"'.$injury1.'",';
							$q .= '"'.$body1.'",';
							$q .= '"'.$injury2.'",';
							$q .= '"'.$body2.'",';
							$q .= '"'.$injury3.'",';
							$q .= '"'.$body3.'",';
							$q .= '"'.$injury4.'",';
							$q .= '"'.$body4.'",';
							$q .= '"'.$injury5.'",';
							$q .= '"'.$body5.'",';
							$q .= '"'.$injury6.'",';
							$q .= '"'.$body6.'",';
							$q .= '"'.$treatment.'",';
							$q .= '"'.$severity.'",';
							$q .= '"'.$legal.'",';
							$q .= $dayslost.')';
					
							$r = mysql_query($q) or die(mysql_error().$q);
			
						}
			
					} elseif ($akey == "damage" && $new == 'Y') {
						foreach($dvalue as $value) {
							//print_r($value);
			
							$property = $value['property'];
							$damage = $value['damage'];
					
							$q = "insert into incdamage (incident_id,property,damage) values (";
							$q .= $incid.',';
							$q .= '"'.$property.'",';
							$q .= '"'.$damage.'")';
					
							$r = mysql_query($q) or die(mysql_error().$q);
			
						}

					} elseif ($akey == "pictures" && $new == 'Y') {
						foreach($dvalue as $value) {
							//print_r($value);
							
							foreach($value as $k=>$v){
								
								$q = "insert into incpictures (incident_id,picture) values (";
								$q .= $incid.',';
								$q .= '"'.$v.'")';
					
								$r = mysql_query($q) or die(mysql_error().$q);
								
							}
			
						}
					}
		

				}
			}
		}
	}
	
				// send email to administator
				
						$dbase = 'logtracc';
						mysql_select_db($dbase) or die(mysql_error());
						
						$q = "select notifyincident from users where sub_id = ".$sub_id." and notifyincident != ''";
						$r = mysql_query($q) or die (mysql_error().' '.$q);
						$numrows = mysql_num_rows($r);
						if ($numrows == 1) {
							$row = mysql_fetch_array($r);
							extract($row);
							$email = $notifyincident;
						} else {
							$email = "";
						}
						
						
						$q = "select coyname from companies where coyid = ".$coy_id;
						$r = mysql_query($q) or die (mysql_error().' '.$q);
						$row = mysql_fetch_array($r);
						extract($row);
						
						$email_from = 'admin@logtracc.co.nz';
						$email_to = $email;
						$coyid = $coy_id;	
						$client = "Administrator";

								
						$ok = 'Y';
						if (trim($email_from) == "") {
							$ok = 'N';
						}
						if (trim($email_to) == "") {
							$ok = 'N';
						}
						
						$dbase = 'log'.$sub_id.'_'.$coy_id;
						mysql_select_db($dbase) or die(mysql_error());
			
								
						if ($ok == 'Y') {
						
							require_once '../../includes/swift_email/swift_required.php';
							
							$t = $_SESSION['s_transport'];
							$te = explode('~',$t);
										
							$transport = Swift_SmtpTransport::newInstance($te[0], $te[1], $te[2])
							  ->setUsername($te[3])
							  ->setPassword($te[4])
							  ;
							
							// Create the Mailer using your created Transport
							$mailer = Swift_Mailer::newInstance($transport);
							
					
							$message = Swift_Message::newInstance();
							$message->setSubject('Incident Occured');
							$message->setFrom(array($email_from => $coyname));
							$message->setTo(array($email_to => $client));
							$mstring = "Dear Sir/Madam\r\n\r\n"."Incident number ".$incid." has been added.";
							$message->setBody($mstring,'text/plain');
							
							$result = $mailer->send($message);
						}
				
				
			
	
	
	echo 2;
} else {
		
	echo 5;

}




?>

