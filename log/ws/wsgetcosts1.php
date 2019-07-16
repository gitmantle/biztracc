<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>

<?php
session_start();
error_reporting (E_ALL);

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
$user = "logtracc9";
$pwd = "dun480can";
$connect = mysql_connect($server,$user,$pwd) or die("Check your database connection");

mysql_select_db($dbase) or die(mysql_error());
/*
$json = '{"items":[{"item":{"itemid":"1","catid":"1","itemcode":"DIESEL","item":"Diesel Fuel"}},{"item":{"itemid":"2","catid":"1","itemcode":"ENGOIL","item":"Engine Oil"}},{"item":{"itemid":"3","catid":"1","itemcode":"PSOIL","item":"Power Steering Oil"}},{"item":{"itemid":"4","catid":"1","itemcode":"GEAROIL","item":"Gearbox Oil"}},{"item":{"itemid":"5","catid":"2","itemcode":"11R-22,5","item":"Tyre 11r-22.5"}},{"item":{"itemid":"6","catid":"2","itemcode":"275\/70-19.5","item":"Tyre 275\/70-19.5"}},{"item":{"itemid":"7","catid":"3","itemcode":"LED","item":"Led Light Bulb"}},{"item":{"itemid":"8","catid":"3","itemcode":"LBULB","item":"Light Bulb"}},{"item":{"itemid":"9","catid":"5","itemcode":"MISC","item":"Miscellaneous Item"}},{"item":{"itemid":"10","catid":"4","itemcode":"LABOUR","item":"Labour"}}]}';

$json =  '{"costs":[
	{"cost":[
		{"header":
			{
				"subid":"30",
				"coyid":"8",
				"truckno":"Truck TPT 8507",
				"truckbranch":"T1",
				"trailerno":"",
				"trailerbranch":"",
				"driverid":"18",
				"date":"2012-12-01",
				"supplierid":"20000002~0",
				"supplier":"Caltex",
				"supplierref":"INV12345",
				"descriptions":"Desc 01",
				"paidbyoperator":"Y"
			}
		},

		{"line":
			{
				"quantity":"5",
				"catid":"1",
				"itemid":"2",
				"itemcode":"SAE30",
				"item":"Engine Oil",
				"unitcost":"23",
				"total":"118.7",
				"gst":"3.7"
			}
		},
		{"line":
			{
			"quantity":"4",
			"catid":"2",
			"itemid":"6",
			"itemcode":"275/70-19.5",
			"item":"Tyre 275/70-19.5",
			"unitcost":"7.77",
			"total":"34.08",
			"gst":"3"
			}
		},
		{"line":
			{
			"quantity":"3",
			"catid":"4",
			"itemid":"10",
			"itemcode":"LABOUR",
			"item":"Labour",
			"unitcost":"5",
			"total":"18",
			"gst":"3"
			}
		},
		{"line":
			{
			"quantity":"123",
			"catid":"5",
			"itemid":"9",
			"itemcode":"MISC",
			"item":"Miscellaneous Item",
			"unitcost":"1",
			"total":"124",
			"gst":"1"
			}
		}
	]},
	{"cost":[
		{"header":
			{
			"subid":"30",
			"coyid":"8",
			"truckno":"Truck TPT 8507",
			"truckbranch":"T1",
			"trailerno":"",
			"trailerbranch":"",
			"driverid":"18",
			"date":"2012-12-05",
			"supplierid":"20000003~0",
			"supplier":"Miscellaneous Supplier  ",
			"supplierref":"INV45675",
			"descriptions":"Desc 02",
			"paidbyoperator":"N"
			}
		},
		{"line":
			{
			"quantity":"3",
			"catid":"1",
			"itemid":"4",
			"itemcode":"SAE210",
			"item":"Gearbox Oil",
			"unitcost":"23.33",
			"total":"75.68",
			"gst":"5.69"}}
	]}
]}
';
*/

