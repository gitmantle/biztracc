<?php
session_start();
$tref = strtoupper($_REQUEST['tref']);
$usersession = $_SESSION['usersession'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];
$userip = $row['userip'];

$findb = $_SESSION['s_findb'];
$cltdb = $_SESSION['s_cltdb'];

// test tref exists
$db->query('select uid,accountno,reconciled from '.$findb.'.trmain where reference = "'.$tref.'"');
$rows = $db->resultset();
$numrows = count($rows);
if ($numrows == 0) { 
	echo 'The reference '.$tref.' does not exist.';
	return;
} else {
	foreach ($rows as $row) {
		extract($row);	
		//test no reconciled in trmain against that ref
		if (($accountno >= 751 and $accountno <= 800) and $reconciled == 'Y') { 
			echo 'The reference '.$tref.' has been reconciled and may not be deleted.';
			return;
		} 		
	}
}
	
// test cash + cheque + eftpos + ccard + valretured = 0 in invhead
$db->query('select uid,cash,cheque,eftpos,ccard,valreturned from '.$findb.'.invhead where ref_no = "'.$tref.'"');
$row = $db->single();
if (!empty($row)) {
	extract($row);
	if ($cash + $cheque + $eftpos + $ccard + $valreturned > 0) {
		echo 'The reference '.$tref.' has allocations against it and may not be deleted.';
		return;
	}
} else {
	echo $tref.' was not a trading transaction. Please reverse it through Standard Transactions, if you are sure it will not affect any other elements of the accounts.';
	return;
}
	
// test all allocations to and from tref
$db->query('select uid from '.$findb.'.allocations where fromref = "'.$tref.'" or toref = "'.$tref.'"');
$rows = $db->resultset();
$numrows = count($rows);
if ($numrows != 0) { 
	echo 'The reference '.$tref.' has allocations to or from it and may not be deleted.';
	return;
}



$db->query('delete from '.$findb.'.invtrans where ref_no = "'.$tref.'"');
$db->execute();

$db->query('delete from '.$findb.'.invhead where ref_no = "'.$tref.'"');
$db->execute();

$db->query('delete from '.$findb.'.stktrans where ref_no = "'.$tref.'"');
$db->execute();

