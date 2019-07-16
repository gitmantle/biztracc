<?php

session_start();
$sub_id = $_REQUEST['sid'];
$coy_id = $_REQUEST['cid'];

$json = file_get_contents('php://input');

$File = "logcosts.txt"; 
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
							$costid = $value["costid"];
							
							
							$qd = "SELECT tablet_uid as dc FROM costheader WHERE tablet_uid = '".$costid."'";
							$result = mysql_query($qd) or die (mysql_error().' '.$qd);
							$numrows = mysql_num_rows($result);
							if ($numrows > 0)	{
								// not added
								$cid = 0;
							} else {
							
							
							
								$q = "insert into costheader (truckbranch,trailerbranch,truckno,trailerno,driverid,date,supplierid,supplier,description,supplierref,paid,tablet_uid) values (";
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
								$q .= "'".$paidbyoperator."',";
								$q .= "'".$costid."')";
								
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

