<?php
session_start();
$id = $_REQUEST['id'];
$fxcode = $_REQUEST['fxcode'];
$fxrate = $_REQUEST['fxrate'];
$topay = $_REQUEST['topay'] * $fxrate;
$paymethod = $_REQUEST['paymethod'];
$refno = $_REQUEST['refno'];
$transref = strtoupper($_SESSION['s_transref']);
$ddate = $_REQUEST['ddate'];
$coyno = $_SESSION['s_coyid'];

if ($transref == '') {
	$transref = 'Allocate';
}

include_once("../../includes/DBClass.php");
$db = new DBClass();

//if ($topay == 'part') {
	//$topay = $_SESSION['s_partpay'];
//}

$findb = $_SESSION['s_findb'];
$cltdb = $_SESSION['s_cltdb'];

$db->query("update ".$findb.".invtrans set paid = paid + ".$topay." where uid = ".$id);
$db->execute();

$db->query("select ref_no from ".$findb.".invtrans where uid = ".$id);
$row = $db->single();
extract($row);
$pref = $ref_no;

switch ($paymethod) {
	case 'eft':
		$db->query("update ".$findb.".invhead set eftpos = eftpos + ".$topay." where ref_no = '".$refno."'");
		break;
	case 'crd':
		$db->query("update ".$findb.".invhead set ccard = ccard + ".$topay." where ref_no = '".$refno."'");
		break;
	case 'csh':
		$db->query("update ".$findb.".invhead set cash = cash + ".$topay." where ref_no = '".$refno."'");
		break;
	case 'chq':
		$db->query("update ".$findb.".invhead set cheque = cheque + ".$topay." where ref_no = '".$refno."'");
		break;
}
$db->execute();

$db->query("select accountno,sub,branch from ".$findb.".invhead where ref_no = '".$pref."'");
$row = $db->single();
extract($row);
$ac = $accountno;
$sb = $sub;
$br = $branch;

$db->query("update ".$findb.".trmain set allocated = allocated + ".$topay." where accountno = ".$ac." and sub = ".$sb." and reference = '".$pref."'");
$db->execute();

$db->query("update ".$findb.".trmain set inv = '".$refno."' where accountno = ".$ac." and sub = ".$sb." and reference = '".$transref."'");
$db->execute();

$db->query('insert into '.$findb.'.allocations (ddate,amount,fromref,toref,currency,rate) values ("'.$ddate.'",'.$topay.',"'.$transref.'","'.$refno.'","'.$fxcode.'",'.$fxrate.')');
$db->execute();

