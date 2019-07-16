<?php
session_start();

$usersession = $_SESSION['usersession'];

require_once("../db.php");
$admindb = $_SESSION['s_admindb'];
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$gstinvpay = $_REQUEST['gstinvpay'];
$clientid = $_REQUEST['clientid'];
$st1 = $_REQUEST['st1'];	
$st2 = $_REQUEST['st2'];
$st3 = $_REQUEST['st3'];
$st4 = $_REQUEST['st4'];
$qty1 = $_REQUEST['qty1'];
$qty2 = $_REQUEST['qty2'];
$qty3 = $_REQUEST['qty3'];
$qty4 = $_REQUEST['qty4'];
$amt1 = $_REQUEST['amt1'];
$amt2 = $_REQUEST['amt2'];
$amt3 = $_REQUEST['amt3'];
$amt4 = $_REQUEST['amt4'];

$tradetable = 'ztmp'.$user_id.'_trading';
$serialtable = 'ztmp'.$user_id.'_serialnos';

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

$query = "drop table if exists ".$tradetable;
$result = mysql_query($query) or die(mysql_error());
$query = "drop table if exists ".$serialtable;
$result = mysql_query($query) or die(mysql_error());

$query = "create table ".$tradetable." ( uid int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, itemcode varchar(30) default '',item varchar(100) default '',price decimal(16,2) default 0, unit varchar(20) default '',quantity decimal(16,2) default 0,tax decimal(16,2) default 0, value decimal(16,2) default 0, discount decimal(16,2) default 0, disctype char(1) default '', discamount decimal(16,2) default 0, tot decimal(16,2) default 0, taxindex int(11) default 0, taxtype char(3) default '', taxpcent decimal(5,2) default 0, sellacc int(11) default 0, sellbr char(4) default '', sellsub int(4) default 0, purchacc int(11) default 0, purchbr char(4) default '', purchsub int(4) default 0, groupid int(11) default 0, catid int(11) default 0, loc int(11) default 0 )  engine myisam";
$calc = mysql_query($query) or die(mysql_error());

$query = "create table ".$serialtable." ( uid int(11) primary key, itemcode varchar(30) default '',item varchar(100) default '',serialno varchar(50) default '', locationid int(11) default 0, location varchar(30) default '', ref_no varchar(15) default '', selected char(1) default 'N')  engine myisam";
$calc = mysql_query($query) or die(mysql_error());

// build records in temp trading table

if ($st1 != "") {
	$s = explode('~',$st1);
	$stkcode = $s[0];
	$stkname = $s[1];
	$stkunit = $s[2];
	$stax = $s[3];
	$sac = $s[4];
	$sbr = $s[5];
	$ssb = $s[6];
	$pac = $s[7];
	$pbr = $s[8];
	$psb = $s[9];
	$grp = $s[10];
	$cat = $s[11];
	$cost = $s[12];
	$setsell = $s[13];
	$trackserial = $s[14];
	$staxpcent = $s[15];
	
	$val = $amt1 * 100/(100 + $staxpcent);
	$st = $amt1 - $val;

	$q = "insert into ".$tradetable." (itemcode,item,price,unit,quantity,tax,value,tot,taxtype,taxpcent,sellacc,sellbr,sellsub,purchacc,purchbr,purchsub,groupid,catid,loc) values ('".$stkcode."','".$stkname."',".$cost.",'".$stkunit."',".$qty1.",".$st.",".$val.",".$amt1.",'".$stax."',".$staxpcent.",".$sac.",'".$sbr."',".$ssb.",".$pac.",'".$pbr."',".$psb.",".$grp.",".$cat.",1)";
$r = mysql_query($q) or die(mysql_error().' '.$q);
}

