<?php
session_start();
$coyno = $_SESSION['s_coyid'];

date_default_timezone_set($_SESSION['s_timezone']);

$ddate = date("d/m/Y");
$ddateh = date("Y-m-d");
$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
require("../../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$table = 'ztmp'.$user_id.'_trading';
$serialtable = 'ztmp'.$user_id.'_serialnos';

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

$query = "drop table if exists ".$table;
$result = mysql_query($query) or die(mysql_error());
$query = "drop table if exists ".$serialtable;
$result = mysql_query($query) or die(mysql_error());

$query = "create table ".$table." ( uid int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, itemcode varchar(30) default '',item varchar(100) default '',price decimal(16,2) default 0, unit varchar(20) default '',quantity decimal(16,2) default 0,tax decimal(16,2) default 0, value decimal(16,2) default 0, discount decimal(16,2) default 0, disctype char(1) default '', discamount decimal(16,2) default 0, taxindex int(11) default 0, taxtype char(3) default '', taxpcent decimal(5,2) default 0, sellacc int(11) default 0, sellbr char(4) default '', sellsub int(4) default 0, purchacc int(11) default 0, purchbr char(4) default '', purchsub int(4) default 0, groupid int(11) default 0, catid int(11) default 0 )  engine myisam";
$calc = mysql_query($query) or die(mysql_error());

$query = "create table ".$serialtable." (itemcode varchar(30) default '',item varchar(100) default '',serialno varchar(50) default '', locationid int(11) default 0, location varchar(30) default '', ref_no varchar(15) default '', selected char(1) default 'N')  engine myisam";
$calc = mysql_query($query) or die(mysql_error());

//get next grn number
$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());
$query = "lock tables numbers write";
$result = mysql_query($query) or die($query);
$query = "select grn from numbers";
$result = mysql_query($query) or die($query);
$row = mysql_fetch_array($result);
extract($row);
$refno = $grn + 1;
$postgrn = 'GRN'.$refno;
$query = "unlock tables";
$result = mysql_query($query) or die($query);

$cid = $_REQUEST['cid'];
$postgst = $_REQUEST['postgst'];

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

//type:type,ddate:ddate,descript:descript,ref:ref,acc:acc,asb:asb,loc:loc,paytype:paytype,postaladdress:postaladdress,deliveryaddress:deliveryaddress,clt:clt
$q = "select truckbranch,trailerbranch,costid,date,supplierid,supplier,description,supplierref,driverid from costheader where uid = ".$cid;
$r = mysql_query($q) or die(mysql_error().' '.$q);
$row = mysql_fetch_array($r);
extract($row);
$type = 'GRN';
$chcostid = $costid;
$ddate = $date;
$descript = $description;
$sid = split('~',$supplierid);
$did = $driverid;
$acc = $sid[0];
$asb = $sid[1];
$loc = 1;
$paytype = "";
$postaladdress = "";
$deliveryaddress = "";
$clt = $supplier;
if ($truckbranch != '') {
	$br = $truckbranch;
}
if ($trailerbranch != '') {
	$br = $trailerbranch;
}

$q = "select * from costlines where costid = ".$chcostid;
$r = mysql_query($q) or die(mysql_error().' '.$q);
while ($row = mysql_fetch_array($r)) {
	extract($row);
	$categoryid = $catid;
	$icode = $itemcode;

	$moduledb = $_SESSION['s_cltdb'];
	mysql_select_db($moduledb) or die(mysql_error());

	$moduledb = $_SESSION['s_findb'];
	mysql_select_db($moduledb) or die(mysql_error());
	
	$query = "select tax,taxpcent from taxtypes where tax = 'GST'";
	$result = mysql_query($query) or die($query);
	$row = mysql_fetch_array($result);
	extract($row);
	$taxtype = $tax;
	
	$query = "select groupid from stkcategory where catid = ".$categoryid;
	$result = mysql_query($query) or die($query);
	$row = mysql_fetch_array($result);
	extract($row);
	$gid = $groupid;
	
	$query = "select purchacc,purchsub from stkmast where itemcode = '".$icode."'";
	$result = mysql_query($query) or die($query);
	$row = mysql_fetch_array($result);
	extract($row);
	$pacc = $purchacc;
	$psub = $purchsub;
	
	
	$sql = "insert into ".$table." (itemcode,item,price,unit,quantity,tax,value,taxtype,taxpcent,sellacc,sellbr,sellsub,purchacc,purchbr,purchsub,groupid,catid) values (";
	$sql .= "'".$icode."',";
	$sql .= "'".$item."',";
	$sql .= $unitcost.",";
	$sql .= "'Each',";
	$sql .= $quantity.",";
	$sql .= $gst.",";
	$sql .= $total.",";
	$sql .= "'".$taxtype."',";
	$sql .= $taxpcent.",";
	$sql .= "0,";
	$sql .= "'',";
	$sql .= "0,";
	$sql .= $pacc.",";
	$sql .= "'".$br."',";
	$sql .= $psub.",";
	$sql .= $gid.",";
	$sql .= $catid.")";
	
	$result = mysql_query($sql) or die(mysql_error().' - '.$sql);
	
	$moduledb = $_SESSION['s_logdb'];
	mysql_select_db($moduledb) or die(mysql_error());
	
}

