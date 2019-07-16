<?php
session_start();
//ini_set('display_errors', true);

$coyno = $_SESSION['s_coyid'];

if (isset($_REQUEST['paymethod'])) {
	$paymentmethod = $_REQUEST['paymethod'];				
} else {
	$paymentmethod = "";					
}

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

$table = 'ztmp'.$user_id.'_trans';
date_default_timezone_set($_SESSION['s_timezone']);

$findb = $_SESSION['s_findb'];
$cltdb = $_SESSION['s_cltdb'];

$db->query("select gsttype as gstinvpay, roundto as rnd from ".$findb.".globals");
$row = $db->single();
extract($row);



$db->query("show columns FROM ".$findb.".".$table." like 'debtor'");
$rows = $db->resultset();
$exists = $db->rowCount();

$db->query("select * from ".$findb.".".$table);
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	
	$rno = '';
	
	$transtype = substr($reference,0,3);
	
	$rno = '';
	$xrefstr = '';
	
	if ($transtype == 'REC' || $transtype == 'PAY') {
		$db->query("select inv from ".$findb.".".$table." limit 1");
		$row = $db->single();
		extract($row);
		$rno = $inv;
	}
	
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

	if (empty($br2cr)) {$br2cr = '0001';}
	if (empty($sub2cr)) {$sub2cr = 0;}
	if (empty($br2dr)) {$br2dr = '0001';}
	if (empty($sub2dr)) {$sub2dr = 0;}

	//**********************************************************
	// DEBIT LEG
	//**********************************************************		
	switch ($dacctype) {
		case 'din':
			// debit an income account
			
			// debit the income account with the amount excluding tax in trmain
			// add the amount excluding tax to the acc2dr in glmast
			// if tax > 0 
				// grosssales = grosssales * -1
				// if drgst = Y, useingst = N else useingst = Y
				// if gstinpay = Invoice
					// debit 870 in trmain, populate grosssales
					// add the tax to 870 in glmast
				// else
					// debit 871 in trmain, populate grosssales
					// add the tax to 871 in glmast
			break;
		case 'dex':
			// debit cost of sales or expense account
			
			// debit the expense account with the excluding tax in trmain
			// add the amount excluding tax to the acc2dr in glmast
			// if tax > 0 
				// if drgst = Y, useingst = N else useingst = Y
				// if gstinpay = Invoice
					// debit 870 in trmain, populate grosspurchases
					// add the tax to 870 in glmast
				// else
					// debit 871 in trmain, populate grosspurchases
					// add the tax to 871 in glmast			

			break;
		case 'dbs':
			// debit balance sheet account
			
			// if acc2dr = 870, useingst = Y else useingst = N
			// debit the balance sheet account with the amount including tax in trmain
			// add the amount including tax to the acc2dr in glmast

			break;
		case 'das':
			// debit fixed asset account
			
			// debit the asset account with the amount excluding tax in trmain
			// debit the asset control account with the amount excluding tax in trmain
			// add the amount excluding tax to asset control account 701 in glmast
			// if tax > 0 
				// if drgst = Y, useingst = N else useingst = Y
				// if gstinpay = Invoice
					// debit 870 in trmain, populate grosspurchases with total including tax
					// add the tax to 870 in glmast
				// else
					// debit 871 in trmain, populate grosspurchases with total including tax
					// add the tax to 871 in glmast		
			// add cost + amount excluding tax to asset in fixassets
			
			break;
		case 'd50':
			// debit accumulated depreciation control
			
			// debit 702 with amount excluding tax in trmain
			// add amount excluding tax to 702 in glmast
			// totdep = totdep - amount excluding tax in fixassets, dep5000 = dep5000 - amount excluding tax where accountno = asset account
					
			break;
		case 'dcr':
			// debit creditor account
			
			// debit the creditor account with the amount including tax in trmain
			// add amount including tax to relevant current ....d120 in client_company_xref
			// debit the creditor control account with the amount including tax in trmain	
			// add the amount including tax to the creditor control account 851 in glmast
			// allocate amount including tax to cash, cheque, eftpos, ccard vairable dependant on paymentmethod

			// add record to invhead
			// add record to invtrans
				
			break;
		case 'ddr':
		
			// debit debtor account
			
			// debit the debtor with the amount including tax in trmain
			// add amount including tax to relevant current ....d120 in client_company_xref
			// debit the debtors control 801 with the amount including tax in trmain
			// add the amount including tax to debtors control 801 in glmast
			// allocate amount including tax to cash, cheque, eftpos, ccard vairable dependant on paymentmethod
			
			// add record to invhead
			// add record to invtrans

			break;
	}

	// if gst based on payment
	
	// if tax >0
		//if drgst = Y, useingst = N else useingst = Y
	// sort out branches and sub account if empty
	// if acc2dr >= 751 && acc2dr <= 800
		// debit 871 with tax in trmain
		// add tax to 871 in glmast
		// credit 870 with tax in trmain
		// subtract tax from 870 in glmast


	//**********************************************************
	// CREDIT LEG
	//**********************************************************
	
	switch ($cacctype) {
		case 'cin':
			// credit an income account

			$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
			$db->bind(':ddate', $ddate);
			$db->bind(':accountno', $acc2cr);	// credit the income account
			$db->bind(':branch', $br2cr);
			$db->bind(':sub', $sub2cr);
			$db->bind(':accno', $acc2dr);
			$db->bind(':br', $br2dr);
			$db->bind(':subbr', $sub2dr);
			$db->bind(':debit', 0);	
			$db->bind(':credit', $amount);  // with the amount excluding GST
			$db->bind(':reference', $reference);
			$db->bind(':gsttype', $taxtype);
			$db->bind(':descript1', $descript1);
			$db->bind(':taxpcent', $taxpcent);
			
			$db->execute();	
			
			$db->query("update ".$findb.".glmast set obal = obal - ".$amount." where accountno = ".$acc2cr." and branch = '".$br2cr."' and sub = ".$sub2cr);
			$db->execute();	
			
			if ($tax > 0) {
				if ($crgst == 'Y') {
					$useingst = 'N';
				} else {
					$useingst = 'Y';
				}

				if ($gstinvpay == 'Invoice') {
					$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,gstrecon,grosssales,descript1,taxpcent) values (:ddate,:accountno,:branch,:accno,:br,:debit,:credit,:reference,:gsttype,:gstrecon,:grosssales,:descript1,:taxpcent)");
					$db->bind(':ddate', $ddate);
					$db->bind(':accountno', 870);	//credit GST Payable
					$db->bind(':branch', $br2cr);
					$db->bind(':accno', $acc2cr);
					$db->bind(':br', $br2cr);
					$db->bind(':debit', 0);	
					$db->bind(':credit', $tax);  // with the amount of GST
					$db->bind(':reference', $reference);
					$db->bind(':gsttype', $taxtype);
					$db->bind(':gstrecon', $useingst);
					$db->bind(':grosssales', $total);
					$db->bind(':descript1', $descript1);
					$db->bind(':taxpcent', $taxpcent);
					
					$db->execute();						
					
					$db->query("update ".$findb.".glmast set obal = obal - ".$tax." where accountno = 870 and branch = '".$br2cr."'");
					$db->execute();						
					
				} else {
					
					$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,gstrecon,grosssales,descript1,taxpcent) values (:ddate,:accountno,:branch,:accno,:br,:debit,:credit,:reference,:gsttype,:gstrecon,:grosssales,:descript1,:taxpcent)");
					$db->bind(':ddate', $ddate);
					$db->bind(':accountno', 871);	//debit GST Payable
					$db->bind(':branch', $br2cr);
					$db->bind(':accno', $acc2cr);
					$db->bind(':br', $br2cr);
					$db->bind(':debit', 0);	
					$db->bind(':credit', $tax);	// with the amount of GST
					$db->bind(':reference', $reference);
					$db->bind(':gsttype', $taxtype);
					$db->bind(':gstrecon', $useingst);
					$db->bind(':grosssales', $total);
					$db->bind(':descript1', $descript1);
					$db->bind(':taxpcent', $taxpcent);
					
					$db->execute();	
					
					$db->query("update ".$findb.".glmast set obal = obal - ".$tax." where accountno = 871 and branch = '".$br2cr."'");
					$db->execute();						
					
				}
			}
					
			break;
		case 'cex':
			// credit cost of sales or expense account
			$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
			$db->bind(':ddate', $ddate);
			$db->bind(':accountno', $acc2cr);	// credit the expense or purchase
			$db->bind(':branch', $br2cr);
			$db->bind(':sub', $sub2cr);
			$db->bind(':accno', $acc2dr);
			$db->bind(':br', $br2dr);
			$db->bind(':subbr', $sub2dr);
			$db->bind(':debit', 0);	
			$db->bind(':credit', $amount);  // with the amount excluding GST
			$db->bind(':reference', $reference);
			$db->bind(':gsttype', $taxtype);
			$db->bind(':descript1', $descript1);
			$db->bind(':taxpcent', $taxpcent);
			
			$db->execute();	
			
			$db->query("update ".$findb.".glmast set obal = obal - ".$amount." where accountno = ".$acc2cr." and branch = '".$br2cr."' and sub = ".$sub2cr);
			$db->execute();	
	
			
			if ($tax > 0) {
				if ($crgst == 'Y') {
					$useingst = 'N';
				} else {
					$useingst = 'Y';
				}
				if (gstinvpay == 'Invoice') {
					
					$total = $total * -1;
					
					$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,gstrecon,grosspurchases,descript1,taxpcent) values (:ddate,:accountno,:branch,:accno,:br,:debit,:credit,:reference,:gsttype,:gstrecon,:grosspurchases,:descript1,:taxpcent)");
					$db->bind(':ddate', $ddate);
					$db->bind(':accountno', 870);	//credit GST Payable
					$db->bind(':branch', $br2cr);
					$db->bind(':accno', $acc2cr);
					$db->bind(':br', $br2cr);
					$db->bind(':debit', 0);	
					$db->bind(':credit', $tax);  // with the amount of GST
					$db->bind(':reference', $reference);
					$db->bind(':gsttype', $taxtype);
					$db->bind(':gstrecon', $useingst);
					$db->bind(':grosspurchases', $total);
					$db->bind(':descript1', $descript1);
					$db->bind(':taxpcent', $taxpcent);
					
					$db->execute();						
					
					$db->query("update ".$findb.".glmast set obal = obal - ".$tax." where accountno = 870 and branch = '".$br2cr."'");
					$db->execute();						
					
				} else {
					
					$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,gstrecon,grosspurchases,descript1,taxpcent) values (:ddate,:accountno,:branch,:accno,:br,:debit,:credit,:reference,:gsttype,:gstrecon,:grosspurchases,:descript1,:taxpcent)");
					$db->bind(':ddate', $ddate);
					$db->bind(':accountno', 870);	//credit GST Payable
					$db->bind(':branch', $br2cr);
					$db->bind(':accno', $acc2cr);
					$db->bind(':br', $br2cr);
					$db->bind(':debit', 0);	
					$db->bind(':credit', $tax);  // with the amount of GST
					$db->bind(':reference', $reference);
					$db->bind(':gsttype', $taxtype);
					$db->bind(':gstrecon', $useingst);
					$db->bind(':grosspurchases', $total);
					$db->bind(':descript1', $descript1);
					$db->bind(':taxpcent', $taxpcent);
					
					$db->execute();	
					
					$db->query("update ".$findb.".glmast set obal = obal - ".$tax." where accountno = 871 and branch = '".$br2cr."'");
					$db->execute();	
					
				}
			
			}
			
			break;
		case 'cbs':
			// credit balance sheet account
			if ($acc2cr == 870) {
				$useingst = 'Y';
			} else {
				$useingst = '';
			}

			$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,gstrecon,descript1,taxpcent) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:gstrecon,:descript1,:taxpcent)");
			$db->bind(':ddate', $ddate);
			$db->bind(':accountno', $acc2cr);	
			$db->bind(':branch', $br2cr);
			$db->bind(':sub', $sub2cr);
			$db->bind(':accno', $acc2dr);
			$db->bind(':br', $br2dr);
			$db->bind(':subbr', $sub2dr);
			$db->bind(':debit', 0);	
			$db->bind(':credit', $total);  // with the amount including GST
			$db->bind(':reference', $reference);
			$db->bind(':gsttype', $taxtype);
			$db->bind(':gstrecon', $useingst);
			$db->bind(':descript1', $descript1);
			$db->bind(':taxpcent', $taxpcent);
			
			$db->execute();	
			
			$db->query("update ".$findb.".glmast set obal = obal - ".$total." where accountno = ".$acc2cr." and branch = '".$br2cr."' and sub = ".$sub2cr);
			$db->execute();	
			
			
			break;
		case 'cas':
			// credit fixed asset account
			$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:branch,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
			$db->bind(':ddate', $ddate);
			$db->bind(':accountno', $acc2cr);	// credit the asset
			$db->bind(':branch', $br2cr);
			$db->bind(':accno', $acc2dr);
			$db->bind(':br', $br2dr);
			$db->bind(':subbr', $sub2dr);
			$db->bind(':debit', 0);
			$db->bind(':credit', $amount);	// with the amount excluding GST
			$db->bind(':reference', $reference);
			$db->bind(':gsttype', $taxtype);
			$db->bind(':descript1', $descript1);
			$db->bind(':taxpcent', $taxpcent);
			
			$db->execute();	
			
			$db->query("update ".$findb.".glmast set obal = obal - ".$amount." where accountno = ".$acc2cr." and branch = '".$br2cr."' and sub = ".$sub2cr);
			$db->execute();	
			
			$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:branch,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
			$db->bind(':ddate', $ddate);
			$db->bind(':accountno', 701);	// credit the asset conrol
			$db->bind(':branch', $br2cr);
			$db->bind(':accno', $acc2dr);
			$db->bind(':br', $br2dr);
			$db->bind(':subbr', $sub2dr);
			$db->bind(':debit', 0);
			$db->bind(':credit', $amount);	// with the amount excluding GST
			$db->bind(':reference', $reference);
			$db->bind(':gsttype', $taxtype);
			$db->bind(':descript1', $descript1);
			$db->bind(':taxpcent', $taxpcent);
			
			$db->execute();		
			
			$db->query("update ".$findb.".glmast set obal = obal - ".$amount." where accountno = 701 and branch = '".$br2cr."'");
			$db->execute();		

			if ($tax > 0) {
				if ($crgst == 'Y') {
					$useingst = 'N';
				} else {
					$useingst = 'Y';
				}
				if ($gstinvpay == 'Invoice') {
					$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,gstrecon,grosspurchases,descript1,taxpcent) values (:ddate,:accountno,:branch,:accno,:br,:debit,:credit,:reference,:gsttype,:gstrecon,:grosspurchases,:descript1,:taxpcent)");
					$db->bind(':ddate', $ddate);
					$db->bind(':accountno', 870);	//credit GST Payable
					$db->bind(':branch', $br2cr);
					$db->bind(':accno', $acc2cr);
					$db->bind(':br', $br2cr);
					$db->bind(':debit', 0);	
					$db->bind(':credit', $tax);  // with the amount of GST
					$db->bind(':reference', $reference);
					$db->bind(':gsttype', $taxtype);
					$db->bind(':gstrecon', $useingst);
					$db->bind(':grosspurchases', $total);
					$db->bind(':descript1', $descript1);
					$db->bind(':taxpcent', $taxpcent);
					
					$db->execute();						
					
					$db->query("update ".$findb.".glmast set obal = obal - ".$tax." where accountno = 870 and branch = '".$br2cr."'");
					$db->execute();						
					
				} else {
					
					$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,gstrecon,grosspurchases,descript1,taxpcent) values (:ddate,:accountno,:branch,:accno,:br,:debit,:credit,:reference,:gsttype,:gstrecon,:grosspurchases,:descript1,:taxpcent)");
					$db->bind(':ddate', $ddate);
					$db->bind(':accountno', 871);	//credit GST Payable
					$db->bind(':branch', $br2cr);
					$db->bind(':accno', $acc2cr);
					$db->bind(':br', $br2cr);
					$db->bind(':debit', 0);	
					$db->bind(':credit', $tax);  // with the amount of GST
					$db->bind(':reference', $reference);
					$db->bind(':gsttype', $taxtype);
					$db->bind(':gstrecon', $useingst);
					$db->bind(':grosspurchases', $total);
					$db->bind(':descript1', $descript1);
					$db->bind(':taxpcent', $taxpcent);
					
					$db->execute();						
					
					$db->query("update ".$findb.".glmast set obal = obal - ".$tax." where accountno = 871 and branch = '".$br2cr."'");
					$db->execute();						
				}
		
			}
			
			$db->query("update ".$findb.".fixassets set cost = cost - ".$amount." where accountno = ".$acc2cr." and branch = '".$br2cr."'");
			$db->execute();						
								
			break;
		case 'c50':
			$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:branch,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
			$db->bind(':ddate', $ddate);
			$db->bind(':accountno', 702);	// debit accumulated depreciation control
			$db->bind(':branch', $br2cr);
			$db->bind(':accno', $acc2dr);
			$db->bind(':br', $br2dr);
			$db->bind(':subbr', $sub2dr);
			$db->bind(':debit', 0);	
			$db->bind(':credit', $amount); // with the amount excluding GST
			$db->bind(':reference', $assetno);
			$db->bind(':gsttype', $taxtype);
			$db->bind(':descript1', $descript1);
			$db->bind(':taxpcent', $taxpcent);
						
			$db->execute();						
			
			$db->query("update ".$findb.".glmast set obal = obal - ".$amount." where accountno = 702 and branch = '".$br2cr."'");
			$db->execute();						
			
			$db->query("update ".$findb.".fixassets set totdep = totdep + ".$amount.", dep5000 = dep5000 + ".$amount." where accountno = ".$assetno);
			$db->execute();						

											
			break;			
		case 'ccr':
			// credit creditor account
			$db->query("insert into ".$findb.".trmain (ddate,accountno,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
			$db->bind(':ddate', $ddate);
			$db->bind(':accountno', $acc2cr);	// credit the creditor
			$db->bind(':sub', $sub2cr);
			$db->bind(':accno', $acc2dr);
			$db->bind(':br', $br2dr);
			$db->bind(':subbr', $sub2dr);
			$db->bind(':debit', 0);	
			$db->bind(':credit', $total);	// with the amount including GST
			$db->bind(':reference', $reference);
			$db->bind(':gsttype', $taxtype);
			$db->bind(':descript1', $descript1);
			$db->bind(':taxpcent', $taxpcent);
						
			$db->execute();	
			
			if ($aged == 'Current') {
				$db->query("update ".$cltdb.".client_company_xref set current = current - ".$total." where company_id = ".$coyno." and crno = ".$acc2cr." and crsub = ".$sub2cr);
			}
			if ($aged == 'D30') {
				$db->query("update ".$cltdb.".client_company_xref set d30 = d30 - ".$total." where company_id = ".$coyno." and crno = ".$acc2cr." and crsub = ".$sub2cr);
			}
			if ($aged == 'D60') {
				$db->query("update ".$cltdb.".client_company_xref set d60 = d60 - ".$total." where company_id = ".$coyno." and crno = ".$acc2cr." and crsub = ".$sub2cr);
			}
			if ($aged == 'D90') {
				$db->query("update ".$cltdb.".client_company_xref set d90 = d90 - ".$total." where company_id = ".$coyno." and crno = ".$acc2cr." and crsub = ".$sub2cr);
			}
			if ($aged == 'D120') {
				$db->query("update ".$cltdb.".client_company_xref set d120 = d120 - ".$total." where company_id = ".$coyno." and crno = ".$acc2cr." and crsub = ".$sub2cr);
			}
			
			$db->execute();	
			
			$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:branch,:accno,:br,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
			$db->bind(':ddate', $ddate);
			$db->bind(':accountno', 851);	// credit the creditor conrol
			$db->bind(':branch', $br2dr);
			$db->bind(':accno', $acc2dr);
			$db->bind(':br', $br2dr);
			$db->bind(':debit', 0);	
			$db->bind(':credit', $total);	// with the amount including GST
			$db->bind(':reference', $reference);
			$db->bind(':gsttype', $taxtype);
			$db->bind(':descript1', $descript1);
			$db->bind(':taxpcent', $taxpcent);
						
			$db->execute();	
			
			$db->query("update ".$findb.".glmast set obal = obal - ".$total." where accountno = 851 and branch = '".$br2dr."'");
			$db->execute();	
			
			$payrec = substr($reference,0,3);
			//if ($payrec <> 'PAY' && $payrec <> 'REC') {

				// add record to invhead
				
				$db->query("insert into ".$findb.".invhead (ddate,accountno,branch,sub,gldesc,transtype,ref_no,totvalue,tax,client,staff) values (:ddate,:accountno,:branch,:sub,:gldesc,:transtype,:ref_no,:totvalue,:tax,:client,:staff)");
				$db->bind(':ddate', $ddate);
				$db->bind(':accountno', $acc2cr);	
				$db->bind(':branch', $br2dr);
				$db->bind(':sub', $sub2cr);
				$db->bind(':gldesc', $descript1);
				$db->bind(':transtype', $payrec);
				$db->bind(':ref_no', $reference);	
				$db->bind(':totvalue', $amount);	
				$db->bind(':tax', $tax);
				$db->bind(':client', $client);
				$db->bind(':staff', $uname);
							
				$db->execute();	
				
				// insert records into invtrans
			
				$db->query("insert into ".$findb.".invtrans (item,price,quantity,taxtype,taxpcent,tax,ref_no,value) values (item,price,quantity,taxtype,taxpcent,tax,ref_no,value)");
				$db->bind(':item', $descript1);
				$db->bind(':price', $amount);
				$db->bind(':quantity', 1);	
				$db->bind(':taxtype', $taxtype);	
				$db->bind(':taxpcent', $taxpcent);
				$db->bind(':tax', $tax);
				$db->bind(':ref_no', $reference);				
				$db->bind(':value', $amount);
				
				$db->execute();	
			//}

			break;
		case 'cdr':
			// credit debtor account
			$db->query("insert into ".$findb.".trmain (ddate,accountno,sub,accno,br,subbr,debit,credit,reference,inv,gsttype,descript1,taxpcent) values (:ddate,:accountno,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:inv,:gsttype,:descript1,:taxpcent)");
			$db->bind(':ddate', $ddate);
			$db->bind(':accountno', $acc2cr);	//credit the debtor
			$db->bind(':sub', $sub2cr);
			$db->bind(':accno', $acc2dr);
			$db->bind(':br', $br2dr);
			$db->bind(':subbr', $sub2dr);
			$db->bind(':debit', 0);	
			$db->bind(':credit', $total);	// with the amount including GST
			$db->bind(':reference', $reference);
			$db->bind(':inv', $xrefstr);
			$db->bind(':gsttype', $taxtype);
			$db->bind(':descript1', $descript1);
			$db->bind(':taxpcent', $taxpcent);
			
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
			
			$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:branch,:accno,:br,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
			$db->bind(':ddate', $ddate);
			$db->bind(':accountno', 801);	// credit the debtors conrol
			$db->bind(':branch', $br2dr);
			$db->bind(':accno', $acc2dr);
			$db->bind(':br', $br2dr);
			$db->bind(':debit', 0);	
			$db->bind(':credit', $total);	// with the amount including GST
			$db->bind(':reference', $reference);
			$db->bind(':gsttype', $taxtype);
			$db->bind(':descript1', $descript1);
			$db->bind(':taxpcent', $taxpcent);
						
			$db->execute();	

			$db->query("update ".$findb.".glmast set obal = obal - ".$total." where accountno = 801 and branch = '".$br2dr."'");
			$db->execute();	
			
			$payrec = substr($reference,0,3);
			//if ($payrec <> 'PAY' && $payrec <> 'REC') {
				
				
			$totalvalue = $total;
			
				switch ($paymentmethod) {
				case 'csh':
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
				

				// add record to invhead
				
				$db->query("insert into ".$findb.".invhead (ddate,accountno,branch,sub,gldesc,transtype,ref_no,totvalue,tax,cash,cheque,eftpos,ccard,client,staff) values (:ddate,:accountno,:branch,:sub,:gldesc,:transtype,:ref_no,:totvalue,:tax,:cash,:cheque,:eftpos,:ccard,:client,:staff)");
				$db->bind(':ddate', $ddate);
				$db->bind(':accountno', $acc2cr);	
				$db->bind(':branch', $br2dr);
				$db->bind(':sub', $sub2cr);
				$db->bind(':gldesc', $descript1);
				$db->bind(':transtype', $payrec);
				$db->bind(':ref_no', $reference);	
				$db->bind(':totvalue', $amount);	
				$db->bind(':tax', $tax);
				$db->bind(':cash', $cash);	
				$db->bind(':cheque', $cheque);	
				$db->bind(':eftpos', $eftpos);
				$db->bind(':ccard', $ccard);
				$db->bind(':client', $client);
				$db->bind(':staff', $uname);
							
				$db->execute();					
				
				// insert records into invtrans

				$db->query("insert into ".$findb.".invtrans (item,price,quantity,taxtype,taxpcent,tax,ref_no,value) values (item,price,quantity,taxtype,taxpcent,tax,ref_no,value)");
				$db->bind(':item', $descript1);
				$db->bind(':price', $amount);
				$db->bind(':quantity', 1);	
				$db->bind(':taxtype', $taxtype);	
				$db->bind(':taxpcent', $taxpcent);
				$db->bind(':tax', $tax);
				$db->bind(':ref_no', $reference);				
				$db->bind(':value', $amount);
				
				$db->execute();	
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
					$useingst = 'N';
				}
				
				if (empty($br2cr)) {$br2cr = '0001';}
				if (empty($sub2cr)) {$sub2cr = 0;}
				if (empty($br2dr)) {$br2dr = '0001';}
				if (empty($sub2dr)) {$sub2dr = 0;}
				
				if ($acc2cr >= 751 && $acc2cr <= 800) {
					$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,gstrecon,grosspurchases,descript1,taxpcent) values (:ddate,:accountno,:branch,:accno,:br,:debit,:credit,:reference,:gsttype,:gstrecon,:grosspurchases,:descript1,:taxpcent)");
					$db->bind(':ddate', $ddate);
					$db->bind(':accountno', 870);	// debit GST on Payment
					$db->bind(':branch', $br2dr);
					$db->bind(':accno', 871);
					$db->bind(':br', $br2cr);
					$db->bind(':debit', $tax);	// with the amount of GST
					$db->bind(':credit', 0);
					$db->bind(':reference', $reference);
					$db->bind(':gsttype', $taxtype);
					$db->bind(':gstrecon', $useingst);
					$db->bind(':grosspurchases', $total);
					$db->bind(':descript1', $descript1);
					$db->bind(':taxpcent', $taxpcent);
					
					$db->execute();						
				
					$db->query("update ".$findb.".glmast set obal = obal + ".$tax." where accountno = 870 and branch = '".$br2dr."'");
					$db->execute();						
				
					$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,gstrecon,descript1,taxpcent) values (:ddate,:accountno,:branch,:accno,:br,:debit,:credit,:reference,:gsttype,:gstrecon,:descript1,:taxpcent)");
					$db->bind(':ddate', $ddate);
					$db->bind(':accountno', 871);	
					$db->bind(':branch', $br2cr);
					$db->bind(':accno', 870);
					$db->bind(':br', $br2dr);
					$db->bind(':debit', 0);	
					$db->bind(':credit', $tax);	// with the amount of GST
					$db->bind(':reference', $reference);
					$db->bind(':gsttype', $taxtype);
					$db->bind(':gstrecon', $useingst);
					$db->bind(':descript1', $descript1);
					$db->bind(':taxpcent', $taxpcent);
					
					$db->execute();						
					
					$db->query("update ".$findb.".glmast set obal = obal - ".$tax." where accountno = 871 and branch = '".$br2cr."'");
					$db->execute();						
				
				}
			}

	}
	

	// Inter branch Transactions
	if ($DGL == 'Y' and $CGL == 'Y' and $br2dr != $br2cr) {
	
		// credit inter branch transfer account
		$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
		$db->bind(':ddate', $ddate);
		$db->bind(':accountno', 997);	
		$db->bind(':branch', $br2dr);
		$db->bind(':sub', 0);
		$db->bind(':accno', $acc2cr);
		$db->bind(':br', $br2cr);
		$db->bind(':subbr', $sub2cr);
		$db->bind(':debit', 0);	
		$db->bind(':credit', $total);	// with the amount including GST
		$db->bind(':reference', $reference);
		$db->bind(':gsttype', $taxtype);
		$db->bind(':descript1', $descript1);
		$db->bind(':taxpcent', $taxpcent);
					
		$db->execute();						
		
		$db->query("update ".$findb.".glmast set obal = obal - ".$total." where accountno = 997 and branch = '".$br2dr."'");
		$db->execute();						
			
		// debit inter branch transfer account
		$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
		$db->bind(':ddate', $ddate);
		$db->bind(':accountno', 997);	
		$db->bind(':branch', $br2cr);
		$db->bind(':sub', $sub2cr);
		$db->bind(':accno', $acc2dr);
		$db->bind(':br', $br2dr);
		$db->bind(':subbr', $sub2dr);
		$db->bind(':debit', $total);	// with the amount including GST
		$db->bind(':credit', 0);	
		$db->bind(':reference', $reference);
		$db->bind(':gsttype', $taxtype);
		$db->bind(':descript1', $descript1);
		$db->bind(':taxpcent', $taxpcent);
					
		$db->execute();						
		
		$db->query("update ".$findb.".glmast set obal = obal + ".$total." where accountno = 997 and branch = '".$br2cr."'");
		$db->execute();						
			
	} // if posting between different branches
				
	// add transaction to audit trail
		$db->query("insert into ".$findb.".audit (ddate,acc2dr,brdr,subdr,acc2cr,brcr,subcr,descript1,reference,amount,tax,total,taxtype,taxpcent,entrydate,entrytime,username,userip) values (:ddate,:acc2dr,:brdr,:subdr,:acc2cr,:brcr,:subcr,:descript1,:reference,:amount,:tax,:total,:taxtype,:taxpcent,:entrydate,:entrytime,:username,:userip)");
		$db->bind(':ddate', $ddate);
		$db->bind(':acc2dr', $acc2dr);	
		$db->bind(':brdr', $br2dr);
		$db->bind(':subdr', $sub2dr);
		$db->bind(':acc2cr', $acc2cr);
		$db->bind(':brcr', $br2cr);
		$db->bind(':subcr', $sub2cr);
		$db->bind(':descript1', $descript1);	
		$db->bind(':reference', $reference);	
		$db->bind(':amount', $amount);
		$db->bind(':tax', $tax);
		$db->bind(':total', $total);
		$db->bind(':taxtype', $taxtype);
		$db->bind(':taxpcent', $taxpcent);	
		$db->bind(':entrydate', date("Y-m-d"));
		$db->bind(':entrytime', date("H:i:s"));
		$db->bind(':username', $unm);
		$db->bind(':userip', $uip);
					
		$db->execute();						

		// update the reference number
		$ref = substr($reference,0,3);
		$refno = substr($reference,3);
		if ($ref != 'CNV') {		// do not update when a Creditor Invoice
			$db->query("update ".$findb.".numbers set ".$ref." = ".$refno);
		$db->execute();						
		}
		
}

//$db->query("delete from ".$findb.".".$table);
//$db->execute();						

$db->closeDB();

?>

