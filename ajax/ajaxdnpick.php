<?php
session_start();
$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$tradetable = 'ztmp'.$user_id.'_dn';
$serialtable = 'ztmp'.$user_id.'_serialnos';

$lineref = $_REQUEST['ref'];
$r = explode('-',$lineref);
$num = $r[0];
$sno = 'S_O'.$num;
$qno = 'QOT'.$num;
$dnno = 'D_N'.$lineref;
$loc = $_REQUEST['loc'];

$findb = $_SESSION['s_findb'];

// check there are serial numbers against items that require them


// calculate value, tax of just the picked items for this insert
$db->query("select sum((price * picked) - (picked / quantity * discount)) as val, sum(((price * picked) - (picked / quantity * discount)) * taxpcent / 100) as tx from ".$findb.".".$tradetable);
$row = $db->single();
extract($row);

// get header details from invhead for the sales order
$db->query("select * from ".$findb.".invhead where ref_no = '".$sno."'");
$row = $db->single();
extract($row);

$dte = date('Y-m-d');

// insert into invhead
$db->query("insert into ".$findb.".invhead (ddate,accountno,branch,sub,gldesc,transtype,ref_no,xref,totvalue,tax,cash,cheque,eftpos,ccard,staff,postaladdress,deliveryaddress,client,signature,currency,rate) values (:ddate,:accountno,:branch,:sub,:gldesc,:transtype,:ref_no,:xref,:totvalue,:tax,:cash,:cheque,:eftpos,:ccard,:staff,:postaladdress,:deliveryaddress,:client,:signature,:currency,:rate)");
$db->bind(':ddate', $dte);
$db->bind(':accountno', $accountno);
$db->bind(':branch', $branch);
$db->bind(':sub', $sub);
$db->bind(':gldesc', $gldesc);
$db->bind(':transtype', 'D_N');
$db->bind(':ref_no', $dnno);
$db->bind(':xref', $ref_no);
$db->bind(':totvalue', $val);
$db->bind(':tax', $tx);
$db->bind(':cash', 0);
$db->bind(':cheque', 0);
$db->bind(':eftpos', 0);
$db->bind(':ccard', 0);
$db->bind(':staff', $staff);
$db->bind(':postaladdress', $postaladdress);
$db->bind(':deliveryaddress', $deliveryaddress);
$db->bind(':client', $client);
$db->bind(':signature', '');
$db->bind(':currency', $currency);
$db->bind(':rate', $rate);

$db->execute();	

$db->query("select groupid,catid,itemcode,item,picked,price,discount,disctype,taxtype,taxpcent,tax,unit,trackserial,quantity,currency,rate from ".$findb.".".$tradetable." where picked > 0");
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	
	$ctax = (($price * $picked) - ($picked / $quantity * $discount))  * $taxpcent / 100;
	$cvalue = ($price * $picked) - ($picked / $quantity * $discount);
	
	//echo "insert into ".$findb.".invtrans (itemcode,item,price,discount,quantity,unit,taxtype,taxpcent,tax,ref_no,value,disc_type) values ('".$itemcode."','".$item."',".$price.",".$discount.",".$quantity.",'".$unit."','".$taxtype."',".$taxpcent.",".$ctax.",'".$dnno."',".$cvalue.",'".$disctype."')";
	

// insert records into invtrans
	$db->query("insert into ".$findb.".invtrans (itemcode,item,price,discount,quantity,unit,taxtype,taxpcent,tax,ref_no,value,disc_type,currency,rate) values (:itemcode,:item,:price,:discount,:quantity,:unit,:taxtype,:taxpcent,:tax,:ref_no,:value,:disc_type,:currency,:rate)");
	$db->bind(':itemcode', $itemcode);
	$db->bind(':item', $item);
	$db->bind(':price', $price);
	$db->bind(':discount', $discount);
	$db->bind(':quantity', $picked);
	$db->bind(':unit', $unit);
	$db->bind(':taxtype', $taxtype);
	$db->bind(':taxpcent', $taxpcent);
	$db->bind(':tax', $ctax);  
	$db->bind(':ref_no', $dnno);
	$db->bind(':value', $cvalue);
	$db->bind(':disc_type', $disctype);
	$db->bind(':currency', $currency);
	$db->bind(':rate', $rate);
		
	$db->execute();	

	// update sent quantities in invtrans
	$db->query("update ".$findb.".invtrans set returns = returns + ".$picked." where itemcode = '".$itemcode."' and ref_no = '".$sno."'");
	$db->execute();	
	
	// update stkserials if appropriate
	if ($trackserial == 'Yes') {
		$db->query("select serialno from ".$findb.".".$serialtable);
		$rows = $db->resultset();
		foreach ($rows as $row) {
			extract($row);
			$db->query("update ".$findb.".stkserials set sold = '".$dnno."' where sold = '' and serialno = '".$serialno."'");
			$db->execute();
		}
	}
	
	$db->query("select groupid,catid from ".$findb.".stkmast where itemcode = '".$itemcode."'");
	$rowv = $db->single();
	extract($rowv);
	
	// decrease stock on hand quantity
	$db->query("update ".$findb.".stkmast set onhand = onhand - ".$picked." where itemcode = '".$itemcode."'");
	$db->execute();		
	
	// insert records into stktrans
	$db->query("insert into ".$findb.".stktrans (groupid,catid,itemcode,item,locid,ddate,increase,decrease,ref_no,transtype,amount,currency,rate) values (:groupid,:catid,:itemcode,:item,:locid,:ddate,:increase,:decrease,:ref_no,:transtype,:amount,:currency,:rate)");
	$db->bind(':groupid', $groupid);
	$db->bind(':catid', $catid);
	$db->bind(':itemcode', $itemcode);
	$db->bind(':item', $item);
	$db->bind(':locid', $loc);
	$db->bind(':ddate', $dte);
	$db->bind(':increase', 0);
	$db->bind(':decrease', $picked);	
	$db->bind(':ref_no', $dnno);		
	$db->bind(':transtype', 'D_N');
	$db->bind(':amount', $cvalue);
	$db->bind(':currency', $currency);
	$db->bind(':rate', $rate);
	
	$db->execute();
	
}

// check if sales order complete

$db->query("select ref_no from ".$findb.".invtrans where ref_no = '".$sno."' and returns != quantity");
$rows = $db->resultset();
$numrows = $db->rowCount();

if ($numrows == 0) {
	$db->query("update ".$findb.".invhead set xref = 'Complete' where ref_no = '".$sno."'");
	$db->execute();
}

$db->closeDB();
?>