// get serial number for tyres if relevant
$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());
$rf = 'ORC'.$chcostid;
$q = "select * from tyres where refno = '".$rf."'";
$r = mysql_query($q) or die(mysql_error().' - '.$q);
$numrows = mysql_num_rows($r);
if ($numrows > 0) {
	while ($row = mysql_fetch_array($r)) {
		extract($row);
		$moduledb = $_SESSION['s_findb'];
		mysql_select_db($moduledb) or die(mysql_error());
		$qi = "insert into ".$serialtable." (itemcode,item,serialno,location) values (";
		$qi .= "'".$itemcode."',";
		$qi .= "'".$item."',";
		$qi .= "'".$serialno."',";
		$qi .= "'".$br."')";
		$ri = mysql_query($qi) or die(mysql_error().' - '.$qi);
		
		$moduledb = $_SESSION['s_logdb'];
		mysql_select_db($moduledb) or die(mysql_error());

	}
}


$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());
$query = "lock tables numbers write";
$result = mysql_query($query) or die($query);
$query = "select grn,req from numbers";
$result = mysql_query($query) or die($query);
$row = mysql_fetch_array($result);
extract($row);
$refno = $grn + 1;
$reqrefno = $req + 1;
$query = "update numbers set grn = ".$refno.", req = ".$reqrefno;
$result = mysql_query($query) or die($query);
$query = "unlock tables";
$result = mysql_query($query) or die($query);
				
$ref = 'GRN'.$refno;
$refq ='REQ'.$reqrefno;
/*
//type:type,ddate:ddate,descript:descript,ref:ref,acc:acc,asb:asb,loc:loc,paytype:paytype,postaladdress:postaladdress,deliveryaddress:deliveryaddress,clt:clt
require("../../fin/includes/ajaxPostTrade.php?type=".$type."&ddate=".$ddate."&descript=".$descript."&ref=".$ref."&acc=".$acc."&asb=".$asb."&loc=".$loc."&paytype=N&postaddress=NA&deliveryaddress=NA&clt=".$clt);
*/

//**************************************************************************************
// post GRN transactions
//**************************************************************************************

//type:type,ddate:ddate,descript:descript,ref:ref,acc:acc,asb:asb,loc:loc,postaladdress:postaladdres,deliveryaddress:deliveryaddress,client:clt
$transtype = 'GRN';
$ddate = $ddate;
$descript1 = $descript;
$reference = $ref;
$acc = $acc;
$asb = $asb;
$loc = 1;
$paytype = 'N';
$xref = '';
$postaladdress = 'NA';
$deliveryaddress = 'NA';
$client = $clt;
$defbranch = '0001';

// work out current,d30,d60,d90,d120
$today = date('Y-m-d');
$date1 = new DateTime($ddate);
$date2 = new DateTime($today);
$interval = $date1->diff($date2);
$days = $interval->days;
	
if ($days < 31) {
	$aged = 'Current';
}
if ($days > 30 && $days < 61) {
	$aged = 'D30';
}
if ($days > 60 && $days < 91) {
	$aged = 'D60';
}
if ($days > 90) {
	$aged = 'D120';
}

$a2cr = $acc;
$b2cr = '';
$s2cr = $asb;
$a2dr = 0;
$b2dr = '';
$s2dr = 0;

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select sum(tax) as totaltax, sum(value) as totalamount from ".$table;
$r = mysql_query($q) or die(mysql_error().' '.$q);
$row = mysql_fetch_array($r);
extract($row);
$totalvalue = $totalamount + $totaltax;

$cash = 0;
$cheque = 0;
$mcard = 0;
$ecard = 0;
if (empty($b2dr)) {$br2dr = ' ';}
if (empty($s2dr)) {$sub2dr = 0;}
if (empty($b2cr)) {$br2cr = ' ';}
if (empty($s2cr)) {$sub2cr = 0;}	
	
