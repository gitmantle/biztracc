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

$findb = $_SESSION['s_findb'];
$defbranch = '1000';

$soref = $_REQUEST['ref'];
$_SESSION['s_salesorderref'] = $soref;
$sno_ref = $_SESSION['s_salesorderref'];

// if edit delete existing sales order
$act = $_REQUEST['act'];
if ($act == 'edit') {
	$db->query("delete from ".$findb.".invtrans where ref_no = '".$sno_ref."'");
	$db->execute();
	$db->query("delete from ".$findb.".invhead where ref_no = '".$sno_ref."'");
	$db->execute();
}


$tradetable = 'ztmp'.$user_id.'_trading';
date_default_timezone_set($_SESSION['s_timezone']);

		$db->query("select sum(tax) as totaltax, sum(value) as totalamount from ".$findb.".".$tradetable);
		$row = $db->single();
		extract($row);
		
		$f = explode('~',$_REQUEST['forex']);
		$fxcode = $f[0];
		$fxrate = $f[1];
		
		//convert to local currency
		$totalamount = $totalamount / $fxrate;
		$totaltax = $totaltax / $fxrate;
		$totalvalue = $totalamount + $totaltax;		

		$today = $_REQUEST['ddate'];
		$accountno = $_REQUEST['acc'];
		$branch = "1000";
		$sub = $_REQUEST['asb'];
		$gldesc = $_REQUEST['descript'];
		$soref = $_REQUEST['ref'];
		$totvalue = $totalvalue;
		$tax = $totaltax;
		$staff = $_REQUEST['staffmember'];
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


		// insert into invhead
		$db->query("insert into ".$findb.".invhead (ddate,accountno,branch,sub,gldesc,transtype,ref_no,xref,totvalue,tax,cash,cheque,eftpos,ccard,staff,postaladdress,deliveryaddress,client,signature,currency,rate) values (:ddate,:accountno,:branch,:sub,:gldesc,:transtype,:ref_no,:xref,:totvalue,:tax,:cash,:cheque,:eftpos,:ccard,:staff,:postaladdress,:deliveryaddress,:client,:signature,:currency,:rate)");
		$db->bind(':ddate', $today);
		$db->bind(':accountno', $accountno);
		$db->bind(':branch', $branch);
		$db->bind(':sub', $sub);
		$db->bind(':gldesc', $gldesc);
		$db->bind(':transtype', 'S_O');
		$db->bind(':ref_no', $soref);
		$db->bind(':xref', '');
		$db->bind(':totvalue', $totalamount);
		$db->bind(':tax', $totaltax);
		$db->bind(':cash', 0);
		$db->bind(':cheque', 0);
		$db->bind(':eftpos', 0);
		$db->bind(':ccard', 0);
		$db->bind(':staff', $staff);
		$db->bind(':postaladdress', $postaladdress);
		$db->bind(':deliveryaddress', $deliveryaddress);
		$db->bind(':client', $client);
		$db->bind(':signature', '');
		$db->bind(':currency', $fxcode);
		$db->bind(':rate', $fxrate);
		
		$db->execute();	
		
		$db->query("select itemcode,item,quantity,price,taxtype,taxpcent,tax,value,unit,discount,disctype,currency,rate from ".$findb.".".$tradetable);
		$rows = $db->resultset();

		foreach ($rows as $row) {
			extract($row);
			
			// insert records into invtrans
			
			$db->query("insert into ".$findb.".invtrans (itemcode,item,price,quantity,unit,taxtype,taxpcent,tax,ref_no,value,discount,disc_type,currency,rate) values (:itemcode,:item,:price,:quantity,:unit,:taxtype,:taxpcent,:tax,:ref_no,:value,:discount,:disc_type,:currency,:rate)");
			$db->bind(':itemcode', $itemcode);
			$db->bind(':item', $item);
			$db->bind(':price', $price);
			$db->bind(':quantity', $quantity);
			$db->bind(':unit', $unit);
			$db->bind(':taxtype', $taxtype);
			$db->bind(':taxpcent', $taxpcent);
			$db->bind(':tax', $tax);
			$db->bind(':ref_no', $soref);
			$db->bind(':value', $value);
			$db->bind(':discount', $discount);
			$db->bind(':disc_type', $disctype);
			$db->bind(':currency', $currency);
			$db->bind(':rate', $rate);
				
			$db->execute();	
			
		}
	

	//**************************************************************************************************************************************************************************
	// add transaction to audit trail
	//**************************************************************************************************************************************************************************
			$db->query("insert into ".$findb.".audit (ddate,descript1,reference,amount,tax,total,entrydate,entrytime,username) values (:ddate,:descript1,:reference,:amount,:tax,:total,:entrydate,:entrytime,:username)");
			$db->bind(':ddate', $today);
			$db->bind(':descript1', $gldesc);	
			$db->bind(':reference', $soref);		
			$db->bind(':amount', $totalamount);
			$db->bind(':tax', $totaltax);
			$db->bind(':total', $totalvalue);
			$db->bind(':entrydate', date("Y-m-d"));
			$db->bind(':entrytime', date("H:i:s"));
			$db->bind(':username', $staff);
					
			$db->execute();		

	$db->query("delete from ".$findb.".".$tradetable);
	$db->execute();	


$db->closeDB();

?>


