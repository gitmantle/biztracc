<?php
session_start();

$dbs = "ken47109_kenny";
require("../../db.php");
mysql_select_db($dbs) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error().$query);
$row = mysql_fetch_array($result);
extract($row);

$filterfile = "ztmp".$user_id."_filter";
$consfile = "consfile".$user_id;

if( mysql_num_rows( mysql_query("SHOW TABLES LIKE '".$consfile."'")) == 0) {
	$query = "create table ".$consfile." (uid integer(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, member_id int, lastname varchar(45) default ' ', firstname varchar(45) default ' ', preferredname varchar(45) default ' ', address varchar(45) default ' ', suburb varchar(45) default ' ', town varchar(45) default ' ', postcode char(4) default ' ', phone varchar(25) default ' ', advisor varchar(45), age int(3) default 0, reviewmonth char(3) default ' ', status varchar(20) default ' ',gender char(6) default ' ',smoker char(3) default ' ',married char(8) default ' ')  engine myisam";
	$calc = mysql_query($query) or die(mysql_error().$query);
}

$q = 'select member_id as mid,lastname as lname,firstname as fname,preferredname as pname,address as ad,suburb as sbb,town as twn,postcode as pcd,phone as phn,advisor as adv,age as ag,reviewmonth as rev,status as sta,gender as gen,smoker as smk,married as mar from '.$filterfile;
$r = mysql_query($q) or die(mysql_error().$q);
while ($row = mysql_fetch_array($r)) {
	extract($row);
	$memid = $mid;
	$ql = 'select member_id from '.$consfile.' where member_id = '.$memid;
	$rl = mysql_query($ql) or die(mysql_error().$ql);
	if (mysql_num_rows($rl) == 0) {
		$qi = 'insert into '.$consfile.'(member_id,lastname,firstname,preferredname,address,suburb,town,postcode,phone,advisor,age,reviewmonth,status,gender,smoker,married) values (';
		$qi .= $mid.',';
		$qi .= '"'.$lname.'",';
		$qi .= '"'.$fname.'",';
		$qi .= '"'.$pname.'",';
		$qi .= '"'.$ad.'",';
		$qi .= '"'.$sbb.'",';
		$qi .= '"'.$twn.'",';
		$qi .= '"'.$pcd.'",';
		$qi .= '"'.$phn.'",';
		$qi .= '"'.$adv.'",';
		$qi .= '"'.$ag.'",';
		$qi .= '"'.$rev.'",';
		$qi .= '"'.$sta.'",';
		$qi .= '"'.$gen.'",';
		$qi .= '"'.$smk.'",';
		$qi .= '"'.$mar.'")';
		$ri = mysql_query($qi) or die(mysql_error().$qi);
	}
}

?>

