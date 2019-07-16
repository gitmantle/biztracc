<?php
session_start();
//ini_set('display_errors', true);

$coyno = $_SESSION['s_coyid'];

$usersession = $_SESSION['usersession'];
require("../../db.php");

$admindb = $_SESSION['s_admindb'];
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$uip = $userip;
$unm = $uname;

$table = 'ztmp'.$user_id.'_trans';
date_default_timezone_set($_SESSION['s_timezone']);

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select gsttype as gstinvpay from globals";
$r = mysql_query($q) or die (mysql_error());
$row = mysql_fetch_array($r);
extract($row);

$rno = '';

if ($transtype == 'REC' || $transtype == 'PAY') {
	$qt = "select inv from ".$table." limit 1";
	$rt = mysql_query($qt) or die(mysql_error().' '.$qt);
	$row = mysql_fetch_array($rt);
	extract($row);
	$rno = $ref;
}

$qe = "show columns FROM ".$table." like 'debtor'";
$re = mysql_query($qe);
$exists = mysql_num_rows($re);

$q = "select * from ".$table;
$result = mysql_query($q) or die(mysql_error());
while ($row = mysql_fetch_array($result)) {
	extract($row);
	
	if ($exists == 0) {
		$client = '';
	} else {
		$client = $debtor;	
	}
	
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


	//acc2dr,subdr,brdr,acc2cr,subcr,brcr,ddate,descript1,reference,amount,tax,taxtype,taxpcent,total,grn,inv,

	// debit variables
	if ($acc2dr >= 1 and $acc2dr <= 100) {
		$dacctype = 'din';
		$br2dr = $brdr;
		$sub2dr = $subdr;		
		$DGL = 'Y';
	}
	if ($acc2dr >= 101 and $acc2dr <= 700) {
		$dacctype = 'dex';
		$br2dr = $brdr;
		$sub2dr = $subdr;
		$DGL = 'Y';
	}
	if ($acc2dr >= 701 and $acc2dr <= 999) {
		$dacctype = 'dbs';
		$br2dr = $brdr;
		$sub2dr = $subdr;		
		$DGL = 'Y';
	}
	if ($acc2dr == 5000) {
		$dacctype = 'd50';
		$br2dr = $brdr;
		$sub2dr = $subdr;		
		$DGL = 'Y';
	}	
	if ($acc2dr >= 10000000 and $acc2dr < 20000000) {
		$dacctype = 'das';
		$br2dr = $brdr;
		$sub2dr = $subdr;		
		$DGL = 'N';
	}							
	if ($acc2dr >= 20000000 and $acc2dr < 30000000) {
		$dacctype = 'dcr';
		$br2dr = $brdr;
		$sub2dr = $subdr;		
		$DGL = 'N';
	}		
	if ($acc2dr >= 30000000) {
		$dacctype = 'ddr';
		$br2dr = $brdr;		
		$sub2dr = $subdr;		
		$DGL = 'N';
	}	

	if (empty($br2dr)) {$br2dr = ' ';}
	if (empty($sub2dr)) {$sub2dr = 0;}

	// credit variables
	if ($acc2cr >= 1 and $acc2cr <= 100) {
		$cacctype = 'cin';
		$br2cr = $brcr;
		$sub2cr = $subcr;		
		$CGL = 'Y';
	}
	if ($acc2cr >= 101 and $acc2cr <= 700) {
		$cacctype = 'cex';
		$br2cr = $brcr;
		$sub2cr = $subcr;		
		$CGL = 'Y';
	}
	if ($acc2cr >= 701 and $acc2cr <= 999) {
		$cacctype = 'cbs';
		$br2cr = $brcr;
		$sub2cr = $subcr;		
		$CGL = 'Y';
	}
	if ($acc2cr == 5000) {
		$cacctype = 'c50';
		$br2cr = $brcr;
		$sub2cr = $subcr;		
		$CGL = 'Y';
	}
	if ($acc2cr >= 10000000 and $acc2cr < 20000000) {
		$cacctype = 'cas';
		$br2cr = $brcr;
		$sub2cr = $subcr;		
		$CGL = 'N';
	}							
	if ($acc2cr >= 20000000 and $acc2cr < 30000000) {
		$cacctype = 'ccr';
		$br2cr = $brcr;
		$sub2cr = $subcr;		
		$CGL = 'N';
	}		
	if ($acc2cr >= 30000000) {
		$cacctype = 'cdr';
		$br2cr = $brcr;
		$sub2cr = $subcr;		
		$CGL = 'N';
	}			

	if (empty($br2cr)) {$br2cr = ' ';}
	if (empty($sub2cr)) {$sub2cr = 0;}

	//**********************************************************
	// DEBIT LEG
	//**********************************************************		
	switch ($dacctype) {
		case 'din':
			// debit an income account
			
	
			$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
			$sSQLString .= "('".$ddate."',";
			$sSQLString .= $acc2dr.",'";		// debit the income account
			$sSQLString .= $br2dr."',";
			$sSQLString .= $sub2dr.",";
			$sSQLString .= $acc2cr.",'";
			$sSQLString .= $br2cr."',";			
			$sSQLString .= $sub2cr.",";			
			$sSQLString .= $amount.",";		// with the amount excluding GST
			$sSQLString .= "0,'";
			$sSQLString .= $reference."','";					
			$sSQLString .= $taxtype."',";
			$sSQLString .= '"'.$descript1.'",';
			$sSQLString .= $taxpcent.")";			
			
			$r1 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);		
			
			$sql = "update glmast set obal = obal + ".$amount." where accountno = ".$acc2dr." and branch = '".$br2dr."' and sub = ".$sub2dr;
			$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
	
			
			if ($tax > 0) {
		
				$sSQLString = "insert into trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,descript1,taxpcent) values ";
				$sSQLString .= "('".$ddate."',";
				$sSQLString .= "870,'";		// debit GST payable
				$sSQLString .= $br2dr."',";
				$sSQLString .= $acc2cr.",'";
				$sSQLString .= $br2cr."',";	
				$sSQLString .= $tax.",";		// with the amount of the GST
				$sSQLString .= "0,'";
				$sSQLString .= $reference."','";					
				$sSQLString .= $taxtype."',";
				$sSQLString .= '"'.$descript1.'",';
				$sSQLString .= $taxpcent.")";					
			
				$r2 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
				
				$sql = "update glmast set obal = obal + ".$tax." where accountno = 870 and branch = '".$br2dr."'";
				$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);	
	
			}
					
			break;
		case 'dex':
			// debit cost of sales or expense account
			$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
			$sSQLString .= "('".$ddate."',";
			$sSQLString .= $acc2dr.",'";		// debit the expense or purchase
			$sSQLString .= $br2dr."',";
			$sSQLString .= $sub2dr.",";
			$sSQLString .= $acc2cr.",'";
			$sSQLString .= $br2cr."',";			
			$sSQLString .= $sub2cr.",";			
			$sSQLString .= $amount.",";		// with the amount excluding GST
			$sSQLString .= "0,'";
			$sSQLString .= $reference."','";					
			$sSQLString .= $taxtype."',";
			$sSQLString .= '"'.$descript1.'",';
			$sSQLString .= $taxpcent.")";	
			
			$r3 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
			
			$sql = "update glmast set obal = obal + ".$amount." where accountno = ".$acc2dr." and branch = '".$br2dr."' and sub = ".$sub2dr;
			$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
			
	
			
			if ($tax > 0) {
				if ($drgst == 'Y') {
					$useingst = 'N';
				} else {
					$useingst = 'Y';
				}
				$sSQLString = "insert into trmain (ddate,accountno,branch,accno,br,subbr,debit,credit,reference,gsttype,gstrecon,descript1,taxpcent) values ";
				$sSQLString .= "('".$ddate."',";
				$sSQLString .= "870,'";		// debit GST payable
				$sSQLString .= $br2dr."',";
				$sSQLString .= $acc2cr.",'";
				$sSQLString .= $br2cr."',";	
				$sSQLString .= $sub2cr.",";			
				$sSQLString .= $tax.",";		// with the amount of the GST
				$sSQLString .= "0,'";
				$sSQLString .= $reference."','";					
				$sSQLString .= $taxtype."','";
				$sSQLString .= $useingst."',";
				$sSQLString .= '"'.$descript1.'",';
				$sSQLString .= $taxpcent.")";					
			
				$r4 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
				
				$sql = "update glmast set obal = obal + ".$tax." where accountno = 870 and branch = '".$br2dr."'";
				$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
				
			
			}
			
			break;
		case 'dbs':
			// debit balance sheet account
			
			if ($acc2dr == 870) {
				$useingst = 'Y';
			} else {
				$useingst = '';
			}	
						
			$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,gstrecon,descript1,taxpcent) values ";
			$sSQLString .= "('".$ddate."',";
			$sSQLString .= $acc2dr.",'";		
			$sSQLString .= $br2dr."',";
			$sSQLString .= $sub2dr.",";
			$sSQLString .= $acc2cr.",'";
			$sSQLString .= $br2cr."',";			
			$sSQLString .= $sub2cr.",";			
			$sSQLString .= $total.",";		// with the amount including GST
			$sSQLString .= "0,'";
			$sSQLString .= $reference."','";					
			$sSQLString .= $taxtype."','";
			$sSQLString .= $useingst."',";
			$sSQLString .= '"'.$descript1.'",';
			$sSQLString .= $taxpcent.")";			
			
			$r5 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
			
			$sql = "update glmast set obal = obal + ".$total." where accountno = ".$acc2dr." and branch = '".$br2dr."' and sub = ".$sub2dr;
			$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
			
			
			break;
		case 'das':
			// debit fixed asset account
			$sSQLString = "insert into trmain (ddate,accountno,branch,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
			$sSQLString .= "('".$ddate."',";
			$sSQLString .= $acc2dr.",'";		// debit the asset
			$sSQLString .= $br2dr."',";
			$sSQLString .= $acc2cr.",'";
			$sSQLString .= $br2cr."',";			
			$sSQLString .= $sub2cr.",";			
			$sSQLString .= $amount.",";		// with the amount excluding GST
			$sSQLString .= "0,'";
			$sSQLString .= $reference."','";					
			$sSQLString .= $taxtype."',";
			$sSQLString .= '"'.$descript1.'",';
			$sSQLString .= $taxpcent.")";			
			
			$r6 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
			
			
			$sSQLString = "insert into trmain (ddate,accountno,branch,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
			$sSQLString .= "('".$ddate."',";
			$sSQLString .= "701,'";		// debit asset control
			$sSQLString .= $br2dr."',";
			$sSQLString .= $acc2cr.",'";
			$sSQLString .= $br2cr."',";	
			$sSQLString .= $sub2cr.",";		
			$sSQLString .= $amount.",";		// with the amount excluding GST
			$sSQLString .= "0,'";
			$sSQLString .= $reference."','";					
			$sSQLString .= $taxtype."',";
			$sSQLString .= '"'.$descript1.'",';
			$sSQLString .= $taxpcent.")";					
			
			$r7 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
			
			$sql = "update glmast set obal = obal + ".$amount." where accountno = 701 and branch = '".$br2dr."'";
			$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
			
						

			if ($tax > 0) {
				if ($drgst == 'Y') {
					$useingst = 'N';
				} else {
					$useingst = 'Y';
				}
				$sSQLString = "insert into trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,gstrecon,descript1,taxpcent) values ";
				$sSQLString .= "('".$ddate."',";
				$sSQLString .= "870,'";		// debit trading tax payable
				$sSQLString .= $br2dr."',";
				$sSQLString .= $acc2cr.",'";
				$sSQLString .= $br2cr."',";	
				$sSQLString .= $tax.",";		// with the amount of GST
				$sSQLString .= "0,'";
				$sSQLString .= $reference."','";					
				$sSQLString .= $taxtype."','";
				$sSQLString .= $useingst."',";
				$sSQLString .= '"'.$descript1.'",';
				$sSQLString .= $taxpcent.")";					
			
				$r8 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
				
				$sql = "update glmast set obal = obal + ".$tax." where accountno = 870 and branch = '".$br2dr."'";
				$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
				
		
			}
			
			$sSQLString = "update fixassets set cost = cost + ".$amount." where accountno = ".$acc2dr." and branch = '".$br2dr."'";
			
			$r9 = mysql_query($sSQLString) or die($sSQLString);

			
			break;
		case 'd50':
			$sSQLString = "insert into trmain (ddate,accountno,branch,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
			$sSQLString .= "('".$ddate."',";
			$sSQLString .= "702,'";		// debit accumulated depreciation control
			$sSQLString .= $br2cr."',";
			$sSQLString .= $acc2cr.",'";
			$sSQLString .= $br2cr."',";	
			$sSQLString .= $sub2cr.",";		
			$sSQLString .= $amount.",";		// with the amount excluding GST
			$sSQLString .= "0,'";
			$sSQLString .= $assetno."','";					
			$sSQLString .= $taxtype."',";
			$sSQLString .= '"'.$descript1.'",';
			$sSQLString .= $taxpcent.")";		
			
			$r10 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
			
			$sql = "update glmast set obal = obal + ".$amount." where accountno = 702 and branch = '".$br2cr."'";
			$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
			
		
			
			$sSQLString = "update fixassets set totdep = totdep - ".$amount.", dep5000 = dep5000 - ".$amount." where accountno = ".$assetno;
			
			$r11 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);

											
			break;
		case 'dcr':
			// debit creditor account
			$sSQLString = "insert into trmain (ddate,accountno,sub,accno,br,subbr,debit,credit,reference,grn,gsttype,descript1,taxpcent) values ";
			$sSQLString .= "('".$ddate."',";
			$sSQLString .= $acc2dr.",";		// debit the creditor
			$sSQLString .= $sub2dr.",";
			$sSQLString .= $acc2cr.",'";
			$sSQLString .= $br2cr."',";			
			$sSQLString .= $sub2cr.",";			
			$sSQLString .= $total.",";		// with the amount including GST
			$sSQLString .= "0,'";
			$sSQLString .= $reference."','";				
			$sSQLString .= $xrefstr."','";				
			$sSQLString .= $taxtype."',";
			$sSQLString .= '"'.$descript1.'",';
			$sSQLString .= $taxpcent.")";			
			
			$r12 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
			
			$cltdb = $_SESSION['s_cltdb'];
			mysql_select_db($cltdb) or die(mysql_error());	
			
			if ($aged == 'Current') {
				$sql = "update client_company_xref set current = current + ".$total." where company_id = ".$coyno." and crno = ".$acc2dr." and crsub = ".$sub2dr;
			}
			if ($aged == 'D30') {
				$sql = "update client_company_xref set d30 = d30 + ".$total." where company_id = ".$coyno." and crno = ".$acc2dr." and crsub = ".$sub2dr;
			}
			if ($aged == 'D60') {
				$sql = "update client_company_xref set d60 = d60 + ".$total." where company_id = ".$coyno." and crno = ".$acc2dr." and crsub = ".$sub2dr;
			}
			if ($aged == 'D90') {
				$sql = "update client_company_xref set d90 = d90 + ".$total." where company_id = ".$coyno." and crno = ".$acc2dr." and crsub = ".$sub2dr;
			}
			if ($aged == 'D120') {
				$sql = "update client_company_xref set d120 = d120 + ".$total." where company_id = ".$coyno." and crno = ".$acc2dr." and crsub = ".$sub2dr;
			}
			
			$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
			
			$moduledb = $_SESSION['s_findb'];
			mysql_select_db($moduledb) or die(mysql_error());	
			
			$sSQLString = "insert into trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,descript1,taxpcent) values ";
			$sSQLString .= "('".$ddate."',";
			$sSQLString .= "851,'";		// debit creditors control
			$sSQLString .= $br2cr."',";
			$sSQLString .= $acc2cr.",'";
			$sSQLString .= $br2cr."',";	
			$sSQLString .= $total.",";		// with the amount including GST
			$sSQLString .= "0,'";
			$sSQLString .= $reference."','";					
			$sSQLString .= $taxtype."',";
			$sSQLString .= '"'.$descript1.'",';
			$sSQLString .= $taxpcent.")";					
			
			$r13 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
			
			$sql = "update glmast set obal = obal + ".$total." where accountno = 851 and branch = '".$br2cr."'";
			$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
			
			$payrec = substr($reference,0,3);
			//if ($payrec <> 'PAY' && $payrec <> 'REC') {

				// add record to invhead
				
				$sSQLString = "insert into invhead (ddate,accountno,branch,sub,gldesc,transtype,ref_no,totvalue,tax,client,staff) values ";
				$sSQLString .= "('".$ddate."',";
				$sSQLString .= $acc2dr.",'";
				$sSQLString .= $br2cr."',";
				$sSQLString .= $sub2dr.",";
				$sSQLString .= '"'.$descript1.'",';
				$sSQLString .= "'".$payrec."','";
				$sSQLString .= $reference."',";
				$sSQLString .= $amount.",";
				$sSQLString .= $tax.",";
				$sSQLString .= "'".$client."',";
				$sSQLString .= "'".$uname."')";
				
				$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
				
				
				// insert records into invtrans
			
				$sSQLString = "insert into invtrans (item,price,quantity,taxtype,taxpcent,tax,ref_no,value) values ";
				$sSQLString .= '("'.$descript1.'",';
				$sSQLString .= $amount.",";
				$sSQLString .= "1,'";
				$sSQLString .= $taxtype."',";
				$sSQLString .= $taxpcent.",";
				$sSQLString .= $tax.",'";
				$sSQLString .= $reference."',";
				$sSQLString .= $amount.")";
				
				$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
			//}
						
