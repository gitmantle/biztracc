<?php
// function to populate trading table with relevant delivery note lines
session_start();
$usersession = $_SESSION['usersession'];

require("../db.php");
$admindb = $_SESSION['s_admindb'];
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$dstring = $_REQUEST['dstring'];
if (strlen($dstring) == 0) {
	echo '<script>';
	echo 'alert("No Delivery Notes selected");';
	echo 'this.close();';
	echo '</script>';
	return false;
}

$alines = explode(',',$dstring);

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());
$fdb = $moduledb;

$table = 'ztmp'.$user_id.'_trading';

$query = "drop table if exists ".$table;
$result = mysql_query($query) or die(mysql_error());

$query = "create table ".$table." ( uid int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, itemcode varchar(30) default '',item varchar(100) default '',price decimal(16,2) default 0, unit varchar(20) default '',quantity decimal(16,2) default 0,tax decimal(16,2) default 0, value decimal(16,2) default 0, discount decimal(16,2) default 0, disctype char(1) default '', discamount decimal(16,2) default 0, tot decimal(16,2) default 0, taxindex int(11) default 0, taxtype char(3) default '', taxpcent decimal(5,2) default 0, sellacc int(11) default 0, sellbr char(4) default '', sellsub int(4) default 0, purchacc int(11) default 0, purchbr char(4) default '', purchsub int(4) default 0, groupid int(11) default 0, catid int(11) default 0, loc int(11) default 0 )  engine myisam";
$calc = mysql_query($query) or die(mysql_error());


foreach ($alines as $itm) {
	$moduledb = $_SESSION['s_cltdb'];
	mysql_select_db($moduledb) or die(mysql_error());
	
	$qc = "select quotelines.itemcode,quotelines.item,quotelines.price,quotelines.unit,quotelines.quantity,quotelines.tax,quotelines.value,quotelines.taxindex,quotelines.taxtype,quotelines.taxpcent from quotelines,quotes where quotelines.ref_no = quotes.ref_no and quotes.uid = ".$itm;
	$rc = mysql_query($qc) or die(mysql_error().' '.$qc);
	
	echo $qc;
	
	
	while ($row = mysql_fetch_array($rc)) {
		extract($row);
		$citemcode = $itemcode;
		$citem = $item;
		$cprice = $price;
		$cunit = $unit;
		$cquantity = $quantity;
		$ctax = $tax;
		$cvalue = $value;
		$ctot = $cvalue + $ctax;
		$ctaxindex = $taxindex;
		$ctaxtype = $taxtype;
		$ctaxpcent = $taxpcent;
		
		$moduledb = $_SESSION['s_findb'];
		mysql_select_db($moduledb) or die(mysql_error());
		
		// get default sales account for each stock item
		$qs = "select sellacc,sellbr,sellsub,groupid,catid from stkmast where itemcode = '".$citemcode."'";
		$rs = mysql_query($qs) or die(mysql_error());
		$row = mysql_fetch_array($rs);
		extract($row);
		
		
		$qf = "insert into ".$table." (itemcode,item,price,unit,quantity,tax,value,tot,taxindex,taxtype,taxpcent,sellacc,sellbr,sellsub,groupid,catid) values ('".$citemcode."','".$citem."',".$cprice.",'".$cunit."',".$cquantity.",".$ctax.",".$cvalue.",".$ctot.",".$ctaxindex.",'".$ctaxtype."',".$ctaxpcent.",".$sellacc.",'".$sellbr."',".$sellsub.",".$groupid.",".$catid.")";	
		$rf = mysql_query($qf) or die(mysql_error().' '.$qf);
	}
	
}


?>
