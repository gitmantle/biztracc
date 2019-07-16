<?php

session_start();
$sid = $_REQUEST['sid'];
$cid = $_REQUEST['cid'];

$json = file_get_contents('php://input');


$File = "logdriver.txt"; 
 $Handle = fopen($File, 'a');
 $Data.=$json;
 fwrite($Handle, $Data);  
 fclose($Handle); 


$dbase = 'log'.$sid.'_'.$cid;

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
					
						$str = '';
						if (($ruckms - $hubodometer) < 700 || ($ruckms < $hubodometer)) {
							$str = "$truckno RUC expires at $rk. Odometer now at $hb\r\n\r\n"; 
						}
					
						$dbase = 'logtracc';
						mysql_select_db($dbase) or die(mysql_error());
						
						$q = "select notifyruc from users where sub_id = ".$sid." and notifyruc != ''";
						$r = mysql_query($q) or die (mysql_error().' '.$q);
						$numrows = mysql_num_rows($r);
						if ($numrows == 1) {
							$row = mysql_fetch_array($r);
							extract($row);
							$email = $notifyruc;
						} else {
							$email = "";
						}
						
						
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
							
							$t = $_SESSION['s_transport'];
							$te = explode('~',$t);
										
							$transport = Swift_SmtpTransport::newInstance($te[0], $te[1], $te[2])
							  ->setUsername($te[3])
							  ->setPassword($te[4])
							  ;
							
							// Create the Mailer using your created Transport
							$mailer = Swift_Mailer::newInstance($transport);
							
					
							$message = Swift_Message::newInstance();
							$message->setSubject('Expiring RUCs');
							$message->setFrom(array($email_from => $coyname));
							$message->setTo(array($email_to => $client));
							$mstring = "Dear Sir/Madam\r\n\r\n"."The following vehicle needs its RUC renewing:-\r\n\r\n".$str;
							$message->setBody($mstring,'text/plain');
							
							$result = $mailer->send($message);
						}
					}
					
					$dbase = 'log'.$sid.'_'.$cid;
					mysql_select_db($dbase) or die(mysql_error().' '.$dbase);
					
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

