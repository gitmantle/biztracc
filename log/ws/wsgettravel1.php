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
$sub_id = 30;
$coy_id = 8;

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



$dbase = 'log'.$sub_id.'_'.$coy_id;

date_default_timezone_set($_SESSION['s_timezone']);
$ddate = date("Y-m-d");
	
$server = 'localhost';
//$server = "mysql3.webhost.co.nz";
$user = "logtracc9";
$pwd = "dun480can";
$connect = mysql_connect($server,$user,$pwd) or die("Check your database connection");

mysql_select_db($dbase) or die(mysql_error());

/*
$json = '{"travellogs":[
						{"travellog":{"date":"2013-07-11 03:46:20 +0000","coyid":"10","unitno":"1","driverid":"24","subid":"31","truckno":"Truck AB 50","truckbranch":"0002","speed":"-1","latitude":"-36.8484597","longitude":"174.7633315"}},
						{"travellog":{"date":"2013-07-11 03:46:26 +0000","coyid":"10","unitno":"1","driverid":"24","subid":"31","truckno":"Truck AB 50","truckbranch":"0002","speed":"-1","latitude":"-36.8484597","longitude":"174.7633315"}},
						{"travellog":{"date":"2013-07-11 04:02:46 +0000","coyid":"10","unitno":"1","driverid":"24","subid":"31","truckno":"Truck AB 50","truckbranch":"0002","speed":"-1","latitude":"-36.8484597","longitude":"174.7633315"}},
						{"travellog":{"date":"2013-07-11 04:02:50 +0000","coyid":"10","unitno":"1","driverid":"24","subid":"31","truckno":"Truck AB 50","truckbranch":"0002","speed":"-1","latitude":"-36.8484597","longitude":"174.7633315"}},
						{"travellog":{"date":"2013-07-11 04:05:12 +0000","coyid":"10","unitno":"1","driverid":"24","subid":"31","truckno":"Truck AB 50","truckbranch":"0002","speed":"-1","latitude":"-36.8484597","longitude":"174.7633315"}},
						{"travellog":{"date":"2013-07-11 04:05:16 +0000","coyid":"10","unitno":"1","driverid":"24","subid":"31","truckno":"Truck AB 50","truckbranch":"0002","speed":"-1","latitude":"-36.8484597","longitude":"174.7633315"}},
						{"travellog":{"date":"2013-07-11 04:18:42 +0000","coyid":"10","unitno":"1","driverid":"24","subid":"31","truckno":"Truck AB 50","truckbranch":"0002","speed":"-1","latitude":"-36.8484597","longitude":"174.7633315"}},
						{"travellog":{"date":"2013-07-11 04:18:46 +0000","coyid":"10","unitno":"1","driverid":"24","subid":"31","truckno":"Truck AB 50","truckbranch":"0002","speed":"-1","latitude":"-36.8484597","longitude":"174.7633315"}},
						{"travellog":{"date":"2013-07-11 04:38:43 +0000","coyid":"10","unitno":"1","driverid":"24","subid":"31","truckno":"Truck AB 50","truckbranch":"0002","speed":"-1","latitude":"-36.8484597","longitude":"174.7633315"}},
						{"travellog":{"date":"2013-07-11 04:58:43 +0000","coyid":"10","unitno":"1","driverid":"24","subid":"31","truckno":"Truck AB 50","truckbranch":"0002","speed":"-1","latitude":"-36.8484597","longitude":"174.7633315"}},
						{"travellog":{"date":"2013-07-11 05:18:43 +0000","coyid":"10","unitno":"1","driverid":"24","subid":"31","truckno":"Truck AB 50","truckbranch":"0002","speed":"-1","latitude":"-36.8484597","longitude":"174.7633315"}}]}

{"travellogs":[{"travellog":{"date":"2013-07-11 03:46:20 +0000","coyid":"10","unitno":"1","driverid":"24","subid":"31","truckno":"Truck AB 50","truckbranch":"0002","speed":"-1","latitude":"-36.8484597","longitude":"174.7633315"}},{"travellog":{"date":"2013-07-11 03:46:26 +0000","coyid":"10","unitno":"1","driverid":"24","subid":"31","truckno":"Truck AB 50","truckbranch":"0002","speed":"-1","latitude":"-36.8484597","longitude":"174.7633315"}},{"travellog":{"date":"2013-07-11 04:02:46 +0000","coyid":"10","unitno":"1","driverid":"24","subid":"31","truckno":"Truck AB 50","truckbranch":"0002","speed":"-1","latitude":"-36.8484597","longitude":"174.7633315"}},{"travellog":{"date":"2013-07-11 04:02:50 +0000","coyid":"10","unitno":"1","driverid":"24","subid":"31","truckno":"Truck AB 50","truckbranch":"0002","speed":"-1","latitude":"-36.8484597","longitude":"174.7633315"}},{"travellog":{"date":"2013-07-11 04:05:12 +0000","coyid":"10","unitno":"1","driverid":"24","subid":"31","truckno":"Truck AB 50","truckbranch":"0002","speed":"-1","latitude":"-36.8484597","longitude":"174.7633315"}},{"travellog":{"date":"2013-07-11 04:05:16 +0000","coyid":"10","unitno":"1","driverid":"24","subid":"31","truckno":"Truck AB 50","truckbranch":"0002","speed":"-1","latitude":"-36.8484597","longitude":"174.7633315"}},{"travellog":{"date":"2013-07-11 04:18:42 +0000","coyid":"10","unitno":"1","driverid":"24","subid":"31","truckno":"Truck AB 50","truckbranch":"0002","speed":"-1","latitude":"-36.8484597","longitude":"174.7633315"}},{"travellog":{"date":"2013-07-11 04:18:46 +0000","coyid":"10","unitno":"1","driverid":"24","subid":"31","truckno":"Truck AB 50","truckbranch":"0002","speed":"-1","latitude":"-36.8484597","longitude":"174.7633315"}},{"travellog":{"date":"2013-07-11 04:38:43 +0000","coyid":"10","unitno":"1","driverid":"24","subid":"31","truckno":"Truck AB 50","truckbranch":"0002","speed":"-1","latitude":"-36.8484597","longitude":"174.7633315"}},{"travellog":{"date":"2013-07-11 04:58:43 +0000","coyid":"10","unitno":"1","driverid":"24","subid":"31","truckno":"Truck AB 50","truckbranch":"0002","speed":"-1","latitude":"-36.8484597","longitude":"174.7633315"}},{"travellog":{"date":"2013-07-11 05:18:43 +0000","coyid":"10","unitno":"1","driverid":"24","subid":"31","truckno":"Truck AB 50","truckbranch":"0002","speed":"-1","latitude":"-36.8484597","longitude":"174.7633315"}}]}';

*/

