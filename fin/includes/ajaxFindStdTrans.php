<?php
	session_start();
	$usersession = $_SESSION['usersession'];
	
	$ref = strtoupper($_REQUEST['ref']);
	
	$findb = $_SESSION['s_findb'];
	
	$today = date('Y-m-d');
	$now = date('H:i:s');
	
	include_once("../../includes/DBClass.php");
	$db = new DBClass();
	
	$db->query("select * from sessions where session = :vusersession");
	$db->bind(':vusersession', $usersession);
	$row = $db->single();
	$unm = $row['uname'];
	
	// see if stock items involved

	$db->query("select * from ".$findb.".stktrans where ref_no = '".$ref."'");
	$rows = $db->resultset();
	$numrows = $db->rowCount();
	
	if ($numrows > 0) {
		$ret = 'stock';
	} else {
	
		// create reversed transaction in trmain
	
		$db->query("select * from ".$findb.".trmain where reference = '".$ref."'");
		$rows = $db->resultset();
		$numrows = $db->rowCount();
		
		if ($numrows > 0) {
			$db->query("select rev from ".$findb.".numbers");
			$row = $db->single();
			extract($row);
			$refno = $rev + 1;
			$db->query("update ".$findb.".numbers set rev = :refno");
			$db->bind(':refno', $refno);
			$db->execute();
			
			$refno = 'REV'.$refno;
			
			foreach ($rows as $row) {
				extract($row);
				$ac1 = $accountno;
				$br1 = $branch;
				$sb1 = $sub;
				$ac2 = $accno;
				$br2 = $br;
				$sb2 = $subbr;
				$dr = $debit;
				$cr = $credit;
				$dsc = $descript1;
				$txpc = $taxpcent;
				$gstyp = $gsttype;
				$gpurch = $grosspurchases;
				$gsales = $grosssales;
				$cur = $currency;
				$rt = $rate;
				
				
				$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,descript1,taxpcent,gsttype,grosspurchases,grosssales,currency,rate,your_ref) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:descript1,:taxpcent,:gsttype,:grosspurchases,:grosssales,:currency,:rate,:your_ref)");
				$db->bind(':ddate', $today);
				$db->bind(':accountno', $ac1);
				$db->bind(':branch', $br1);
				$db->bind(':sub', $sb1);
				$db->bind(':accno', $ac2);
				$db->bind(':br', $br2);
				$db->bind(':subbr', $sb2);
				$db->bind(':debit', $cr);
				$db->bind(':credit', $dr);
				$db->bind(':reference', $ref);
				$db->bind(':descript1', 'Reversal - '.$dsc);
				$db->bind(':taxpcent', $txpc);
				$db->bind(':gsttype', $gstyp);
				$db->bind(':grosspurchases', $gsales);
				$db->bind(':grosssales', $gpurch);
				$db->bind(':currency', $cur);
				$db->bind(':rate', $rt);
				$db->bind(':your_ref', $refno);
	
				$db->execute();
	
			}
			
			// create entry in audit
			
			$db->query("insert into ".$findb.".audit (entrydate,entrytime,descript1,reference,username) values (:entrydate,:entrytime,:descript1,:reference,:username)");
			$db->bind(':entrydate', $today);
			$db->bind(':entrytime', $now);
			$db->bind(':descript1', 'Reversal of '.$ref);
			$db->bind(':reference', $refno);
			$db->bind(':username', $unm);
			
			$db->execute();
			
			// create reversal in invhead if applicable
				
			$db->query("select * from ".$findb.".invhead where ref_no = '".$ref."'");
			$rows = $db->resultset();
			$numrows = $db->rowCount();
			
			if ($numrows > 0) {
				foreach ($rows as $row) {
					extract($row);
					$ac = $accountno;
					$br = $branch;
					$sb = $sub;
					$dsc = $gldesc;
					$typ = $transtype;
					$tot = $totvalue;
					$tx = $tax;
					$clt = $client;
					$cur = $currency;
					$rt = $rate;
					
					$db->query("insert into ".$findb.".invhead (accountno,branch,sub,gldesc,transtype,ref_no,ddate,totvalue,tax,xref,client,currency,rate,your_ref,staff) values (:accountno,:branch,:sub,:gldesc,:transtype,:ref_no,:ddate,:totvalue,:tax,:xref,:client,:currency,:rate,:your_ref,:staff)");
					$db->bind(':accountno', $ac);
					$db->bind(':branch', $br);
					$db->bind(':sub', $sb);
					$db->bind(':gldesc', 'Reversal - '.$dsc);
					$db->bind(':transtype', $typ);
					$db->bind(':ref_no', $ref);
					$db->bind(':ddate', $today);
					$db->bind(':totvalue', ($tot * -1));
					$db->bind(':tax', ($tx * -1));
					$db->bind(':xref', $refno);
					$db->bind(':client', $clt);
					$db->bind(':currency', $cur);
					$db->bind(':rate', $rt);
					$db->bind(':your_ref', $refno);
					$db->bind(':staff', $unm);
					
					$db->execute();
				}
				
			}
			
			// create reversal in invtrans if applicable
				
			$db->query("select * from ".$findb.".invtrans where ref_no = '".$ref."'");
			$rows = $db->resultset();
			$numrows = $db->rowCount();
			
			if ($numrows > 0) {
				foreach ($rows as $row) {
					extract($row);
					$itc = $itemcode;
					$qt = $quantity;
					$pr = $price;
					$txt = $taxtype;
					$txp = $taxpcent;
					$tx = $tax;
					$tx = $tax;
					$itm = $item;
					$un = $unit;
					$val = $value;
					$cur = $currency;
					$rt = $rate;
					$ret = $returns;
					
					$db->query("insert into ".$findb.".invtrans (itemcode,quantity,price,taxtype,taxpcent,tax,ref_no,item,unit,value,rate,currency,returns,your_ref) values (:itemcode,:quantity,:price,:taxtype,:taxpcent,:tax,:ref_no,:item,:unit,:value,:rate,:currency,:returns,:your_ref)");
					$db->bind(':itemcode', $itc);
					$db->bind(':quantity', ($qt * -1));
					$db->bind(':price', ($pr * -1));
					$db->bind(':taxtype', $txt);
					$db->bind(':taxpcent', $txp);
					$db->bind(':tax', ($tx * -1));
					$db->bind(':ref_no', $ref);
					$db->bind(':item', 'Reversal - '.$itm);
					$db->bind(':unit', $un);
					$db->bind(':value', ($val * -1));
					$db->bind(':rate', $rt);
					$db->bind(':currency', $cur);
					$db->bind(':returns', $qt);
					$db->bind(':your_ref', $refno);
					
					$db->execute();
				}
				
			}		
			
			//*************************************************
			// TODO - add entry in audit trail and recalc balances
			//**************************************************
			
			
			
			$ret = 'Done';
		} else {
			
			$ret = 'N';
		}
	}
	echo $ret;

	$db->closeDB();

?>