$ano = $a2cr;
$bno = $b2cr;
$sno = $s2cr;
$taxdrcr = 'dr';


	// insert invhead record
	$sSQLString = "insert into invhead (ddate,accountno,branch,sub,gldesc,transtype,ref_no,xref,totvalue,tax,cash,cheque,eftpos,ccard,staff,postaladdress,deliveryaddress,client) values ";
	$sSQLString .= "('".$ddate."',";
	$sSQLString .= $ano.",'";
	$sSQLString .= " "."',";
	$sSQLString .= $sno.",'";
	$sSQLString .= $descript1."','";
	$sSQLString .= $transtype."','";
	$sSQLString .= $reference."','";
	$sSQLString .= $xref."',";
	$sSQLString .= $totalamount.",";
	$sSQLString .= $totaltax.",";
	$sSQLString .= $cash.",";
	$sSQLString .= $cheque.",";
	$sSQLString .= $mcard.",";
	$sSQLString .= $ecard.",'";
	$sSQLString .= $uname."','";
	$sSQLString .= $postaladdress."','";
	$sSQLString .= $deliveryaddress."','";
	$sSQLString .= $client."')";
	

	$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);

	$q = "select * from ".$table;
	$result = mysql_query($q) or die(mysql_error());
	while ($row = mysql_fetch_array($result)) {
		extract($row);
		
		// insert records into invtrans
		
			$sSQLString = "insert into invtrans (itemcode,item,price,quantity,unit,taxtype,taxpcent,tax,ref_no,value,discount,disc_type) values ";
			$sSQLString .= "('".$itemcode."','";
			$sSQLString .= $item."',";
			$sSQLString .= $price.",";
			$sSQLString .= $quantity.",'";
			$sSQLString .= $unit."','";
			$sSQLString .= $taxtype."',";
			$sSQLString .= $taxpcent.",";
			$sSQLString .= $tax.",'";
			$sSQLString .= $reference."',";
			$sSQLString .= $value.",";
			$sSQLString .= $discount.",";
			$sSQLString .= "'".$disctype."')";
			
			$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
		
	}

	// Create entries between closing stock and stock on hand
	if ($transtype == 'INV' || $transtype == 'C_S' || $transtype == 'REQ' || $transtype == 'RET') {
		
		$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
		$sSQLString .= "('".$ddate."',";
		$sSQLString .= "187,";		// debit closing stock
		$sSQLString .= "'".$defbranch."',";			
		$sSQLString .= "0,";
		$sSQLString .= '825'.",";
		$sSQLString .= "'".$defbranch."',";			
		$sSQLString .= '0'.",";			
		$sSQLString .= $totalamount.",";		// with the amount excluding GST
		$sSQLString .= "0,'";
		$sSQLString .= $reference."','";					
		$sSQLString .= $taxtype."','";
		$sSQLString .= $descript1."',";
		$sSQLString .= $taxpcent.")";			
			
		$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
		
		$sql = "update glmast set obal = obal + ".$totalamount." where accountno = 187 and branch = '".$defbranch."' and sub = 0";
		$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
		
		
		$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
		$sSQLString .= "('".$ddate."',";
		$sSQLString .= "825,";		// credit stock on hand
		$sSQLString .= "'".$defbranch."',";			
		$sSQLString .= "0,";
		$sSQLString .= '187'.",";
		$sSQLString .= "'".$defbranch."',";			
		$sSQLString .= '0'.",";			
		$sSQLString .= "0,";		
		$sSQLString .= $totalamount.",'"; // with the amount excluding GST
		$sSQLString .= $reference."','";					
		$sSQLString .= $taxtype."','";
		$sSQLString .= $descript1."',";
		$sSQLString .= $taxpcent.")";			
			
		$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
		
		$sql = "update glmast set obal = obal - ".$totalamount." where accountno = 825 and branch = '".$defbranch."' and sub = 0";
		$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
		
		
	} else {
		
		$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
		$sSQLString .= "('".$ddate."',";
		$sSQLString .= "825,";		// debit stock on hand
		$sSQLString .= "'".$defbranch."',";			
		$sSQLString .= "0,";
		$sSQLString .= '187'.",";
		$sSQLString .= "'".$defbranch."',";			
		$sSQLString .= '0'.",";			
		$sSQLString .= $totalamount.",";		// with the amount excluding GST
		$sSQLString .= "0,'";
		$sSQLString .= $reference."','";					
		$sSQLString .= $taxtype."','";
		$sSQLString .= $descript1."',";
		$sSQLString .= $taxpcent.")";			
			
		$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
		
		$sql = "update glmast set obal = obal + ".$totalamount." where accountno = 825 and branch = '".$defbranch."' and sub = 0";
		$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
		
		
		$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
		$sSQLString .= "('".$ddate."',";
		$sSQLString .= "187,";		// credit closing stock
		$sSQLString .= "'".$defbranch."',";			
		$sSQLString .= "0,";
		$sSQLString .= '825'.",";
		$sSQLString .= "'".$defbranch."',";			
		$sSQLString .= '0'.",";			
		$sSQLString .= "0,";		
		$sSQLString .= $totalamount.",'"; // with the amount excluding GST
		$sSQLString .= $reference."','";					
		$sSQLString .= $taxtype."','";
		$sSQLString .= $descript1."',";
		$sSQLString .= $taxpcent.")";			
			
		$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
		
		$sql = "update glmast set obal = obal - ".$totalamount." where accountno = 187 and branch = '".$defbranch."' and sub = 0";
		$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
		
	
	}
	


	// insert GL records	
	
	
	//*******************************************************************************************************************************************
	// Goods Received
	//*******************************************************************************************************************************************
	if ($transtype == 'GRN') {
			// credit creditor account
			$sSQLString = "insert into trmain (ddate,accountno,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
			$sSQLString .= "('".$ddate."',";
			$sSQLString .= $a2cr.",";		// credit the creditor
			$sSQLString .= $s2cr.",";
			$sSQLString .= '0'.",'";
			$sSQLString .= ' '."',";			
			$sSQLString .= '0'.",";			
			$sSQLString .= "0,";		
			$sSQLString .= $totalvalue.",'";  // with the amount including GST
			$sSQLString .= $reference."','";					
			$sSQLString .= $taxtype."','";
			$sSQLString .= $descript1." ".$item."',";
			$sSQLString .= $taxpcent.")";			
			
			$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
			
			$cltdb = $_SESSION['s_cltdb'];
			mysql_select_db($cltdb) or die(mysql_error());	
			
			if ($aged == 'Current') {
				$sql = "update client_company_xref set current = current - ".$totalvalue." where company_id = ".$coyno." and crno = ".$a2cr." and crsub = ".$s2cr;
			}
			if ($aged == 'D30') {
				$sql = "update client_company_xref set d30 = d30 - ".$totalvalue." where company_id = ".$coyno." and crno = ".$a2cr." and crsub = ".$s2cr;
			}
			if ($aged == 'D60') {
				$sql = "update client_company_xref set d60 = d60 - ".$totalvalue." where company_id = ".$coyno." and crno = ".$a2cr." and crsub = ".$s2cr;
			}
			if ($aged == 'D90') {
				$sql = "update client_company_xref set d90 = d90 - ".$totalvalue." where company_id = ".$coyno." and crno = ".$a2cr." and crsub = ".$s2cr;
			}
			if ($aged == 'D120') {
				$sql = "update client_company_xref set d120 = d120 - ".$totalvalue." where company_id = ".$coyno." and crno = ".$a2cr." and crsub = ".$s2cr;
			}
			
			$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
			
			$moduledb = $_SESSION['s_findb'];
			mysql_select_db($moduledb) or die(mysql_error());	
			
			
			// credit creditor control account
			$sSQLString = "insert into trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,descript1,taxpcent) values ";
			$sSQLString .= "('".$ddate."',";
			$sSQLString .= "851,'";		// credit creditor control
			$sSQLString .= $defbranch."',";
			$sSQLString .= '0'.",'";
			$sSQLString .= ' '."',";	
			$sSQLString .= "0,";		// with the amount including GST
			$sSQLString .= $totalvalue.",'";
			$sSQLString .= $reference."','";					
			$sSQLString .= $taxtype."','";
			$sSQLString .= $descript1." ".$item."',";
			$sSQLString .= $taxpcent.")";					
			
			$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
			
				
			$sql = "update glmast set obal = obal - ".$totalamount." where accountno = 851 and branch = '".$defbranch."' and sub = 0";
			$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);	
			
			// debit relevant cos accounts
			$q = "select * from ".$table;
			$result = mysql_query($q) or die(mysql_error());
			while ($row = mysql_fetch_array($result)) {
				extract($row);
				$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
				$sSQLString .= "('".$ddate."',";
				$sSQLString .= $purchacc.",";		//debit the cos account
				$sSQLString .= "'".$defbranch."',";
				$sSQLString .= $purchsub.",";
				$sSQLString .= $a2cr.",'";
				$sSQLString .= ' '."',";			
				$sSQLString .= $s2cr.",";			
				$sSQLString .= $value.",";		// with the amount excluding GST
				$sSQLString .= "0,'";
				$sSQLString .= $reference."','";					
				$sSQLString .= $taxtype."','";
				$sSQLString .= $descript1." ".$item."',";
				$sSQLString .= $taxpcent.")";			
				
				$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
				
				$sql = "update glmast set obal = obal + ".$totalamount." where accountno = ".$purchacc." and branch = '".$defbranch."' and sub = ".$purchsub;
				$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);	

			}
	}
	
	
	//*************************************************************************************************************************************************************
	// Create entry relating to GST for each line of trading document
	//*************************************************************************************************************************************************************
						
	$q = "select * from ".$table;
	$result = mysql_query($q) or die(mysql_error());
	while ($row = mysql_fetch_array($result)) {
		extract($row);
		
		// insert each line's tax where applicable into trmain
		if ($tax > 0) {

				$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
				$sSQLString .= "('".$ddate."',";
				$sSQLString .= "870,'";		// debit GST payable
				$sSQLString .= $defbranch."',";
				$sSQLString .= "0,";
				$sSQLString .= $a2cr.",'";
				$sSQLString .= $defbranch."',";	
				$sSQLString .= $s2cr.",";
				$sSQLString .= $tax.",";		// with the amount of the GST	
				$sSQLString .= "0,'";		
				$sSQLString .= $reference."','";					
				$sSQLString .= $taxtype."','";
				$sSQLString .= $descript1." ".$item."',";
				$sSQLString .= $taxpcent.")";					
				
			$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
	
		} //if
		
	
	} //while

	//**************************************************************************************************************************************************************************
	// add serial numbers if applicable
	//**************************************************************************************************************************************************************************
			$qs = "select * from ".$serialtable;
			$rs = mysql_query($qs) or die(mysql_error().' '.$qs);
			$numrows = mysql_num_rows($rs);
			if ($numrows > 0) {
				while ($row = mysql_fetch_array($rs)) {
					extract($row);
					
					// get driver
					$moduledb = $_SESSION['s_admindb'];
					mysql_select_db($moduledb) or die(mysql_error());
					
					$qv = "select concat(ufname,' ',ulname) as drivername from users where uid = ".$did;
					$rv = mysql_query($qv) or die(mysql_error().' '.$qv);
					$row = mysql_fetch_array($rv);
					extract($row);
					$desc = 'Bought by '.$drivername;
					
					$moduledb = $_SESSION['s_findb'];
					mysql_select_db($moduledb) or die(mysql_error());
					
					$qi = "insert into stkserials (itemcode,item,serialno,locationid,ref_no,sold,branch,date,activity) values (";
					$qi .= "'".$itemcode."','".$item."','".$serialno."',".$locationid.",'".$reference."','','".$defbranch."','".$ddate."','".$desc."')";
					$ri = mysql_query($qi) or die(mysql_error().' '.$qi);
					
					if ($transtype == 'INV' || $transtype == 'C_S' || $transtype == 'REQ' || $transtype == 'CRN') {
						$qu = "update stkserials set sold = '".$reference."' where serialno = '".$serialno."'";
						$ru = mysql_query($qu) or die(mysql_error().' '.$qu);
					}
				}
			}


	//***********************************************************************************************************************************
	// Create entries in stktrans for each stock recorded item
	//***********************************************************************************************************************************
	$q = "select * from ".$table;
	$result = mysql_query($q) or die(mysql_error());
	while ($row = mysql_fetch_array($result)) {
		extract($row);
		if ($transtype == 'INV' || $transtype == 'C_S' || $transtype == 'REQ' || $transtype == 'RET') {
			$increase = 0;
			$decrease = $quantity;
			$sql = "update stkmast set onhand = onhand - ".$decrease." where itemcode = '".$itemcode."'";
			$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);	
		} else {
			$increase = $quantity;
			$decrease = 0;
			$sql = "update stkmast set onhand = onhand + ".$increase." where itemcode = '".$itemcode."'";
			$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);
			if ($transtype == 'GRN' && $value == 0) {
				$sqlc = "update stkmast set uncosted = uncosted + ".$increase." where itemcode = '".$itemcode."'";
				$rsqlc = mysql_query($sqlc) or die(mysql_error().' '.$sqlc);
			} //if
			
			// calculate new average cost
			if ($transtype == 'GRN') {
				if ($value > 0) {
					$qav = "select avgcost,onhand-uncosted as tqty from stkmast where itemcode = '".$itemcode."'";
					$rav = mysql_query($qav) or die(mysql_error().' '.$qav);
					$row = mysql_fetch_array($rav);
					extract($row);
					$newtotval = ($avgcost*$tqty) + $value;
					$newtotqty = $tqty ;
					$newavgcost = $newtotval/$newtotqty;
					$sqla = "update stkmast set avgcost = ".$newavgcost." where itemcode = '".$itemcode."'";
					$rsqla = mysql_query($sqla) or die(mysql_error().' '.$sqla);
				}
				
			} //if
			
		} // if
		
		
		$sSQLString = "insert into stktrans (groupid,catid,itemcode,item,locid,ddate,increase,decrease,ref_no,transtype,amount) values ";
		$sSQLString .= "(".$groupid.",";
		$sSQLString .= $catid.",";
		$sSQLString .= "'".$itemcode."','";
		$sSQLString .= $item."','";
		$sSQLString .= $loc."','";
		$sSQLString .= $ddate."',";
		$sSQLString .= $increase.",";
		$sSQLString .= $decrease.",'";
		$sSQLString .= $reference."','";
		$sSQLString .= $transtype."',";
		$sSQLString .= $value.")";
		
		$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
		
	} // while




	//**************************************************************************************************************************************************************************
	// add transaction to audit trail
	//**************************************************************************************************************************************************************************
			$sSQLString = "insert into audit (ddate,acc2dr,brdr,subdr,acc2cr,brcr,subcr,descript1,reference,amount,tax,total,entrydate,entrytime,username,userip) values ";
			$sSQLString .= "('".$ddate."',";
			$sSQLString .= $a2dr.",'";		
			$sSQLString .= $defbranch."',";
			$sSQLString .= $s2dr.",";
			$sSQLString .= $a2cr.",'";
			$sSQLString .= $defbranch."',";			
			$sSQLString .= $s2cr.",'";			
			$sSQLString .= $descript1." ".$item."','";
			$sSQLString .= $reference."',";		
			$sSQLString .= $totalamount.",";					
			$sSQLString .= $totaltax.",";
			$sSQLString .= $totalvalue.",'";
			$sSQLString .= date("Y-m-d")."','";
			$sSQLString .= date("H:i:s")."','";				
			$sSQLString .= $unm."','";
			$sSQLString .= $uip."')";		
			
			$r35 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);





