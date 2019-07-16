<?php
session_start();
//ini_set('display_errors', true);
require_once("../../db.php");

$ddate = $_REQUEST['ddate'];
$refno = $_REQUEST['ref'];
$totwage = $_REQUEST['totwage'];
$bank = $_REQUEST['bank'];
$as = $_REQUEST['labac'];
$lac = explode(' ',$as);
$acc2dr = $lac[0];
$subdr = $lac[1];

$usersession = $_SESSION['usersession'];
$admindb = $_SESSION['s_admindb'];
date_default_timezone_set($_SESSION['s_timezone']);

mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$r3 = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($r3);
extract($row);
$uip = $userip;
$unm = $uname;

$table = 'ztmp'.$user_id.'_wages';
$trans = 'ztmp'.$user_id.'_trans';

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

$query = "drop table if exists ".$trans;
$r2 = mysql_query($query) or die(mysql_error());

$query = "create table ".$trans." ( uid int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, acc2dr int(11) default 0,subdr int(11) default 0,brdr char(4) default '',drindex int(10) default 0,acc2cr int(11) default 0,subcr int(11) default 0,brcr char(4)default '',crindex int(10) default 0,ddate date default '0000-00-00',descript1 varchar(60),reference char(9) default '',refindex int(10) default 0,amount double(16,2) default 0,depdr int(11),depbrdr char(4),depcr int(11),depbrcr char(4),nallocate int(11),tax double(16,2),taxtype char(3),taxpcent double(5,2),applytax char(1),total double(16,2) default 0, done int(11) default 0,type char(1),grn char(10),inv char(10),currency char(3), rate double(7,3),a2d varchar(45),a2c varchar(45),drgst char(1) default 'N', crgst char(1) default 'N')  engine myisam";
$calc = mysql_query($query) or die(mysql_error());

$q = "select sum(total) as tot from ".$table;
$r0 = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($r0);
extract($row);

if ($tot <> $totwage) {
	echo "Total allocated must equal total wage bill";
	
} else {
	$b = explode('~',$bank);
	$acc2cr = $b[1];
	$brcr = $b[2];
	$subcr = $b[3];
	

	$q = "select * from ".$table;
	$result = mysql_query($q) or die(mysql_error());
	while ($row = mysql_fetch_array($result)) {
		extract($row);
		
		if ($truckbranch <> '' and $trailerbranch <> '') {
		
			$sql = "insert into ".$trans." (acc2dr,subdr,brdr,acc2cr,subcr,brcr,ddate,descript1,reference,amount,tax,taxtype,taxpcent,total) values ";
			$sql .= "(".$acc2dr.",";
			$sql .= $subdr.",";
			$sql .= "'".$truckbranch."',";
			$sql .= $acc2cr.",";
			$sql .= $subcr.",";
			$sql .= "'".$brcr."',";
			$sql .= "'".$ddate."',";
			$sql .= "'Allocate Wages',";
			$sql .= "'".$refno."',";
			$sql .= $truckamt.",";
			$sql .= "0,";
			$sql .= "'"." "."',";
			$sql .= "0,";
			$sql .= $truckamt.")";
			
			$r1 = mysql_query($sql) or die(mysql_error().' - '.$sql);
			
		
			$sql = "insert into ".$trans." (acc2dr,subdr,brdr,acc2cr,subcr,brcr,ddate,descript1,reference,amount,tax,taxtype,taxpcent,total) values ";
			$sql .= "(".$acc2dr.",";
			$sql .= $subdr.",";
			$sql .= "'".$trailerbranch."',";
			$sql .= $acc2cr.",";
			$sql .= $subcr.",";
			$sql .= "'".$brcr."',";
			$sql .= "'".$ddate."',";
			$sql .= "'Allocate Wages',";
			$sql .= "'".$refno."',";
			$sql .= $traileramt.",";
			$sql .= "0,";
			$sql .= "'"." "."',";
			$sql .= "0,";
			$sql .= $traileramt.")";

			$r1 = mysql_query($sql) or die(mysql_error().' - '.$sql);

		} else {
		
			$sql = "insert into ".$trans." (acc2dr,subdr,brdr,acc2cr,subcr,brcr,ddate,descript1,reference,amount,tax,taxtype,taxpcent,total) values ";
			$sql .= "(".$acc2dr.",";
			$sql .= $subdr.",";
			$sql .= "'".$truckbranch."',";
			$sql .= $acc2cr.",";
			$sql .= $subcr.",";
			$sql .= "'".$brcr."',";
			$sql .= "'".$ddate."',";
			$sql .= "'Allocate Wages',";
			$sql .= "'".$refno."',";
			$sql .= $truckamt.",";
			$sql .= "0,";
			$sql .= "'"." "."',";
			$sql .= "0,";
			$sql .= $truckamt.")";
			$r1 = mysql_query($sql) or die(mysql_error().' - '.$sql);
		}
	
	}
	
	
	$sql = "delete from ".$table;
	$rst = mysql_query($sql) or die (mysql_error());
	
	echo 'Y';
}


?>