if ($st2 != "") {
	$s = explode('~',$st2);
	$stkcode = $s[0];
	$stkname = $s[1];
	$stkunit = $s[2];
	$stax = $s[3];
	$sac = $s[4];
	$sbr = $s[5];
	$ssb = $s[6];
	$pac = $s[7];
	$pbr = $s[8];
	$psb = $s[9];
	$grp = $s[10];
	$cat = $s[11];
	$cost = $s[12];
	$setsell = $s[13];
	$trackserial = $s[14];
	$staxpcent = $s[15];
	
	$val = $amt2 * 100/(100 + $staxpcent);
	$st = $amt2 - $val;
	
	$q = "insert into ".$tradetable." (itemcode,item,price,unit,quantity,tax,value,tot,taxtype,taxpcent,sellacc,sellbr,sellsub,purchacc,purchbr,purchsub,groupid,catid,loc) values ('".$stkcode."','".$stkname."',".$cost.",'".$stkunit."',".$qty2.",".$st.",".$val.",".$amt2.",'".$stax."',".$staxpcent.",".$sac.",'".$sbr."',".$ssb.",".$pac.",'".$pbr."',".$psb.",".$grp.",".$cat.",1)";
$r = mysql_query($q) or die(mysql_error().' '.$q);
}

if ($st3 != "") {
	$s = explode('~',$st3);
	$stkcode = $s[0];
	$stkname = $s[1];
	$stkunit = $s[2];
	$stax = $s[3];
	$sac = $s[4];
	$sbr = $s[5];
	$ssb = $s[6];
	$pac = $s[7];
	$pbr = $s[8];
	$psb = $s[9];
	$grp = $s[10];
	$cat = $s[11];
	$cost = $s[12];
	$setsell = $s[13];
	$trackserial = $s[14];
	$staxpcent = $s[15];
	
	$val = $amt3 * 100/(100 + $staxpcent);
	$st = $amt3 - $val;
	
	$q = "insert into ".$tradetable." (itemcode,item,price,unit,quantity,tax,value,tot,taxtype,taxpcent,sellacc,sellbr,sellsub,purchacc,purchbr,purchsub,groupid,catid,loc) values ('".$stkcode."','".$stkname."',".$cost.",'".$stkunit."',".$qty3.",".$st.",".$val.",".$amt3.",'".$stax."',".$staxpcent.",".$sac.",'".$sbr."',".$ssb.",".$pac.",'".$pbr."',".$psb.",".$grp.",".$cat.",1)";
$r = mysql_query($q) or die(mysql_error().' '.$q);
}

if ($st4 != "") {
	$s = explode('~',$st4);
	$stkcode = $s[0];
	$stkname = $s[1];
	$stkunit = $s[2];
	$stax = $s[3];
	$sac = $s[4];
	$sbr = $s[5];
	$ssb = $s[6];
	$pac = $s[7];
	$pbr = $s[8];
	$psb = $s[9];
	$grp = $s[10];
	$cat = $s[11];
	$cost = $s[12];
	$setsell = $s[13];
	$trackserial = $s[14];
	$staxpcent = $s[15];
	
	$val = $amt4 * 100/(100 + $staxpcent);
	$st = $amt4 - $val;
	
	$q = "insert into ".$tradetable." (itemcode,item,price,unit,quantity,tax,value,tot,taxtype,taxpcent,sellacc,sellbr,sellsub,purchacc,purchbr,purchsub,groupid,catid,loc) values ('".$stkcode."','".$stkname."',".$cost.",'".$stkunit."',".$qty4.",".$st.",".$val.",".$amt4.",'".$stax."',".$staxpcent.",".$sac.",'".$sbr."',".$ssb.",".$pac.",'".$pbr."',".$psb.",".$grp.",".$cat.",1)";
$r = mysql_query($q) or die(mysql_error().' '.$q);
}


// get next invoice number

$query = "lock tables numbers write";
$result = mysql_query($query) or die($query);
$query = "select inv from numbers";
$result = mysql_query($query) or die(mysql_error().' '.$query);
$row = mysql_fetch_array($result);
extract($row);
$refno = $inv + 1;
$query = "update numbers set inv = ".$refno;
$result = mysql_query($query) or die($query);
$query = "unlock tables";
$result = mysql_query($query) or die($query);
$a = explode('~',$acc);
$clientac = $a[1];
$clientsb = $a[2];
$clientname = $a[0];
$ref = 'INV'.$refno;

echo $ref;

?>

