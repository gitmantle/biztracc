<?php
session_start();
//ini_set('display_errors', true);

$coyno = $_SESSION['s_coyid'];

$usersession = $_SESSION['usersession'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

//$db->beginTransaction();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
extract($row);
$uip = $row['userip'];
$user_id = $row['user_id'];
$unm = $row['uname'];

$table = 'ztmp'.$user_id.'_trading';
date_default_timezone_set($_SESSION['s_timezone']);
$serialtable = 'ztmp'.$user_id.'_serialnos';

$transtype = strtoupper($_REQUEST['type']);
$ddate = $_REQUEST['ddate'];
$descript1 = $_REQUEST['descript'];
$reference = strtoupper($_REQUEST['ref']);
$yourref = $_REQUEST['yourref'];
$reference = strtr($reference,chr(13),''); // replace carriage return blank
$reference = strtr($reference,chr(10),''); // replace line feed with blank
$reference = trim($reference);
if (isset($_REQUEST['staffmember'])) {
	$staffmember = $_REQUEST['staffmember'];
} else {
	$staffmember = '';
}

$findb = $_SESSION['s_findb'];
$cltdb = $_SESSION['s_cltdb'];

$acc = $_REQUEST['acc'];
$asb = $_REQUEST['asb'];
$loc = $_REQUEST['loc'];
if ($_REQUEST['forex'] != '') {
	$f = explode('~',$_REQUEST['forex']);
	$fxcode = $f[0];
	$fxrate = $f[1];
} else {
	$db->query("select currency from ".$findb.".forex where def_forex = 'Yes'");
	$row = $db->single();
	extract($row);
	$fxcode = $currency;
	$fxrate = 1;
}
$localcurrency = $_SESSION['s_localcurrency'];

$xref = '';
if (isset($_REQUEST['postaladdress'])) {
	$postaladdress = $_REQUEST['postaladdress'];
} else {
	$postaladdress = '';
}
if (isset($_REQUEST['deliveryaddress'])) {
	$deliveryaddress = $_REQUEST['deliveryaddress'];
} else {
	$deliveryaddress = '';
}
$client = $_REQUEST['clt'];
$paymentmethod = strtolower($_REQUEST['paymethod']);
if(isset($_SESSION["s_signature"])) {
	$signature = $_SESSION["s_signature"];
} else {
	$signature = "";
}
if(isset($_SESSION["s_purchref"])) {
	$transref = $_SESSION["s_purchref"];
} else {
	$transref = "";
}


$db->query("select gsttype as gstinvpay, roundto as rnd from ".$findb.".globals");
$row = $db->single();
extract($row);

$rno = '';

if (isset($_SESSION['s_dn2inv'])) {	
	$dn2inv = $_SESSION['s_dn2inv'];				
} else {
	$dn2inv = 'N';
}

if ($transtype == 'RET' || $transtype == 'CRN') {
	$db->query("select ref from ".$findb.".".$table." limit 1");
	$row = $db->single();
	extract($row);
	$rno = $ref;
	
	$db->query("select branch from ".$findb.".invhead where ref_no = :rno"); 
	$db->bind(':rno', $rno);
	$row = $db->single();
	extract($row);
	$defbranch = $branch;
	
} else {
	
	$db->query("select loc from ".$findb.".".$table." limit 1");
	$row = $db->single();
	extract($row);
	$lid = $loc;

	// get branch from stklocs
	$db->query("select branch from ".$findb.".stklocs where uid = :loc");
	$db->bind(':loc', $lid);
	$row = $db->single();
	extract($row);
	$defbranch = $branch;
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

switch ($transtype) {
	case 'C_S':
	$a2dr = $acc;
	$b2dr = $defbranch;
	$s2dr = $asb;
	$a2cr = 0;
	$b2cr = $defbranch;
	$s2cr = 0;
	break;
	case 'C_P':
	$a2cr = $acc;
	$b2cr = $defbranch;
	$s2cr = $asb;
	$a2dr = 0;
	$b2dr = $defbranch;
	$s2dr = 0;
	break;
	case 'INV':
	$a2dr = $acc;
	$b2dr = $defbranch;
	$s2dr = $asb;
	$a2cr = 0;
	$b2cr = $defbranch;
	$s2cr = 0;
	break;
	case 'CRN':
	$a2cr = $acc;
	$b2cr = $defbranch;
	$s2cr = $asb;
	$a2dr = 0;
	$b2dr = $defbranch;
	$s2dr = 0;
	break;
	case 'GRN':
	$a2cr = $acc;
	$b2cr = $defbranch;
	$s2cr = $asb;
	$a2dr = 0;
	$b2dr = $defbranch;
	$s2dr = 0;
	break;
	case 'RET':
	$a2dr = $acc;
	$b2dr = $defbranch;
	$s2dr = $asb;
	$a2cr = 0;
	$b2cr = $defbranch;
	$s2cr = 0;
	break;
	case 'REQ':
	$a2dr = $acc;
	$b2dr = $defbranch;
	$s2dr = $asb;
	$a2cr = 0;
	$b2cr = $defbranch;
	$s2cr = 0;
	break;

}


$db->query('select taxpcent from '.$findb.'.taxtypes where uid = 1');
$row = $db->single();
extract($row);
$tpcent = $taxpcent;

$db->query("select sum(tax) as totaltax, sum(value) as totalamount from ".$findb.".".$table);
$row = $db->single();
extract($row);

$totalvalue = $totalamount + $totaltax; // in local currency
$rounding = 0;

	switch ($paymentmethod) {
	case 'csh':
		if ($transtype == 'C_S') {
			// sort out rounding
			$totval = $totalvalue;
			switch ($rnd) {
				case 0:
					$totalvalue = $totalvalue;
					break;
				case 5:
					$tot = round($totalvalue * 2, 1) / 2;
					$totalvalue = $tot;
					break;
				case 10:
					$tot = round($totalvalue,1);
					$totalvalue = $tot;
					break;
			}
		
			$totalamount = $totalvalue/(1 + $tpcent/100);
			$totaltax = $totalvalue - $totalamount;
			$rounding = round(($totalvalue - $totval),2);
		}
		$cash = $totalvalue;
		$cheque = 0;
		$eftpos = 0;
		$ccard = 0;
		break;
	case 'chq':
		$cash = 0;
		$cheque = $totalvalue;
		$eftpos = 0;
		$ccard = 0;
		break;	
	case 'crd':
		$cash = 0;
		$cheque = 0;
		$ccard = $totalvalue;
		$eftpos = 0;
		break;		
	case 'eft':
		$cash = 0;
		$cheque = 0;
		$ccard = 0;
		$eftpos = $totalvalue;
		break;		
	default:
		$cash = 0;
		$cheque = 0;
		$ccard = 0;
		$eftpos = 0;
		break;			
	}

	if (empty($b2dr)) {$br2dr = $defbranch;}
	if (empty($s2dr)) {$sub2dr = 0;}
	if (empty($b2cr)) {$br2cr = $defbranch;}
	if (empty($s2cr)) {$sub2cr = 0;}	
	
	switch ($transtype) {
		case 'C_S':
			$ano = $a2dr;
			$bno = $b2dr;
			$sno = $s2dr;
			$taxdrcr = 'cr';
			break;
		case 'C_P':
			$ano = $a2cr;
			$bno = $b2cr;
			$sno = $s2cr;
			$taxdrcr = 'dr';
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
	$db->query("insert into ".$findb.".invhead (ddate,accountno,branch,sub,gldesc,transtype,ref_no,xref,totvalue,tax,cash,cheque,eftpos,ccard,staff,postaladdress,deliveryaddress,client,signature,currency,rate,your_ref) values (:ddate,:accountno,:branch,:sub,:gldesc,:transtype,:ref_no,:xref,:totvalue,:tax,:cash,:cheque,:eftpos,:ccard,:staff,:postaladdress,:deliveryaddress,:client,:signature,:currency,:rate,:yourref)");
	$db->bind(':ddate', $ddate);
	$db->bind(':accountno', $ano);
	$db->bind(':branch', $bno);
	$db->bind(':sub', $sno);
	$db->bind(':gldesc', $descript1);
	$db->bind(':transtype', $transtype);
	$db->bind(':ref_no', $reference);
	$db->bind(':xref', $rno);
	$db->bind(':totvalue', $totalamount);
	$db->bind(':tax', $totaltax);
	$db->bind(':cash', $cash);
	$db->bind(':cheque', $cheque);
	$db->bind(':eftpos', $eftpos);
	$db->bind(':ccard', $ccard);
	$db->bind(':staff', $staffmember);
	$db->bind(':postaladdress', $postaladdress);
	$db->bind(':deliveryaddress', $deliveryaddress);
	$db->bind(':client', $client);
	$db->bind(':signature', $signature);
	$db->bind(':currency', $fxcode);
	$db->bind(':rate', $fxrate);
	$db->bind(':yourref', $yourref);
	
	$db->execute();	
	
	// add records to allocations if RET or CRN
	if ($transtype == 'CRN' || $transtype == 'RET') {
		$tot = $totalamount + $totaltax;
		$db->query('insert into '.$findb.'.allocations (ddate,amount,fromref,toref) values (:ddate,:tot,:reference,:transref)');
		$db->bind(':ddate', $ddate);
		$db->bind(':tot', $tot);
		$db->bind(':reference', $reference);
		$db->bind(':transref', $transref);
		
		$db->execute();	
	}
		
		
	$db->query("select * from ".$findb.".".$table);
	$rows = $db->resultset();
	foreach ($rows as $row) {
		extract($row);
		$rid = $uid;
		$qty = $quantity;
		$stk = $stock;
		$cst = $avcost;
		
		// insert records into invtrans
		
		$db->query("insert into ".$findb.".invtrans (itemcode,item,price,quantity,unit,taxtype,taxpcent,tax,ref_no,value,discount,disc_type,grnlineno,currency,rate,your_ref) values (:itemcode,:item,:price,:quantity,:unit,:taxtype,:taxpcent,:tax,:ref_no,:value,:discount,:disc_type,:grnlineno,:currency,:rate,:yourref)");
		$db->bind(':itemcode', $itemcode);
		$db->bind(':item', $item);
		$db->bind(':price', $price);
		$db->bind(':quantity', $quantity);
		$db->bind(':unit', $unit);
		$db->bind(':taxtype', $taxtype);
		$db->bind(':taxpcent', $taxpcent);
		$db->bind(':tax', $tax);
		$db->bind(':ref_no', $reference);
		$db->bind(':value', $value + $discount);
		$db->bind(':discount', $discount);
		$db->bind(':disc_type', $disctype);
		$db->bind(':grnlineno', $rid);
		$db->bind(':currency', $fxcode);
		$db->bind(':rate', $fxrate);
		$db->bind(':yourref', $yourref);
			
		$db->execute();	
			
		if ($transtype == 'RET' || $transtype == 'CRN') {
			$db->query("update ".$findb.".invtrans set returns = returns + ".$qty." where ref_no = '".$rno."' and uid = ".$rid);
			$db->execute();	
			
			// update invhead.valreturned with calculated value of return
			$valret = $value + $tax;
			$db->query("update ".$findb.".invhead set valreturned = valreturned + ".$valret." where ref_no = '".$rno."'");
			$db->execute();	
			
		}
		
		if ($transtype == 'INV' || $transtype == 'C_S') {
			// if not serial numbered allocate sale items against oldest grn or c_p
			$db->query("select trackserial,stock from ".$findb.".stkmast where itemcode = '".$itemcode."'");
			$row = $db->single();
			extract($row);
			
			if ($trackserial == 'No' and $stock == 'Stock') {
				$db->query("select uid as sid,quantity as q,returns as r from ".$findb.".invtrans where (substring(ref_no,1,3) = 'GRN' or substring(ref_no,1,3) = 'C_P')  and itemcode = '".$itemcode."' order by uid");
				$rows = $db->resultset();
				$available = $quantity;
				foreach ($rows as $row) {
					extract($row);
					$dif = $q - $r;
					
					if ($dif > 0 && $available > 0) {
						if ($dif <= $available) {
							$toallocate = $available - $dif;
							$db->query("update ".$findb.".invtrans set returns = returns + ".$dif." where uid = ".$sid);
							$db->execute();
							$available = $toallocate - $dif;
						} else {
							$db->query("update ".$findb.".invtrans set returns = returns + ".$available." where uid = ".$sid);
							$db->execute();
							$available = 0;
						}
					}
				}
			}
		}
		if ($transtype == 'CRN') {
			// if not serial numbered allocate sale items against oldest grn or c_p
			$db->query("select trackserial from ".$findb.".stkmast where itemcode = '".$itemcode."'");
			$row = $db->single();
			extract($row);
				
			if ($trackserial == 'No') {
				$db->query("select uid as sid,quantity as q,returns as r from ".$findb.".invtrans where (substring(ref_no,1,3) = 'GRN' or substring(ref_no,1,3) = 'C_P')  and itemcode = '".$itemcode."' order by uid desc");
				$rows = $db->resultset();
				$available = $quantity;
				foreach ($rows as $row) {
					extract($row);	
					$dif = $q - $r;
					
					if ($dif > 0 && $available > 0 && $r > 0) {
						if ($dif < $available) {
							$db->query("update ".$findb.".invtrans set returns = returns - ".$dif." where uid = ".$sid);
							$db->execute();
							$available = $available - $dif;
						} else {
							$db->query("update ".$findb.".invtrans set returns = returns - ".$available." where uid = ".$sid);
							$db->execute();
							$available = 0;
						}
					}
				}
			}
		}
	}
	

	if ($transtype == 'REQ') {
		$db->query("update ".$findb.".invhead set branch = '".$defbranch."' where ref_no = '".$reference."'");
		$db->execute();	
	}
	
	if ($rounding != 0) {
		// insert rounding record into invtrans
		
		$db->query("insert into ".$findb.".invtrans (itemcode,item,price,quantity,unit,taxtype,taxpcent,tax,ref_no,value,discount,disc_type,grnlineno,currency,rate,your_ref) values (:itemcode,:item,:price,:quantity,:unit,:taxtype,:taxpcent,:tax,:ref_no,:value,:discount,:disc_type,:currency,:rate,:yourref)");
		$db->bind(':itemcode', $itemcode);
		$db->bind(':item', 'Rounding');
		$db->bind(':price', 0);
		$db->bind(':quantity', 0);
		$db->bind(':unit', '');
		$db->bind(':taxtype', $taxtype);
		$db->bind(':taxpcent', $taxpcent);
		$db->bind(':tax', 0);
		$db->bind(':ref_no', $reference);
		$db->bind(':value', $rounding);
		$db->bind(':discount', 0);
		$db->bind(':disc_type', '');
		$db->bind(':currency', $fxcode);
		$db->bind(':rate', $fxrate);
		$db->bind(':yourref', $yourref);
			
		$db->execute();	
	}

	// insert GL records	
	
	//*******************************************************************************************************************************************
	// Invoice
	//*******************************************************************************************************************************************
	if ($transtype == 'INV') {
			// debit debtor account
			$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent,currency,rate,your_ref) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent,:currency,:rate,:yourref)");
			$db->bind(':ddate', $ddate);
			$db->bind(':accountno', $a2dr);	//debit the debtor
			$db->bind(':branch', $defbranch);
			$db->bind(':sub', $s2dr);
			$db->bind(':accno', $a2cr);
			$db->bind(':br', $defbranch);
			$db->bind(':subbr', 0);
			$db->bind(':debit', $totalvalue);	// with the amount including GST
			$db->bind(':credit', 0);
			$db->bind(':reference', $reference);
			$db->bind(':gsttype', $taxtype);
			$db->bind(':descript1', $descript1);
			$db->bind(':taxpcent', $taxpcent);
			$db->bind(':currency', $fxcode);
			$db->bind(':rate', $fxrate);
			$db->bind(':yourref', $yourref);
			
			$db->execute();	
			
			if ($aged == 'Current') {
				$db->query("update ".$cltdb.".client_company_xref set current = current + ".$totalvalue." where company_id = ".$coyno." and drno = ".$a2dr." and drsub = ".$s2dr);
			}
			if ($aged == 'D30') {
				$db->query("update ".$cltdb.".client_company_xref set d30 = d30 + ".$totalvalue." where company_id = ".$coyno." and drno = ".$a2dr." and drsub = ".$s2dr);
			}
			if ($aged == 'D60') {
				$db->query("update ".$cltdb.".client_company_xref set d60 = d60 + ".$totalvalue." where company_id = ".$coyno." and drno = ".$a2dr." and drsub = ".$s2dr);
			}
			if ($aged == 'D90') {
				$db->query("update ".$cltdb.".client_company_xref set d90 = d90 + ".$totalvalue." where company_id = ".$coyno." and drno = ".$a2dr." and drsub = ".$s2dr);
			}
			if ($aged == 'D120') {
				$db->query("update ".$cltdb.".client_company_xref set d120 = d120 + ".$totalvalue." where company_id = ".$coyno." and drno = ".$a2dr." and drsub = ".$s2dr);
			}
			
			$db->execute();	
			
			// debit debtor control account
			$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,descript1,taxpcent,currency,rate,your_ref) values (:ddate,:accountno,:branch,:accno,:br,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent,:currency,:rate,:yourref)");
			$db->bind(':ddate', $ddate);
			$db->bind(':accountno', 801);	// debit debtors control
			$db->bind(':branch', $defbranch);
			$db->bind(':accno', $a2cr);
			$db->bind(':br', $defbranch);
			$db->bind(':debit', $totalvalue);	// with the amount including GST
			$db->bind(':credit', 0);
			$db->bind(':reference', $reference);
			$db->bind(':gsttype', $taxtype);
			$db->bind(':descript1', $descript1);
			$db->bind(':taxpcent', $taxpcent);
			$db->bind(':currency', $fxcode);
			$db->bind(':rate', $fxrate);
			$db->bind(':yourref', $yourref);
			
			$db->execute();	
			
			$db->query("update ".$findb.".glmast set obal = obal + ".$totalvalue." where accountno = 801 and branch = '".$defbranch."' and sub = 0");
			$db->execute();	
			
			// credit relevant income accounts
			$db->query("select * from ".$findb.".".$table);
			$rows = $db->resultset();
			foreach ($rows as $row) {
				extract($row);
				$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent,currency,rate,your_ref) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent,:currency,:rate,:yourref)");
				$db->bind(':ddate', $ddate);
				$db->bind(':accountno', $sellacc);	//credit the income account
				$db->bind(':branch', $defbranch);
				$db->bind(':sub', $sellsub);
				$db->bind(':accno', $a2dr);
				$db->bind(':br', '');
				$db->bind(':subbr', $s2dr);
				$db->bind(':debit', 0);	
				$db->bind(':credit', $value - $discamount);		// with the amount excluding GST less any discount
				$db->bind(':reference', $reference);
				$db->bind(':gsttype', $taxtype);
				$db->bind(':descript1', $descript1);
				$db->bind(':taxpcent', $taxpcent);
				$db->bind(':currency', $fxcode);
				$db->bind(':rate', $fxrate);
				$db->bind(':yourref', $yourref);
				
				$db->execute();	
			
				$db->query("update ".$findb.".glmast set obal = obal - ".$value." where accountno = ".$sellacc." and branch = '".$defbranch."' and sub = ".$sellsub);
				$db->execute();	
				
				if ($discamount != 0) {
					$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent,currency,rate,your_ref) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent,:currency,:rate,:yourref)");
					$db->bind(':ddate', $ddate);
					$db->bind(':accountno', 76);	//credit the discount on sales account
					$db->bind(':branch', $defbranch);
					$db->bind(':sub', $sellsub);
					$db->bind(':accno', $a2dr);
					$db->bind(':br', '');
					$db->bind(':subbr', $s2dr);
					$db->bind(':debit', 0);	
					$db->bind(':credit', $discamount);		// with  discount
					$db->bind(':reference', $reference);
					$db->bind(':gsttype', $taxtype);
					$db->bind(':descript1', $descript1);
					$db->bind(':taxpcent', $taxpcent);
					$db->bind(':currency', $fxcode);
					$db->bind(':rate', $fxrate);
					$db->bind(':yourref', $yourref);
					
					$db->execute();	
				
					$db->query("update ".$findb.".glmast set obal = obal - ".$discount." where accountno = 76 and branch = '".$defbranch."' and sub = ".$sellsub);
					$db->execute();	
				}

			}
	}

	//*******************************************************************************************************************************************
	// Cash Sale
	//*******************************************************************************************************************************************
	if ($transtype == 'C_S') {
			// debit bank etc. account
			$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
			$db->bind(':ddate', $ddate);
			$db->bind(':accountno', $a2dr);	//debit the debtor
			$db->bind(':branch', $defbranch);
			$db->bind(':sub', $s2dr);
			$db->bind(':accno', $sellacc);
			$db->bind(':br', $defbranch);
			$db->bind(':subbr', $sellsub);
			$db->bind(':debit', $totalvalue);	// with the amount including GST
			$db->bind(':credit', 0);
			$db->bind(':reference', $reference);
			$db->bind(':gsttype', $taxtype);
			$db->bind(':descript1', $descript1);
			$db->bind(':taxpcent', $taxpcent);
			
			$db->execute();	
			
			$db->query("update ".$findb.".glmast set obal = obal + ".$totalvalue." where accountno = ".$a2dr." and branch = '".$defbranch."' and sub = ".$s2dr);
			$db->execute();	
			
			// credit relevant income accounts
			$db->query("select * from ".$findb.".".$table);
			$rows = $db->resultset();
			
			foreach ($rows as $row) {
				extract($row);
				$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
				$db->bind(':ddate', $ddate);
				$db->bind(':accountno', $sellacc);	//credit the income account
				$db->bind(':branch', $defbranch);
				$db->bind(':sub', $sellsub);
				$db->bind(':accno', $a2dr);
				$db->bind(':br', $defbranch);
				$db->bind(':subbr', $s2dr);
				$db->bind(':debit', 0);	
				$db->bind(':credit', $value-$discamount);		// with the amount excluding GST and discount
				$db->bind(':reference', $reference);
				$db->bind(':gsttype', $taxtype);
				$db->bind(':descript1', $descript1);
				$db->bind(':taxpcent', $taxpcent);
				
				$db->execute();	
			
				$db->query("update ".$findb.".glmast set obal = obal - ".$value." where accountno = ".$sellacc." and branch = '".$defbranch."' and sub = ".$sellsub);
				$db->execute();	
				
				if ($discamount != 0) {
					$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent,currency,rate,your_ref) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent,:currency,:rate,:yourref)");
					$db->bind(':ddate', $ddate);
					$db->bind(':accountno', 76);	//credit the discount on sales account
					$db->bind(':branch', $defbranch);
					$db->bind(':sub', $sellsub);
					$db->bind(':accno', $a2dr);
					$db->bind(':br', '');
					$db->bind(':subbr', $s2dr);
					$db->bind(':debit', 0);	
					$db->bind(':credit', $discamount);		// with the  discount
					$db->bind(':reference', $reference);
					$db->bind(':gsttype', $taxtype);
					$db->bind(':descript1', $descript1);
					$db->bind(':taxpcent', $taxpcent);
					$db->bind(':currency', $fxcode);
					$db->bind(':rate', $fxrate);
					$db->bind(':yourref', $yourref);
					
					$db->execute();	
				
					$db->query("update ".$findb.".glmast set obal = obal - ".$discount." where accountno = 76 and branch = '".$defbranch."' and sub = ".$sellsub);
					$db->execute();					
				}
			}
		
		if ($rounding > 0) {
			// credit rounding account
			
			$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent,yourref) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent,:yourref)");
			$db->bind(':ddate', $ddate);
			$db->bind(':accountno', 99);	
			$db->bind(':branch', $defbranch);
			$db->bind(':sub', 0);
			$db->bind(':accno', $a2dr);
			$db->bind(':br', $defbranch);
			$db->bind(':subbr', $s2dr);
			$db->bind(':debit', 0);	
			$db->bind(':credit', $rounding);
			$db->bind(':reference', $reference);
			$db->bind(':gsttype', 'N_T');
			$db->bind(':descript1', 'Rounding');
			$db->bind(':taxpcent', 0);
			$db->bind(':yourref', $yourref);
			
			$db->execute();	
			
			$db->query("update ".$findb.".glmast set obal = obal - ".$rounding." where accountno = 99 and branch = '".$defbranch."' and sub = 0");
			$db->execute();	
		}
		
		if ($rounding < 0) {
			$rounding = $rounding * -1;

			// credit rounding account
			
			$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent,yourref) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent,:yourref)");
			$db->bind(':ddate', $ddate);
			$db->bind(':accountno', 99);	
			$db->bind(':branch', $defbranch);
			$db->bind(':sub', 0);
			$db->bind(':accno', $a2dr);
			$db->bind(':br', $defbranch);
			$db->bind(':subbr', $s2dr);
			$db->bind(':debit', $rounding);	
			$db->bind(':credit', 0);
			$db->bind(':reference', $reference);
			$db->bind(':gsttype', 'N_T');
			$db->bind(':descript1', 'Rounding');
			$db->bind(':taxpcent', 0);
			$db->bind(':yourref', $yourref);
			
			$db->execute();	
			
			$db->query("update ".$findb.".glmast set obal = obal + ".$rounding." where accountno = 99 and branch = '".$defbranch."' and sub = 0");
			$db->execute();	
		}
			
	}
	
	//*******************************************************************************************************************************************
	// Cash Purchase
	//*******************************************************************************************************************************************
	if ($transtype == 'C_P') {
			// credit bank/cash account
			$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
			$db->bind(':ddate', $ddate);
			$db->bind(':accountno', $a2cr);	// credit the paying account
			$db->bind(':branch', $defbranch);
			$db->bind(':sub', $s2cr);
			$db->bind(':accno', $purchacc);
			$db->bind(':br', $defbranch);
			$db->bind(':subbr', 0);
			$db->bind(':debit', 0);	
			$db->bind(':credit', $totalvalue);	// with the amount including GST
			$db->bind(':reference', $reference);
			$db->bind(':gsttype', $taxtype);
			$db->bind(':descript1', $descript1);
			$db->bind(':taxpcent', $taxpcent);
			
			$db->execute();	
			
			$db->query("update ".$findb.".glmast set obal = obal - ".$totalvalue." where accountno = ".$a2cr." and branch = '".$defbranch."' and sub = ".$s2cr);
			$db->execute();	
			
			// debit relevant cos accounts
			$db->query("select * from ".$findb.".".$table);
			$rows = $db->resultset();
			foreach ($rows as $row) {
				extract($row);
				$rid = $row['uid'];
				$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent,grnlineno) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent,:grnlineno)");
				$db->bind(':ddate', $ddate);
				$db->bind(':accountno', $purchacc);	//debit the cos account
				$db->bind(':branch', $defbranch);
				$db->bind(':sub', $purchsub);
				$db->bind(':accno', $a2cr);
				$db->bind(':br', $defbranch);
				$db->bind(':subbr', $s2cr);
				$db->bind(':debit', $value);	// with the amount excluding GST
				$db->bind(':credit', 0);		
				$db->bind(':reference', $reference);
				$db->bind(':gsttype', $taxtype);
				$db->bind(':descript1', $descript1);
				$db->bind(':taxpcent', $taxpcent);
				$db->bind(':grnlineno', $rid);
				
				$db->execute();	
				
				$db->query("update ".$findb.".glmast set obal = obal + ".$value." where accountno = ".$purchacc." and branch = '".$defbranch."' and sub = ".$purchsub);
				$db->execute();	

			}
			
	}
	


	//*******************************************************************************************************************************************
	// Goods Received
	//*******************************************************************************************************************************************
	if ($transtype == 'GRN') {
			// credit creditor account
			$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
			$db->bind(':ddate', $ddate);
			$db->bind(':accountno', $a2cr);	// credit the paying account
			$db->bind(':branch', $defbranch);
			$db->bind(':sub', $s2cr);
			$db->bind(':accno', $a2dr);
			$db->bind(':br', $defbranch);
			$db->bind(':subbr', 0);
			$db->bind(':debit', 0);	
			$db->bind(':credit', $totalvalue);	// with the amount including GST
			$db->bind(':reference', $reference);
			$db->bind(':gsttype', $taxtype);
			$db->bind(':descript1', $descript1);
			$db->bind(':taxpcent', $taxpcent);
			
			$db->execute();	
			
			if ($aged == 'Current') {
				$db->query("update ".$cltdb.".client_company_xref set current = current - ".$totalvalue." where company_id = ".$coyno." and crno = ".$a2cr." and crsub = ".$s2cr);
			}
			if ($aged == 'D30') {
				$db->query("update ".$cltdb.".client_company_xref set d30 = d30 - ".$totalvalue." where company_id = ".$coyno." and crno = ".$a2cr." and crsub = ".$s2cr);
			}
			if ($aged == 'D60') {
				$db->query("update ".$cltdb.".client_company_xref set d60 = d60 - ".$totalvalue." where company_id = ".$coyno." and crno = ".$a2cr." and crsub = ".$s2cr);
			}
			if ($aged == 'D90') {
				$db->query("update ".$cltdb.".client_company_xref set d90 = d90 - ".$totalvalue." where company_id = ".$coyno." and crno = ".$a2cr." and crsub = ".$s2cr);
			}
			if ($aged == 'D120') {
				$db->query("update ".$cltdb.".client_company_xref set d120 = d120 - ".$totalvalue." where company_id = ".$coyno." and crno = ".$a2cr." and crsub = ".$s2cr);
			}
			
			$db->execute();	
			
			// credit creditor control account
			$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:branch,:accno,:br,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
			$db->bind(':ddate', $ddate);
			$db->bind(':accountno', 851);	// debit creditors control
			$db->bind(':branch', $defbranch);
			$db->bind(':accno', $a2dr);
			$db->bind(':br', $defbranch);
			$db->bind(':debit', 0);	
			$db->bind(':credit', $totalvalue);	// with the amount including GST
			$db->bind(':reference', $reference);
			$db->bind(':gsttype', $taxtype);
			$db->bind(':descript1', $descript1);
			$db->bind(':taxpcent', $taxpcent);
			
			$db->execute();	
				
			$db->query("update ".$findb.".glmast set obal = obal - ".$totalvalue." where accountno = 851 and branch = '".$defbranch."' and sub = 0");
			$db->execute();	
			
			// debit relevant cos accounts
			$db->query("select * from ".$findb.".".$table);
			$rows = $db->resultset();
			foreach ($rows as $row) {
				extract($row);
				$rid = $row['uid'];
				$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent,grnlineno) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent,:grnlineno)");
				$db->bind(':ddate', $ddate);
				$db->bind(':accountno', $purchacc);	//debit the cos account
				$db->bind(':branch', $defbranch);
				$db->bind(':sub', $purchsub);
				$db->bind(':accno', $a2cr);
				$db->bind(':br', $purchbr);
				$db->bind(':subbr', $s2cr);
				$db->bind(':debit', $value);	// with the amount excluding GST
				$db->bind(':credit', 0);		
				$db->bind(':reference', $reference);
				$db->bind(':gsttype', $taxtype);
				$db->bind(':descript1', $descript1);
				$db->bind(':taxpcent', $taxpcent);
				$db->bind(':grnlineno', $rid);
				
				$db->execute();	
				
				$db->query("update ".$findb.".glmast set obal = obal + ".$value." where accountno = ".$purchacc." and branch = '".$defbranch."' and sub = ".$purchsub);
				$db->execute();	

			}
			
	}
	

	//*******************************************************************************************************************************************
	// Requisition
	//*******************************************************************************************************************************************
	if ($transtype == 'REQ') {
		$db->query("select * from ".$findb.".".$table);
		$rows = $db->resultset();
		foreach ($rows as $row) {
			extract($row);
			$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
			$db->bind(':ddate', $ddate);
			$db->bind(':accountno', $a2dr);	//debit the expense account
			$db->bind(':branch', $purchbr);
			$db->bind(':sub', $s2dr);
			$db->bind(':accno', $purchacc);
			$db->bind(':br', $defbranch);
			$db->bind(':subbr', $purchsub);
			$db->bind(':debit', $value);	// with the amount excluding GST
			$db->bind(':credit', 0);		
			$db->bind(':reference', $reference);
			$db->bind(':gsttype', $taxtype);
			$db->bind(':descript1', $descript1.' '.$item);
			$db->bind(':taxpcent', $taxpcent);
				
			$db->execute();	
			
			$db->query("update ".$findb.".glmast set obal = obal + ".$value." where accountno = ".$a2dr." and branch = '".$purchbr."' and sub = ".$s2dr);
			$db->execute();	
			
			// credit relevant cos account
			$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
			$db->bind(':ddate', $ddate);
			$db->bind(':accountno', $purchacc);	//credit the cos account
			$db->bind(':branch', $defbranch);
			$db->bind(':sub', $purchsub);
			$db->bind(':accno', $a2dr);
			$db->bind(':br', $purchbr);
			$db->bind(':subbr', $s2dr);
			$db->bind(':debit', 0);	
			$db->bind(':credit', $value);		// with the amount excluding GST
			$db->bind(':reference', $reference);
			$db->bind(':gsttype', $taxtype);
			$db->bind(':descript1', $descript1.' '.$item);
			$db->bind(':taxpcent', $taxpcent);
				
			$db->execute();	
				
			$db->query("update ".$findb.".glmast set obal = obal - ".$value." where accountno = ".$purchacc." and branch = '".$defbranch."' and sub = ".$purchsub);
			$db->execute();	

	
			// Inter branch Transactions
			if ($purchbr != $defbranch) {
			
				// credit inter branch transfer account
				$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
				$db->bind(':ddate', $ddate);
				$db->bind(':accountno', 997);	
				$db->bind(':branch', $purchbr);
				$db->bind(':sub', 0);
				$db->bind(':accno', $purchacc);
				$db->bind(':br', $defbranch);
				$db->bind(':subbr', $purchsub);
				$db->bind(':debit', 0);	
				$db->bind(':credit', $value);		// with the amount excluding GST
				$db->bind(':reference', $reference);
				$db->bind(':gsttype', $taxtype);
				$db->bind(':descript1', $descript1.' '.$item);
				$db->bind(':taxpcent', $taxpcent);
					
				$db->execute();					
					
				$db->query("update ".$findb.".glmast set obal = obal - ".$value." where accountno = 997 and branch = '".$purchbr."'");
				$db->execute();					
					
				// debit inter branch transfer account
				$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values 
				(:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
				$db->bind(':ddate', $ddate);
				$db->bind(':accountno', 997);	
				$db->bind(':branch', $defbranch);
				$db->bind(':sub', 0);
				$db->bind(':accno', $purchacc);
				$db->bind(':br', $purchbr);
				$db->bind(':subbr', $purchsub);
				$db->bind(':debit', $value);			// with the amount excluding GST
				$db->bind(':credit', 0);
				$db->bind(':reference', $reference);
				$db->bind(':gsttype', $taxtype);
				$db->bind(':descript1', $descript1.' '.$item);
				$db->bind(':taxpcent', $taxpcent);
					
				$db->execute();					
					
				$db->query("update ".$findb.".glmast set obal = obal + ".$value." where accountno = 997 and branch = '".$defbranch."'");
				$db->execute();					
					
			} // if posting between different branches
					
		} // while
			
	} // if
	
	//*******************************************************************************************************************************************
	// Credit Note
	//*******************************************************************************************************************************************
	if ($transtype == 'CRN') {
		
			$db->query("select branch from ".$findb.".stklocs where uid = :loc");
			$db->bind(':loc', $loc);
			$row = $db->single();
			extract($row);
			$defbranch = $branch;		
		
			$db->query("select * from ".$findb.".".$table." where pay = 'Y'");
			$rows = $db->resultset();
			foreach ($rows as $row) {
				extract($row);
				$docref = substr($ref,0,3);
					
				if ($docref == 'INV') {
					
					// credit debtor account
					$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent,inv) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent,:inv)");
					$db->bind(':ddate', $ddate);
					$db->bind(':accountno', $a2cr);	// credit the debtor
					$db->bind(':branch', $defbranch);
					$db->bind(':sub', $s2cr);
					$db->bind(':accno', $sellacc);
					$db->bind(':br', $defbranch);
					$db->bind(':subbr', $sellsub);
					$db->bind(':debit', 0);	
					$db->bind(':credit', $value+$tax);		// with the amount including GST
					$db->bind(':reference', $reference);
					$db->bind(':gsttype', $taxtype);
					$db->bind(':descript1', $descript1);
					$db->bind(':taxpcent', $taxpcent);
					$db->bind(':inv', $rno);
					
					$db->execute();	
					
					$amt = $value+$tax;
					
					if ($aged == 'Current') {
						$db->query("update ".$cltdb.".client_company_xref set current = current - ".$amt." where company_id = ".$coyno." and drno = ".$a2cr." and drsub = ".$s2cr);
					}
					if ($aged == 'D30') {
						$db->query("update ".$cltdb.".client_company_xref set d30 = d30 - ".$amt." where company_id = ".$coyno." and drno = ".$a2cr." and drsub = ".$s2cr);
					}
					if ($aged == 'D60') {
						$db->query("update ".$cltdb.".client_company_xref set d60 = d60 - ".$amt." where company_id = ".$coyno." and drno = ".$a2cr." and drsub = ".$s2cr);
					}
					if ($aged == 'D90') {
						$db->query("update ".$cltdb.".client_company_xref set d90 = d90 - ".$amt." where company_id = ".$coyno." and drno = ".$a2cr." and drsub = ".$s2cr);
					}
					if ($aged == 'D120') {
						$db->query("update ".$cltdb.".client_company_xref set d120 = d120 - ".$amt." where company_id = ".$coyno." and drno = ".$a2cr." and drsub = ".$s2cr);
					}
					
					$db->execute();	
					
					// credit debtor control account
					$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:branch,:accno,:br,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
					$db->bind(':ddate', $ddate);
					$db->bind(':accountno', 801);	// credit debtors control
					$db->bind(':branch', $defbranch);
					$db->bind(':accno', 0);
					$db->bind(':br', '');
					$db->bind(':debit', 0);	
					$db->bind(':credit', $value+$tax);	// with the amount including GST
					$db->bind(':reference', $reference);
					$db->bind(':gsttype', $taxtype);
					$db->bind(':descript1', $descript1);
					$db->bind(':taxpcent', $taxpcent);
					
					$db->execute();	
					
					$amt = $value+$tax;
					
					$db->query("update ".$findb.".glmast set obal = obal - ".$amt." where accountno = 801 and branch = '".$defbranch."' and sub = 0");
					$db->execute();	
					
				} else {
				
					// credit cash/bank account
					$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent,inv) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent,:inv)");
					$db->bind(':ddate', $ddate);
					$db->bind(':accountno', $a2cr);	// credit the debtor
					$db->bind(':branch', $defbranch);
					$db->bind(':sub', $s2cr);
					$db->bind(':accno', $sellacc);
					$db->bind(':br', $defbranch);
					$db->bind(':subbr', $sellsub);
					$db->bind(':debit', 0);	
					$db->bind(':credit', $value+$tax);		// with the amount including GST
					$db->bind(':reference', $reference);
					$db->bind(':gsttype', $taxtype);
					$db->bind(':descript1', $descript1);
					$db->bind(':taxpcent', $taxpcent);
					$db->bind(':inv', $rno);
					
					$db->execute();						
					
					$amt = $value+$tax;
					
					$db->query("update ".$findb.".glmast set obal = obal - ".$amt." where accountno = ".$a2cr." and branch = '".$defbranch."' and sub = ".$s2cr);
					$db->execute();						
					
				}
			}	
			
			// debit relevant income accounts
			$db->query("select * from ".$findb.".".$table);
			$rows = $db->resultset();
			foreach ($rows as $row) {
				extract($row);
				$rid = $row['uid'];
				$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent,grnlineno) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent,:grnlineno)");
				$db->bind(':ddate', $ddate);
				$db->bind(':accountno', $sellacc);	//debit the income account
				$db->bind(':branch', $defbranch);
				$db->bind(':sub', $sellsub);
				$db->bind(':accno', $a2cr);
				$db->bind(':br', $defbranch);
				$db->bind(':subbr', $s2cr);
				$db->bind(':debit', $value);	// with the amount excluding GST
				$db->bind(':credit', 0);		
				$db->bind(':reference', $reference);
				$db->bind(':gsttype', $taxtype);
				$db->bind(':descript1', $descript1);
				$db->bind(':taxpcent', $taxpcent);
				$db->bind(':grnlineno', $rid);
					
				$db->execute();				
					
				$db->query("update ".$findb.".glmast set obal = obal + ".$value." where accountno = ".$sellacc." and branch = '".$defbranch."' and sub = ".$sellsub);
				$db->execute();				
	
			}
			
	}
	
	//*******************************************************************************************************************************************
	// Goods Returned
	//*******************************************************************************************************************************************
	if ($transtype == 'RET') {
		
			$db->query("select branch from ".$findb.".stklocs where uid = :loc");
			$db->bind(':loc', $loc);
			$row = $db->single();
			extract($row);
			$defbranch = $branch;	
			
			$db->query("select * from ".$findb.".".$table." where pay = 'Y'");
			$rows = $db->resultset();
			foreach ($rows as $row) {
				extract($row);
				$docref = substr($ref,0,3);
					
				if ($docref == 'GRN') {
		
					// debit creditor account
					$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent,inv) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent,:inv)");
					$db->bind(':ddate', $ddate);
					$db->bind(':accountno', $a2dr);	//debit the creditor
					$db->bind(':branch', $defbranch);
					$db->bind(':sub', $s2dr);
					$db->bind(':accno', $purchacc);
					$db->bind(':br', $defbranch);
					$db->bind(':subbr', $purchsub);
					$db->bind(':debit', $value+$tax);	// with the amount including GST
					$db->bind(':credit', 0);
					$db->bind(':reference', $reference);
					$db->bind(':gsttype', $taxtype);
					$db->bind(':descript1', $descript1);
					$db->bind(':taxpcent', $taxpcent);
					$db->bind(':inv', $rno);
					
					$db->execute();	

					$amt = $value+$tax;

					if ($aged == 'Current') {
						$db->query("update ".$cltdb.".client_company_xref set current = current + ".$amt." where company_id = ".$coyno." and crno = ".$a2dr." and crsub = ".$s2dr);
					}
					if ($aged == 'D30') {
						$db->query("update ".$cltdb.".client_company_xref set d30 = d30 + ".$amt." where company_id = ".$coyno." and crno = ".$a2dr." and crsub = ".$s2dr);
					}
					if ($aged == 'D60') {
						$db->query("update ".$cltdb.".client_company_xref set d60 = d60 + ".$amt." where company_id = ".$coyno." and crno = ".$a2dr." and crsub = ".$s2dr);
					}
					if ($aged == 'D90') {
						$db->query("update ".$cltdb.".client_company_xref set d90 = d90 + ".$amt." where company_id = ".$coyno." and crno = ".$a2dr." and crsub = ".$s2dr);
					}
					if ($aged == 'D120') {
						$db->query("update ".$cltdb.".client_company_xref set d120 = d120 + ".$amt." where company_id = ".$coyno." and crno = ".$a2dr." and crsub = ".$s2dr);
					}
						
					$db->execute();	
						
					// debit creditor control account
					$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:branch,:accno,:br,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
					$db->bind(':ddate', $ddate);
					$db->bind(':accountno', 851);	// credit creditors control
					$db->bind(':branch', $defbranch);
					$db->bind(':accno', 0);
					$db->bind(':br', '');
					$db->bind(':debit', $value+$tax);	// with the amount including GST
					$db->bind(':credit', 0);	
					$db->bind(':reference', $reference);
					$db->bind(':gsttype', $taxtype);
					$db->bind(':descript1', $descript1);
					$db->bind(':taxpcent', $taxpcent);
					
					$db->execute();						
					
					$amt = $value+$tax;
							
					$db->query("update ".$findb.".glmast set obal = obal + ".$amt." where accountno = 851 and branch = '".$defbranch."' and sub = 0");
					$db->execute();						
						
				} else {
				
					// debit cash/bank account
					$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent,inv) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent,:inv)");
					$db->bind(':ddate', $ddate);
					$db->bind(':accountno', $a2dr);	// debit the creditor
					$db->bind(':branch', $defbranch);
					$db->bind(':sub', $s2dr);
					$db->bind(':accno', $purchacc);
					$db->bind(':br', $defbranch);
					$db->bind(':subbr', $purchsub);
					$db->bind(':debit', $totalvalue);	// with the amount including GST
					$db->bind(':credit', 0);	
					$db->bind(':reference', $reference);
					$db->bind(':gsttype', $taxtype);
					$db->bind(':descript1', $descript1);
					$db->bind(':taxpcent', $taxpcent);
					$db->bind(':inv', $rno);
					
					$db->execute();						
					
					$db->query("update ".$findb.".glmast set obal = obal + ".$totalvalue." where accountno = ".$a2dr." and branch = '".$defbranch."' and sub = ".$s2dr);
					$db->execute();						
				}
			}
			
			// credit relevant cos accounts
			$db->query("select * from ".$findb.".".$table);
			$rows = $db->resultset();
			foreach ($rows as $row) {
				extract($row);
				$rid = $row['uid'];
				$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent,grnlineno) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent,:grnlineno)");
				$db->bind(':ddate', $ddate);
				$db->bind(':accountno', $purchacc);	//debit the income account
				$db->bind(':branch', $defbranch);
				$db->bind(':sub', $purchsub);
				$db->bind(':accno', $a2dr);
				$db->bind(':br', $defbranch);
				$db->bind(':subbr', $s2dr);
				$db->bind(':debit', 0);	
				$db->bind(':credit', $value);		// with the amount excluding GST
				$db->bind(':reference', $reference);
				$db->bind(':gsttype', $taxtype);
				$db->bind(':descript1', $descript1);
				$db->bind(':taxpcent', $taxpcent);
				$db->bind(':grnlineno', $rid);
					
				$db->execute();					
				
				$db->query("update ".$findb.".glmast set obal = obal - ".$value." where accountno = ".$purchacc." and branch = '".$defbranch."' and sub = ".$purchsub);
				$db->execute();					

			}
			
	}
	

	//*************************************************************************************************************************************************************
	// Create double entries relating to GST for each line of trading document
	//*************************************************************************************************************************************************************
	if ($transtype != 'REQ') {	
		$tref = substr($transref,0,3);
		$db->query("select * from ".$findb.".".$table);
		$rows = $db->resultset();
		foreach ($rows as $row) {
			extract($row);
			$rid = $row['uid'];
			
			// insert each line's tax where applicable into trmain. 
			if ($tax > 0 || $transtype == 'GRN') {
				if ($gstinvpay == 'Invoice' || ($transtype == 'C_S' || $transtype == 'C_P') || ($transtype == 'RET' && $tref == 'C_P') || ($transtype == 'CRN' && $tref == 'C_S')) {
					
					if ($taxdrcr == 'dr') {
						$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent,grnlineno) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent,:grnlineno)");
						$db->bind(':ddate', $ddate);
						$db->bind(':accountno', 870);	// debit GST payable
						$db->bind(':branch', $defbranch);
						$db->bind(':sub', 0);
						$db->bind(':accno', $a2cr);
						$db->bind(':br', $defbranch);
						$db->bind(':subbr', $s2cr);
						$db->bind(':debit', $tax);	// with the of GST
						$db->bind(':credit', 0);		
						$db->bind(':reference', $reference);
						$db->bind(':gsttype', $taxtype);
						$db->bind(':descript1', $descript1);
						$db->bind(':taxpcent', $taxpcent);
						$db->bind(':grnlineno', $rid);
							
						$db->execute();					
						
						$addedrec = $db->lastInsertId();
						
						$db->query("update ".$findb.".glmast set obal = obal + ".$tax." where accountno = 870 and branch = '".$defbranch."' and sub = 0");
						$db->execute();					
						
					} else {
						$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent,grnlineno) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent,:grnlineno)");
						$db->bind(':ddate', $ddate);
						$db->bind(':accountno', 870);	// credit GST payable
						$db->bind(':branch', $defbranch);
						$db->bind(':sub', 0);
						$db->bind(':accno', $a2dr);
						$db->bind(':br', $defbranch);
						$db->bind(':subbr', $s2dr);
						$db->bind(':debit', 0);	
						$db->bind(':credit', $tax);		// with the of GST
						$db->bind(':reference', $reference);
						$db->bind(':gsttype', $taxtype);
						$db->bind(':descript1', $descript1);
						$db->bind(':taxpcent', $taxpcent);
						$db->bind(':grnlineno', $rid);
							
						$db->execute();							
						
						$addedrec = $db->lastInsertId();
						
						$db->query("update ".$findb.".glmast set obal = obal - ".$tax." where accountno = 870 and branch = '".$defbranch."' and sub = 0");
						$db->execute();							
						
					}// if
				
				} else {
					
					if ($taxdrcr == 'dr') {
						$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent,grnlineno) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent,:grnlineno)");
						$db->bind(':ddate', $ddate);
						$db->bind(':accountno', 871);	// debit GST payable
						$db->bind(':branch', $defbranch);
						$db->bind(':sub', 0);
						$db->bind(':accno', $a2cr);
						$db->bind(':br', $defbranch);
						$db->bind(':subbr', $s2cr);
						$db->bind(':debit', $tax);	// with the of GST
						$db->bind(':credit', 0);		
						$db->bind(':reference', $reference);
						$db->bind(':gsttype', $taxtype);
						$db->bind(':descript1', $descript1);
						$db->bind(':taxpcent', $taxpcent);
						$db->bind(':grnlineno', $rid);
							
						$db->execute();					
						
						$addedrec = $db->lastInsertId();
						
						$db->query("update ".$findb.".glmast set obal = obal + ".$tax." where accountno = 871 and branch = '".$defbranch."' and sub = 0");
						$db->execute();					
						
					} else {
						$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent,grnlineno) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent,:grnlineno)");
						$db->bind(':ddate', $ddate);
						$db->bind(':accountno', 871);	// credit GST payable
						$db->bind(':branch', $defbranch);
						$db->bind(':sub', 0);
						$db->bind(':accno', $a2dr);
						$db->bind(':br', $defbranch);
						$db->bind(':subbr', $s2dr);
						$db->bind(':debit', 0);	
						$db->bind(':credit', $tax);		// with the amount of GST
						$db->bind(':reference', $reference);
						$db->bind(':gsttype', $taxtype);
						$db->bind(':descript1', $descript1);
						$db->bind(':taxpcent', $taxpcent);
						$db->bind(':grnlineno', $rid);
							
						$db->execute();							
						
						$addedrec = $db->lastInsertId();
						
						$db->query("update ".$findb.".glmast set obal = obal - ".$tax." where accountno = 871 and branch = '".$defbranch."' and sub = 0");
						$db->execute();							
						
					}// if
					
				} //if
				
				$atot = abs($tot);
				if ($gstinvpay == 'Invoice') {
					if ($transtype == 'INV' || $transtype == 'C_S') {
						$db->query('update '.$findb.'.trmain set grosssales = '.$atot.' where uid = '.$addedrec);
						$db->execute();							
					}
					if ($transtype == 'GRN' || $transtype == 'C_P') {
						$db->query('update '.$findb.'.trmain set grosspurchases = '.$atot.' where uid = '.$addedrec);
						$db->execute();							
					}
					if ($transtype == 'CRN') {
						$tot = $tot * -1;
						$db->query('update '.$findb.'.trmain set grosssales = '.$atot.' where uid = '.$addedrec);
						$db->execute();							
					}
					if ($transtype == 'GRN' || $transtype == 'RET') {
						$tot = $tot * -1;
						$db->query('update '.$findb.'.trmain set grosspurchases = '.$atot.' where uid = '.$addedrec);
						$db->execute();							
					}
					
				} else {
					if ($transtype == 'REC' || $transtype == 'C_S') {
						$db->query('update '.$findb.'.trmain set grosssales = '.$atot.' where uid = '.$addedrec);
						$db->execute();							
					}
					if ($transtype == 'PAY' || $transtype == 'C_P') {
						$db->query('update '.$findb.'.trmain set grosspurchases = '.$atot.' where uid = '.$addedrec);
						$db->execute();							
					}
					if ($transtype == 'RET' && $purchref = 'C_P') {
						$tot = $tot * -1;
						$db->query('update '.$findb.'.trmain set grosspurchases = '.$atot.' where uid = '.$addedrec);
						$db->execute();							
					}
					if ($transtype == 'CRN' && $purchref = 'C_S') {
						$tot = $tot * -1;
						$db->query('update '.$findb.'.trmain set grosssales = '.$atot.' where uid = '.$addedrec);
						$db->execute();							
					}
				}
		
			} //if
			
		
		} //while
	}

	//***********************************************************************************************************************************
	// Create entries in stktrans for each stock recorded item
	//***********************************************************************************************************************************

	$db->query("select * from ".$findb.".".$table);
	$rows = $db->resultset();
	foreach ($rows as $row) {
		extract($row);
		
		// get quantity, average cost before purchase
		$db->query("select onhand,avgcost from ".$findb.".stkmast where itemcode = :itemcode");
		$db->bind(':itemcode', $itemcode);
		$row = $db->single();
		extract($row);
		$origqty = $onhand;
		$origacst = $avgcost;
		$origtotcost = $origqty * $origacst;
		
		if ($transtype == 'INV' || $transtype == 'C_S' || $transtype == 'REQ' || $transtype == 'RET') {
			$increase = 0;
			$decrease = $quantity;
			$db->query("update ".$findb.".stkmast set onhand = onhand - ".$decrease." where itemcode = '".$itemcode."'");
			$db->execute();							
		} else {
			$increase = $quantity;
			$decrease = 0;
			$db->query("update ".$findb.".stkmast set onhand = onhand + ".$increase." where itemcode = '".$itemcode."'");
			$db->execute();							
			if ($transtype == 'GRN' && $value == 0) {
				$db->query("update ".$findb.".stkmast set uncosted = uncosted + ".$increase." where itemcode = '".$itemcode."'");
				$db->execute();							
			} //if
			
			// calculate new average cost
			if ($transtype == 'GRN' || $transtype == 'C_P') {
				if ($value > 0) {
					$db->query("select avgcost,(onhand-uncosted) as avail from ".$findb.".stkmast where itemcode = :itemcode");
					$db->bind(':itemcode', $itemcode);
					$row = $db->single();
					extract($row);
					$newqty = $avail;
					$newcost = $origtotcost + $value;
					$newavgcost = $newcost/$newqty;
					$newavgcost = round($newavgcost,2);
					
					$db->query("update ".$findb.".stkmast set avgcost = ".$newavgcost." where itemcode = '".$itemcode."'");
					$db->execute();							
				}
				
			} //if
			
		} // if
		
		if ($dn2inv == 'N') {
			$db->query("insert into ".$findb.".stktrans (groupid,catid,itemcode,item,locid,ddate,increase,decrease,ref_no,transtype,amount,your_ref) values (:groupid,:catid,:itemcode,:item,:locid,:ddate,:increase,:decrease,:ref_no,:transtype,:amount,:yourref)");
			$db->bind(':groupid', $groupid);
			$db->bind(':catid', $catid);
			$db->bind(':itemcode', $itemcode);
			$db->bind(':item', $item);
			$db->bind(':locid', $loc);
			$db->bind(':ddate', $ddate);
			$db->bind(':increase', $increase);
			$db->bind(':decrease', $decrease);	
			$db->bind(':ref_no', $reference);		
			$db->bind(':transtype', $transtype);
			$db->bind(':amount', $value);
			$db->bind(':yourref', $yourref);
			
			$db->execute();							
		}
		
		// Create entries between closing stock and stock on hand
		if ($stk == 'Stock') {
			
				$cost = $origacst * $qty;

				if ($transtype == 'INV' || $transtype == 'C_S' || $transtype == 'REQ' || $transtype == 'RET') {
					
				$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
				$db->bind(':ddate', $ddate);
				$db->bind(':accountno', 187);	// debit closing stock
				$db->bind(':branch', $defbranch);
				$db->bind(':sub', 0);
				$db->bind(':accno', 825);
				$db->bind(':br', $defbranch);
				$db->bind(':subbr', 0);
				$db->bind(':debit', $cost);	// with the amount excluding GST
				$db->bind(':credit', 0);		
				$db->bind(':reference', $reference);
				$db->bind(':gsttype', $taxtype);
				$db->bind(':descript1', $descript1);
				$db->bind(':taxpcent', $taxpcent);
					
				$db->execute();							
					
				$db->query("update ".$findb.".glmast set obal = obal + ".$cost." where accountno = 187 and branch = '".$defbranch."' and sub = 0");
				$db->execute();							
				
				$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
				$db->bind(':ddate', $ddate);
				$db->bind(':accountno', 825);	// credit stock on hand
				$db->bind(':branch', $defbranch);
				$db->bind(':sub', 0);
				$db->bind(':accno', 187);
				$db->bind(':br', $defbranch);
				$db->bind(':subbr', 0);
				$db->bind(':debit', 0);	
				$db->bind(':credit', $cost);		// with the amount excluding GST
				$db->bind(':reference', $reference);
				$db->bind(':gsttype', $taxtype);
				$db->bind(':descript1', $descript1);
				$db->bind(':taxpcent', $taxpcent);
					
				$db->execute();							

				$db->query("update ".$findb.".glmast set obal = obal - ".$cost." where accountno = 825 and branch = '".$defbranch."' and sub = 0");
				$db->execute();							
				
			} else {
				
				$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
				$db->bind(':ddate', $ddate);
				$db->bind(':accountno', 825);	// debit stock on hand
				$db->bind(':branch', $defbranch);
				$db->bind(':sub', 0);
				$db->bind(':accno', 187);
				$db->bind(':br', $defbranch);
				$db->bind(':subbr', 0);
				$db->bind(':debit', $cost);	// with the amount excluding GST
				$db->bind(':credit', 0);		
				$db->bind(':reference', $reference);
				$db->bind(':gsttype', $taxtype);
				$db->bind(':descript1', $descript1);
				$db->bind(':taxpcent', $taxpcent);
					
				$db->execute();						
				
				$db->query("update ".$findb.".glmast set obal = obal + ".$cost." where accountno = 825 and branch = '".$defbranch."' and sub = 0");
				$db->execute();						
				
				$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
				$db->bind(':ddate', $ddate);
				$db->bind(':accountno', 187);	// credit closing stock
				$db->bind(':branch', $defbranch);
				$db->bind(':sub', 0);
				$db->bind(':accno', 825);
				$db->bind(':br', $defbranch);
				$db->bind(':subbr', 0);
				$db->bind(':debit', 0);	
				$db->bind(':credit', $cost);		// with the amount excluding GST
				$db->bind(':reference', $reference);
				$db->bind(':gsttype', $taxtype);
				$db->bind(':descript1', $descript1);
				$db->bind(':taxpcent', $taxpcent);
					
				$db->execute();		
				
				$db->query("update ".$findb.".glmast set obal = obal - ".$cost." where accountno = 187 and branch = '".$defbranch."' and sub = 0");
				$db->execute();		
			}
		}		
		
	} // while
	
	// add invoice number to D_N records in invhead
	if ($dn2inv == 'Y') {
		// get relevant D_N numbers
		$db->query("select item from ".$findb.".".$table);
		$rows = $db->resultset();
		foreach ($rows as $row) {
			extract($row);
			$d = explode('~',$item);
			$dn = $d[0];
			$db->query("update ".$findb.".invhead set invoice = :reference where ref_no = :ref_no");
			$db->bind(':reference', $reference);
			$db->bind(':ref_no', $dn);
			$db->execute();
			
			//move dn ref_no to xref and replace ref_no with inv number
		    $db->query("update ".$findb.".stktrans set xref = '".$dn."', transtype = 'INV' where ref_no = '".$dn."'");
			$db->execute();
			$db->query("update ".$findb.".stktrans set ref_no = '".$reference ."' where ref_no = '".$dn."'");
			$db->execute();			
			
		}
	}



	//**************************************************************************************************************************************************************************
	// add serial numbers if applicable
	//**************************************************************************************************************************************************************************
			$db->query("select * from ".$findb.".".$serialtable);
			$rows = $db->resultset();
			if ($db->rowCount() > 0) {
				foreach ($rows as $row) {
					extract($row);
					if ($transtype == 'INV' || $transtype == 'C_S' || $transtype == 'REQ' || $transtype == 'RET') {
						$db->query("update ".$findb.".stkserials set sold = '".$reference."' where serialno = '".$serialno."'");
						$db->execute();		
					} else {
						$db->query("insert into ".$findb.".stkserials (itemcode,item,serialno,locationid,ref_no,sold,branch,date,activity) values (:itemcode,:item,:serialno,:locationid,:ref_no,:sold,:branch,:date,:activity)");
						$db->bind(':itemcode', $itemcode);
						$db->bind(':item', $item);
						$db->bind(':serialno', $serialno);
						$db->bind(':locationid', $locationid);
						$db->bind(':ref_no', $reference);
						$db->bind(':sold', '');
						$db->bind(':branch', $defbranch);
						$db->bind(':date', $ddate);	
						$db->bind(':activity', $descript1);		
						
						$db->execute();		
					}
				}
			}


	//**************************************************************************************************************************************************************************
	// add transaction to audit trail
	//**************************************************************************************************************************************************************************
			$db->query("insert into ".$findb.".audit (ddate,acc2dr,brdr,subdr,acc2cr,brcr,subcr,descript1,reference,amount,tax,total,entrydate,entrytime,username,userip) values (:ddate,:acc2dr,:brdr,:subdr,:acc2cr,:brcr,:subcr,:descript1,:reference,:amount,:tax,:total,:entrydate,:entrytime,:username,:userip)");
			$db->bind(':ddate', $ddate);
			$db->bind(':acc2dr', $a2dr);	
			$db->bind(':brdr', $defbranch);
			$db->bind(':subdr', $s2dr);
			$db->bind(':acc2cr', $a2cr);
			$db->bind(':brcr', $defbranch);
			$db->bind(':subcr', $s2cr);
			$db->bind(':descript1', $descript1);	
			$db->bind(':reference', $reference);		
			$db->bind(':amount', $totalamount);
			$db->bind(':tax', $totaltax);
			$db->bind(':total', $totalvalue);
			$db->bind(':entrydate', date("Y-m-d"));
			$db->bind(':entrytime', date("H:i:s"));
			$db->bind(':username', $unm);
			$db->bind(':userip', $uip);
					
			$db->execute();		

if ($transtype != 'GRN') {
	$db->query("delete from ".$findb.".".$table);
	$db->execute();	
}
$db->query("delete from ".$findb.".".$serialtable);
$db->execute();		

/*
if ($err ==  ) {
	$db->cancelTransaction();
} else {
	$db->endTransaction();	
}
*/

$db->closeDB();

?>