$json = '{"costs":[{"cost":[{"header":{"subid":"31","coyid":"10","truckno":"Truck AB 50","truckbranch":"0002","trailerno":"","trailerbranch":"","driverid":"24","date":"2013-05-07","supplierid":"20000008~0","supplier":"Allied Petroleum Ltd  ","supplierref":"2.00km/ltr","descriptions":"123456","paidbyoperator":"N"}},{"line":{"quantity":"302.68","catid":"1","itemid":"1","itemcode":"DIESEL","item":"Diesel Fuel","total":"0","serialnos":"","gst":"0"}},{"line":{"quantity":"176","catid":"1","itemid":"1","itemcode":"DIESEL","item":"Diesel Fuel","total":"0","serialnos":"","gst":"0"}}]}]}';



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
							$truckno = $value["truckno"];
							$truckbranch = $value["truckbranch"];
							$trailerno = $value["trailerno"];
							$trailerbranch = $value["trailerbranch"];
							$driverid = $value["driverid"];
							$date = $value["date"];
							$supplierid = $value["supplierid"];
							$supplier = $value["supplier"];
							$supplierref = $value["supplierref"];
							$description = $value["descriptions"];
							$paidbyoperator = $value["paidbyoperator"];
							
							
							$qd = "SELECT description as dc FROM costheader WHERE description = '".$description."'";
							$result = mysql_query($qd) or die (mysql_error().' '.$qd);
							$numrows = mysql_num_rows($result);
							if ($numrows > 0)	{
								// not added
								$cid = 0;
							} else {
							
							
							
								$q = "insert into costheader (truckbranch,trailerbranch,truckno,trailerno,driverid,date,supplierid,supplier,description,supplierref,paid) values (";
								$q .= "'".$truckbranch."',";
								$q .= "'".$trailerbranch."',";
								$q .= "'".$truckno."',";
								$q .= "'".$trailerno."',";
								$q .= $driverid.",";
								$q .= "'".$date."',";
								$q .= "'".$supplierid."',";
								$q .= "'".$supplier."',";
								$q .= "'".$description."',";
								$q .= "'".$supplierref."',";
								$q .= "'".$paidbyoperator."')";
								
								$r = mysql_query($q) or die(mysql_error()." ".$q);
								$cid =  mysql_insert_id();
								
								$q = "update costheader set costid = ".$cid." where uid = ".$cid;
								$r = mysql_query($q) or die(mysql_error()." ".$q);
							}
							
						}
					} else {
						foreach($dvalue as $value) {
							//print_r($value);
							
							if ($cid <> 0) {
								$quantity = $value["quantity"];
								$catid = $value["catid"];
								$itemid = $value["itemid"];
								$itemcode = $value["itemcode"];
								$item = $value["item"];
								//$unitcost = $value["unitcost"];
								$total = $value["total"];
								$gst = $value["gst"];
								$serialnos = $value["serialnos"];
								$refno = 'ORC'.strval($cid);
								
								$unitcost = ($total - $gst) / $quantity;
								
								$q = "insert into costlines (costid,quantity,catid,itemid,itemcode,item,unitcost,gst,total) values (";
								$q .= $cid.",";
								$q .= $quantity.",";
								$q .= $catid.",";
								$q .= $itemid.",";
								$q .= "'".$itemcode."',";
								$q .= "'".$item."',";
								$q .= $unitcost.",";
								$q .= $gst.",";
								$q .= $total.")";
								
								$r = mysql_query($q) or die(mysql_error()." ".$q);
								
								// tyre serial numbers
								if ($serialnos != "") {
									$sns = explode(',',$serialnos);
									
									if ($truckno == '') {
										$tr = $trailerno;
									} else {
										$tr = $truckno;
									}
									
									foreach($sns as $tsn) {
										$sn = trim($tsn);
										$qt = "insert into tyres (itemid,itemcode,item,serialno,activity,date,vehicle,refno) values (";
										$qt .= $itemid.",";
										$qt .= "'".$itemcode."',";
										$qt .= "'".$item."',";
										$qt .= "'".$sn."',";
										$qt .= "'Fit to vehicle',";
										$qt .= "'".$date."',";
										$qt .= "'".$tr."',";
										$qt .= "'".$refno."')";
										
										$rt = mysql_query($qt) or die(mysql_error()." ".$qt);
										
									}
								}
							}
						}
					}
				}
			}
		}
	}
	echo 2;
} else {
		
	echo 5;

}





?>










</body>
</html>