//**************************************************************************************
// post REQ transactions
//**************************************************************************************
$transtype = 'REQ';

$a2dr = $acc;
$b2dr = '';
$s2dr = $asb;
$a2cr = 0;
$b2cr = '';
$s2cr = 0;

$ano = $a2dr;
$bno = $b2dr;
$sno = $s2dr;
$taxdrcr = '';

$cash = 0;
$cheque = 0;
$eftpos = 0;
$ccard = 0;

	// insert invhead record
	$sSQLString = "insert into invhead (ddate,accountno,branch,sub,gldesc,transtype,ref_no,xref,totvalue,tax,cash,cheque,eftpos,ccard,staff,postaladdress,deliveryaddress,client) values ";
	$sSQLString .= "('".$ddate."',";
	$sSQLString .= $ano.",'";
	$sSQLString .= $bno."',";
	$sSQLString .= $sno.",'";
	$sSQLString .= $descript1."','";
	$sSQLString .= $transtype."','";
	$sSQLString .= $refq."','";
	$sSQLString .= $xref."',";
	$sSQLString .= $totalamount.",";
	$sSQLString .= $totaltax.",";
	$sSQLString .= $cash.",";
	$sSQLString .= $cheque.",";
	$sSQLString .= $eftpos.",";
	$sSQLString .= $ccard.",'";
	$sSQLString .= $uname."','";
	$sSQLString .= $postaladdress."','";
	$sSQLString .= $deliveryaddress."','";
	$sSQLString .= $client."')";
	

	$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
		
		
	$q = "select * from ".$table;
	$result = mysql_query($q) or die(mysql_error());
	while ($row = mysql_fetch_array($result)) {
		extract($row);
		
		$pbr = $purchbr;
		
		// insert records into invtrans
		
			$sSQLString = "insert into invtrans (itemcode,item,price,quantity,unit,taxtype,taxpcent,tax,ref_no,value,discount,disc_type,grnlineno) values ";
			$sSQLString .= "('".$itemcode."','";
			$sSQLString .= $item."',";
			$sSQLString .= $price.",";
			$sSQLString .= $quantity.",'";
			$sSQLString .= $unit."','";
			$sSQLString .= $taxtype."',";
			$sSQLString .= $taxpcent.",";
			$sSQLString .= $tax.",'";
			$sSQLString .= $refq."',";
			$sSQLString .= $value.",";
			$sSQLString .= $discount.",";
			$sSQLString .= "'".$disctype."',";
			$sSQLString .= $uid.")";
			
			$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
		
	}

		$qq = "update invhead set branch = '".$pbr."' where ref_no = '".$reference."'";
		$rq = mysql_query($qq) or die(mysql_error());

	// insert GL records	
	
	// Create entries between closing stock and stock on hand
	if ($transtype == 'INV' || $transtype == 'C_S' || $transtype == 'REQ' || $transtype == 'RET') {
		
		$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
		$sSQLString .= "('".$ddate."',";
		$sSQLString .= "187,";		// debit closing stock
		$sSQLString .= "'".$defbranch."',";			
		$sSQLString .= "0,";
		$sSQLString .= '825'.",";
		$sSQLString .= "'".$defbranch."',";			
		$sSQLString .= '0'.",";			
		$sSQLString .= $totalamount.",";		// with the amount excluding GST
		$sSQLString .= "0,'";
		$sSQLString .= $refq."','";					
		$sSQLString .= $taxtype."','";
		$sSQLString .= $descript1."',";
		$sSQLString .= $taxpcent.")";			
			
		$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
		
		$sql = "update glmast set obal = obal + ".$totalamount." where accountno = 187 and branch = '".$defbranch."' and sub = 0";
		$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
		
		
		$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
		$sSQLString .= "('".$ddate."',";
		$sSQLString .= "825,";		// credit stock on hand
		$sSQLString .= "'".$defbranch."',";			
		$sSQLString .= "0,";
		$sSQLString .= '187'.",";
		$sSQLString .= "'".$defbranch."',";			
		$sSQLString .= '0'.",";			
		$sSQLString .= "0,";		
		$sSQLString .= $totalamount.",'"; // with the amount excluding GST
		$sSQLString .= $refq."','";					
		$sSQLString .= $taxtype."','";
		$sSQLString .= $descript1."',";
		$sSQLString .= $taxpcent.")";			
			
		$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
		
		$sql = "update glmast set obal = obal - ".$totalamount." where accountno = 825 and branch = '".$defbranch."' and sub = 0";
		$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
		
		
	} else {
		
		$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
		$sSQLString .= "('".$ddate."',";
		$sSQLString .= "825,";		// debit stock on hand
		$sSQLString .= "'".$defbranch."',";			
		$sSQLString .= "0,";
		$sSQLString .= '187'.",";
		$sSQLString .= "'".$defbranch."',";			
		$sSQLString .= '0'.",";			
		$sSQLString .= $totalamount.",";		// with the amount excluding GST
		$sSQLString .= "0,'";
		$sSQLString .= $refq."','";					
		$sSQLString .= $taxtype."','";
		$sSQLString .= $descript1."',";
		$sSQLString .= $taxpcent.")";			
			
		$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
		
		$sql = "update glmast set obal = obal + ".$totalamount." where accountno = 825 and branch = '".$defbranch."' and sub = 0";
		$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
		
		
		$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
		$sSQLString .= "('".$ddate."',";
		$sSQLString .= "187,";		// credit closing stock
		$sSQLString .= "'".$defbranch."',";			
		$sSQLString .= "0,";
		$sSQLString .= '825'.",";
		$sSQLString .= "'".$defbranch."',";			
		$sSQLString .= '0'.",";			
		$sSQLString .= "0,";		
		$sSQLString .= $totalamount.",'"; // with the amount excluding GST
		$sSQLString .= $refq."','";					
		$sSQLString .= $taxtype."','";
		$sSQLString .= $descript1."',";
		$sSQLString .= $taxpcent.")";			
			
		$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
		
		$sql = "update glmast set obal = obal - ".$totalamount." where accountno = 187 and branch = '".$defbranch."' and sub = 0";
		$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
		
	
	}
	
	//*******************************************************************************************************************************************
	// Requisition
	//*******************************************************************************************************************************************
	if ($transtype == 'REQ') {
		$q = "select * from ".$table;
		$result = mysql_query($q) or die(mysql_error());
		while ($row = mysql_fetch_array($result)) {
			extract($row);

			// debit relevant cos account
			$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
			$sSQLString .= "('".$ddate."',";
			$sSQLString .= $purchacc.",";		// debit the debtor
			$sSQLString .= "'".$purchbr."',";			
			$sSQLString .= $purchsub.",";
			$sSQLString .= $purchacc.",";
			$sSQLString .= "'".$defbranch."',";			
			$sSQLString .= $purchsub.",";			
			$sSQLString .= $value.",";		// with the amount including GST
			$sSQLString .= "0,'";
			$sSQLString .= $refq."','";					
			$sSQLString .= $taxtype."','";
			$sSQLString .= $descript1." ".$item."',";
			$sSQLString .= $taxpcent.")";			
			
			$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
			
			$sql = "update glmast set obal = obal + ".$totalamount." where accountno = ".$purchacc." and branch = '".$purchbr."' and sub = ".$purchsub;
			$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);	
			
			// credit relevant cos account
			$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
			$sSQLString .= "('".$ddate."',";
			$sSQLString .= $purchacc.",";		//credit the income account
			$sSQLString .= "'".$defbranch."',";
			$sSQLString .= $purchsub.",";
			$sSQLString .= $purchacc.",";
			$sSQLString .= "'".$purchbr."',";
			$sSQLString .= $purchsub.",";			
			$sSQLString .= "0,";		
			$sSQLString .= $value.",'";  // with the amount excluding GST
			$sSQLString .= $refq."','";					
			$sSQLString .= $taxtype."','";
			$sSQLString .= $descript1." ".$item."',";
			$sSQLString .= $taxpcent.")";			
				
			$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
				
			$sql = "update glmast set obal = obal - ".$value." where accountno = ".$purchacc." and branch = '".$defbranch."' and sub = ".$purchsub;
			$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);	

	
			// Inter branch Transactions
			if ($purchbr != $defbranch) {
			
				// credit inter branch transfer account
				$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
				$sSQLString .= "('".$ddate."',";
				$sSQLString .= "997".",'";		
				$sSQLString .= $purchbr."',";
				$sSQLString .= "0".",";
				$sSQLString .= $purchacc.",'";
				$sSQLString .= $defbranch."',";			
				$sSQLString .= $purchsub.",";		
				$sSQLString .= "0,";
				$sSQLString .= $value.",'";  // with the amount excluding GST
				$sSQLString .= $refq."','";					
				$sSQLString .= $taxtype."','";
				$sSQLString .= $descript1." ".$item."',";
				$sSQLString .= $taxpcent.")";			
				
				$r33 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
				
				$sql = "update glmast set obal = obal - ".$value." where accountno = 997 and branch = '".$purchbr."'";
				$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
				
				// debit inter branch transfer account
				$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
				$sSQLString .= "('".$ddate."',";
				$sSQLString .= "997".",'";		
				$sSQLString .= $defbranch."',";
				$sSQLString .= "0,";
				$sSQLString .= $purchacc.",'";
				$sSQLString .= $purchbr."',";			
				$sSQLString .= $purchsub.",";			
				$sSQLString .= $value.",";  // with the amount excluding GST
				$sSQLString .= "0,'";
				$sSQLString .= $refq."','";					
				$sSQLString .= $taxtype."','";
				$sSQLString .= $descript1." ".$item."',";
				$sSQLString .= $taxpcent.")";			
				
				$r34 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
				
				$sql = "update glmast set obal = obal + ".$value." where accountno = 997 and branch = '".$defbranch."'";
				$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
					
			} // if posting between different branches
					
		} // while
			
	} // if
	
	//***********************************************************************************************************************************
	// Create entries in stktrans for each stock recorded item
	//***********************************************************************************************************************************
	$q = "select * from ".$table;
	$result = mysql_query($q) or die(mysql_error());
	while ($row = mysql_fetch_array($result)) {
		extract($row);
		if ($transtype == 'INV' || $transtype == 'C_S' || $transtype == 'REQ' || $transtype == 'RET') {
			$increase = 0;
			$decrease = $quantity;
			$sql = "update stkmast set onhand = onhand - ".$decrease." where itemcode = '".$itemcode."'";
			$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);	
		} else {
			$increase = $quantity;
			$decrease = 0;
			$sql = "update stkmast set onhand = onhand + ".$increase." where itemcode = '".$itemcode."'";
			$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);
			if ($transtype == 'GRN' && $value == 0) {
				$sqlc = "update stkmast set uncosted = uncosted + ".$increase." where itemcode = '".$itemcode."'";
				$rsqlc = mysql_query($sqlc) or die(mysql_error().' '.$sqlc);
			} //if
			
			// calculate new average cost
			if ($transtype == 'GRN') {
				if ($value > 0) {
					$qav = "select avgcost,onhand-uncosted as tqty from stkmast where itemcode = '".$itemcode."'";
					$rav = mysql_query($qav) or die(mysql_error().' '.$qav);
					$row = mysql_fetch_array($rav);
					extract($row);
					$newtotval = ($avgcost*$tqty) + $value;
					$newtotqty = $tqty ;
					$newavgcost = $newtotval/$newtotqty;
					$sqla = "update stkmast set avgcost = ".$newavgcost." where itemcode = '".$itemcode."'";
					$rsqla = mysql_query($sqla) or die(mysql_error().' '.$sqla);
				}
				
			} //if
			
		} // if
		
		
		$sSQLString = "insert into stktrans (groupid,catid,itemcode,item,locid,ddate,increase,decrease,ref_no,transtype,amount) values ";
		$sSQLString .= "(".$groupid.",";
		$sSQLString .= $catid.",";
		$sSQLString .= "'".$itemcode."','";
		$sSQLString .= $item."','";
		$sSQLString .= $loc."','";
		$sSQLString .= $ddate."',";
		$sSQLString .= $increase.",";
		$sSQLString .= $decrease.",'";
		$sSQLString .= $refq."','";
		$sSQLString .= $transtype."',";
		$sSQLString .= $value.")";
		
		$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
		
	} // while

	//**************************************************************************************************************************************************************************
	// add serial numbers if applicable
	//**************************************************************************************************************************************************************************
			$qs = "select * from ".$serialtable;
			$rs = mysql_query($qs) or die(mysql_error().' '.$qs);
			$numrows = mysql_num_rows($rs);
			if ($numrows > 0) {
				while ($row = mysql_fetch_array($rs)) {
					extract($row);
					
					// get vehicle no
					$qv = "select branchname from branch where branch = '".$purchbr."'";
					$rv = mysql_query($qv) or die(mysql_error().' '.$qv);
					$row = mysql_fetch_array($rv);
					extract($row);
					$desc = 'Fit to '.$branchname;
					
					$qi = "insert into stkserials (itemcode,item,serialno,locationid,ref_no,sold,branch,date,activity) values (";
					$qi .= "'".$itemcode."','".$item."','".$serialno."',".$locationid.",'".$refq."','','".$purchbr."','".$ddate."','".$desc."')";
					$ri = mysql_query($qi) or die(mysql_error().' '.$qi);
					
					if ($transtype == 'INV' || $transtype == 'C_S' || $transtype == 'REQ' || $transtype == 'CRN') {
						$qu = "update stkserials set sold = '".$refq."' where serialno = '".$serialno."'";
						$ru = mysql_query($qu) or die(mysql_error().' '.$qu);
					}
				}
			}


