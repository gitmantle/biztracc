<?php

session_start();
$sub_id = $_REQUEST['sid'];
$coy_id = $_REQUEST['cid'];

$json = file_get_contents('php://input');

$File = "logtravel.txt"; 
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

function ConvertGMTToLocalTimezone($gmttime,$timezoneRequired)
{
    $system_timezone = date_default_timezone_get();

    date_default_timezone_set("GMT");
    $gmt = date("Y-m-d h:i:s A");

    $local_timezone = $timezoneRequired;
    date_default_timezone_set($local_timezone);
    $local = date("Y-m-d h:i:s A");

    date_default_timezone_set($system_timezone);
    $diff = (strtotime($local) - strtotime($gmt));

    $date = new DateTime($gmttime);
    $date->modify("+$diff seconds");
    $timestamp = $date->format("Y-m-d H:i:s");
    return $timestamp;
}


mysql_select_db($dbase) or die(mysql_error());

//$js = explode('}}]}',$json);

//foreach ($js as $jvalue) {
	//$jsv = $jvalue.'}}]}';


$input = json_decode($json,true);

if (is_array($input)) {

	foreach ($input as $dvalue) {
		foreach($dvalue as $tvalue) {
			//print_r($tvalue);
			foreach($tvalue as $value) {
				$truckno = $value["truckno"];
				$driverid = $value["driverid"];
				$unitno = $value["unitno"];
				$dt = $value["date"];
				$dattim = ConvertGMTToLocalTimezone($dt,"Pacific/Auckland");
				//$datetime = $dt;
				$latitude = $value["latitude"];
				$longitude = $value["longitude"];
				$spd = $value["speed"];
				
				$speed = round($spd*3600/1000,2);
				
				$dbase = 'logtracc';
				mysql_select_db($dbase) or die(mysql_error());
				
				$qd = "select concat(ufname,' ',ulname) as un from users where uid = ".$driverid;
				$rd = mysql_query($qd) or die (mysql_error());
				$row = mysql_fetch_array($rd);
				extract($row);
				$uname = $un;
				
				$dbase = 'log'.$sub_id.'_'.$coy_id;
				mysql_select_db($dbase) or die(mysql_error());
				
				$qd = "SELECT datetime as dno FROM travellog WHERE datetime = '".$dattim."' and truckno = '".$truckno."'";
				$result = mysql_query($qd) or die (mysql_error().' '.$qd);
				$numrows = mysql_num_rows($result);
				if ($numrows > 0)	{
					// not added
				} else {

				
				
				$q = "insert into travellog (truckno,driverid,driver,unitno,datetime,latitude,longitude,speed) values (";
				$q .= "'".$truckno."',";
				$q .= $driverid.",";
				$q .= "'".$uname."',";
				$q .= $unitno.",";
				$q .= "'".$dattim."',";
				$q .= "'".$latitude."',";
				$q .= "'".$longitude."',";
				$q .= $speed.")";
				
				$r = mysql_query($q) or die(mysql_error()." ".$q);
				
				// update odometer in vehicles table
				
				$qm = "SELECT s.ruckms,s.ruclicence,s.fromkms FROM rucs s JOIN (SELECT MAX(ruckms) AS id FROM rucs where vehicleno = '".$truckno."') max ON s.ruckms = max.id";
				$rm = mysql_query($qm) or die(mysql_error()." ".$qm);
				$row = mysql_fetch_array($rm);
				extract($row);
				$topkms = $ruckms;
				
				$qh = "select max(hubodometer) as hubo from rucs where vehicleno = '".$truckno."'";
				$rh = mysql_query($qh) or die(mysql_error()." ".$qh);
				$row = mysql_fetch_array($rh);
				extract($row);
				$hubodometer = $hubo + 1;
				
				if ($hubodometer > $topkms) {
					$rucl = $ruclicence;
				} else {
					$qf = "select ruclicence from rucs where vehicleno = '".$truckno."' and fromkms <= ".$hubodometer." and ruckms >= ".$hubodometer;
					$rf = mysql_query($qf) or die(mysql_error()." ".$qf);
					$row = mysql_fetch_array($rf);
					extract($row);
					$rucl = $ruclicence;
				}
				
				$q = "update rucs set hubodometer = ".$hubodometer." where ruclicence = '".$ruclicence."'";
				$r = mysql_query($q) or die(mysql_error()." ".$q);
				}
			}
		}
		echo 2;
	}
} else {
	
echo 5;

}

//}


?>


