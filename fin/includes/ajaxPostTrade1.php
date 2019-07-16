
<?php
session_start();
//ini_set('display_errors', true);
if ($_SESSION['s_server'] == 'localhost') {
	$pathdb = '../../db.php';
	require($pathdb);
} else {
	$root = $_SERVER['DOCUMENT_ROOT'];
	$pathdb = $root.'/logtracc/db.php';
	require($pathdb);
}

$defbranch = $_SESSION['s_ubranch'];
$coyno = $_SESSION['s_coyid'];

$usersession = $_SESSION['usersession'];
$admindb = $_SESSION['s_admindb'];
date_default_timezone_set($_SESSION['s_timezone']);

mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$uip = $userip;
$unm = $uname;

$table = 'ztmp'.$user_id.'_trading';
date_default_timezone_set($_SESSION['s_timezone']);

//type:type,ddate:ddate,descript:descript,ref:ref,acc:acc,asb:asb,loc:loc,postaladdress:postaladdres,deliveryaddress:deliveryaddress,client:clt
$transtype = strtoupper($_REQUEST['type']);
$ddate = $_REQUEST['ddate'];
$descript1 = $_REQUEST['descript'];
$reference = strtoupper($_REQUEST['ref']);
$acc = $_REQUEST['acc'];
$asb = $_REQUEST['asb'];
$loc = $_REQUEST['loc'];
$paytype = $_REQUEST['paytype'];
$xref = '';
$postaladdress = $_REQUEST['postaladdress'];
$deliveryaddress = $_REQUEST['deliveryaddress'];
$client = $_REQUEST['clt'];

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

switch ($transtype) {
	case 'C_S':
	$a2dr = $acc;
	$b2dr = '';
	$s2dr = $asb;
	$a2cr = 0;
	$b2cr = '';
	$s2cr = 0;
	break;
	case 'INV':
	$a2dr = $acc;
	$b2dr = '';
	$s2dr = $asb;
	$a2cr = 0;
	$b2cr = '';
	$s2cr = 0;
	break;
	case 'CRN':
	$a2cr = $acc;
	$b2cr = '';
	$s2cr = $asb;
	$a2dr = 0;
	$b2dr = '';
	$s2dr = 0;
	break;
	case 'GRN':
	$a2cr = $acc;
	$b2cr = '';
	$s2cr = $asb;
	$a2dr = 0;
	$b2dr = '';
	$s2dr = 0;
	break;
	case 'RET':
	$a2dr = $acc;
	$b2dr = '';
	$s2dr = $asb;
	$a2cr = 0;
	$b2cr = '';
	$s2cr = 0;
	break;
	case 'REQ':
	$a2dr = $acc;
	$b2dr = '';
	$s2dr = $asb;
	$a2cr = 0;
	$b2cr = '';
	$s2cr = 0;
	break;

}