$sql = "delete from ".$table;
$rst = mysql_query($sql) or die (mysql_error());
	

// if gst to all go to admin branch
if ($postgst == 'admin') {
	$postreference = $reference;
	$moduledb = $_SESSION['s_findb'];
	mysql_select_db($moduledb) or die(mysql_error());
			
	$q = "select debit,credit,branch,reference as ref,descript1,taxpcent,gsttype from trmain where accountno = 870 and (reference >= '".$postgrn."' and reference <= '".$postreference."')";
	$r = mysql_query($q) or die(mysql_error().' '.$q);
	while ($row = mysql_fetch_array($r)) {
		extract($row);
				
		if ($debit > 0) {
			$amt = $debit;
		} else {
			$amt = $credit;
		}
			
		$sql = "insert into ".$table." (acc2dr,subdr,brdr,acc2cr,subcr,brcr,ddate,descript1,reference,amount,tax,taxtype,taxpcent,applytax,total,debtor) values (";
		$sql .= "870,";
		$sql .= "0,";
		$sql .= "'0001',";
		$sql .= "870,";
		$sql .= "0,";
		$sql .= "'".$branch.",";
		$sql .= "'".$ddate."',";
		$sql .= "'".$descript1."',";
		$sql .= "'".$ref."',";
		$sql .= $amt.",";
		$sql .= "0,";
		$sql .= "'".$gsttype."',";
		$sql .= $taxpcent.",";
		$sql .= "'N',";
		$sql .= $amt.",";
		$sql .= "'')";
						
		$result = mysql_query($sql) or die(mysql_error().' - '.$sql);
			
	}
			
	include("../fin/includes/ajaxPostTrans.php");
			
}


//**************************************************************************************
// end of post transactions
//**************************************************************************************


$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "update costheader set posted = 'Y' where uid = ".$cid;
$r = mysql_query($q) or die(mysql_error().' '.$q);

?>




