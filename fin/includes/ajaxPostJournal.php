<?php
session_start();
//ini_set('display_errors', true);
$root = $_SERVER['DOCUMENT_ROOT'];
//$pathdb = $root.'/db.php';
//require($pathdb);

$usersession = $_SESSION['usersession'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$uip = $row['userip'];
$unm = $row['uname'];

date_default_timezone_set($_SESSION['s_timezone']);

$table = 'ztmp'.$user_id.'_journal';
$trans = 'ztmp'.$user_id.'_trans';

$findb = $_SESSION['s_findb'];

$db->query("drop table if exists ".$findb.".".$trans);
$db->execute();

$db->query("create table ".$findb.".".$trans." ( uid int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, acc2dr int(11) default 0,subdr int(11) default 0,brdr char(4) default '',drindex int(10) default 0,acc2cr int(11) default 0,subcr int(11) default 0,brcr char(4)default '',crindex int(10) default 0,ddate date default '0000-00-00',descript1 varchar(60),reference char(9) default '',refindex int(10) default 0,amount double(16,2) default 0,depdr int(11),depbrdr char(4),depcr int(11),depbrcr char(4),nallocate int(11),tax double(16,2),taxtype char(3),taxpcent double(5,2),applytax char(1),total double(16,2) default 0, done int(11) default 0,type char(1),grn char(10),inv char(10),currency char(3), rate double(7,3),a2d varchar(45),a2c varchar(45),drgst char(1) default 'N', crgst char(1) default 'N', your_ref varchar(30) default '')  engine myisam");
$db->execute();

$db->query("select (sum(debit) - sum(credit)) as balance from ".$findb.".".$table);
$row = $db->single();
extract($row);

// set local currency
$db->query("select currency from ".$findb.".forex where def_forex = 'Yes'");
$row = $db->single();
extract($row);

$db->query("update ".$findb.".".$table." set currency = :currency, rate = :rate");
$db->bind(':currency', $currency);
$db->bind(':rate', 1);
$db->execute();

if ($balance <> 0) {
	echo "Total debits must equal total credits";
	
} else {

	$sql = "insert into ".$findb.".".$trans." (acc2dr,subdr,brdr,acc2cr,subcr,brcr,ddate,descript1,reference,amount,tax,taxtype,taxpcent,total,currency,rate) values ";

	$db->query("select * from ".$findb.".".$table);
	$rows = $db->resultset();
	foreach ($rows as $row) {
		extract($row);
		
		if ($debit > 0) {
			$acc2dr = $accno;
			$subdr = $subac;
			$brdr = $brac;
			$amount = $debit;
			$acc2cr = 999;
			$subcr = 0;
			$brcr = $brac;
		} else {
			$acc2cr = $accno;
			$subcr = $subac;
			$brcr = $brac;
			$amount = $credit;
			$acc2dr = 999;
			$subdr = 0;
			$brdr = $brac;
		}
		
		if ($drgst == 'N') {
			$db->query("select tax,taxpcent from ".$findb.".taxtypes where defgst = 'Yes'");
			$row = $db->single();
			extract($row);
		} else {
			$tax = 'N-T';
			$taxpcent = 0;
		}
	
		$sql .= "(".$acc2dr.",";
		$sql .= $subdr.",";
		$sql .= "'".$brdr."',";
		$sql .= $acc2cr.",";
		$sql .= $subcr.",";
		$sql .= "'".$brcr."',";
		$sql .= "'".$ddate."',";
		$sql .= "'".$note."',";
		$sql .= "'".$reference."',";
		$sql .= $amount.",";
		$sql .= "0,";
		$sql .= "'".$tax."',";
		$sql .= $taxpcent.",";
		$sql .= $amount.",";
		$sql .= "'".$currency."',";
		$sql .= "1),";
	}
	$sql = rtrim($sql,',');
	$db->query($sql);
	$db->execute();
	
	$db->query("delete from ".$findb.".".$table);
	$db->execute();
	
	echo 'Y';
}


?>