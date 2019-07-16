<?php
session_start();
//ini_set('display_errors', true);

if ($_SESSION['s_server'] == 'localhost') {
	$root = $_SERVER['DOCUMENT_ROOT'];
	$pathdb = $root.'logtracc/db.php';
	require($pathdb);
} else {
	$root = $_SERVER['DOCUMENT_ROOT'];
	$pathdb = $root.'/db.php';
	require($pathdb);
}


$coyno = $_SESSION['s_coyid'];

$usersession = $_SESSION['usersession'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$rtfile = $_SESSION['s_rtfile'];

switch($rtfile) {
	case "rt1":
		$table = "z_1rec";
		break;
	case "rt2":
		$table = "z_2rec";
		break;
	case "rt3":
		$table = "z_3rec";
		break;
}

date_default_timezone_set($_SESSION['s_timezone']);

$findb = $_SESSION['s_findb'];

// give each entry an appropriate reference number
$db->query("select uid, reference from ".$findb.".".$table);
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	$id = $uid;
	$ref = $reference;
	
	$db->query("select ".$ref." from ".$findb.".numbers");
	$row = $db->single();
	extract($row);
	$refno = $$ref + 1;
	$db->query("update ".$findb.".numbers set ".$ref." = :refno");
	$db->bind(':refno', $refno);
	$db->execute();
	
}

$db->query("select gsttype as gstinvpay from ".$findb.".globals");
$row = $db->single();
extract($row);

$db->query("show columns FROM ".$findb.".".$table." like 'debtor'");
$rows = $db->resultset();
$exists = count($rows);

