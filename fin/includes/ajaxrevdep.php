<?php
session_start();
$coyidno = $_SESSION['s_coyid'];

$findb = $_SESSION['s_findb'];

$asid = $_REQUEST['asid'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select bedate,yrdate from ".$findb.".globals");
$row = $db->single();
extract($row);
	
$db->query("select * from ".$findb.".fixassets where cost - totdep > 0 and blocked != 'Y' and bought < '".$depdate."' and depndate < '".$depdate."'");
$rows = $db->resultset(0);
foreach ($rows as $row) {
	extract($row);
					
	$recno = $uid;
	$asbranch = $branch;
	$asacno = $accountno;
					
	// calculate the whole number of days to calculate depreciation on
	if ($bought < $bedate) {
		$startdate = $bedate;
	} else {
		$startdate = $bought;
	}

	$depdays = (strtotime($depdate) - strtotime($startdate)) / 86400;
	// calculate depreciation on Diminishing (Book Value) or Fixed (Cost) for number of days
	if ($way == 'D') {
		$depreciation = ($cost - $totdep) * $rate/100 * $depdays/365;
	} else {
		$depreciation = $cost * $rate/100 * $depdays/365;
	}
	// insert transactions into trmain
	$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:branch,:accno,:br,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
	$db->bind(':ddate', $depdate);
	$db->bind(':accountno', 250);		
	$db->bind(':branch', $asbranch);
	$db->bind(':accno', 702);
	$db->bind(':br', $asbranch);			
	$db->bind(':debit', $depreciation);		
	$db->bind(':credit', 0);
	$db->bind(':reference', $asacno);					
	$db->bind(':gsttype', 'N-T');
	$db->bind(':descript1', 'Depreciate '.$asset);
	$db->bind(':taxpcent', 0);		
					
	$db->execute();
	
	$db->query("update ".$findb.".glmast set obal = obal + ".$depreciation." where accountno = 250 and branch = '".$asbranch."'");
	$db->execute();		
					
	$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,accno,br,debit,credit,reference,gsttype,descript1,taxpcent) values (:ddate,:accountno,:branch,:accno,:br,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent)");
	$db->bind(':ddate', $depdate);
	$db->bind(':accountno', 702);		
	$db->bind(':branch', $asbranch);
	$db->bind(':accno', 250);
	$db->bind(':br', $asbranch);			
	$db->bind(':debit', 0);		
	$db->bind(':credit', $depreciation);
	$db->bind(':reference', $asacno);					
	$db->bind(':gsttype', 'N-T');
	$db->bind(':descript1', 'Depreciate '.$asset);
	$db->bind(':taxpcent', 0);		
					
	$db->execute();

	$db->query("update ".$findb.".glmast set obal = obal - ".$depreciation." where accountno = 702 and branch = '".$asbranch."'");
	$db->execute();		
	
					
	// update totdep, anndep and depndate in fixassets
	$db->query("update ".$findb.".fixassets set totdep = totdep + ".$depreciation.",anndep = anndep + ".$depreciation.",depndate = '".$depdate."' where uid = ".$recno);
	$db->execute();	
	
}
			
$db->closeDB();

?>
