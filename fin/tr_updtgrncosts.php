<?php
session_start();
$usersession = $_SESSION['usersession'];

$ref_no = $_SESSION['s_uncostgrn'];
$coyno = $_SESSION['s_coyid'];				  
$findb = $_SESSION['s_findb'];
$cltdb = $_SESSION['s_cltdb'];

include_once("../includes/DBClass.php");
$db_trd = new DBClass();

$db_trd->query("select * from sessions where session = :vusersession");
$db_trd->bind(':vusersession', $usersession);
$row = $db_trd->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$chargefile = 'ztmp'.$user_id.'_charges';

// ensure all invtrans lines have same currency
$db_trd->query("select distinct currency from ".$findb.".invtrans where ref_no = '".$ref_no."'");
$rows = $db_trd->resultset();
$numrows = $db_trd->rowcount();

$ok = 'Y';

if ($numrows > 1) {
	echo '<script>';
	echo 'alert("Please ensure all lines in this GRN have the same currency");';
	echo '</script>';
	$ok = 'N';
}

$db_trd->query("select tempcost from ".$findb.".invtrans where ref_no = '".$ref_no."' and tempcost = 0");
$row = $db_trd->resultset();
$numrows = $db_trd->rowcount();

if ($ok == 'Y') {
	if ($numrows > 0) {
		echo '<script>';
		echo 'alert("Please cost all rows in this GRN");';
		echo '</script>';
	} else {
	
		
		$db_trd->query("select uid,itemcode,quantity,taxpcent,tempcost,charges,grnlineno,currency,rate from ".$findb.".invtrans where ref_no = '".$ref_no."'");
		$rows = $db_trd->resultset();
		
		foreach($rows as $row) {
			$tx = $row['taxpcent'];
			$amt = $row['tempcost'];
			$id = $row['uid'];
			$chg = $row['charges'];
			$qt = $row['quantity'];
			$itemcode = $row['itemcode'];
			$tx = round($amt*$tx/100,2);
			$pr = round($amt/$qt,2);
			$lineno = $row['grnlineno'];
			$fxcode = $row['currency'];
			$fxrate = $row['rate'];
			
			$db_trd->query("update ".$findb.".invtrans set price = ".$pr.", tax = ".$tx.", value = tempcost where uid = ".$id);	
			$db_trd->execute();
				
			$db_trd->query("update ".$findb.".stktrans set amount = ".($amt + $chg)." where itemcode = '".$itemcode."' and ref_no = '".$ref_no."'");
			$db_trd->execute();
			
			// reduce uncosted in stkmast
			$db_trd->query("update ".$findb.".stkmast set uncosted = uncosted - ".$qt." where itemcode = '".$itemcode."'");
			$db_trd->execute();
			
			// recalculate average cost
			$db_trd->query("select avgcost,onhand-uncosted as tqty from ".$findb.".stkmast where itemcode = '".$itemcode."'");
			$row = $db_trd->single();
			extract($row);
			$newtotval = ($avgcost*$tqty) + $amt + $chg;
			$newtotqty = $tqty ;
			$newavgcost = $newtotval/$newtotqty;
			$db_trd->query("update ".$findb.".stkmast set avgcost = ".$newavgcost." where itemcode = '".$itemcode."'");
			$db_trd->execute();
			
			
			
		}
	
		
		// update invhead
		$db_trd->query("select sum(value) as val, sum(tax) as tx from ".$findb.".invtrans where ref_no = '".$ref_no."'");
		$row = $db_trd->single();
		$tamt = $row['val'];
		$gstamt = $row['tx'];
		$amtinc = $tamt + $gstamt;
		
		$db_trd->query("update ".$findb.".invhead set totvalue = ".$tamt.", tax = ".$gstamt." where ref_no = '".$ref_no."'");
		$db_trd->execute();
		
		// get gsttype
		$db_trd->query("select gsttype from ".$findb.".globals");
		$row = $db_trd->single();
		extract($row);
		
		// update GRN in trmain.
		
		$db_trd->query("select invtrans.uid,invhead.accountno,invhead.sub from ".$findb.".invtrans,".$findb.".invhead,".$findb.".stkmast where (".$findb.".invhead.ref_no = ".$findb.".invtrans.ref_no) and (".$findb.".stkmast.itemcode = ".$findb.".invtrans.itemcode) and (".$findb.".invhead.ref_no = '".$ref_no."')"); 	
		$row = $db_trd->single();
		extract($row);
		$supplierac = $accountno;
		$suppliersb = $sub;	
		
		$db_trd->query("update ".$findb.".trmain set debit = debit + ".$tamt." where reference = '".$ref_no."' and accountno = 825");
		$db_trd->execute();
		
		$db_trd->query("update ".$findb.".trmain set credit = credit + ".$tamt." where reference = '".$ref_no."' and accountno = 187");
		$db_trd->execute();
		
		$db_trd->query("update ".$findb.".trmain set credit = credit + ".$amtinc." where reference = '".$ref_no."' and accountno = ".$supplierac." and sub = ".$suppliersb);
		$db_trd->execute();
		
		$db_trd->query("update ".$findb.".trmain set credit = credit + ".$amtinc." where reference = '".$ref_no."' and accountno = 851");
		$db_trd->execute();
		
		$db_trd->query("select invtrans.uid,invtrans.grnlineno,invtrans.currency,invtrans.rate,stkmast.purchacc,stkmast.purchsub from ".$findb.".invtrans,".$findb.".invhead,".$findb.".stkmast where (".$findb.".invhead.ref_no = ".$findb.".invtrans.ref_no) and (".$findb.".stkmast.itemcode = ".$findb.".invtrans.itemcode) and (".$findb.".invhead.ref_no = '".$ref_no."')");	
		
		$rows = $db_trd->resultset();
		foreach ($rows as $row) {
			extract($row);
			$rid = $uid;
			$lineno = $grnlineno;
			
			$db_trd->query("update ".$findb.".trmain set debit = debit + ".$tamt." where reference = '".$ref_no."' and grnlineno = ".$lineno." and accountno = ".$purchacc." and sub = ".$purchsub);
			$db_trd->execute();
			
			if ($gsttype == 'Invoice') {
				$db_trd->query("update ".$findb.".trmain set debit = debit + ".$gstamt." where reference = '".$ref_no."' and accountno = 870 and grnlineno = ".$lineno);
				$db_trd->execute();
			} else {
				$db_trd->query("update ".$findb.".trmain set debit = debit + ".$gstamt." where reference = '".$ref_no."' and accountno = 871 and grnlineno = ".$lineno);
				$db_trd->execute();
			}		
		}
		
		// update balance in client_company_xref
		/*
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
		
		if ($aged == 'Current') {
	*/		
			$db_trd->query("update ".$cltdb.".client_company_xref set current = current - ".$amtinc." where company_id = ".$coyno." and crno = ".$supplierac." and crsub = ".$suppliersb);
	/*		
		}
		if ($aged == 'D30') {
			$db_trd->query("update ".$cltdb.".client_company_xref set d30 = d30 - ".$amtinc." where company_id = ".$coyno." and crno = ".$supplierac." and crsub = ".$suppliersb);
		}
		if ($aged == 'D60') {
			$db_trd->query("update ".$cltdb.".client_company_xref set d60 = d60 - ".$amtinc." where company_id = ".$coyno." and crno = ".$supplierac." and crsub = ".$suppliersb);
		}
		if ($aged == 'D90') {
			$db_trd->query("update ".$cltdb.".client_company_xref set d90 = d90 - ".$amtinc." where company_id = ".$coyno." and crno = ".$supplierac." and crsub = ".$suppliersb);
		}
		if ($aged == 'D120') {
			$db_trd->query("update ".$cltdb.".client_company_xref set d120 = d120 - ".$amtinc." where company_id = ".$coyno." and crno = ".$supplierac." and crsub = ".$suppliersb);
		}
	*/	
		$db_trd->execute();	
		
		// add transactions to credit suppliers of charges if applicable
		
		$db_trd->query("select gsttype as gstinvpay, roundto as rnd from ".$findb.".globals");
		$row = $db_trd->single();
		extract($row);
		
		$db_trd->query("select * from ".$findb.".".$chargefile);
		$rows = $db_trd->resultset();
		$numrows = $db_trd->rowcount();
		if ($numrows > 0) {
			foreach ($rows as $row) {
				extract($row);
				$fxcode = $currency;
				$fxrate = $rate;
				$db_trd->query("insert into ".$findb.".charges (supplier,account,sub,charge,ref_no,descript,taxpcent,cosacno,cossub,currency,exgrate) values (:supplier,:account,:sub,:charge,:ref_no,:descript,:taxpcent,:cosacno,:cossub,:currency,:exgrate)");
				$db_trd->bind(':supplier', $supplier);
				$db_trd->bind(':account', $acno);
				$db_trd->bind(':sub', $sbno);
				$db_trd->bind(':charge', $charge);
				$db_trd->bind(':ref_no', $ref_no);
				$db_trd->bind(':descript', $descript);
				$db_trd->bind(':taxpcent', $taxpcent);
				$db_trd->bind(':cosacno', $cosacno);
				$db_trd->bind(':cossub', $cossub);
				$db_trd->bind(':currency', $fxcode);
				$db_trd->bind(':exgrate', $fxrate);
				
				$db_trd->execute();
				
				
				$ddate = date("Y-m-d"); 
				$acc2dr = $cosacno;
				$br2dr = '1000';
				$sub2dr = $cossub;
				$acc2cr = $acno;
				$br2cr = '1000';
				$sub2cr = $cossub;
				$lcharge = $charge / $rate;
				$amount = $lcharge + ($lcharge * $taxpcent /100);
				$tax = $lcharge * $taxpcent /100;
				$amtinc = $amount;
				$reference = $ref_no;
				$descript1 = $descript; 
				$supplierac = $acno;
				$suppliersb = $sbno;
				
				
				// debit cost of sales 
				$db_trd->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent,currency,rate) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent,:currency,:rate)");
				$db_trd->bind(':ddate', $ddate);
				$db_trd->bind(':accountno', $acc2dr);	// debit the expense or purchase
				$db_trd->bind(':branch', $br2dr);
				$db_trd->bind(':sub', $sub2dr);
				$db_trd->bind(':accno', $acc2cr);
				$db_trd->bind(':br', $br2cr);
				$db_trd->bind(':subbr', $sub2cr);
				$db_trd->bind(':debit', $lcharge);	// with the amount excluding GST
				$db_trd->bind(':credit', 0);
				$db_trd->bind(':reference', $reference);
				$db_trd->bind(':gsttype', $taxtype);
				$db_trd->bind(':descript1', $descript1);
				$db_trd->bind(':taxpcent', $taxpcent);
				$db_trd->bind(':currency', $fxcode);
				$db_trd->bind(':rate', $fxrate);
				
				$db_trd->execute();				
				
				$db_trd->query("update ".$findb.".glmast set obal = obal + ".$lcharge." where accountno = ".$acc2dr." and branch = '".$br2dr."' and sub = ".$sub2dr);
				$db_trd->execute();				
				
				if ($tax > 0) {
		
					$useingst = 'Y';
				
					if ($gstinvpay == 'Invoice') {
						$db_trd->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,gstrecon,descript1,taxpcent) values (:ddate,:accountno,:branch,:accno,:br,:debit,:credit,:reference,:gsttype,:gstrecon,:descript1,:taxpcent)");
						$db_trd->bind(':ddate', $ddate);
						$db_trd->bind(':accountno', 870);	//debit GST Payable
						$db_trd->bind(':branch', $br2dr);
						$db_trd->bind(':accno', $acc2cr);
						$db_trd->bind(':br', $br2cr);
						$db_trd->bind(':debit', $tax);	// with the amount of GST
						$db_trd->bind(':credit', 0);
						$db_trd->bind(':reference', $reference);
						$db_trd->bind(':gsttype', $taxtype);
						$db_trd->bind(':gstrecon', $useingst);
						$db_trd->bind(':descript1', $descript1);
						$db_trd->bind(':taxpcent', $taxpcent);
							
						$db_trd->execute();					
		
						$db_trd->query("update ".$findb.".glmast set obal = obal + ".$tax." where accountno = 870 and branch = '".$br2dr."'");
						$db_trd->execute();					
					
					} else {
						
						$db_trd->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,gstrecon,descript1,taxpcent) values (:ddate,:accountno,:branch,:accno,:br,:debit,:credit,:reference,:gsttype,:gstrecon,:descript1,:taxpcent)");
						$db_trd->bind(':ddate', $ddate);
						$db_trd->bind(':accountno', 871);	//debit GST Payable
						$db_trd->bind(':branch', $br2dr);
						$db_trd->bind(':accno', $acc2cr);
						$db_trd->bind(':br', $br2cr);
						$db_trd->bind(':debit', $tax);	// with the amount of GST
						$db_trd->bind(':credit', 0);
						$db_trd->bind(':reference', $reference);
						$db_trd->bind(':gsttype', $taxtype);
						$db_trd->bind(':gstrecon', $useingst);
						$db_trd->bind(':descript1', $descript1);
						$db_trd->bind(':taxpcent', $taxpcent);
							
						$db_trd->execute();					
						
						$db_trd->query("update ".$findb.".glmast set obal = obal + ".$tax." where accountno = 871 and branch = '".$br2dr."'");
						$db_trd->execute();					
						
					}	
				}
				$db_trd->query("update ".$cltdb.".client_company_xref set current = current - ".$amtinc." where company_id = ".$coyno." and crno = ".$supplierac." and crsub = ".$suppliersb);
				$db_trd->execute();	
				
				// credit creditor account
					
				$db_trd->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent,currency,rate) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent,:currency,:rate)");
				$db_trd->bind(':ddate', $ddate);
				$db_trd->bind(':accountno', $acc2cr);	// credit the paying account
				$db_trd->bind(':branch', $br2cr);
				$db_trd->bind(':sub', $sub2cr);
				$db_trd->bind(':accno', $acc2dr);
				$db_trd->bind(':br', $br2dr);
				$db_trd->bind(':subbr', $sub2dr);
				$db_trd->bind(':debit', 0);	
				$db_trd->bind(':credit', $amount);	// with the amount including GST
				$db_trd->bind(':reference', $reference);
				$db_trd->bind(':gsttype', $taxtype);
				$db_trd->bind(':descript1', $descript1);
				$db_trd->bind(':taxpcent', $taxpcent);
				$db_trd->bind(':currency', $fxcode);
				$db_trd->bind(':rate', $fxrate);
				
				$db_trd->execute();	
				
				$db_trd->query("update ".$findb.".glmast set obal = obal - ".$amount." where accountno = ".$acc2cr." and branch = '".$br2cr."' and sub = ".$sub2cr);
				
				$db_trd->execute();	
				
				// credit creditor control account
				$db_trd->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,descript1,taxpcent,currency,rate) values (:ddate,:accountno,:branch,:accno,:br,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent,:currency,:rate)");
				$db_trd->bind(':ddate', $ddate);
				$db_trd->bind(':accountno', 851);	// debit creditors control
				$db_trd->bind(':branch', $br2cr);
				$db_trd->bind(':accno', $acc2dr);
				$db_trd->bind(':br', $br2dr);
				$db_trd->bind(':debit', 0);	
				$db_trd->bind(':credit', $amount);	// with the amount including GST
				$db_trd->bind(':reference', $reference);
				$db_trd->bind(':gsttype', $taxtype);
				$db_trd->bind(':descript1', $descript1);
				$db_trd->bind(':taxpcent', $taxpcent);
				$db_trd->bind(':currency', $fxcode);
				$db_trd->bind(':rate', $fxrate);
				
				$db_trd->execute();	
					
				$db_trd->query("update ".$findb.".glmast set obal = obal - ".$amount." where accountno = 851 and branch = '".$br2cr."' and sub = 0");
				$db_trd->execute();	
					
				// Inter branch Transactions
				if ($br2dr != $br2cr) {
				
					// credit inter branch transfer account
					$db_trd->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
					$db_trd->bind(':ddate', $ddate);
					$db_trd->bind(':accountno', 997);	
					$db_trd->bind(':branch', $br2dr);
					$db_trd->bind(':sub', 0);
					$db_trd->bind(':accno', $acc2cr);
					$db_trd->bind(':br', $br2cr);
					$db_trd->bind(':subbr', $sub2cr);
					$db_trd->bind(':debit', 0);	
					$db_trd->bind(':credit', $amount);	// with the amount including GST
					$db_trd->bind(':reference', $reference);
					$db_trd->bind(':gsttype', $taxtype);
					$db_trd->bind(':descript1', $descript1);
					$db_trd->bind(':taxpcent', $taxpcent);
								
					$db_trd->execute();						
					
					$db_trd->query("update ".$findb.".glmast set obal = obal - ".$amount." where accountno = 997 and branch = '".$br2dr."'");
					$db_trd->execute();						
						
					// debit inter branch transfer account
					$db_trd->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
					$db_trd->bind(':ddate', $ddate);
					$db_trd->bind(':accountno', 997);	
					$db_trd->bind(':branch', $br2cr);
					$db_trd->bind(':sub', $sub2cr);
					$db_trd->bind(':accno', $acc2dr);
					$db_trd->bind(':br', $br2dr);
					$db_trd->bind(':subbr', $sub2dr);
					$db_trd->bind(':debit', $amount);	// with the amount including GST
					$db_trd->bind(':credit', 0);	
					$db_trd->bind(':reference', $reference);
					$db_trd->bind(':gsttype', $taxtype);
					$db_trd->bind(':descript1', $descript1);
					$db_trd->bind(':taxpcent', $taxpcent);
								
					$db_trd->execute();						
					
					$db_trd->query("update ".$findb.".glmast set obal = obal + ".$amount." where accountno = 997 and branch = '".$br2cr."'");
					$db_trd->execute();						
						
				} // if posting between different branches				  
			  
		  }
		}
	
	}
}
$db_trd->closeDB();

?>
	<script>
	window.open("","uncostgrn").jQuery("#uncostlist").trigger("reloadGrid");
	</script>