$db->query("select * from ".$findb.".".$table);
$rows = $db->resultset();
foreach ($rows as $row) {
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
			
			$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
			$db->bind(':ddate', $ddate);
			$db->bind(':accountno', $acc2dr);	//debit the debtor
			$db->bind(':branch', $br2dr);
			$db->bind(':sub', $sub2dr);
			$db->bind(':accno', $acc2cr);
			$db->bind(':br', $br2cr);
			$db->bind(':subbr', $sub2cr);
			$db->bind(':debit', $amount);	// with the amount including GST
			$db->bind(':credit', 0);
			$db->bind(':reference', $reference);
			$db->bind(':gsttype', $taxtype);
			$db->bind(':descript1', $descript1);
			$db->bind(':taxpcent', $taxpcent);
			
			$db->execute();	
			
			$db->query("update ".$findb.".glmast set obal = obal + ".$amount." where accountno = ".$acc2dr." and branch = '".$br2dr."' and sub = ".$sub2dr);
			$db->execute();	
			
			if ($tax > 0) {
				if ($drgst == 'Y') {
					$useingst = 'N';
				} else {
					$useingst = 'Y';
				}
				
				if ($gstinvpay == 'Invoice') {
			
					$total = $total * -1;
					
					$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,gstrecon,grosssales,descript1,taxpcent) values (:ddate,:accountno,:branch,:accno,:br,:debit,:credit,:reference,:gsttype,:gstrecon,:grosssales,:descript1,:taxpcent)");
					$db->bind(':ddate', $ddate);
					$db->bind(':accountno', 870);	//debit GST Payable
					$db->bind(':branch', $br2dr);
					$db->bind(':accno', $acc2cr);
					$db->bind(':br', $br2cr);
					$db->bind(':debit', $tax);	// with the amount of GST
					$db->bind(':credit', 0);
					$db->bind(':reference', $reference);
					$db->bind(':gsttype', $taxtype);
					$db->bind(':gstrecon', $useingst);
					$db->bind(':grosssales', $total);
					$db->bind(':descript1', $descript1);
					$db->bind(':taxpcent', $taxpcent);
					
					$db->execute();	
					
					$sql = "update ".$findb.".glmast set obal = obal + ".$tax." where accountno = 870 and branch = '".$br2dr."'";
					$db->execute();	
					
				} else {
					
					$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,gstrecon,grosssales,descript1,taxpcent) values (:ddate,:accountno,:branch,:accno,:br,:debit,:credit,:reference,:gsttype,:gstrecon,:grosssales,:descript1,:taxpcent)");
					$db->bind(':ddate', $ddate);
					$db->bind(':accountno', 871);	//debit GST Payable
					$db->bind(':branch', $br2dr);
					$db->bind(':accno', $acc2cr);
					$db->bind(':br', $br2cr);
					$db->bind(':debit', $tax);	// with the amount of GST
					$db->bind(':credit', 0);
					$db->bind(':reference', $reference);
					$db->bind(':gsttype', $taxtype);
					$db->bind(':gstrecon', $useingst);
					$db->bind(':grosssales', $total);
					$db->bind(':descript1', $descript1);
					$db->bind(':taxpcent', $taxpcent);
					
					$db->execute();	
					
					$db->query("update ".$findb.".glmast set obal = obal + ".$tax." where accountno = 871 and branch = '".$br2dr."'");
					$db->execute();	
				}
	
			}
					
			break;
		case 'dex':
		case 'dex':
			// debit cost of sales or expense account
			$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
			$db->bind(':ddate', $ddate);
			$db->bind(':accountno', $acc2dr);	// debit the expense or purchase
			$db->bind(':branch', $br2dr);
			$db->bind(':sub', $sub2dr);
			$db->bind(':accno', $acc2cr);
			$db->bind(':br', $br2cr);
			$db->bind(':subbr', $sub2cr);
			$db->bind(':debit', $amount);	// with the amount including GST
			$db->bind(':credit', 0);
			$db->bind(':reference', $reference);
			$db->bind(':gsttype', $taxtype);
			$db->bind(':descript1', $descript1);
			$db->bind(':taxpcent', $taxpcent);
			
			$db->execute();				
			
			$db->query("update ".$findb.".glmast set obal = obal + ".$amount." where accountno = ".$acc2dr." and branch = '".$br2dr."' and sub = ".$sub2dr);
			$db->execute();				
			
			if ($tax > 0) {
				if ($drgst == 'Y') {
					$useingst = 'N';
				} else {
					$useingst = 'Y';
				}
			
				if ($gstinvpay == 'Invoice') {
					$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,gstrecon,grosspurchases,descript1,taxpcent) values (:ddate,:accountno,:branch,:accno,:br,:debit,:credit,:reference,:gsttype,:gstrecon,:grosspurchases,:descript1,:taxpcent)");
					$db->bind(':ddate', $ddate);
					$db->bind(':accountno', 870);	//debit GST Payable
					$db->bind(':branch', $br2dr);
					$db->bind(':accno', $acc2cr);
					$db->bind(':br', $br2cr);
					$db->bind(':debit', $tax);	// with the amount of GST
					$db->bind(':credit', 0);
					$db->bind(':reference', $reference);
					$db->bind(':gsttype', $taxtype);
					$db->bind(':gstrecon', $useingst);
					$db->bind(':grosssales', $total);
					$db->bind(':descript1', $descript1);
					$db->bind(':taxpcent', $taxpcent);
						
					$db->execute();					
	
					$db->query("update ".$findb.".glmast set obal = obal + ".$tax." where accountno = 870 and branch = '".$br2dr."'");
					$db->execute();					
				
				} else {
					
					$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,gstrecon,grosspurchases,descript1,taxpcent) values (:ddate,:accountno,:branch,:accno,:br,:debit,:credit,:reference,:gsttype,:gstrecon,:grosspurchases,:descript1,:taxpcent)");
					$db->bind(':ddate', $ddate);
					$db->bind(':accountno', 871);	//debit GST Payable
					$db->bind(':branch', $br2dr);
					$db->bind(':accno', $acc2cr);
					$db->bind(':br', $br2cr);
					$db->bind(':debit', $tax);	// with the amount of GST
					$db->bind(':credit', 0);
					$db->bind(':reference', $reference);
					$db->bind(':gsttype', $taxtype);
					$db->bind(':gstrecon', $useingst);
					$db->bind(':grosssales', $total);
					$db->bind(':descript1', $descript1);
					$db->bind(':taxpcent', $taxpcent);
						
					$db->execute();					
					
					$db->query("update ".$findb.".glmast set obal = obal + ".$tax." where accountno = 871 and branch = '".$br2dr."'");
					$db->execute();					
					
				}
					
			
			}
			
			break;
		case 'dbs':
			// debit balance sheet account
			
			if ($acc2dr == 870) {
				$useingst = 'Y';
			} else {
				$useingst = '';
			}	

			$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,gstrecon,descript1,taxpcent) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:gstrecon,:descript1,:taxpcent)");
			$db->bind(':ddate', $ddate);
			$db->bind(':accountno', $acc2dr);	
			$db->bind(':branch', $br2dr);
			$db->bind(':sub', $sub2dr);
			$db->bind(':accno', $acc2cr);
			$db->bind(':br', $br2cr);
			$db->bind(':subbr', $sub2cr);
			$db->bind(':debit', $total);	// with the amount including GST
			$db->bind(':credit', 0);
			$db->bind(':reference', $reference);
			$db->bind(':gsttype', $taxtype);
			$db->bind(':gstrecon', $useingst);
			$db->bind(':descript1', $descript1);
			$db->bind(':taxpcent', $taxpcent);
			
			$db->execute();				
			
			$db->query("update ".$findb.".glmast set obal = obal + ".$total." where accountno = ".$acc2dr." and branch = '".$br2dr."' and sub = ".$sub2dr);
			$db->execute();				
			
			
			break;
		case 'das':
			// debit fixed asset account
			$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:branch,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
			$db->bind(':ddate', $ddate);
			$db->bind(':accountno', $acc2dr);	// debit the asset
			$db->bind(':branch', $br2dr);
			$db->bind(':accno', $acc2cr);
			$db->bind(':br', $br2cr);
			$db->bind(':subbr', $sub2cr);
			$db->bind(':debit', $amount);	// with the amount excluding GST
			$db->bind(':credit', 0);
			$db->bind(':reference', $reference);
			$db->bind(':gsttype', $taxtype);
			$db->bind(':descript1', $descript1);
			$db->bind(':taxpcent', $taxpcent);
			
			$db->execute();				
			
			$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:branch,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
			$db->bind(':ddate', $ddate);
			$db->bind(':accountno', 701);	// debit the asset conrol
			$db->bind(':branch', $br2dr);
			$db->bind(':accno', $acc2cr);
			$db->bind(':br', $br2cr);
			$db->bind(':subbr', $sub2cr);
			$db->bind(':debit', $amount);	// with the amount excluding GST
			$db->bind(':credit', 0);
			$db->bind(':reference', $reference);
			$db->bind(':gsttype', $taxtype);
			$db->bind(':descript1', $descript1);
			$db->bind(':taxpcent', $taxpcent);
			
			$db->execute();	
			
			$db->query("update ".$findb.".glmast set obal = obal + ".$amount." where accountno = 701 and branch = '".$br2dr."'");
			$db->execute();	

			if ($tax > 0) {
				if ($drgst == 'Y') {
					$useingst = 'N';
				} else {
					$useingst = 'Y';
				}
				if ($gstinvpay == 'Invoice') {
					$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,gstrecon,grosspurchases,descript1,taxpcent) values (:ddate,:accountno,:branch,:accno,:br,:debit,:credit,:reference,:gsttype,:gstrecon,:grosspurchases,:descript1,:taxpcent)");
					$db->bind(':ddate', $ddate);
					$db->bind(':accountno', 870);	//debit GST Payable
					$db->bind(':branch', $br2dr);
					$db->bind(':accno', $acc2cr);
					$db->bind(':br', $br2cr);
					$db->bind(':debit', $tax);	// with the amount of GST
					$db->bind(':credit', 0);
					$db->bind(':reference', $reference);
					$db->bind(':gsttype', $taxtype);
					$db->bind(':gstrecon', $useingst);
					$db->bind(':grosssales', $total);
					$db->bind(':descript1', $descript1);
					$db->bind(':taxpcent', $taxpcent);
						
					$db->execute();						
					
					$db->query("update ".$findb.".glmast set obal = obal + ".$tax." where accountno = 870 and branch = '".$br2dr."'");
					$db->execute();						
					
				} else {
					
					$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,gstrecon,grosspurchases,descript1,taxpcent) values (:ddate,:accountno,:branch,:accno,:br,:debit,:credit,:reference,:gsttype,:gstrecon,:grosspurchases,:descript1,:taxpcent)");
					$db->bind(':ddate', $ddate);
					$db->bind(':accountno', 871);	//debit GST Payable
					$db->bind(':branch', $br2dr);
					$db->bind(':accno', $acc2cr);
					$db->bind(':br', $br2cr);
					$db->bind(':debit', $tax);	// with the amount of GST
					$db->bind(':credit', 0);
					$db->bind(':reference', $reference);
					$db->bind(':gsttype', $taxtype);
					$db->bind(':gstrecon', $useingst);
					$db->bind(':grosssales', $total);
					$db->bind(':descript1', $descript1);
					$db->bind(':taxpcent', $taxpcent);
						
					$db->execute();						
					
					$db->query("update ".$findb.".glmast set obal = obal + ".$tax." where accountno = 871 and branch = '".$br2dr."'");
					$db->execute();						
					
				}
		
			}
			
			$db->query("update ".$findb.".fixassets set cost = cost + ".$amount." where accountno = ".$acc2dr." and branch = '".$br2dr."'");
			$db->execute();						
			
			break;
		case 'd50':
			$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:branch,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
			$db->bind(':ddate', $ddate);
			$db->bind(':accountno', 702);	// debit accumulated depreciation control
			$db->bind(':branch', $br2cr);
			$db->bind(':accno', $acc2cr);
			$db->bind(':br', $br2cr);
			$db->bind(':subbr', $sub2cr);
			$db->bind(':debit', $amount);	// with the amount excluding GST
			$db->bind(':credit', 0);
			$db->bind(':reference', $assetno);
			$db->bind(':gsttype', $taxtype);
			$db->bind(':descript1', $descript1);
			$db->bind(':taxpcent', $taxpcent);
						
			$db->execute();						
			
			$db->query("update ".$findb.".glmast set obal = obal + ".$amount." where accountno = 702 and branch = '".$br2cr."'");
			$db->execute();						
			
			$db->query("update ".$findb.".fixassets set totdep = totdep - ".$amount.", dep5000 = dep5000 - ".$amount." where accountno = ".$assetno);
			$db->execute();						
											
			break;
		case 'dcr':
			// debit creditor account
			$db->query("insert into ".$findb.".trmain (ddate,accountno,sub,accno,br,subbr,debit,credit,reference,grn,gsttype,descript1,taxpcent) values (:ddate,:accountno,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:grn,:gsttype,:descript1,:taxpcent)");
			$db->bind(':ddate', $ddate);
			$db->bind(':accountno', $acc2dr);	// debit the creditor
			$db->bind(':sub', $sub2dr);
			$db->bind(':accno', $acc2cr);
			$db->bind(':br', $br2cr);
			$db->bind(':subbr', $sub2cr);
			$db->bind(':debit', $total);	// with the amount including GST
			$db->bind(':credit', 0);
			$db->bind(':reference', $reference);
			$db->bind(':grn', $xrefstr);
			$db->bind(':gsttype', $taxtype);
			$db->bind(':descript1', $descript1);
			$db->bind(':taxpcent', $taxpcent);
						
			$db->execute();						
			
			if ($aged == 'Current') {
				$db->query("update ".$cltdb.".client_company_xref set current = current + ".$total." where company_id = ".$coyno." and crno = ".$acc2dr." and crsub = ".$sub2dr);
			}
			if ($aged == 'D30') {
				$db->query("update ".$cltdb.".client_company_xref set d30 = d30 + ".$total." where company_id = ".$coyno." and crno = ".$acc2dr." and crsub = ".$sub2dr);
			}
			if ($aged == 'D60') {
				$db->query("update ".$cltdb.".client_company_xref set d60 = d60 + ".$total." where company_id = ".$coyno." and crno = ".$acc2dr." and crsub = ".$sub2dr);
			}
			if ($aged == 'D90') {
				$db->query("update ".$cltdb.".client_company_xref set d90 = d90 + ".$total." where company_id = ".$coyno." and crno = ".$acc2dr." and crsub = ".$sub2dr);
			}
			if ($aged == 'D120') {
				$db->query("update ".$cltdb.".client_company_xref set d120 = d120 + ".$total." where company_id = ".$coyno." and crno = ".$acc2dr." and crsub = ".$sub2dr);
			}
			
			$db->execute();						
			
			$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:branch,:accno,:br,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
			$db->bind(':ddate', $ddate);
			$db->bind(':accountno', 851);	// debit creditors control
			$db->bind(':branch', $br2cr);
			$db->bind(':accno', $acc2cr);
			$db->bind(':br', $br2cr);
			$db->bind(':debit', $total);	// with the amount including GST
			$db->bind(':credit', 0);
			$db->bind(':reference', $reference);
			$db->bind(':gsttype', $taxtype);
			$db->bind(':descript1', $descript1);
			$db->bind(':taxpcent', $taxpcent);
						
			$db->execute();						
			
			$db->query("update ".$findb.".glmast set obal = obal + ".$total." where accountno = 851 and branch = '".$br2cr."'");
			$db->execute();						
			
			$payrec = substr($reference,0,3);
			//if ($payrec <> 'PAY' && $payrec <> 'REC') {
				
			$totalvalue = $amount + $tax;
				
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
				
				$db->query("insert into ".$findb.".invhead (ddate,accountno,branch,sub,gldesc,transtype,ref_no,totvalue,tax,client,staff,cash,cheque,eftpos,ccard) values (:ddate,:accountno,:branch,:sub,:gldesc,:transtype,:ref_no,:totvalue,:tax,:client,:staff,:cash,:cheque,:eftpos,:ccard)");
				$db->bind(':ddate', $ddate);
				$db->bind(':accountno', $acc2dr);	
				$db->bind(':branch', $br2cr);
				$db->bind(':sub', $sub2dr);
				$db->bind(':gldesc', $descript1);
				$db->bind(':transtype', $payrec);	
				$db->bind(':ref_no', $referencce);
				$db->bind(':totalvalue', $amount);
				$db->bind(':tax', $tax);
				$db->bind(':client', $client);
				$db->bind(':staff', $uname);
				$db->bind(':cash', $cash);
				$db->bind(':cheque', $cheque);
				$db->bind(':eftpos', $eftpos);
				$db->bind(':ccard', $ccard);
				
				$db->execute();						
				
				// insert records into invtrans
			
				$db->query("insert into ".$findb.".invtrans (item,price,quantity,taxtype,taxpcent,tax,ref_no,value) values (:item,:price,:quantity,:taxtype,:taxpcent,:tax,ref_no,:value)");
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
			$db->query("insert into ".$findb.".trmain (ddate,accountno,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
			$db->bind(':ddate', $ddate);
			$db->bind(':accountno', $acc2dr);	//debit the debtor
			$db->bind(':sub', $sub2dr);
			$db->bind(':accno', $acc2cr);
			$db->bind(':br', $br2cr);
			$db->bind(':subbr', $sub2cr);
			$db->bind(':debit', $total);	// with the amount including GST
			$db->bind(':credit', 0);
			$db->bind(':reference', $reference);
			$db->bind(':gsttype', $taxtype);
			$db->bind(':descript1', $descript1);
			$db->bind(':taxpcent', $taxpcent);
			
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
			
			$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:branch,:accno,:br,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
			$db->bind(':ddate', $ddate);
			$db->bind(':accountno', 801);	//debit the debtor control
			$db->bind(':branch', $br2dr);
			$db->bind(':accno', $acc2cr);
			$db->bind(':br', $br2cr);
			$db->bind(':debit', $total);	// with the amount including GST
			$db->bind(':credit', 0);
			$db->bind(':reference', $reference);
			$db->bind(':gsttype', $taxtype);
			$db->bind(':descript1', $descript1);
			$db->bind(':taxpcent', $taxpcent);
			
			$db->execute();	
			
			$db->query("update ".$findb.".glmast set obal = obal + ".$total." where accountno = 801 and branch = '".$br2cr."'");
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
				
				$db->query("insert into ".$findb.".invhead (ddate,accountno,branch,sub,gldesc,transtype,ref_no,totvalue,tax,client,staff,cash,cheque,eftpos,ccard) values (:ddate,:accountno,:branch,:sub,:gldesc,:transtype,:ref_no,:totvalue,:tax,:client,:staff,:cash,:cheque,:eftpos,:ccard)");
				$db->bind(':ddate', $ddate);
				$db->bind(':accountno', $acc2dr);	
				$db->bind(':branch', $br2cr);
				$db->bind(':sub', $sub2dr);
				$db->bind(':gldesc', $descript1);
				$db->bind(':transtype', $payrec);	
				$db->bind(':ref_no', $reference);
				$db->bind(':totvalue', $amount);
				$db->bind(':tax', $tax);
				$db->bind(':client', $client);
				$db->bind(':staff', $uname);
				$db->bind(':cash', $cash);
				$db->bind(':cheque', $cheque);
				$db->bind(':eftpos', $eftpos);
				$db->bind(':ccard', $ccard);
				
				$db->execute();						
				
				// insert records into invtrans
			
				$db->query("insert into ".$findb.".invtrans (item,price,quantity,taxtype,taxpcent,tax,ref_no,value) values (:item,:price,:quantity,:taxtype,:taxpcent,:tax,:ref_no,:value)");
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
	}

	// if gst based on payment
	if ($gstinvpay == 'Payment') {

			if ($tax > 0) {
				if ($drgst == 'Y') {
					$useingst = 'N';
				} else {
					$useingst = 'N';
				}
				
				if (empty($br2cr)) {$br2cr = '1000';}
				if (empty($sub2cr)) {$sub2cr = 0;}
				if (empty($br2dr)) {$br2dr = '1000';}
				if (empty($sub2dr)) {$sub2dr = 0;}
				
				if ($acc2dr >= 751 && $acc2dr <= 800) {
					$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,gstrecon,descript1,taxpcent) values (:ddate,:accountno,:branch,:accno,:br,:debit,:credit,:reference,:gsttype,:gstrecon,:descript1,:taxpcent)");
					$db->bind(':ddate', $ddate);
					$db->bind(':accountno', 871);	// debit GST on Payment
					$db->bind(':branch', $br2dr);
					$db->bind(':accno', 870);
					$db->bind(':br', $br2cr);
					$db->bind(':debit', $tax);	// with the amount of GST
					$db->bind(':credit', 0);
					$db->bind(':reference', $reference);
					$db->bind(':gsttype', $taxtype);
					$db->bind(':gstrecon', $useingst);
					$db->bind(':descript1', $descript1);
					$db->bind(':taxpcent', $taxpcent);
					
					$db->execute();	
					
					$db->query("update ".$findb.".glmast set obal = obal + ".$tax." where accountno = 871 and branch = '".$br2dr."'");
					$db->execute();	
				
					$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,gstrecon,descript1,taxpcent) values (:ddate,:accountno,:branch,:accno,:br,:debit,:credit,:reference,:gsttype,:gstrecon,:descript1,:taxpcent)");
					$db->bind(':ddate', $ddate);
					$db->bind(':accountno', 870);	
					$db->bind(':branch', $br2dr);
					$db->bind(':accno', 871);
					$db->bind(':br', $br2cr);
					$db->bind(':debit', 0);	
					$db->bind(':credit', $tax); // with the amount of GST
					$db->bind(':reference', $reference);
					$db->bind(':gsttype', $taxtype);
					$db->bind(':gstrecon', $useingst);
					$db->bind(':descript1', $descript1);
					$db->bind(':taxpcent', $taxpcent);
					
					$db->execute();	
					
					$db->query("update ".$findb.".glmast set obal = obal - ".$tax." where accountno = 870 and branch = '".$br2cr."'");
					$db->execute();	
				
				}
			}
	}


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

				$db->query("insert into ".$findb.".invtrans (item,price,quantity,taxtype,taxpcent,tax,ref_no,value) values (:item,:price,:quantity,:taxtype,:taxpcent,:tax,:ref_no,:value)");
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
				
				if (empty($br2cr)) {$br2cr = '1000';}
				if (empty($sub2cr)) {$sub2cr = 0;}
				if (empty($br2dr)) {$br2dr = '1000';}
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

$db->query("update ".$findb.".".$table." set reference = substring(reference,1,3)");
$db->execute();						
$db->closeDB();

echo 'Y';
?>