$json = '{"travellogs":[{"travellog":{"date":"2013-07-11 03:46:20 +0000","coyid":"10","unitno":"1","driverid":"24","subid":"31","truckno":"Truck AB 50","truckbranch":"0002","speed":"-1","latitude":"-36.8484597","longitude":"174.7633315"}},{"travellog":{"date":"2013-07-11 03:46:26 +0000","coyid":"10","unitno":"1","driverid":"24","subid":"31","truckno":"Truck AB 50","truckbranch":"0002","speed":"-1","latitude":"-36.8484597","longitude":"174.7633315"}},{"travellog":{"date":"2013-07-11 04:02:46 +0000","coyid":"10","unitno":"1","driverid":"24","subid":"31","truckno":"Truck AB 50","truckbranch":"0002","speed":"-1","latitude":"-36.8484597","longitude":"174.7633315"}},{"travellog":{"date":"2013-07-11 04:02:50 +0000","coyid":"10","unitno":"1","driverid":"24","subid":"31","truckno":"Truck AB 50","truckbranch":"0002","speed":"-1","latitude":"-36.8484597","longitude":"174.7633315"}},{"travellog":{"date":"2013-07-11 04:05:12 +0000","coyid":"10","unitno":"1","driverid":"24","subid":"31","truckno":"Truck AB 50","truckbranch":"0002","speed":"-1","latitude":"-36.8484597","longitude":"174.7633315"}},{"travellog":{"date":"2013-07-11 04:05:16 +0000","coyid":"10","unitno":"1","driverid":"24","subid":"31","truckno":"Truck AB 50","truckbranch":"0002","speed":"-1","latitude":"-36.8484597","longitude":"174.7633315"}},{"travellog":{"date":"2013-07-11 04:18:42 +0000","coyid":"10","unitno":"1","driverid":"24","subid":"31","truckno":"Truck AB 50","truckbranch":"0002","speed":"-1","latitude":"-36.8484597","longitude":"174.7633315"}},{"travellog":{"date":"2013-07-11 04:18:46 +0000","coyid":"10","unitno":"1","driverid":"24","subid":"31","truckno":"Truck AB 50","truckbranch":"0002","speed":"-1","latitude":"-36.8484597","longitude":"174.7633315"}},{"travellog":{"date":"2013-07-11 04:38:43 +0000","coyid":"10","unitno":"1","driverid":"24","subid":"31","truckno":"Truck AB 50","truckbranch":"0002","speed":"-1","latitude":"-36.8484597","longitude":"174.7633315"}},{"travellog":{"date":"2013-07-11 04:58:43 +0000","coyid":"10","unitno":"1","driverid":"24","subid":"31","truckno":"Truck AB 50","truckbranch":"0002","speed":"-1","latitude":"-36.8484597","longitude":"174.7633315"}},{"travellog":{"date":"2013-07-11 05:18:43 +0000","coyid":"10","unitno":"1","driverid":"24","subid":"31","truckno":"Truck AB 50","truckbranch":"0002","speed":"-1","latitude":"-36.8484597","longitude":"174.7633315"}}]}';



//$js = explode('}}]}',$json);

//print_r($js);

//foreach ($js as $jvalue) {
	//$jsv = $jvalue.'}}]}';


//$input = json_decode($jsv,true);
$input = json_decode($json,true);

//print_r($input);

if (is_array($input)) {

	foreach ($input as $dvalue) {
		foreach($dvalue as $tvalue) {
			//echo '  ***  ';
			//print_r($tvalue);
			foreach($tvalue as $value) {
				$truckno = $value["truckno"];
				$driverid = $value["driverid"];
				$unitno = $value["unitno"];
				$dt = $value["date"];
				$datetime = ConvertGMTToLocalTimezone($dt,"Pacific/Auckland");
				//$datetime = $dt;
				$latitude = $value["latitude"];
				$longitude = $value["longitude"];
				$spd = $value["speed"];
				
				$speed = round($spd*3600/1000,2);
				
				$dbase = 'logtracc';
				mysql_select_db($dbase) or die(mysql_error());
				
				$qd = "select concat(' ',ufname,ulname) as un from users where uid = ".$driverid;
				$rd = mysql_query($qd) or die (mysql_error());
				$row = mysql_fetch_array($rd);
				extract($row);
				$uname = $un;
				
				$dbase = 'log'.$sub_id.'_'.$coy_id;
				mysql_select_db($dbase) or die(mysql_error());
				
				$qd = "SELECT datetime as dno FROM travellog WHERE datetime = '".$datetime."' and truckno = '".$truckno."'";
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
				$q .= "'".$datetime."',";
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






</body>
</html>