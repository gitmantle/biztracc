<?php
session_start();

$dlist = $_REQUEST['dlist'];
date_default_timezone_set($_SESSION['s_timezone']);

require("../../db.php");
$admindb = $_SESSION['s_admindb'];
mysql_select_db($admindb) or die(mysql_error().' db not selected');

$usersession = $_SESSION['usersession'];

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$uip = $userip;
$unm = $uname;

$mdb = $_SESSION['s_prcdb'];
$cdb = $_SESSION['s_cltdb'];
$fdb = $_SESSION['s_findb'];

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

$serialtable = 'ztmp'.$user_id.'_serialnos';
$query = "drop table if exists ".$serialtable;
$result = mysql_query($query) or die(mysql_error());

$query = "create table ".$serialtable." (itemcode varchar(30) default '',item varchar(100) default '',serialno varchar(50) default '', locationid int(11) default 0, location varchar(30) default '', ref_no varchar(15) default '', selected char(1) default 'N')  engine myisam";
$calc = mysql_query($query) or die(mysql_error());

$tradetable = 'ztmp'.$user_id.'_trading';

$query = "drop table if exists ".$tradetable;
$result = mysql_query($query) or die(mysql_error());

$query = "create table ".$tradetable." ( uid int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, itemcode varchar(30) default '',item varchar(100) default '',price decimal(16,2) default 0, unit varchar(20) default '',quantity decimal(16,2) default 0,tax decimal(16,2) default 0, value decimal(16,2) default 0, discount decimal(16,2) default 0, disctype char(1) default '', discamount decimal(16,2) default 0, tot decimal(16,2) default 0, taxindex int(11) default 0, taxtype char(3) default '', taxpcent decimal(5,2) default 0, sellacc int(11) default 0, sellbr char(4) default '', sellsub int(4) default 0, purchacc int(11) default 0, purchbr char(4) default '', purchsub int(4) default 0, groupid int(11) default 0, catid int(11) default 0, loc int(11) default 0 )  engine myisam";
$calc = mysql_query($query) or die(mysql_error());

$medstable = 'ztmp'.$user_id.'_distmeds';

$query = "drop table if exists ".$medstable;
$result = mysql_query($query) or die(mysql_error());

$query = "create table ".$medstable." ( uid int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, noofunits decimal(9,2) default 0,unit varchar(20) default '', medicine varchar(70) default '',member varchar(80) default '',phone varchar(30) default '', mobile varchar(30) default '', email varchar(80) default '' )  engine myisam";
$calc = mysql_query($query) or die(mysql_error());

$moduledb = $_SESSION['s_prcdb'];
mysql_select_db($moduledb) or die(mysql_error());
	
// check to see period has not been processed
$q = "select processed, distperiod from distlist where uid = ".$dlist;
$r = mysql_query($q) or die(mysql_error().' '.$q);
$row = mysql_fetch_array($r);
extract($row);

$p = explode(' ',$distperiod);
$pd = $p[0];
$q = "select startdate,enddate from periods where period = ".$pd;
$r = mysql_query($q) or die(mysql_error().' '.$q);
$row = mysql_fetch_array($r);
extract($row);

$yr = date('Y');

$edt = explode('-',$startdate);
$mdt = $edt[0];
$ddt = $edt[1];
$bdate = $yr.'-'.$mdt.'-'.$ddt;

$pd = explode('-',$enddate);
$m = $pd[0];
$d = $pd[1];

$ddate = $yr.'-'.$m.'-'.$d;
$type = 'INV';
$ref = 'inv';
$descript = "Medicines supplied for period ".$distperiod;
$loc = 1;

$coyno = $_SESSION['s_coyid'];

