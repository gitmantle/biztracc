<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>Logtracc - Logging Truck Accounting & Administration</title>

</head>


<body>
<?php
session_start();
//ini_set('display_errors', true);
	$server = 'localhost';
	$user = "logtracc9";
	$pwd = "dun480can";
	$connect = mysql_connect($server,$user,$pwd) or die("Check your database connection - db");

//itemcode:itemcode,itemname:itemname,serials:serials,loc:loc,refno:refno

$itemcode = '275/70-19.5';
$itemname = 'Tyre 275/70-19.5';
$serials = '1a,2b,3c,4d,5e';
$loc = 1;
$refno = 'GRN34';

$serialtable = 'ztmp24_serialnos';

$moduledb = 'fin31_10';
mysql_select_db($moduledb) or die(mysql_error());

$s = explode(",",$serials);

print_r($s);

foreach($s as $value) {
	$sql = "insert into ".$serialtable." (itemcode,item,serialno,location,ref_no) values (";
	$sql .= "'".$itemcode."',";
	$sql .= "'".$itemname."',";
	$sql .= "'".$value."',";
	$sql .= $loc.",";
	$sql .= "'".$refno."')";
	
	echo $sql;
	
	$result = mysql_query($sql) or die(mysql_error().' - '.$sql);
}


?>
</body>
</html>