/*
// sort out any overs and unders on exchange rate
// get local currency
$db->query("select currency from ".$findb.".forex where def_forex = 'Yes'");
$row = $db->single();
extract($row);
$local_currency = $currency;

// check if transaction in forex
if ($fxcode != $local_currency) {
	
	// get local amount owed and fx rate at time of purchase
	$db->query("select accountno,branch,sub,totvalue,tax,rate from ".$findb.".invhead where ref_no = '".$pref."'");
	$row = $db->single();
	extract($row);
	$total_local_owed = $totalvalue + $tax;
	$rate_on_purchase = $rate;
	
	// get amount paid and fx rate at time of payment
	$db->query("select totvalue,rate from ".$findb.".invhead where ref_no = '".$transref."'");
	$row = $db->single();
	extract($row);
	$total_paid = $totvalue;
	$rate_on_payment = $rate;
	
	// calculate over/under
	// portion paid as a fraction of amount owed
	$fraction_paid = $fx_paid / $fx_owed;
	// portion foreign owed at first rate
	$portion_owed = $total_local_owed * $fraction_paid * $rate_on_purchase;
	// foreign paid at second rate
	$paid = $total_paid * $rate_on_payment;
	// difference in local currency
	$diff = round(($portion_owed - $paid),2);
	
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
	
	$db->query("select ".$ref." from ".$findb.".numbers");
	$row = $db->single();
	extract($row);
	$rn = $$ref + 1;
	$db->query("update ".$findb.".numbers set ".$ref." = :refno");
	$db->bind(':refno', $rn);
	$db->execute();
	
	$acc2cr = $ac;
	$acc2dr = $ac;
	$sub2cr = $sb;
	$sub2dr = $sb;
	$br2cr = $br;
	$br2dr = $br;
	$total = $diff;
	$amount = $diff;
	$reference = $rn;
	$taxtype = 'N-T';
	$descript1 = "Exchange rate difference on '".$refno."' and '".$pref."'";
	$taxpcent = 0;
	$currency = $local_currency;
	$rate = 1;
	
	
	
	// post to elevant accounts
	if ($diff > 0) {
		// credit debtor
		$db->query("insert into ".$findb.".trmain (ddate,accountno,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent,currency,rate) values (:ddate,:accountno,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent,:currency,:rate)");
		$db->bind(':ddate', $ddate);
		$db->bind(':accountno', $acc2cr);	// credit the creditor
		$db->bind(':sub', $sub2cr);
		$db->bind(':accno', $acc2dr);
		$db->bind(':br', $br2dr);
		$db->bind(':subbr', $sub2dr);
		$db->bind(':debit', 0);	
		$db->bind(':credit', $total);	// with the amount 
		$db->bind(':reference', $reference);
		$db->bind(':gsttype', $taxtype);
		$db->bind(':descript1', $descript1);
		$db->bind(':taxpcent', $taxpcent);
		$db->bind(':currency', $currency);
		$db->bind(':rate', $rate);
					
		$db->execute();	
		
		if ($aged == 'Current') {
			$db->query("update ".$cltdb.".client_company_xref set current = current - ".$total." where company_id = ".$coyno." and drno = ".$acc2cr." and drsub = ".$sub2cr);
		}
		if ($aged == 'D30') {
			$db->query("update ".$cltdb.".client_company_xref set d30 = d30 - ".$total." where company_id = ".$coyno." and drno = ".$acc2cr." and drsub = ".$sub2cr);
		}
		if ($aged == 'D60') {
			$db->query("update ".$cltdb.".client_company_xref set d60 = d60 - ".$total." where company_id = ".$coyno." and drno = ".$acc2cr." and drsub = ".$sub2cr);
		}
		if ($aged == 'D90') {
			$db->query("update ".$cltdb.".client_company_xref set d90 = d90 - ".$total." where company_id = ".$coyno." and drno = ".$acc2cr." and drsub = ".$sub2cr);
		}
		if ($aged == 'D120') {
			$db->query("update ".$cltdb.".client_company_xref set d120 = d120 - ".$total." where company_id = ".$coyno." and drno = ".$acc2cr." and drsub = ".$sub2cr);
		}
		
		$db->execute();	
			
		// credit debtors control

		$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,descript1,taxpcent,currency,rate) values (:ddate,:accountno,:branch,:accno,:br,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent,:currency,:rate)");
		$db->bind(':ddate', $ddate);
		$db->bind(':accountno', 851);	// credit the creditor conrol
		$db->bind(':branch', $br2dr);
		$db->bind(':accno', $acc2dr);
		$db->bind(':br', $br2dr);
		$db->bind(':debit', 0);	
		$db->bind(':credit', $total);	// with the amount 
		$db->bind(':reference', $reference);
		$db->bind(':gsttype', $taxtype);
		$db->bind(':descript1', $descript1);
		$db->bind(':taxpcent', $taxpcent);
		$db->bind(':currency', $currency);
		$db->bind(':rate', $rate);
					
		$db->execute();	
		
		$db->query("update ".$findb.".glmast set obal = obal - ".$total." where accountno = 851 and branch = '".$br2dr."'");
		$db->execute();	
		
		// debit overs and unders
		$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent,currency,rate) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent,:currency,:rate)");
		$db->bind(':ddate', $ddate);
		$db->bind(':accountno', 79);	//debit the debtor
		$db->bind(':branch', $br2dr);
		$db->bind(':sub', $sub2dr);
		$db->bind(':accno', $acc2cr);
		$db->bind(':br', $br2cr);
		$db->bind(':subbr', $sub2cr);
		$db->bind(':debit', $amount);	// with the amount 
		$db->bind(':credit', 0);
		$db->bind(':reference', $reference);
		$db->bind(':gsttype', $taxtype);
		$db->bind(':descript1', $descript1);
		$db->bind(':taxpcent', $taxpcent);
		$db->bind(':currency', $currency);
		$db->bind(':rate', $rate);
		
		$db->execute();	
		
		$db->query("update ".$findb.".glmast set obal = obal + ".$amount." where accountno = 79 and branch = '".$br2dr."' and sub = 0");
		$db->execute();	
		
		
	} else {
		
		// debit debtor
		$db->query("insert into ".$findb.".trmain (ddate,accountno,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent,currency,rate) values (:ddate,:accountno,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent,:currency,:rate)");
		$db->bind(':ddate', $ddate);
		$db->bind(':accountno', $acc2dr);	//debit the debtor
		$db->bind(':sub', $sub2dr);
		$db->bind(':accno', $acc2cr);
		$db->bind(':br', $br2cr);
		$db->bind(':subbr', $sub2cr);
		$db->bind(':debit', $total);	// with the amount 
		$db->bind(':credit', 0);
		$db->bind(':reference', $reference);
		$db->bind(':gsttype', $taxtype);
		$db->bind(':descript1', $descript1);
		$db->bind(':taxpcent', $taxpcent);
		$db->bind(':currency', $currency);
		$db->bind(':rate', $rate);
		
		$db->execute();	
	
		if ($aged == 'Current') {
			$db->query("update ".$cltdb.".client_company_xref set current = current + ".$total." where company_id = ".$coyno." and drno = ".$acc2dr." and drsub = ".$sub2dr);
		}
		if ($aged == 'D30') {
			$db->query("update ".$cltdb.".client_company_xref set d30 = d30 + ".$total." where company_id = ".$coyno." and drno = ".$acc2dr." and drsub = ".$sub2dr);
		}
		if ($aged == 'D60') {
			$db->query("update ".$cltdb.".client_company_xref set d60 = d60 + ".$total." where company_id = ".$coyno." and drno = ".$acc2dr." and drsub = ".$sub2dr);
		}
		if ($aged == 'D90') {
			$db->query("update ".$cltdb.".client_company_xref set d90 = d90 + ".$total." where company_id = ".$coyno." and drno = ".$acc2dr." and drsub = ".$sub2dr);
		}
		if ($aged == 'D120') {
			$db->query("update ".$cltdb.".client_company_xref set d120 = d120 + ".$total." where company_id = ".$coyno." and drno = ".$acc2dr." and drsub = ".$sub2dr);
		}		
		
		$db->execute();	
			
		// debit debtors control
			
		$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,descript1,taxpcent,currency,rate) values (:ddate,:accountno,:branch,:accno,:br,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent,:currency,:rate)");
		$db->bind(':ddate', $ddate);
		$db->bind(':accountno', 801);	//debit the debtor control
		$db->bind(':branch', $br2dr);
		$db->bind(':accno', $acc2cr);
		$db->bind(':br', $br2cr);
		$db->bind(':debit', $total);	// with the amount 
		$db->bind(':credit', 0);
		$db->bind(':reference', $reference);
		$db->bind(':gsttype', $taxtype);
		$db->bind(':descript1', $descript1);
		$db->bind(':taxpcent', $taxpcent);
		$db->bind(':currency', $currency);
		$db->bind(':rate', $rate);
		
		$db->execute();	
		
		$db->query("update ".$findb.".glmast set obal = obal + ".$total." where accountno = 801 and branch = '".$br2cr."'");
		$db->execute();				

		// credit overs and unders
			$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent,currency,rate) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent,:currency,:rate)");
			$db->bind(':ddate', $ddate);
			$db->bind(':accountno', 79);	// credit the income account
			$db->bind(':branch', $br2cr);
			$db->bind(':sub', $sub2cr);
			$db->bind(':accno', $acc2dr);
			$db->bind(':br', $br2dr);
			$db->bind(':subbr', $sub2dr);
			$db->bind(':debit', 0);	
			$db->bind(':credit', $amount);  // with the amount 
			$db->bind(':reference', $reference);
			$db->bind(':gsttype', $taxtype);
			$db->bind(':descript1', $descript1);
			$db->bind(':taxpcent', $taxpcent);
			$db->bind(':currency', $currency);
			$db->bind(':rate', $rate);
			
			$db->execute();	
			
			$db->query("update ".$findb.".glmast set obal = obal - ".$amount." where accountno = 79 and branch = '".$br2cr."' and sub = 0");
			$db->execute();			
	}

}


*/


$db->closeDB();

?>
