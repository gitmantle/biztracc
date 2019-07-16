<?php

session_start();
$sub_id = $_REQUEST['sid'];
$coy_id = $_REQUEST['cid'];
//$json = $_REQUEST['json'];
$dbase = 'log'.$sub_id.'_'.$coy_id;

/*
 $handle = fopen('php://input','r');
                $jsonInput = fgets($handle);
                // Decoding JSON into an Array
                $input = json_decode($jsonInput,true);
*/

$input = json_decode($_POST['json']);

date_default_timezone_set($_SESSION['s_timezone']);
$ddate = date("Y-m-d");
	
$server = "mysql3.webhost.co.nz";
$user = "logtracc9";
$pwd = "dun480can";
$connect = mysql_connect($server,$user,$pwd) or die("Check your database connection");

mysql_select_db($dbase) or die(mysql_error());

//$input = json_decode($json,true);

$q = 'insert into upload_log (json) values ("'.$input.'")';
$r = mysql_query($q) or die(mysql_error()." ".$q);



if (is_array($input)) {
	foreach ($input as $dvalue) {
		foreach($dvalue as $tvalue) {
			foreach($tvalue as $value) {
				$date = $value['date'];
				$docketno = $value['docketnumber'];	
				$forest = $value['forestsource'];
				$cpt = $value['cpt'];
				$stand = $value['skid'];
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
				$contractor = $value['loggingcontractor'];
				$crew = $value['crew'];
				$cartage = $value['cartagecontract'];
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
				
				$q = "insert into dockets (ddate,docket_no,forest,cpt,skid,length,peel,saw,pulp,other,pieces,species,harvest,customer,destination,contractor,crew,cartage,truck,trailer,truckbranch,trailerbranch,gross,tare,net,routeid,debtor,operator,latstart,latend,longstart,longend) values (";
																																																																											$q .= "'".$date."',";
				$q .= $docketno.",";	
				$q .= "'".$forest."',";
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
				$q .= "'".$contractor."',";
				$q .= "'".$crew."',";
				$q .= "'".$cartage."',";
				$q .= "'".$truck."',";
				$q .= "'".$trailer."',";
				$q .= "'".$truckbranch."',";
				$q .= "'".$trailerbranch."',";
				$q .= $gross.",";
				$q .= $tare.",";	
				$q .= $net.",";
				$q .= $routid.",";
				$q .= "'".$debtor."',";
				$q .= $operator.",";
				$q .= "'".$latstart."',";
				$q .= "'".$latend."',";
				$q .= "'".$longstart."',";
				$q .= "'".$longend."')";
	
				$r = mysql_query($q) or die(mysql_error()." ".$q);
					
				}
			}
		echo 2;
	}
} else {
	
echo 5;

}



?>