/*
			// allocate payement against relevant GRN
			if ($xrefuid != 0) {
				$query = "select paid from trmain where uid = ".$xrefuid;
				$result = mysql_query($query) or die($query);
				$row = mysql_fetch_array($result) or die(mysql_error());
				extract($row);
				$pd = $paid + $total;
				$sSQLString = "update trmain set paid = ".$pd." where uid = ".$xrefuid;
				
				$r14 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
						
			}
*/
			
			break;
		case 'ddr':
		
			// debit debtor account
			$sSQLString = "insert into trmain (ddate,accountno,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
			$sSQLString .= "('".$ddate."',";
			$sSQLString .= $acc2dr.",";		// debit the debtor
			$sSQLString .= $sub2dr.",";
			$sSQLString .= $acc2cr.",'";
			$sSQLString .= $br2cr."',";			
			$sSQLString .= $sub2cr.",";			
			$sSQLString .= $total.",";		// with the amount including GST
			$sSQLString .= "0,'";
			$sSQLString .= $reference."','";					
			$sSQLString .= $taxtype."',";
			$sSQLString .= '"'.$descript1.'",';
			$sSQLString .= $taxpcent.")";			
			
			$r15 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
			
			$cltdb = $_SESSION['s_cltdb'];
			mysql_select_db($cltdb) or die(mysql_error());	
			
			if ($aged == 'Current') {
				$sql = "update client_company_xref set current = current + ".$total." where company_id = ".$coyno." and drno = ".$acc2dr." and drsub = ".$sub2dr;
			}
			if ($aged == 'D30') {
				$sql = "update client_company_xref set d30 = d30 + ".$total." where company_id = ".$coyno." and drno = ".$acc2dr." and drsub = ".$sub2dr;
			}
			if ($aged == 'D60') {
				$sql = "update client_company_xref set d60 = d60 + ".$total." where company_id = ".$coyno." and drno = ".$acc2dr." and drsub = ".$sub2dr;
			}
			if ($aged == 'D90') {
				$sql = "update client_company_xref set d90 = d90 + ".$total." where company_id = ".$coyno." and drno = ".$acc2dr." and drsub = ".$sub2dr;
			}
			if ($aged == 'D120') {
				$sql = "update client_company_xref set d120 = d120 + ".$total." where company_id = ".$coyno." and drno = ".$acc2dr." and drsub = ".$sub2dr;
			}
			
			
			$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
			
			$moduledb = $_SESSION['s_findb'];
			mysql_select_db($moduledb) or die(mysql_error());	
			
	
			
			$sSQLString = "insert into trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,descript1,taxpcent) values ";
			$sSQLString .= "('".$ddate."',";
			$sSQLString .= "801,'";		// debit debtors control
			$sSQLString .= $br2cr."',";
			$sSQLString .= $acc2cr.",'";
			$sSQLString .= $br2cr."',";	
			$sSQLString .= $total.",";		// with the amount including GST
			$sSQLString .= "0,'";
			$sSQLString .= $reference."','";					
			$sSQLString .= $taxtype."',";
			$sSQLString .= '"'.$descript1.'",';
			$sSQLString .= $taxpcent.")";					
			
			$r16 = mysql_query($sSQLString)or die(mysql_error().' '.$sSQLString);
			
			$sql = "update glmast set obal = obal + ".$total." where accountno = 801 and branch = '".$br2cr."'";
			$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
			
			$payrec = substr($reference,0,3);
			//if ($payrec <> 'PAY' && $payrec <> 'REC') {
			
				// add record to invhead
				$sSQLString = "insert into invhead (ddate,accountno,branch,sub,gldesc,transtype,ref_no,totvalue,tax,client,staff) values ";
				$sSQLString .= "('".$ddate."',";
				$sSQLString .= $acc2dr.",'";
				$sSQLString .= $br2cr."',";
				$sSQLString .= $sub2dr.",'";
				$sSQLString .= $descript1."',";
				$sSQLString .= "'".$payrec."','";
				$sSQLString .= $reference."',";
				$sSQLString .= $amount.",";
				$sSQLString .= $tax.",";
				$sSQLString .= "'".$client."',";
				$sSQLString .= "'".$uname."')";
				
				$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
				
				
				// insert records into invtrans
			
				$sSQLString = "insert into invtrans (item,price,quantity,taxtype,taxpcent,tax,ref_no,value) values ";
				$sSQLString .= "('".$descript1."',";
				$sSQLString .= $amount.",";
				$sSQLString .= "1,'";
				$sSQLString .= $taxtype."',";
				$sSQLString .= $taxpcent.",";
				$sSQLString .= $tax.",'";
				$sSQLString .= $reference."',";
				$sSQLString .= $amount.")";
				
				$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
			//}
			

			break;
	}
	
	// if gst based on payment
	if ($gstinvpay == 'Payment') {

			if ($tax > 0) {
				if ($drgst == 'Y') {
					$useingst = 'N';
				} else {
					$useingst = 'Y';
				}
				if ($acc2dr >= 751 && $acc2dr <= 800) {
					$sSQLString = "insert into trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,gstrecon,descript1,taxpcent) values ";
					$sSQLString .= "('".$ddate."',";
					$sSQLString .= "77,'";		// debit GST on Payment
					$sSQLString .= "0001',";
					$sSQLString .= "870,'";
					$sSQLString .= "0001',";	
					$sSQLString .= $tax.",";		// with the amount of GST
					$sSQLString .= "0,'";
					$sSQLString .= $reference."','";					
					$sSQLString .= $taxtype."','";
					$sSQLString .= $useingst."',";
					$sSQLString .= '"'.$descript1.'",';
					$sSQLString .= $taxpcent.")";					
				
					$r8 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
					
					$sql = "update glmast set obal = obal + ".$tax." where accountno = 77 and branch = '0001'";
					$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
				
					$sSQLString = "insert into trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,gstrecon,descript1,taxpcent) values ";
					$sSQLString .= "('".$ddate."',";
					$sSQLString .= "870,'";		
					$sSQLString .= "0001',";
					$sSQLString .= "77,'"; 
					$sSQLString .= "0001',";	
					$sSQLString .= "0,";		
					$sSQLString .= $tax.",'"; // with the amount of GST
					$sSQLString .= $reference."','";					
					$sSQLString .= $taxtype."','";
					$sSQLString .= $useingst."',";
					$sSQLString .= '"'.$descript1.'",';
					$sSQLString .= $taxpcent.")";					
				
					$r8 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
					
					$sql = "update glmast set obal = obal - ".$tax." where accountno = 870 and branch = '0001'";
					$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
				
				}
			}
	}
	
	//**********************************************************
	// CREDIT LEG
	//**********************************************************
	
	switch ($cacctype) {
		case 'cin':
			// credit an income account

			$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
			$sSQLString .= "('".$ddate."',";
			$sSQLString .= $acc2cr.",'";		// credit the income account
			$sSQLString .= $br2cr."',";
			$sSQLString .= $sub2cr.",";
			$sSQLString .= $acc2dr.",'";		
			$sSQLString .= $br2dr."',";			
			$sSQLString .= $sub2dr.",";			
			$sSQLString .= "0,";		
			$sSQLString .= $amount.",'";		// with the amount excluding GST
			$sSQLString .= $reference."','";					
			$sSQLString .= $taxtype."',";
			$sSQLString .= '"'.$descript1.'",';
			$sSQLString .= $taxpcent.")";			
			
			$r17 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
			
			$sql = "update glmast set obal = obal - ".$amount." where accountno = ".$acc2cr." and branch = '".$br2cr."' and sub = ".$sub2cr;
			$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
			
	
			
			if ($tax > 0) {

				$sSQLString = "insert into trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,descript1,taxpcent) values ";
				$sSQLString .= "('".$ddate."',";
				$sSQLString .= "870,'";		// credit GST payable
				$sSQLString .= $br2cr."',";
				$sSQLString .= $acc2cr.",'";
				$sSQLString .= $br2cr."',";	
				$sSQLString .= "0,";		
				$sSQLString .= $tax.",'";		// with the amount of the GST
				$sSQLString .= $reference."','";					
				$sSQLString .= $taxtype."',";
				$sSQLString .= '"'.$descript1.'",';
				$sSQLString .= $taxpcent.")";					
		
				$r18 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
				
				$sql = "update glmast set obal = obal - ".$tax." where accountno = 870 and branch = '".$br2cr."'";
				$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
				
	
			}
					
			break;
		case 'cex':
			// credit cost of sales or expense account
			$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
			$sSQLString .= "('".$ddate."',";
			$sSQLString .= $acc2cr.",'";		// credit the expense or purchase
			$sSQLString .= $br2cr."',";
			$sSQLString .= $sub2cr.",";
			$sSQLString .= $acc2dr.",'";
			$sSQLString .= $br2dr."',";			
			$sSQLString .= $sub2dr.",";			
			$sSQLString .= "0,";	
			$sSQLString .= $amount.",'";		// with the amount excluding GST
			$sSQLString .= $reference."','";					
			$sSQLString .= $taxtype."',";
			$sSQLString .= '"'.$descript1.'",';
			$sSQLString .= $taxpcent.")";			
			
			$r19 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
			
			$sql = "update glmast set obal = obal - ".$amount." where accountno = ".$acc2cr." and branch = '".$br2cr."' and sub = ".$sub2cr;
			$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
			
	
			
			if ($tax > 0) {
				if ($crgst == 'Y') {
					$useingst = 'N';
				} else {
					$useingst = 'Y';
				}
				$sSQLString = "insert into trmain (ddate,accountno,branch,accno,br,subbr,debit,credit,reference,gsttype,gstrecon,descript1,taxpcent) values ";
				$sSQLString .= "('".$ddate."',";
				$sSQLString .= "870,'";		// credit GST payable
				$sSQLString .= $br2cr."',";
				$sSQLString .= $acc2cr.",'";
				$sSQLString .= $br2cr."',";	
				$sSQLString .= $sub2cr.",";			
				$sSQLString .= "0,";	
				$sSQLString .= $tax.",'";		// with the amount of the GST
				$sSQLString .= $reference."','";					
				$sSQLString .= $taxtype."','";
				$sSQLString .= $useingst."',";
				$sSQLString .= '"'.$descript1.'",';
				$sSQLString .= $taxpcent.")";					
			
				$r20 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
				
				$sql = "update glmast set obal = obal - ".$tax." where accountno = 870 and branch = '".$br2cr."'";
				$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
				
			
			}
			
			break;
		case 'cbs':
			// credit balance sheet account
			if ($acc2cr == 870) {
				$useingst = 'Y';
			} else {
				$useingst = '';
			}
						
			$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,gstrecon,descript1,taxpcent) values ";
			$sSQLString .= "('".$ddate."',";
			$sSQLString .= $acc2cr.",'";		
			$sSQLString .= $br2cr."',";
			$sSQLString .= $sub2cr.",";
			$sSQLString .= $acc2dr.",'";
			$sSQLString .= $br2dr."',";			
			$sSQLString .= $sub2dr.",";			
			$sSQLString .= "0,";
			$sSQLString .= $total.",'";		// with the amount including GST
			$sSQLString .= $reference."','";					
			$sSQLString .= $taxtype."','";
			$sSQLString .= $useingst."',";
			$sSQLString .= '"'.$descript1.'",';
			$sSQLString .= $taxpcent.")";			

			$r21 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
			
			$sql = "update glmast set obal = obal - ".$total." where accountno = ".$acc2cr." and branch = '".$br2cr."' and sub = ".$sub2cr;
			$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
			
			
			break;
		case 'cas':
			// credit fixed asset account
			$sSQLString = "insert into trmain (ddate,accountno,branch,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
			$sSQLString .= "('".$ddate."',";
			$sSQLString .= $acc2cr.",'";		// credit the asset
			$sSQLString .= $br2cr."',";
			$sSQLString .= $acc2dr.",'";
			$sSQLString .= $br2dr."',";			
			$sSQLString .= $sub2dr.",";			
			$sSQLString .= "0,";
			$sSQLString .= $amount.",'";		// with the amount excluding GST
			$sSQLString .= $reference."','";					
			$sSQLString .= $taxtype."',";
			$sSQLString .= '"'.$descript1.'",';
			$sSQLString .= $taxpcent.")";			
			
			$r22 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
			
			$sql = "update glmast set obal = obal - ".$amount." where accountno = ".$acc2cr." and branch = '".$br2cr."' and sub = ".$sub2cr;
			$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
			
	
			
			$sSQLString = "insert into trmain (ddate,accountno,branch,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
			$sSQLString .= "('".$ddate."',";
			$sSQLString .= "701,'";		// credit asset control
			$sSQLString .= $br2cr."',";
			$sSQLString .= $acc2dr.",'";
			$sSQLString .= $br2dr."',";	
			$sSQLString .= $sub2dr.",";			
			$sSQLString .= "0,";
			$sSQLString .= $amount.",'";		// with the amount excluding GST
			$sSQLString .= $reference."','";					
			$sSQLString .= $taxtype."',";
			$sSQLString .= '"'.$descript1.'",';
			$sSQLString .= $taxpcent.")";					
			
			$r23 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
			
			$sql = "update glmast set obal = obal - ".$amount." where accountno = 701 and branch = '".$br2cr."'";
			$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
			
						

			if ($tax > 0) {
				if ($crgst == 'Y') {
					$useingst = 'N';
				} else {
					$useingst = 'Y';
				}
				$sSQLString = "insert into trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,gstrecon,descript1,taxpcent) values ";
				$sSQLString .= "('".$ddate."',";
				$sSQLString .= "870,'";		// credit trading tax payable
				$sSQLString .= $br2dr."',";
				$sSQLString .= $acc2dr.",'";
				$sSQLString .= $br2dr."',";	
				$sSQLString .= "0,";
				$sSQLString .= $tax.",'";		// with the amount of GST
				$sSQLString .= $reference."','";					
				$sSQLString .= $taxtype."','";
				$sSQLString .= $useingst."',";
				$sSQLString .= '"'.$descript1.'",';
				$sSQLString .= $taxpcent.")";					
			
				$r24 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
				
				$sql = "update glmast set obal = obal - ".$tax." where accountno = 870 and branch = '".$br2cr."'";
				$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
				
		
			}
			
			$sSQLString = "update fixassets set cost = cost - ".$amount." where accountno = ".$acc2cr." and branch = '".$br2cr."'";
			
			$r25 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
			
								
			break;
		case 'c50':
			$sSQLString = "insert into trmain (ddate,accountno,branch,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
			$sSQLString .= "('".$ddate."',";
			$sSQLString .= "702,'";		// credit accumulted depreciation control
			$sSQLString .= $br2cr."',";
			$sSQLString .= $acc2dr.",'";
			$sSQLString .= $br2dr."',";	
			$sSQLString .= $sub2dr.",";			
			$sSQLString .= "0,";
			$sSQLString .= $amount.",'";		// with the amount excluding GST
			$sSQLString .= $assetno."','";					
			$sSQLString .= $taxtype."',";
			$sSQLString .= '"'.$descript1.'",';
			$sSQLString .= $taxpcent.")";		
			
			$r26 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
			
			$sql = "update glmast set obal = obal - ".$amount." where accountno = 702 and branch = '".$br2cr."'";
			$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
			
		
			
			$sSQLString = "update fixassets set totdep = totdep + ".$amount.", dep5000 = dep5000 + ".$amount." where accountno = ".$assetno;
			
			$r27 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);

											
			break;			
		case 'ccr':
			// credit creditor account
			$sSQLString = "insert into trmain (ddate,accountno,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
			$sSQLString .= "('".$ddate."',";
			$sSQLString .= $acc2cr.",";		// credit the creditor
			$sSQLString .= $sub2cr.",";
			$sSQLString .= $acc2dr.",'";
			$sSQLString .= $br2dr."',";			
			$sSQLString .= $sub2dr.",";			
			$sSQLString .= "0,";
			$sSQLString .= $total.",'";		// with the amount including GST
			$sSQLString .= $reference."','";					
			$sSQLString .= $taxtype."',";
			$sSQLString .= '"'.$descript1.'",';
			$sSQLString .= $taxpcent.")";			
			
			$r28 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
			
			$cltdb = $_SESSION['s_cltdb'];
			mysql_select_db($cltdb) or die(mysql_error());	
			
			if ($aged == 'Current') {
				$sql = "update client_company_xref set current = current - ".$total." where company_id = ".$coyno." and crno = ".$acc2cr." and crsub = ".$sub2cr;
			}
			if ($aged == 'D30') {
				$sql = "update client_company_xref set d30 = d30 - ".$total." where company_id = ".$coyno." and crno = ".$acc2cr." and crsub = ".$sub2cr;
			}
			if ($aged == 'D60') {
				$sql = "update client_company_xref set d60 = d60 - ".$total." where company_id = ".$coyno." and crno = ".$acc2cr." and crsub = ".$sub2cr;
			}
			if ($aged == 'D90') {
				$sql = "update client_company_xref set d90 = d90 - ".$total." where company_id = ".$coyno." and crno = ".$acc2cr." and crsub = ".$sub2cr;
			}
			if ($aged == 'D120') {
				$sql = "update client_company_xref set d120 = d120 - ".$total." where company_id = ".$coyno." and crno = ".$acc2cr." and crsub = ".$sub2cr;
			}
			
			$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
			
			$moduledb = $_SESSION['s_findb'];
			mysql_select_db($moduledb) or die(mysql_error());	
			
			
	
			
			$sSQLString = "insert into trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,descript1,taxpcent) values ";
			$sSQLString .= "('".$ddate."',";
			$sSQLString .= "851,'";		// credit creditors control
			$sSQLString .= $br2dr."',";
			$sSQLString .= $acc2dr.",'";
			$sSQLString .= $br2dr."',";	
			$sSQLString .= "0,";
			$sSQLString .= $total.",'";		// with the amount including GST
			$sSQLString .= $reference."','";					
			$sSQLString .= $taxtype."',";
			$sSQLString .= '"'.$descript1.'",';
			$sSQLString .= $taxpcent.")";					
			
			$r29 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
			
			$sql = "update glmast set obal = obal - ".$total." where accountno = 851 and branch = '".$br2dr."'";
			$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
			
			$payrec = substr($reference,0,3);
			//if ($payrec <> 'PAY' && $payrec <> 'REC') {

				// add record to invhead
				
				$sSQLString = "insert into invhead (ddate,accountno,branch,sub,gldesc,transtype,ref_no,totvalue,tax,client,staff) values ";
				$sSQLString .= "('".$ddate."',";
				$sSQLString .= $acc2cr.",'";
				$sSQLString .= $br2dr."',";
				$sSQLString .= $sub2cr.",";
				$sSQLString .= '"'.$descript1.'",';
				$sSQLString .= "'".$payrec."','";
				$sSQLString .= $reference."',";
				$sSQLString .= $amount.",";
				$sSQLString .= $tax.",";
				$sSQLString .= "'".$client."',";
				$sSQLString .= "'".$uname."')";
				
				$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
				
				
				// insert records into invtrans
			
				$sSQLString = "insert into invtrans (item,price,quantity,taxtype,taxpcent,tax,ref_no,value) values ";
				$sSQLString .= '("'.$descript1.'",';
				$sSQLString .= $amount.",";
				$sSQLString .= "1,'";
				$sSQLString .= $taxtype."',";
				$sSQLString .= $taxpcent.",";
				$sSQLString .= $tax.",'";
				$sSQLString .= $reference."',";
				$sSQLString .= $amount.")";
				
				$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
			//}

			break;
		case 'cdr':
			// credit debtor account
			$sSQLString = "insert into trmain (ddate,accountno,sub,accno,br,subbr,debit,credit,reference,inv,gsttype,descript1,taxpcent) values ";
			$sSQLString .= "('".$ddate."',";
			$sSQLString .= $acc2cr.",";		// credit the debtor
			$sSQLString .= $sub2cr.",";
			$sSQLString .= $acc2dr.",'";
			$sSQLString .= $br2dr."',";			
			$sSQLString .= $sub2dr.",";			
			$sSQLString .= "0,";
			$sSQLString .= $total.",'";		// with the amount including GST
			$sSQLString .= $reference."','";			
			$sSQLString .= $xrefstr."','";
			$sSQLString .= $taxtype."',";
			$sSQLString .= '"'.$descript1.'",';
			$sSQLString .= $taxpcent.")";			
			
			$r30 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
			
			$cltdb = $_SESSION['s_cltdb'];
			mysql_select_db($cltdb) or die(mysql_error());	
			
			if ($aged == 'Current') {
				$sql = "update client_company_xref set current = current - ".$total." where company_id = ".$coyno." and drno = ".$acc2cr." and drsub = ".$sub2cr;
			}
			if ($aged == 'D30') {
				$sql = "update client_company_xref set d30 = d30 - ".$total." where company_id = ".$coyno." and drno = ".$acc2cr." and drsub = ".$sub2cr;
			}
			if ($aged == 'D60') {
				$sql = "update client_company_xref set d60 = d60 - ".$total." where company_id = ".$coyno." and drno = ".$acc2cr." and drsub = ".$sub2cr;
			}
			if ($aged == 'D90') {
				$sql = "update client_company_xref set d90 = d90 - ".$total." where company_id = ".$coyno." and drno = ".$acc2cr." and drsub = ".$sub2cr;
			}
			if ($aged == 'D120') {
				$sql = "update client_company_xref set d120 = d120 - ".$total." where company_id = ".$coyno." and drno = ".$acc2cr." and drsub = ".$sub2cr;
			}
			
			$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
			
			$moduledb = $_SESSION['s_findb'];
			mysql_select_db($moduledb) or die(mysql_error());	
			
	
			
			$sSQLString = "insert into trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,descript1,taxpcent) values ";
			$sSQLString .= "('".$ddate."',";
			$sSQLString .= "801,'";		// credit debtors control
			$sSQLString .= $br2dr."',";
			$sSQLString .= $acc2dr.",'";
			$sSQLString .= $br2dr."',";	
			$sSQLString .= "0,";
			$sSQLString .= $total.",'";		// with the amount including GST
			$sSQLString .= $reference."','";					
			$sSQLString .= $taxtype."',";
			$sSQLString .= '"'.$descript1.'",';
			$sSQLString .= $taxpcent.")";					
			
			$r31 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
			
			$sql = "update glmast set obal = obal - ".$total." where accountno = 801 and branch = '".$br2dr."'";
			$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
			
			$payrec = substr($reference,0,3);
			//if ($payrec <> 'PAY' && $payrec <> 'REC') {

				// add record to invhead
				
				$sSQLString = "insert into invhead (ddate,accountno,branch,sub,gldesc,transtype,ref_no,totvalue,tax,client,staff) values ";
				$sSQLString .= "('".$ddate."',";
				$sSQLString .= $acc2cr.",'";
				$sSQLString .= $br2dr."',";
				$sSQLString .= $sub2cr.",";
				$sSQLString .= '"'.$descript1.'",';
				$sSQLString .= "'".$payrec."','";
				$sSQLString .= $reference."',";
				$sSQLString .= $amount.",";
				$sSQLString .= $tax.",";
				$sSQLString .= "'".$client."',";
				$sSQLString .= "'".$uname."')";
				
				$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
				
				
				// insert records into invtrans
			
				$sSQLString = "insert into invtrans (item,price,quantity,taxtype,taxpcent,tax,ref_no,value) values ";
				$sSQLString .= '("'.$descript1.'",';
				$sSQLString .= $amount.",";
				$sSQLString .= "1,'";
				$sSQLString .= $taxtype."',";
				$sSQLString .= $taxpcent.",";
				$sSQLString .= $tax.",'";
				$sSQLString .= $reference."',";
				$sSQLString .= $amount.")";
				
				$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
			//}
						