$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select sum(tax) as totaltax, sum(value) as totalamount from ".$table;
$r = mysql_query($q) or die(mysql_error().' '.$q);
$row = mysql_fetch_array($r);
extract($row);
$totalvalue = $totalamount + $totaltax;

	switch ($paytype) {
	case 'C_S':
		$cash = $totalvalue;
		$cheque = 0;
		$mcard = 0;
		$ecard = 0;
		break;
	case 'CHQ':
		$cash = 0;
		$cheque = $totalvalue;
		$mcard = 0;
		$ecard = 0;
		break;	
	case 'CRD':
		$cash = 0;
		$cheque = 0;
		$mcard = $totalvalue;
		$ecard = 0;
		break;		
	case 'EFT':
		$cash = 0;
		$cheque = 0;
		$mcard = 0;
		$ecard = $totalvalue;
		break;		
	default:
		$cash = 0;
		$cheque = 0;
		$mcard = 0;
		$ecard = 0;
		break;			
	}

	if (empty($b2dr)) {$br2dr = ' ';}
	if (empty($s2dr)) {$sub2dr = 0;}
	if (empty($b2cr)) {$br2cr = ' ';}
	if (empty($s2cr)) {$sub2cr = 0;}	
	
	switch ($transtype) {
		case 'C_S':
			$ano = $a2dr;
			$bno = $b2dr;
			$sno = $s2dr;
			$taxdrcr = 'cr';
			break;
		case 'INV':
			$ano = $a2dr;
			$bno = $b2dr;
			$sno = $s2dr;
			$taxdrcr = 'cr';
			break;
		case 'CRN':
			$ano = $a2cr;
			$bno = $b2cr;
			$sno = $s2cr;
			$taxdrcr = 'dr';
			break;
		case 'GRN':
			$ano = $a2cr;
			$bno = $b2cr;
			$sno = $s2cr;
			$taxdrcr = 'dr';
			break;
		case 'CHQ':
			$ano = $a2cr;
			$bno = $b2cr;
			$sno = $s2cr;
			$taxdrcr = 'dr';
			break;			
		case 'RET':
			$ano = $a2dr;
			$bno = $b2dr;
			$sno = $s2dr;
			$taxdrcr = 'cr';
			break;
		case 'REC':
			$ano = $a2cr;
			$bno = $b2cr;
			$sno = $s2cr;
			$taxdrcr = '';
			break;
		case 'PAY':
			$ano = $a2dr;
			$bno = $b2dr;
			$sno = $s2dr;
			$taxdrcr = '';
			break;
		case 'REQ':
			$ano = $a2dr;
			$bno = $b2dr;
			$sno = $s2dr;
			$taxdrcr = '';
			break;
		case 'ADJ':
			$ano = $a2dr;
			$bno = $b2dr;
			$sno = $s2dr;
			$taxdrcr = '';
			break;
	}


	// insert invhead record
	$sSQLString = "insert into invhead (ddate,accountno,branch,sub,gldesc,transtype,ref_no,xref,totvalue,tax,cash,cheque,mcard,ecard,staff,postaladdress,deliveryaddress,client) values ";
	$sSQLString .= "('".$ddate."',";
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
	
	
	//*******************************************************************************************************************************************
	// Invoice
	//*******************************************************************************************************************************************
	if ($transtype == 'INV') {
			// debit debtor account
			$sSQLString = "insert into trmain (ddate,accountno,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
			$sSQLString .= "('".$ddate."',";
			$sSQLString .= $a2dr.",";		// debit the debtor
			$sSQLString .= $s2dr.",";
			$sSQLString .= '0'.",'";
			$sSQLString .= ' '."',";			
			$sSQLString .= '0'.",";			
			$sSQLString .= $totalvalue.",";		// with the amount including GST
			$sSQLString .= "0,'";
			$sSQLString .= $reference."','";					
			$sSQLString .= $taxtype."','";
			$sSQLString .= $descript1."',";
			$sSQLString .= $taxpcent.")";			
			
			$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
			
			$cltdb = $_SESSION['s_cltdb'];
			mysql_select_db($cltdb) or die(mysql_error());	
			
			if ($aged == 'Current') {
				$sql = "update client_company_xref set current = current + ".$totalvalue." where company_id = ".$coyno." and drno = ".$a2dr." and drsub = ".$s2dr;
			}
			if ($aged == 'D30') {
				$sql = "update client_company_xref set d30 = d30 + ".$totalvalue." where company_id = ".$coyno." and drno = ".$acdr." and drsub = ".$s2dr;
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
			$sSQLString .= "('".$ddate."',";
			$sSQLString .= "801,'";		// debit debtors control
			$sSQLString .= $defbranch."',";
			$sSQLString .= '0'.",'";
			$sSQLString .= ' '."',";	
			$sSQLString .= $totalvalue.",";		// with the amount including GST
			$sSQLString .= "0,'";
			$sSQLString .= $reference."','";					
			$sSQLString .= $taxtype."','";
			$sSQLString .= $descript1."',";
			$sSQLString .= $taxpcent.")";					
			
			$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
			
				
			$sql = "update glmast set obal = obal + ".$totalamount." where accountno = 801 and branch = '".$defbranch."' and sub = 0";
			$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);	
			
			// credit relevant income accounts
			$q = "select * from ".$table;
			$result = mysql_query($q) or die(mysql_error());
			while ($row = mysql_fetch_array($result)) {
				extract($row);
				$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
				$sSQLString .= "('".$ddate."',";
				$sSQLString .= $sellacc.",";		//credit the income account
				$sSQLString .= "'".$defbranch."',";
				$sSQLString .= $sellsub.",";
				$sSQLString .= $a2dr.",'";
				$sSQLString .= ' '."',";			
				$sSQLString .= $s2dr.",";			
				$sSQLString .= "0,";		
				$sSQLString .= $totalamount.",'";  // with the amount excluding GST
				$sSQLString .= $reference."','";					
				$sSQLString .= $taxtype."','";
				$sSQLString .= $descript1."',";
				$sSQLString .= $taxpcent.")";			
				
				$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
				
				$sql = "update glmast set obal = obal - ".$totalamount." where accountno = ".$sellacc." and branch = '".$defbranch."' and sub = ".$sellsub;
				$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);	

			}
	}
	
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
			$sSQLString .= $descript1."',";
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
			$sSQLString .= $descript1."',";
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
				$sSQLString .= $totalamount.",";		// with the amount excluding GST
				$sSQLString .= "0,'";
				$sSQLString .= $reference."','";					
				$sSQLString .= $taxtype."','";
				$sSQLString .= $descript1."',";
				$sSQLString .= $taxpcent.")";			
				
				$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
				
				$sql = "update glmast set obal = obal + ".$totalamount." where accountno = ".$purchacc." and branch = '".$defbranch."' and sub = ".$purchsub;
				$rsql = mysql_query($sql) or die(mysql_error().' '.$sql);	

			}
	}
	
	
	//*************************************************************************************************************************************************************
	// Create double entries relating to GST for each line of trading document
	//*************************************************************************************************************************************************************
						
	$q = "select * from ".$table;
	$result = mysql_query($q) or die(mysql_error());
	while ($row = mysql_fetch_array($result)) {
		extract($row);
		
		// insert each line's tax where applicable into trmain
		if ($tax > 0) {
			if ($taxdrcr == 'dr') {
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
				$sSQLString .= $descript1."',";
				$sSQLString .= $taxpcent.")";					
			} else {
				$sSQLString = "insert into trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values ";
				$sSQLString .= "('".$ddate."',";
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
				$sSQLString .= $descript1."',";
				$sSQLString .= $taxpcent.")";					
			}// if
			$r = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
	
		} //if
		
	
	} //while


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
					$newtotval = ($avgcost*tqty) + $value;
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
			$sSQLString .= $descript1."','";
			$sSQLString .= $reference."',";		
			$sSQLString .= $totalamount.",";					
			$sSQLString .= $totaltax.",";
			$sSQLString .= $totalvalue.",'";
			$sSQLString .= date("Y-m-d")."','";
			$sSQLString .= date("H:i:s")."','";				
			$sSQLString .= $unm."','";
			$sSQLString .= $uip."')";		
			
			$r35 = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);

	
$sql = "delete from ".$table;
$rst = mysql_query($sql) or die (mysql_error());
	


?>

