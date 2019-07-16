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
$sid = 31;
$cid = 10;


$dbase = 'log'.$sid.'_'.$cid;

date_default_timezone_set($_SESSION['s_timezone']);
$ddate = date("Y-m-d");
	
$server = 'localhost';
$user = "logtracc9";
$pwd = "dun480can";
$connect = mysql_connect($server,$user,$pwd) or die("Check your database connection");

mysql_select_db($dbase) or die(mysql_error());

$json = '{"driverlog":[{"driverlog":{"truckno":"Truck AB 50","log":"Start Work","time":"3:50:20","hubodometer":"240307.00","driverid":"20","date":"2013-07-22"}},
{"driverlog":{"truckno":"Trailer AB 51","log":"Start Work","time":"","hubodometer":"26002.00","driverid":"20","date":"2013-07-22"}}]}';



$input = json_decode($json,true);

if (is_array($input)) {

	foreach ($input as $dvalue) {
		foreach($dvalue as $tvalue) {
			//print_r($tvalue);
			foreach($tvalue as $value) {
				
				$dbase = 'logtracc';
				mysql_select_db($dbase) or die(mysql_error());

				$date = $value["date"];
				$driverid = $value["driverid"];
				$truckno = $value["truckno"];
				$log = $value["log"];
				$time = $value["time"];
				$hubodometer = $value["hubodometer"];
				$hbo = $hubodometer;
				
				$q = "select concat(ufname,' ',ulname) as uname from users where uid = ".$driverid;
				$r = mysql_query($q) or die(mysql_error()." ".$q);
				$row = mysql_fetch_array($r);
				extract($row);
				$un = $uname;
				
				$dbase = 'log'.$sid.'_'.$cid;
				mysql_select_db($dbase) or die(mysql_error().' '.$dbase);
				
				$qd = "SELECT date,truckno,log,time from driverlog where date = '".$date."' and truckno = '".$truckno."' and log = '".$log."' and time = '".$time."'";
				$result = mysql_query($qd) or die (mysql_error().' '.$qd);
				$numrows = mysql_num_rows($result);
				if ($numrows > 0)	{
					// not added
				} else {
				
					$q = "insert into driverlog (date,truckno,driverid,driver,log,time,hubodometer) values (";
					$q .= "'".$date."',";
					$q .= "'".$truckno."',";
					$q .= $driverid.",";
					$q .= "'".$un."',";
					$q .= "'".$log."',";
					$q .= "'".$time."',";
					$q .= "'".$hubodometer."')";
					
					$r = mysql_query($q) or die(mysql_error()." ".$q);
					
					// update odometer in vehicles table
					
					$qm = "SELECT s.ruckms,s.ruclicence,s.fromkms FROM rucs s JOIN (SELECT MAX(ruckms) AS id FROM rucs where vehicleno = '".$truckno."') max ON s.ruckms = max.id";
					$rm = mysql_query($qm) or die(mysql_error()." ".$qm);
					$row = mysql_fetch_array($rm);
					extract($row);
					
					if ($hubodometer > $ruckms) {
						$rucl = $ruclicence;
					} else {
						$qf = "select ruclicence from rucs where vehicleno = '".$truckno."' and fromkms <= ".$hubodometer." and ruckms >= ".$hubodometer;
						$rf = mysql_query($qf) or die(mysql_error()." ".$qf);
						$row = mysql_fetch_array($rf);
						extract($row);
						$rucl = $ruclicence;
					}
					
					$qf = "SELECT s.ruckms,s.ruclicence,s.fromkms,s.hubodometer,s.email4ruc FROM rucs s JOIN (SELECT MAX(ruckms) AS id FROM rucs where vehicleno = '".$truckno."') max ON s.ruckms = max.id";
					$rf = mysql_query($qf) or die(mysql_error());
					$row = mysql_fetch_array($rf);
					extract($row);
					$rk = $ruckms;
					$fk = $fromkms;
					$rl = $ruclicence;
					$hb = $hubodometer;
					$er = $email4ruc;
					
					$check = $truckno.'~'.$hubodometer;
					
					if ($check <> $er) {
					
					echo 'check <> er';

/*					$str = '';
						if (($ruckms - $hubodometer) < 700 || ($ruckms < $hubodometer)) {
							$str = "$truckno RUC expires at $rk. Odometer now at $hb\r\n\r\n"; 
						}
					
						$dbase = 'fin'.$sid.'_'.$cid;
						mysql_select_db($dbase) or die(mysql_error());
						
						$q = "select * from globals";
						$r = mysql_query($q) or die (mysql_error().' '.$q);
						$row = mysql_fetch_array($r);
						extract($row);
						
						$email_from = 'admin@logtracc.co.nz';
						$email_to = $email;
						$coyname = $_SESSION['s_coyname'];
						$coyid = $_SESSION['s_coyid'];	
						$client = "Administrator";
								
						$ok = 'Y';
						if (trim($email_from) == "") {
							$ok = 'N';
						}
						if (trim($email_to) == "") {
							$ok = 'N';
						}
						if (trim($str) == "") {
							$ok = 'N';
						}
						
						$dbase = 'log'.$sid.'_'.$cid;
						mysql_select_db($dbase) or die(mysql_error());
			
								
						if ($ok == 'Y') {
						
							require_once '../../includes/swift_email/swift_required.php';
							
							$transport = Swift_SmtpTransport::newInstance('smtp.webhost.co.nz', 25);
							$mailer = Swift_Mailer::newInstance($transport);
							
					
							$message = Swift_Message::newInstance();
							$message->setSubject('Expiring RUCs');
							$message->setFrom(array($email_from => $coyname));
							$message->setTo(array($email_to => $client));
							$mstring = "Dear Sir/Madam\r\n\r\n"."The following vehicle needs its RUC renewing:-\r\n\r\n".$str;
							$message->setBody($mstring,'text/plain');
							
							$result = $mailer->send($message);
						}
*/					}
					
					$q = "update rucs set hubodometer = ".$hbo.", email4ruc = '".$truckno."~".$hbo."' where ruclicence = '".$ruclicence."'";
					$r = mysql_query($q) or die(mysql_error()." ".$q);
					
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