/*
			// allocate payment against relevant INV
			if ($xrefuid != 0) {
				$query = "select paid from trmain where uid = ".$xrefuid;
				$result = mysql_query($query) or die($query);
				$row = mysql_fetch_array($result);
				extract($row);
				$pd = $paid + $total;
				$sSQLString = "update trmain set paid = ".$pd." where uid = ".$xrefuid;
				
				$r32 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
		
			}
*/

			break;
	}
	
	// if gst based on payment
	if ($gstinvpay == 'Payment') {

			if ($tax > 0) {
				if ($drgst == 'Y') {
					$useingst = 'N';
				} else {
					$useingst = 'Y';
				}
				if ($acc2cr >= 751 && $acc2cr <= 800) {
					$sSQLString = "insert into trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,gstrecon,descript1,taxpcent) values ";
					$sSQLString .= "('".$ddate."',";
					$sSQLString .= "871,'";		// debit GST Acount
					$sSQLString .= "0001',";
					$sSQLString .= "77,'";
					$sSQLString .= "0001',";	
					$sSQLString .= $tax.",";		// with the amount of GST
					$sSQLString .= "0,'";
					$sSQLString .= $reference."','";					
					$sSQLString .= $taxtype."','";
					$sSQLString .= $useingst."',";
					$sSQLString .= '"'.$descript1.'",';
					$sSQLString .= $taxpcent.")";					
				
					$r8 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
					
					$sql = "update glmast set obal = obal + ".$tax." where accountno = 871 and branch = '0001'";
					$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
				
					$sSQLString = "insert into trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,gstrecon,descript1,taxpcent) values ";
					$sSQLString .= "('".$ddate."',";
					$sSQLString .= "77,'";		
					$sSQLString .= "0001',";
					$sSQLString .= "871,'"; 
					$sSQLString .= "0001',";	
					$sSQLString .= "0,";		
					$sSQLString .= $tax.",'"; // with the amount of GST
					$sSQLString .= $reference."','";					
					$sSQLString .= $taxtype."','";
					$sSQLString .= $useingst."',";
					$sSQLString .= '"'.$descript1.'",';
					$sSQLString .= $taxpcent.")";					
				
					$r8 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
					
					$sql = "update glmast set obal = obal - ".$tax." where accountno = 77 and branch = '0001'";
					$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
				
				}
			}

	}
	

	// Inter branch Transactions
	if ($DGL == 'Y' and $CGL == 'Y' and $br2dr != $br2cr) {
	
		// credit inter branch transfer account
		$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
		$sSQLString .= "('".$ddate."',";
		$sSQLString .= "997".",'";		
		$sSQLString .= $br2dr."',";
		$sSQLString .= "0".",";
		$sSQLString .= $acc2cr.",'";
		$sSQLString .= $br2cr."',";			
		$sSQLString .= $sub2cr.",";		
		$sSQLString .= "0,";
		$sSQLString .= $total.",'";		// with the amount including GST
		$sSQLString .= $reference."','";					
		$sSQLString .= $taxtype."',";
		$sSQLString .= '"'.$descript1.'",';
		$sSQLString .= $taxpcent.")";			
		
		$r33 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
		
		$sql = "update glmast set obal = obal - ".$total." where accountno = 997 and branch = '".$br2dr."'";
		$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
		
				
			
		// debit inter branch transfer account
		$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
		$sSQLString .= "('".$ddate."',";
		$sSQLString .= "997".",'";		
		$sSQLString .= $br2cr."',";
		$sSQLString .= $sub2cr.",";
		$sSQLString .= $acc2dr.",'";
		$sSQLString .= $br2dr."',";			
		$sSQLString .= $sub2dr.",";			
		$sSQLString .= $total.",";		// with the amount including GST
		$sSQLString .= "0,'";
		$sSQLString .= $reference."','";					
		$sSQLString .= $taxtype."',";
		$sSQLString .= '"'.$descript1.'",';
		$sSQLString .= $taxpcent.")";			
		
		$r34 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
		
		$sql = "update glmast set obal = obal + ".$total." where accountno = 997 and branch = '".$br2cr."'";
		$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);		
		
					
			
	} // if posting between different branches
				
	// add transaction to audit trail
			$sSQLString = "insert into audit (ddate,acc2dr,brdr,subdr,acc2cr,brcr,subcr,descript1,reference,amount,tax,total,taxtype,taxpcent,entrydate,entrytime,username,userip) values ";
			$sSQLString .= "('".$ddate."',";
			$sSQLString .= $acc2dr.",'";		
			$sSQLString .= $br2dr."',";
			$sSQLString .= $sub2dr.",";
			$sSQLString .= $acc2cr.",'";
			$sSQLString .= $br2cr."',";			
			$sSQLString .= $sub2cr.",";			
			$sSQLString .= '"'.$descript1.'",';
			$sSQLString .= "'".$reference."',";		
			$sSQLString .= $amount.",";					
			$sSQLString .= $tax.",";
			$sSQLString .= $total.",'";
			$sSQLString .= $taxtype."',";
			$sSQLString .= $taxpcent.",'";	
			$sSQLString .= date("Y-m-d")."','";
			$sSQLString .= date("H:i:s")."','";				
			$sSQLString .= $unm."','";
			$sSQLString .= $uip."')";		
			
			$r35 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);

			
			// update the reference number
			$ref = substr($reference,0,3);
			$refno = substr($reference,3);
			if ($ref != 'CNV') {		// do not update when a Creditor Invoice
				$sSQLString = "update numbers set ".$ref." = ".$refno;
			}
			$r36 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);		
		
}

$sql = "delete from ".$table;
$rst = mysql_query($sql) or die (mysql_error());

?>