$db->query('delete from '.$findb.'.trmain where reference = "'.$tref.'"');
$db->execute();


	$hdate = date('Y-m-d');
	$ttime = strftime("%H:%M", time());
	$act = "Transaction ".$tref." deleted.";

	$db->query("insert into ".$findb.".audit (entrydate,entrytime,userip,username,reference,descript1) values (:entrydate,:entrytime,:userip,:username,:reference,:descript1)");
	$db->bind(':entrydate', $hdate);
	$db->bind(':entrytime', $ttime);
	$db->bind(':userip', $userip);
	$db->bind(':username', trim($sname));
	$db->bind(':reference', $tref);
	$db->bind(':descript1', $act);
	
	$db->execute();


			//*************************************************************************************************
			// Recalculate balances
			//*************************************************************************************************

			
			$coyid = $_SESSION['s_coyid'];
			
			// create date ranges
			date_default_timezone_set($_SESSION['s_timezone']);
			
			$curdat = date("Y-m-d");
			
			$lastcur = date("Y-m-d", strtotime($curdat));
			$lastd30 = date("Y-m-d", strtotime($lastcur." -1 month"));
			$lastd60 = date("Y-m-d", strtotime($lastd30." -1 month"));
			$lastd90 = date("Y-m-d", strtotime($lastd60." -1 month"));
			$lastd120 = date("Y-m-d", strtotime($lastd90." -1 month"));
			
			// recalcualte Debtor and Creditor aged balances
			
			$db->query("select uid,drno,crno,drsub,crsub from ".$cltdb.".client_company_xref where company_id = ".$coyid);
			$rows = $db->resultset();
			foreach ($rows as $row) {
				extract($row);
				$drac = $drno;
				$drsb = $drsub;
				$crac = $crno;
				$crsb = $crsub;
				$id = $uid;
			
				// recalcualte 120 day plus balances
				if ($drac > 0) {
					$db->query("select sum(debit-credit) as bal from ".$findb.".trmain where accountno = ".$drac." and sub = ".$drsb." and ddate <= '".$lastd120."'");
				} else {
					$db->query("select sum(debit-credit) as bal from ".$findb.".trmain where accountno = ".$crac." and sub = ".$crsb." and ddate <= '".$lastd120."'");
				}
				$row = $db->single();
				extract($row);
				if (is_null($bal)) {$bal = 0;}
				$db->query("update ".$cltdb.".client_company_xref set d120 = ".$bal." where uid = ".$id);
				$db->execute();
				
				// recalcualte 90 day balances
				if ($drac > 0) {
					$db->query("select sum(debit-credit) as bal from ".$findb.".trmain where accountno = ".$drac." and sub = ".$drsb." and ddate > '".$lastd120."' and ddate <= '".$lastd90."'");
				} else {
					$db->query("select sum(debit-credit) as bal from ".$findb.".trmain where accountno = ".$crac." and sub = ".$crsb." and ddate > '".$lastd120."' and ddate <= '".$lastd90."'");
				}
				$row = $db->single();
				extract($row);
				if (is_null($bal)) {$bal = 0;}
				$db->query("update ".$cltdb.".client_company_xref set d90 = ".$bal." where uid = ".$id);
				$db->execute();
			
				// recalcualte 60 day balances
				if ($drac > 0) {
					$db->query("select sum(debit-credit) as bal from ".$findb.".trmain where accountno = ".$drac." and sub = ".$drsb." and ddate > '".$lastd90."' and ddate <= '".$lastd60."'");
				} else {
					$db->query("select sum(debit-credit) as bal from ".$findb.".trmain where accountno = ".$crac." and sub = ".$crsb." and ddate > '".$lastd90."' and ddate <= '".$lastd60."'");
				}
				$row = $db->single();
				extract($row);
				if (is_null($bal)) {$bal = 0;}
				$db->query("update ".$cltdb.".client_company_xref set d60 = ".$bal." where uid = ".$id);
				$db->execute();
			
				// recalcualte 30 day balances
				if ($drac > 0) {
					$db->query("select sum(debit-credit) as bal from ".$findb.".trmain where accountno = ".$drac." and sub = ".$drsb." and ddate > '".$lastd60."' and ddate <= '".$lastd30."'");
				} else {
					$db->query("select sum(debit-credit) as bal from ".$findb.".trmain where accountno = ".$crac." and sub = ".$crsb." and ddate > '".$lastd60."' and ddate <= '".$lastd30."'");
				}
				$row = $db->single();
				extract($row);
				if (is_null($bal)) {$bal = 0;}
				$db->query("update ".$cltdb.".client_company_xref set d30 = ".$bal." where uid = ".$id);
				$db->execute();
			
				// recalcualte current balances
				if ($drac > 0) {
					$db->query("select sum(debit-credit) as bal from ".$findb.".trmain where accountno = ".$drac." and sub = ".$drsb." and ddate > '".$lastd30."'");
				} else {
					$db->query("select sum(debit-credit) as bal from ".$findb.".trmain where accountno = ".$crac." and sub = ".$crsb." and ddate > '".$lastd30."'");
				}
				$row = $db->single();
				extract($row);
				if (is_null($bal)) {$bal = 0;}
				$db->query("update ".$cltdb.".client_company_xref set current = ".$bal." where uid = ".$id);
				$db->execute();
			
			}
			
			//ensure member field in client_company_xref is up to date with member.lastname
			$db->query("update ".$cltdb.".client_company_xref set member = (select concat(members.lastname,' ',members.firstname) from ".$cltdb.".members where members.member_id = client_company_xref.client_id)");
			$db->execute();
			
			
			// recalcualte General Ledger balances
			
			$db->query("select uid,accountno,branch,sub from ".$findb.".glmast");
			$rows = $db->resultset();
			foreach ($rows as $row) {
				extract($row);
				$ac = $accountno;
				$br = $branch;
				$sb = $sub;
				$id = $uid;
				$bal = 0;
				
				// recalcualte balances
				$db->query("select sum(debit-credit) as bal from ".$findb.".trmain where accountno = ".$ac." and sub = ".$sb." and branch = '".$br."'");
				$row = $db->single();
				extract($row);
				if (is_null($bal)) {$bal = 0;}
				$db->query("update ".$findb.".glmast set obal = ".$bal." where uid = ".$id);
				$db->execute();
				
			}
			
			// recalculate stock balances from stktrans
			$db->query("update ".$findb.".stkmast set onhand = 0");
			$db->execute();
			
			$db->query("select itemcode, sum(increase - decrease) as stockonhand from ".$findb.".stktrans group by itemcode");
			$rows = $db->resultset();
			foreach ($rows as $row) {
				extract($row);
				$soh = $stockonhand;
				$ic = $itemcode;
				
				// update stkmast
				$db->query("update ".$findb.".stkmast set onhand = ".$soh." where itemcode = '".$ic."'");
				$db->execute();
			}
			
			// recalculate average costs for stock items
			$db->query("select itemcode, onhand from ".$findb.".stkmast where stock = 'Stock'");
			$rows = $db->resultset();
			foreach ($rows as $row) {
				extract($row);
				$icd = $itemcode;
				$db->query("select sum(quantity) as onhand, sum( value ) as cost from ".$findb.".invtrans where itemcode = '".$icd."' and (ref_no like 'GRN%' or ref_no like 'C_P%')");
				$row = $db->single();
				extract($row);
				$ocst = $cost;
				$oh = $onhand;

				if ($oh > 0) {
					$newavcst = $ocst / $oh;
				} else {
					$newavcst = $ocst;
				}
				$newavcst = number_format($newavcst,2);
				$db->query("update ".$findb.".stkmast set avgcost = ".$newavcst." where itemcode = '".$icd."'");
				$db->execute();
			
			}
			
			echo $act;

$db->closeDB();

?>
