<?php
session_start();
//ini_set('display_errors', true);

//$defbranch = $_SESSION['s_ubranch'];
$coyno = $_SESSION['s_coyid'];
date_default_timezone_set($_SESSION['s_timezone']);

$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

$table = 'ztmp'.$user_id.'_stkobal';
$serialtable = 'ztmp'.$user_id.'_serialnos';

$findb = $_SESSION['s_findb'];

$db->query("select osb from ".$findb.".numbers");
$row = $db->single();
extract($row);
$refno = $osb + 1;
$db->query("update ".$findb.".numbers set osb = :refno");
$db->bind(':refno', $refno);
$db->execute();

$ddate = date('Y-m-d');
$descript1 = $_REQUEST['descript'];

$reference = 'OSB'.$refno;

// add refno to serial table
$db->query("update ".$findb.".".$serialtable." set ref_no = '".$reference."'";
$r = mysql_query($q) or die(mysql_error());

$db->query("delete from ".$findb.".".$table." where quantity = 0";
$r = mysql_query($q) or die(mysql_error());

$db->query("select sum(quantity * avgcost) as totvalue from ".$findb.".".$table;
$r = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($r);
extract($row);
$totalamount = $totvalue;

$defbranch = 'AA';

	// insert invhead record
	$db->query("insert into ".$findb.".invhead (ddate,accountno,branch,sub,gldesc,transtype,ref_no,totvalue,staff) values (:ddate,:accountno,:branch,:sub,:gldesc,:transtype,:ref_no,:totvalue,:staff)");
	$db->bind(':ddate', $ddate);
	$db->bind(':accountno', 825);
	$db->bind(':branch', 'AA');
	$db->bind(':sub', 0);
	$db->bind(':gldesc', 'Opening Stock');
	$db->bind(':transtype', 'O');
	$db->bind(':ref)no', $reference);
	$db->bind(':totvalue', $totalamount);
	$db->bind(':staff', $sname);

	$db->execute();
		
	$db->query("select * from ".$findb.".".$table);
	$rows = $db->resultset();
	foreach ($rows as $row) {
		extract($row);
		
			$value = $quantity * $avgcost;
		// insert records into invtrans
		
			$db->query("insert into ".$findb.".invtrans (itemcode,item,price,quantity,unit,ref_no,value) values (:itemcode,:item,:price,:quantity,:unit,:ref_no,:value)");
			$db->bind(':itemcode', $itemcode);
			$db->bind(':item', $item);
			$db->bind(':price', $avgcost);
			$db->bind(':quantity', $quantity);
			$db->bind(':unit', $unit);
			$db->bind(':ref_no', $reference);
			$db->bind(':value', $value);
			
			$db->execute();
		
	}


	// insert GL records	
	
	// Create entries between closing stock and stock on hand
		
		$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,descript1) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:descript1)");
		$db->bind(':ddate', $ddate);
		$db->bind(':accountno', 825);		// debit stock on hand
		$db->bind(':branch', $defbranch);			
		$db->bind(':sub', 0);
		$db->bind(':accno', 187);
		$db->bind(':br', $defbranch);			
		$db->bind(':subbr', 0);			
		$$db->bind(':debit', $totalamount);		// with the amount excluding GST
		$db->bind(':credit', 0);
		$db->bind(':reference', $reference);					
		$db->bind(':descript1', $descript1);
			
		$db->execute();
		
		$db->query("update ".$findb.".glmast set obal = obal + ".$totalamount." where accountno = 825 and branch = '".$defbranch."' and sub = 0");
		$db->execute();		
		
		
		$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,descript1) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:descript1)");
		$db->bind(':ddate', $ddate);
		$db->bind(':accountno', 187);		// credit closing stock
		$db->bind(':branch', $defbranch);			
		$db->bind(':sub', 0);
		$db->bind(':accno', 825);
		$db->bind(':br', $defbranch);			
		$db->bind(':subbr', 0);			
		$$db->bind(':debit', 0);		
		$db->bind(':credit', $totalamount); // with the amount excluding GST
		$db->bind(':reference', $reference);					
		$db->bind(':descript1', $descript1);
			
		$db->execute();
		
		$db->query("update ".$findb.".glmast set obal = obal - ".$totalamount." where accountno = 187 and branch = '".$defbranch."' and sub = 0");
		$db->execute();		
		

	//***********************************************************************************************************************************
	// Create entries in stktrans for each stock recorded item
	//***********************************************************************************************************************************
	$db->query("select * from ".$findb.".".$table);
	$rows = $db->resultset();
	foreach ($rows as $row) {
		extract($row);
			$value = $quantity * $avgcost;
			// calculate new average cost
				if ($value > 0) {
					$db->query("select avgcost as ac,onhand-uncosted as tqty from ".$findb.".stkmast where itemcode = '".$itemcode."'");
					$row = $db->single();
					extract($row);
					$newtotval = ($ac*$tqty) + $value;
					$newtotqty = $tqty + $quantity;
					$newavgcost = $newtotval/$newtotqty;
					
					$db->query("update ".$findb.".stkmast set avgcost = ".$newavgcost.", onhand = onhand + ".$quantity." where itemcode = '".$itemcode."'");
					$db->execute();
				}
		
		
		$db->query("insert into ".$findb.".stktrans (groupid,catid,itemcode,item,locid,ddate,increase,ref_no,transtype,amount) values (:groupid,:catid,:itemcode,:item,:locid,:ddate,:increase,:ref_no,:transtype,:amount)");
		$db->bind(':groupid', $groupid);
		$db->bind(':catid', $catid);
		$db->bind(':itemcode', $itemcode);
		$db->bind(':item', $item);
		$db->bind(':locid', $location);
		$db->bind(':ddate', $ddate);
		$db->bind(':increase', $quantity);
		$db->bind(':ref_no', $reference);
		$db->bind(':transtype', 'O');
		$db->bind(':amount', $value);
		
		$db->execute();
		
	} // while

	//**************************************************************************************************************************************************************************
	// add serial numbers if applicable
	//**************************************************************************************************************************************************************************
		$db->query("select * from ".$findb.".".$serialtable);
		$rows = $db->resultset();
		$numrows = count($rows);
		if ($numrows > 0) {
			foreach ($rows as $row) {
				extract($row);
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
				
				if ($transtype == 'INV' || $transtype == 'C_S' || $transtype == 'REQ' || $transtype == 'CRN') {
					$db->query("update ".$findb.".stkserials set sold = '".$reference."' where serialno = '".$serialno."'");
					$db->execute();
				}
			}
		}



$db->query("delete from ".$findb.".".$table);
$db->execute();
$db->query("delete from ".$findb.".".$serialtable);
$db->execute();
	
$db->closeDB();

?>