if ($processed == 'No') {
	
	// create an invoice for each member
	$moduledb = $_SESSION['s_prcdb'];
	mysql_select_db($moduledb) or die(mysql_error());
	
	// do not supply to members not checked or who have insuficient funds
	$q = "select member_id,member,depot_id from distdetail where distlist_id = ".$dlist." and (balance + ordered) < 0 and checked = 'Yes'";
	$r = mysql_query($q) or die(mysql_error().' '.$q);
	
	while ($row = mysql_fetch_array($r)) {
		extract($row);	
		$client = $member;
		$did = $depot_id;
		$mid = $member_id;
		
		$moduledb = $_SESSION['s_findb'];
		mysql_select_db($moduledb) or die(mysql_error());
		
		// get next invoice number
		$query = "lock tables numbers write";
		$result = mysql_query($query) or die($query);
		$query = "select ".$ref." from numbers";
		$result = mysql_query($query) or die($query);
		$row = mysql_fetch_array($result);
		extract($row);
		$refno = $$ref + 1;
		$query = "update numbers set ".$ref." = ".$refno;
		$result = mysql_query($query) or die($query);
		$query = "unlock tables";
		$result = mysql_query($query) or die($query);
		
		$reference = 'INV'.$refno;
		
		$moduledb = $_SESSION['s_cltdb'];
		mysql_select_db($moduledb) or die(mysql_error());

		// get account number for member
		$qd = "select drno,drsub from client_company_xref where client_id = ".$mid." and drno <> 0";
		$rd = mysql_query($qd) or die(mysql_error().' '.$qd);
		$rowd1 = mysql_fetch_array($rd);
		extract($rowd1);
		$acc = $drno;
		$asb = $drsub;
		
		$moduledb = $_SESSION['s_findb'];
		mysql_select_db($moduledb) or die(mysql_error());
		
		$q = "select gsttype as gstinvpay from globals";
		$r = mysql_query($q) or die (mysql_error());
		$row = mysql_fetch_array($r);
		extract($row);		

		$qdel = "delete from ".$tradetable;
		$rdel = mysql_query($qdel) or die(mysql_error().' '.$qdel);
		
		$qm = "select ".$mdb.".distmeds.noofunits, ".$mdb.".distmeds.price, ".$mdb.".distmeds.topay, ".$fdb.".stkmast.groupid, ".$fdb.".stkmast.catid, ".$fdb.".stkmast.itemcode, ".$fdb.".stkmast.item, ".$fdb.".stkmast.unit, ".$fdb.".stkmast.sellacc, ".$fdb.".stkmast.sellsub, ".$fdb.".stkmast.purchacc, ".$fdb.".stkmast.purchsub, ".$fdb.".taxtypes.tax, ".$fdb.".taxtypes.taxpcent from ".$mdb.".distmeds, ".$fdb.".stkmast, ".$fdb.".taxtypes where ".$fdb.".stkmast.itemid = ".$mdb.".distmeds.medicineid and ".$mdb.".distmeds.distlist_id = ".$dlist." and ".$fdb.".taxtypes.uid = ".$fdb.".stkmast.deftax and ".$mdb.".distmeds.patientid = ".$mid;
	
		$rm = mysql_query($qm) or die(mysql_error().' '.$qm);
		while ($rowm = mysql_fetch_array($rm)) {
			extract($rowm);	
			$moduledb = $_SESSION['s_findb'];
			mysql_select_db($moduledb) or die(mysql_error());

			$qi = "insert into ".$tradetable." (itemcode,item,price,unit,quantity,tax,value,tot,taxtype,sellacc,sellbr,sellsub,purchacc,purchbr,purchsub,groupid,catid,loc) values (";
			$qi .= "'".$itemcode."',";
			$qi .= "'".$item."',";
			$qi .= $price.",'";
			$qi .= $unit."',";
			$qi .= $noofunits.",";
			$qi .= round(($price * $noofunits * $taxpcent/100),2).",";
			$qi .= ($price * $noofunits).",";
			$qi .= $topay.",";
			$qi .= "'".$tax."',";
			$qi .= $sellacc.",";
			$qi .= "'',";
			$qi .= $sellsub.",";
			$qi .= $purchacc.",";
			$qi .= "'',";
			$qi .= $purchsub.",";
			$qi .= $groupid.",";
			$qi .= $catid.",";
			$qi .= $loc.")";
			
			$ri = mysql_query($qi) or die(mysql_error().' '.$qi);

		}

		//***********************************************************************
		// create invoice
		//***********************************************************************
		
		// get addresses
		$moduledb = $_SESSION['s_cltdb'];
		mysql_select_db($moduledb) or die(mysql_error());

		$qd = "select addresses.street_no,addresses.ad1 as line1,addresses.ad2 as line2,addresses.suburb,addresses.town,addresses.postcode from addresses where addresses.member_id = ".$mid." and addresses.address_type_id = 4";
		$rd = mysql_query($qd) or die (mysql_error().' '.$qd);
		$numrows = mysql_num_rows($qd);
		if ($numrows == 0) {
			$qd = "select addresses.street_no,addresses.ad1 as line1,addresses.ad2 as line2,addresses.suburb,addresses.town,addresses.postcode from addresses where addresses.member_id =  ".$mid." limit 1";
			$rd = mysql_query($qd) or die (mysql_error().' '.$qd);
		}
			
		$rowd = mysql_fetch_array($rd);
		extract($rowd);
		$postaladdress = trim($street_no.' '.$line1)."\n";
		$postaladdress .= trim($line2.' '.$suburb)."\n";
		$postaladdress .= trim($town.' '.$postcode);
		
		$moduledb = $_SESSION['s_prcdb'];
		mysql_select_db($moduledb) or die(mysql_error());
	
		$qp = "select depot, sad1,sad2,stown,spostcode from depots where depot_id = ".$did;
		$rp = mysql_query($qp) or die (mysql_error().' '.$qp);
		$rowp = mysql_fetch_array($rp);
		extract($rowp);
		$deliveryaddress = trim($sad1)."\n";
		$deliveryaddress .= trim($sad2)."\n";
		$deliveryaddress .= trim($stown.' '.$spostcode);

		$transtype = 'INV';
		$staffmember = 'Admin';
		$xref = '';
		$paymentmethod = '';

		// work out current,d30,d60,d90,d120
		$today = date('Y-m-d');
		$date1 = new DateTime($bdate);
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
		
		$a2dr = $acc;
		$b2dr = '0001';
		$s2dr = $asb;
		$a2cr = 0;
		$b2cr = '0001';
		$s2cr = 0;
		
		$moduledb = $_SESSION['s_findb'];
		mysql_select_db($moduledb) or die(mysql_error());

		// get branch from stklocs
		$qb = "select branch from stklocs where uid = ".$loc;
		$rb = mysql_query($qb) or die(mysql_error());
		$row = mysql_fetch_array($rb);
		extract($row);
		$defbranch = $branch;

		$qt = "select sum(tax) as totaltax, sum(value) as totalamount from ".$tradetable;
		$rt = mysql_query($qt) or die(mysql_error().' '.$qt);
		$rowt = mysql_fetch_array($rt);
		extract($rowt);
		$totalvalue = $totalamount + $totaltax;
		
		$cash = 0;
		$cheque = 0;
		$ccard = 0;
		$eftpos = 0;
		
		if (empty($b2dr)) {$br2dr = '0001';}
		if (empty($s2dr)) {$sub2dr = 0;}
		if (empty($b2cr)) {$br2cr = '0001';}
		if (empty($s2cr)) {$sub2cr = 0;}	
			
		$ano = $a2dr;
		$bno = $b2dr;
		$sno = $s2dr;
		$taxdrcr = 'cr';
		
		// insert invhead record
		$sSQLString = "insert into invhead (ddate,accountno,branch,sub,gldesc,transtype,ref_no,xref,totvalue,tax,cash,cheque,eftpos,ccard,staff,postaladdress,deliveryaddress,client,signature) values ";
		$sSQLString .= "('".$bdate."',";
		$sSQLString .= $ano.",'";
		$sSQLString .= $bno."',";
		$sSQLString .= $sno.",'";
		$sSQLString .= $descript1."','";
		$sSQLString .= $transtype."','";
		$sSQLString .= $reference."','";
		$sSQLString .= $xref."',";
		$sSQLString .= $totalamount.",";
		$sSQLString .= $totaltax.",";
		$sSQLString .= $cash.",";
		$sSQLString .= $cheque.",";
		$sSQLString .= $eftpos.",";
		$sSQLString .= $ccard.",";
		$sSQLString .= '"'.$staffmember.'",';
		$sSQLString .= '"'.$postaladdress.'",';
		$sSQLString .= '"'.$deliveryaddress.'",';
		$sSQLString .= '"'.$client.'",';
		$sSQLString .= '"'.$signature.'")';
		
		$r4 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
				
		$qtrd = "select * from ".$tradetable;
		$result5 = mysql_query($qtrd) or die(mysql_error());
		while ($row5 = mysql_fetch_array($result5)) {
			extract($row5);
				
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
			$sSQLString .= $reference."',";
			$sSQLString .= $value.",";
			$sSQLString .= $discount.",";
			$sSQLString .= "'".$disctype."',";
			$sSQLString .= $uid.")";
					
			$r5 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
				
		}
		
		// insert GL records	
			
		// Create entries between closing stock and stock on hand
				
		$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
		$sSQLString .= "('".$bdate."',";
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
		$sSQLString .= $descript."',";
		$sSQLString .= $taxpcent.")";			
					
		$r6 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
				
		$sql = "update glmast set obal = obal + ".$totalamount." where accountno = 187 and branch = '".$defbranch."' and sub = 0";
		$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
				
				
		$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
		$sSQLString .= "('".$bdate."',";
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
		$sSQLString .= $descript."',";
		$sSQLString .= $taxpcent.")";			
					
		$r7 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
				
		$sql = "update glmast set obal = obal - ".$totalamount." where accountno = 825 and branch = '".$defbranch."' and sub = 0";
		$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
				
			
		//*******************************************************************************************************************************************
		// Invoice
		//*******************************************************************************************************************************************
		// debit debtor account
		$sSQLString = "insert into trmain (ddate,accountno,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
		$sSQLString .= "('".$bdate."',";
		$sSQLString .= $a2dr.",";		// debit the debtor
		$sSQLString .= $s2dr.",";
		$sSQLString .= "0".",'";
		$sSQLString .= " "."',";			
		$sSQLString .= "0".",";			
		$sSQLString .= $totalvalue.",";		// with the amount including GST
		$sSQLString .= "0,'";
		$sSQLString .= $reference."','";					
		$sSQLString .= $taxtype."','";
		$sSQLString .= $descript."',";
		$sSQLString .= $taxpcent.")";			
					
		$r8 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
					
		$cltdb = $_SESSION['s_cltdb'];
		mysql_select_db($cltdb) or die(mysql_error());	
					
		if ($aged == 'Current') {
			$sql = "update client_company_xref set current = current + ".$totalvalue." where company_id = ".$coyno." and drno = ".$a2dr." and drsub = ".$s2dr;
		}
		if ($aged == 'D30') {
			$sql = "update client_company_xref set d30 = d30 + ".$totalvalue." where company_id = ".$coyno." and drno = ".$a2dr." and drsub = ".$s2dr;
		}
		if ($aged == 'D60') {
			$sql = "update client_company_xref set d60 = d60 + ".$totalvalue." where company_id = ".$coyno." and drno = ".$a2dr." and drsub = ".$s2dr;
		}
		if ($aged == 'D90') {
			$sql = "update client_company_xref set d90 = d90 + ".$totalvalue." where company_id = ".$coyno." and drno = ".$a2dr." and drsub = ".$s2dr;
		}
		if ($aged == 'D120') {
			$sql = "update client_company_xref set d120 = d120 + ".$totalvalue." where company_id = ".$coyno." and drno = ".$a2dr." and drsub = ".$s2dr;
		}
					
		$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
					
		$moduledb = $_SESSION['s_findb'];
		mysql_select_db($moduledb) or die(mysql_error());	
					
					
		// debit debtor control account
		$sSQLString = "insert into trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,descript1,taxpcent) values ";
		$sSQLString .= "('".$bdate."',";
		$sSQLString .= "801,'";		// debit debtors control
		$sSQLString .= $defbranch."',";
		$sSQLString .= '0'.",'";
		$sSQLString .= ' '."',";	
		$sSQLString .= $totalvalue.",";		// with the amount including GST
		$sSQLString .= "0,'";
		$sSQLString .= $reference."','";					
		$sSQLString .= $taxtype."','";
		$sSQLString .= $descript."',";
		$sSQLString .= $taxpcent.")";					
					
		$r9 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
						
		$sql = "update glmast set obal = obal + ".$totalamount." where accountno = 801 and branch = '".$defbranch."' and sub = 0";
		$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);	
					
		// credit relevant income accounts
		$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
		$sSQLString .= "('".$bdate."',";
		$sSQLString .= $sellacc.",";		//credit the income account
		$sSQLString .= "'".$defbranch."',";
		$sSQLString .= $sellsub.",";
		$sSQLString .= $a2dr.",'";
		$sSQLString .= ' '."',";			
		$sSQLString .= $s2dr.",";			
		$sSQLString .= "0,";		
		$sSQLString .= $value.",'";  // with the amount excluding GST
		$sSQLString .= $reference."','";					
		$sSQLString .= $taxtype."','";
		$sSQLString .= $descript."',";
		$sSQLString .= $taxpcent.")";			
						
		$rtd = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
						
		$sql = "update glmast set obal = obal - ".$totalamount." where accountno = ".$sellacc." and branch = '".$defbranch."' and sub = ".$sellsub;
		$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);	

		//*************************************************************************************************************************************************************
		// Create double entries relating to GST for each line of trading document
		//*************************************************************************************************************************************************************
				
		// insert each line's tax where applicable into trmain. If GRN add record anyway.
		if ($gstinvpay == 'Invoice') {
			$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent,grnlineno) values ";
			$sSQLString .= "('".$bdate."',";
			$sSQLString .= "870,'";		// credit GST payable
			$sSQLString .= $defbranch."',";
			$sSQLString .= "0,";
			$sSQLString .= $a2dr.",'";
			$sSQLString .= $defbranch."',";	
			$sSQLString .= $s2dr.",";
			$sSQLString .= "0,";		
			$sSQLString .= $tax.",'";		// with the amount of the GST
			$sSQLString .= $reference."','";					
			$sSQLString .= $taxtype."','";
			$sSQLString .= $descript."',";
			$sSQLString .= $taxpcent.",";					
			$sSQLString .= $uid.")";					
	
			$r10 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
		
		} else {
			$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent,grnlineno) values ";
			$sSQLString .= "('".$bdate."',";
			$sSQLString .= "871,'";		// credit GST payable
			$sSQLString .= $defbranch."',";
			$sSQLString .= "0,";
			$sSQLString .= $a2dr.",'";
			$sSQLString .= $defbranch."',";	
			$sSQLString .= $s2dr.",";
			$sSQLString .= "0,";		
			$sSQLString .= $tax.",'";		// with the amount of the GST
			$sSQLString .= $reference."','";					
			$sSQLString .= $taxtype."','";
			$sSQLString .= $descript."',";
			$sSQLString .= $taxpcent.",";					
			$sSQLString .= $uid.")";					
	
			$r10 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
			
		}
	
		//***********************************************************************************************************************************
		// Create entries in stktrans for each stock recorded item
		//***********************************************************************************************************************************
		$increase = 0;
		$decrease = $quantity;
		$sql = "update stkmast set onhand = onhand - ".$decrease." where itemcode = '".$itemcode."'";
		$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);	
			
		$sSQLString = "insert into stktrans (groupid,catid,itemcode,item,locid,ddate,increase,decrease,ref_no,transtype,amount) values ";
		$sSQLString .= "(".$groupid.",";
		$sSQLString .= $catid.",";
		$sSQLString .= "'".$itemcode."','";
		$sSQLString .= $item."','";
		$sSQLString .= $loc."','";
		$sSQLString .= $bdate."',";
		$sSQLString .= $increase.",";
		$sSQLString .= $decrease.",'";
		$sSQLString .= $reference."','";
		$sSQLString .= $transtype."',";
		$sSQLString .= $value.")";
			
		$r11 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
		
		//**************************************************************************************************************************************************************************
		// add transaction to audit trail
		//**************************************************************************************************************************************************************************
		$sSQLString = "insert into audit (ddate,acc2dr,brdr,subdr,acc2cr,brcr,subcr,descript1,reference,amount,tax,total,entrydate,entrytime,username,userip) values ";
		$sSQLString .= "('".$bdate."',";
		$sSQLString .= $a2dr.",'";		
		$sSQLString .= $defbranch."',";
		$sSQLString .= $s2dr.",";
		$sSQLString .= $a2cr.",'";
		$sSQLString .= $defbranch."',";			
		$sSQLString .= $s2cr.",'";			
		$sSQLString .= $descript."','";
		$sSQLString .= $reference."',";		
		$sSQLString .= $totalamount.",";					
		$sSQLString .= $totaltax.",";
		$sSQLString .= $totalvalue.",'";
		$sSQLString .= date("Y-m-d")."','";
		$sSQLString .= date("H:i:s")."','";				
		$sSQLString .= $unm."','";
		$sSQLString .= $uip."')";		
					
		$r35 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
		
		$sql = "delete from ".$tradetable;
		$rst = mysql_query($sql) or die (mysql_error());

		$moduledb = $_SESSION['s_prcdb'];
		mysql_select_db($moduledb) or die(mysql_error());

	}
	
	// mark procesed as Yes in distlist
	$moduledb = $_SESSION['s_prcdb'];
	mysql_select_db($moduledb) or die(mysql_error());

	$qu = "update distlist set processed = 'Yes' where uid = ".$dlist;
	$ru = mysql_query($qu) or die (mysql_error().' '.$qu);
		
	$ret =  "This period distribution list invoiced";
	echo $ret;

} else {
	$ret =  "This period distribution list has already been processed.";
	echo $ret;
}


?>

