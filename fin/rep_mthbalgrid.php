<?php
session_start();

date_default_timezone_set($_SESSION['s_timezone']);

$todate = $_REQUEST['emonth'];

$branchcode = substr($_REQUEST['branch'], 0, strlen($_REQUEST['branch'])-1); 	
if ($branchcode == '*' || $branchcode == '') {
	$branchcode = '*';
	$brs = 'all branches';
} else {
		$brcodes = "";
		$br = explode(",",$branchcode);
		foreach ($br as $value) {
			$brcodes .= "'".$value."~";
		}
		$branchcode = substr($brcodes,0,strlen($brcodes)-1);
		$branchcode = str_replace('~',chr(39).',',$branchcode).chr(39);
		$brs = $branchcode;
}

$brcons = $_REQUEST['brcons'];
$subcons = $_REQUEST['subcons'];

$_SESSION['s_brcons'] = $brcons;
$_SESSION['s_subcons'] = $subcons;
$_SESSION['s_todate'] = $todate;
$_SESSION['s_sob'] = 'N';
$_SESSION['s_tbheading'] = ' 12 Months to '.$todate.' for '.$brs;


//  month headings
for ($i = 0; $i <= 11; $i++) {
    $months[] = date("Y-m", strtotime( date( $todate.'-01' )." -$i months"));
}
$y = date('Y', strtotime($months[11]."-01"));
$month1 = date('M', strtotime($months[11]."-01")).' '.$y;
$mth1 = $months[11];
$y = date('Y', strtotime($months[10]."-01"));
$month1 = date('M', strtotime($months[10]."-01")).' '.$y;
$mth2 = $months[10];
$y = date('Y', strtotime($months[9]."-01"));
$month1 = date('M', strtotime($months[9]."-01")).' '.$y;
$mth3 = $months[9];
$y = date('Y', strtotime($months[8]."-01"));
$month1 = date('M', strtotime($months[8]."-01")).' '.$y;
$mth4 = $months[8];
$y = date('Y', strtotime($months[7]."-01"));
$month1 = date('M', strtotime($months[7]."-01")).' '.$y;
$mth5 = $months[7];
$y = date('Y', strtotime($months[6]."-01"));
$month1 = date('M', strtotime($months[6]."-01")).' '.$y;
$mth6 = $months[6];
$y = date('Y', strtotime($months[5]."-01"));
$month1 = date('M', strtotime($months[5]."-01")).' '.$y;
$mth7 = $months[5];
$y = date('Y', strtotime($months[4]."-01"));
$month1 = date('M', strtotime($months[4]."-01")).' '.$y;
$mth8 = $months[4];
$y = date('Y', strtotime($months[3]."-01"));
$month1 = date('M', strtotime($months[3]."-01")).' '.$y;
$mth9 = $months[3];
$y = date('Y', strtotime($months[2]."-01"));
$month1 = date('M', strtotime($months[2]."-01")).' '.$y;
$mth10 = $months[2];
$y = date('Y', strtotime($months[1]."-01"));
$month1 = date('M', strtotime($months[1]."-01")).' '.$y;
$mth11 = $months[1];
$y = date('Y', strtotime($months[0]."-01"));
$month1 = date('M', strtotime($months[0]."-01")).' '.$y;
$mth12 = $months[0];

$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$mbfile = 'ztmp'.$user_id.'_mthbal';

$findb = $_SESSION['s_findb'];

$db->query("drop table if exists ".$findb.".".$mbfile);
$db->execute();

$db->query("create table ".$findb.".".$mbfile." (AccountNumber int, Branch char(4) default '', Sub int default 0, AccountName varchar(45), m1 decimal(16,2) NOT NULL default 0, m2 decimal(16,2) NOT NULL default 0, m3 decimal(16,2) NOT NULL default 0, m4 decimal(16,2) NOT NULL default 0, m5 decimal(16,2) NOT NULL default 0, m6 decimal(16,2) NOT NULL default 0, m7 decimal(16,2) NOT NULL default 0, m8 decimal(16,2) NOT NULL default 0, m9 decimal(16,2) NOT NULL default 0, m10 decimal(16,2) NOT NULL default 0, m11 decimal(16,2) NOT NULL default 0, m12 decimal(16,2) NOT NULL default 0)  engine myisam");
$db->execute();

$heading = '';

// populate mthbal table
	
	$heading .= '12 Months to '.$todate;
	// all branches
	if ($branchcode == '*') {
		// all branches consolidated
		$heading .= ' - All Branches';
		if ($brcons == 'y') {
			// subaccounts consolidated, all branches consolidated, between dates
			if ($subcons == 'y') {
				$db->query("insert into ".$findb.".".$mbfile." SELECT glmast.accountno AS ano, '' AS bno, 0 AS sno, glmast.account,0,0,0,0,0,0,0,0,0,0,0,0 from ".$findb.".glmast where glmast.accountno <1000 GROUP BY glmast.accountno");
				$db->execute();
				
				$db->query("select AccountNumber,Branch,Sub from ".$findb.".".$mbfile);
				$rows = $db->resultset();
				foreach ($rows as $row) {
					extract($row);
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and substr(ddate,1,7) = '".$mth1."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m1 = ".$bal." where AccountNumber = ".$AccountNumber);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and substr(ddate,1,7) = '".$mth2."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m2 = ".$bal." where AccountNumber = ".$AccountNumber);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and substr(ddate,1,7) = '".$mth3."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m3 = ".$bal." where AccountNumber = ".$AccountNumber);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and substr(ddate,1,7) = '".$mth4."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m4 = ".$bal." where AccountNumber = ".$AccountNumber);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and substr(ddate,1,7) = '".$mth5."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m5 = ".$bal." where AccountNumber = ".$AccountNumber);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and substr(ddate,1,7) = '".$mth6."'");
					$rowt = $db->single();
					extract($rowt);
					$bal = $dr - $cr;
					$db->query("update ".$findb.".".$mbfile." set m6 = ".$bal." where AccountNumber = ".$AccountNumber);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and substr(ddate,1,7) = '".$mth7."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m7 = ".$bal." where AccountNumber = ".$AccountNumber);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and substr(ddate,1,7) = '".$mth8."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m8 = ".$bal." where AccountNumber = ".$AccountNumber);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and substr(ddate,1,7) = '".$mth9."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m9 = ".$bal." where AccountNumber = ".$AccountNumber);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and substr(ddate,1,7) = '".$mth10."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m10 = ".$bal." where AccountNumber = ".$AccountNumber);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and substr(ddate,1,7) = '".$mth11."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m11 = ".$bal." where AccountNumber = ".$AccountNumber);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and substr(ddate,1,7) = '".$mth12."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m12 = ".$bal." where AccountNumber = ".$AccountNumber);
					$db->execute();
				
				}
			} else {
			// subaccounts detailed, all branches consolidated, between dates
				$db->query("insert into ".$findb.".".$mbfile." SELECT glmast.accountno AS ano, '' AS bno, sub AS sno, glmast.account,0,0,0,0,0,0,0,0,0,0,0,0 from ".$findb.".glmast where glmast.accountno <1000 GROUP BY glmast.accountno,sno");
				$db->execute();
				
				$db->query("select AccountNumber,Branch,Sub from ".$findb.".".$mbfile);
				$rows = $db->resultset();
				foreach ($rows as $row) {
					extract($row);
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and sub = ".$Sub." and substr(ddate,1,7) = '".$mth1."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m1 = ".$bal." where AccountNumber = ".$AccountNumber." and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and sub = ".$Sub." and substr(ddate,1,7) = '".$mth2."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m2 = ".$bal." where AccountNumber = ".$AccountNumber." and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and sub = ".$Sub." and substr(ddate,1,7) = '".$mth3."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m3 = ".$bal." where AccountNumber = ".$AccountNumber." and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and sub = ".$Sub." and substr(ddate,1,7) = '".$mth4."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m4 = ".$bal." where AccountNumber = ".$AccountNumber." and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and sub = ".$Sub." and substr(ddate,1,7) = '".$mth5."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m5 = ".$bal." where AccountNumber = ".$AccountNumber." and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and sub = ".$Sub." and substr(ddate,1,7) = '".$mth6."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m6 = ".$bal." where AccountNumber = ".$AccountNumber." and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and sub = ".$Sub." and substr(ddate,1,7) = '".$mth7."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m7 = ".$bal." where AccountNumber = ".$AccountNumber." and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and sub = ".$Sub." and substr(ddate,1,7) = '".$mth8."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m8 = ".$bal." where AccountNumber = ".$AccountNumber." and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and sub = ".$Sub." and substr(ddate,1,7) = '".$mth9."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m9 = ".$bal." where AccountNumber = ".$AccountNumber." and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and sub = ".$Sub." and substr(ddate,1,7) = '".$mth10."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m10 = ".$bal." where AccountNumber = ".$AccountNumber." and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and sub = ".$Sub." and substr(ddate,1,7) = '".$mth11."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m11 = ".$bal." where AccountNumber = ".$AccountNumber." and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and sub = ".$Sub." and substr(ddate,1,7) = '".$mth12."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m12 = ".$bal." where AccountNumber = ".$AccountNumber." and sub = ".$Sub);
					$db->execute();
				
				}
			}
		} else {
		// all branches detailed
			// subaccounts consolidated,all branches detailed, between dates
			if ($subcons == 'y') {
				$db->query("insert into ".$findb.".".$mbfile." SELECT glmast.accountno AS ano, branch AS bno, 0 AS sno, glmast.account,0,0,0,0,0,0,0,0,0,0,0,0 from ".$findb.".glmast where glmast.accountno <1000 GROUP BY glmast.accountno,bno");
				$db->execute();
				
				$db->query("select AccountNumber,Branch,Sub from ".$findb.".".$mbfile);
				$rows = $db->resultset();
				foreach ($rows as $row) {
					extract($row);
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and substr(ddate,1,7) = '".$mth1."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m1 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."'");
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and substr(ddate,1,7) = '".$mth2."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m2 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."'");
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and substr(ddate,1,7) = '".$mth3."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m3 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."'");
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and substr(ddate,1,7) = '".$mth4."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m4 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."'");
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and substr(ddate,1,7) = '".$mth5."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m5 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."'");
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and substr(ddate,1,7) = '".$mth6."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m6 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."'");
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and substr(ddate,1,7) = '".$mth7."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m7 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."'");
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and substr(ddate,1,7) = '".$mth8."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m8 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."'");
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and substr(ddate,1,7) = '".$mth9."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m9 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."'");
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and substr(ddate,1,7) = '".$mth10."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m10 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."'");
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and substr(ddate,1,7) = '".$mth11."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m11 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."'");
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and substr(ddate,1,7) = '".$mth12."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m12 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."'");
					$db->execute();
					
				}
			} else {
			// subaccounts detailed, all branches detailed, between dates
				$db->query("insert into ".$findb.".".$mbfile." SELECT glmast.accountno AS ano, branch AS bno, sub AS sno, glmast.account,0,0,0,0,0,0,0,0,0,0,0,0 from ".$findb.".glmast where glmast.accountno <1000 GROUP BY glmast.accountno,bno,sno");
				$db->execute();
				
				$db->query("select AccountNumber,Branch,Sub from ".$findb.".".$mbfile);
				$rows = $db->resultset();
				foreach ($rows as $row) {
					extract($row);
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub." and substr(ddate,1,7) = '".$mth1."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m1 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub." and substr(ddate,1,7) = '".$mth2."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m2 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub." and substr(ddate,1,7) = '".$mth3."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m3 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub." and substr(ddate,1,7) = '".$mth4."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m4 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub." and substr(ddate,1,7) = '".$mth5."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m5 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub." and substr(ddate,1,7) = '".$mth6."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m6 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub." and substr(ddate,1,7) = '".$mth7."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m7 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub." and substr(ddate,1,7) = '".$mth8."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m8 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub." and substr(ddate,1,7) = '".$mth9."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m9 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub." and substr(ddate,1,7) = '".$mth10."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m10 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub." and substr(ddate,1,7) = '".$mth11."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m11 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub." and substr(ddate,1,7) = '".$mth12."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m12 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub);
					$db->execute();
					
				}
			}
		}
	} else {
	// one or many branch
		// one or many branch consolidated
		if ($brcons == 'y') {
			// subaccounts consolidated, chosen branches consolidated between dates
			if ($subcons == 'y') {
				$db->query("insert into ".$findb.".".$mbfile." SELECT glmast.accountno AS ano, '' AS bno, 0 AS sno, glmast.account,0,0,0,0,0,0,0,0,0,0,0,0 from ".$findb.".glmast where glmast.accountno <1000 and branch in (".$branchcode.") GROUP BY glmast.accountno");
				$db->execute();
				
				$db->query("select AccountNumber,Branch,Sub from ".$findb.".".$mbfile);
				$rows = $db->resultset();
				foreach ($rows as $row) {
					extract($row);
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and ddate >= '".$fromdate."' and substr(ddate,1,7) = '".$mth1."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m1 = ".$bal." where AccountNumber = ".$AccountNumber);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and ddate >= '".$fromdate."' and substr(ddate,1,7) = '".$mth2."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m2 = ".$bal." where AccountNumber = ".$AccountNumber);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and ddate >= '".$fromdate."' and substr(ddate,1,7) = '".$mth3."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m3 = ".$bal." where AccountNumber = ".$AccountNumber);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and ddate >= '".$fromdate."' and substr(ddate,1,7) = '".$mth4."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m4 = ".$bal." where AccountNumber = ".$AccountNumber);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and ddate >= '".$fromdate."' and substr(ddate,1,7) = '".$mth5."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m5 = ".$bal." where AccountNumber = ".$AccountNumber);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and ddate >= '".$fromdate."' and substr(ddate,1,7) = '".$mth6."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m6 = ".$bal." where AccountNumber = ".$AccountNumber);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and ddate >= '".$fromdate."' and substr(ddate,1,7) = '".$mth7."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m7 = ".$bal." where AccountNumber = ".$AccountNumber);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and ddate >= '".$fromdate."' and substr(ddate,1,7) = '".$mth8."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m8 = ".$bal." where AccountNumber = ".$AccountNumber);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and ddate >= '".$fromdate."' and substr(ddate,1,7) = '".$mth9."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m9 = ".$bal." where AccountNumber = ".$AccountNumber);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and ddate >= '".$fromdate."' and substr(ddate,1,7) = '".$mth10."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m10 = ".$bal." where AccountNumber = ".$AccountNumber);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and ddate >= '".$fromdate."' and substr(ddate,1,7) = '".$mth11."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m11 = ".$bal." where AccountNumber = ".$AccountNumber);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and ddate >= '".$fromdate."' and substr(ddate,1,7) = '".$mth12."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m12 = ".$bal." where AccountNumber = ".$AccountNumber);
					$db->execute();
					
				}
			} else {
			// subaccounts detailed, chosen branches consolidated between dates
				$db->query("insert into ".$findb.".".$mbfile." SELECT glmast.accountno AS ano, '' AS bno, sub AS sno, glmast.account,0,0,0,0,0,0,0,0,0,0,0,0 from ".$findb.".glmast where glmast.accountno <1000 and branch in (".$branchcode.") GROUP BY glmast.accountno,sno");
				$db->execute();
				
				$db->query("select AccountNumber,Branch,Sub from ".$findb.".".$mbfile);
				$rows = $db->resultset();
				foreach ($rows as $row) {
					extract($row);
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and sub = ".$Sub." and substr(ddate,1,7) = '".$mth1."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m1 = ".$bal." where AccountNumber = ".$AccountNumber." and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and sub = ".$Sub." and substr(ddate,1,7) = '".$mth2."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m2 = ".$bal." where AccountNumber = ".$AccountNumber." and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and sub = ".$Sub." and substr(ddate,1,7) = '".$mth3."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m3 = ".$bal." where AccountNumber = ".$AccountNumber." and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and sub = ".$Sub." and substr(ddate,1,7) = '".$mth4."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m4 = ".$bal." where AccountNumber = ".$AccountNumber." and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and sub = ".$Sub." and substr(ddate,1,7) = '".$mth5."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m5 = ".$bal." where AccountNumber = ".$AccountNumber." and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and sub = ".$Sub." and substr(ddate,1,7) = '".$mth6."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m6 = ".$bal." where AccountNumber = ".$AccountNumber." and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and sub = ".$Sub." and substr(ddate,1,7) = '".$mth7."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m7 = ".$bal." where AccountNumber = ".$AccountNumber." and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and sub = ".$Sub." and substr(ddate,1,7) = '".$mth8."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m8 = ".$bal." where AccountNumber = ".$AccountNumber." and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and sub = ".$Sub." and substr(ddate,1,7) = '".$mth9."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m9 = ".$bal." where AccountNumber = ".$AccountNumber." and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and sub = ".$Sub." and substr(ddate,1,7) = '".$mth10."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m10 = ".$bal." where AccountNumber = ".$AccountNumber." and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and sub = ".$Sub." and substr(ddate,1,7) = '".$mth11."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m11 = ".$bal." where AccountNumber = ".$AccountNumber." and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and sub = ".$Sub." and substr(ddate,1,7) = '".$mth12."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m12 = ".$bal." where AccountNumber = ".$AccountNumber." and sub = ".$Sub);
					$db->execute();
					
				}
			}
		} else {
		// one or many branch detailed
			// subaccounts consolidated, chosen branches detailed between dates
			if ($subcons == 'y') {
				$db->query("insert into ".$findb.".".$mbfile." SELECT glmast.accountno AS ano, branch AS bno, 0 AS sno, glmast.account,0,0,0,0,0,0,0,0,0,0,0,0 from ".$findb.".glmast where glmast.accountno <1000 and branch in (".$branchcode.") GROUP BY glmast.accountno,bno");
				$db->execute();
				
				$db->query("select AccountNumber,Branch,Sub from ".$findb.".".$mbfile);
				$rows = $db->resultset();
				foreach ($rows as $row) {
					extract($row);
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and substr(ddate,1,7) = '".$mth1."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m1 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."'");
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and substr(ddate,1,7) = '".$mth2."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m2 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."'");
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and substr(ddate,1,7) = '".$mth3."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m3 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."'");
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and substr(ddate,1,7) = '".$mth4."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m4 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."'");
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and substr(ddate,1,7) = '".$mth5."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m5 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."'");
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and substr(ddate,1,7) = '".$mth6."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m6 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."'");
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and substr(ddate,1,7) = '".$mth7."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m7 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."'");
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and substr(ddate,1,7) = '".$mth8."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m8 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."'");
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and substr(ddate,1,7) = '".$mth9."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m9 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."'");
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and substr(ddate,1,7) = '".$mth10."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m10 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."'");
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and substr(ddate,1,7) = '".$mth11."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m11 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."'");
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and substr(ddate,1,7) = '".$mth12."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m12 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."'");
					$db->execute();
					
				}
			} else {
			// subaccounts detailed, chosen branches detailed between dates
				$db->query("insert into ".$findb.".".$mbfile." SELECT glmast.accountno AS ano, branch AS bno, sub AS sno, glmast.account,0,0,0,0,0,0,0,0,0,0,0,0 from ".$findb.".glmast where glmast.accountno <1000 and branch in (".$branchcode.") GROUP BY glmast.accountno,bno,sno");
				$db->execute();
				
				$db->query("select AccountNumber,Branch,Sub from ".$findb.".".$mbfile);
				$rows = $db->resultset();
				foreach ($rows as $row) {
					extract($row);
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub." and substr(ddate,1,7) = '".$mth1."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m1 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub." and substr(ddate,1,7) = '".$mth2."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m2 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub." and substr(ddate,1,7) = '".$mth3."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m3 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub." and substr(ddate,1,7) = '".$mth4."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m4 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub." and substr(ddate,1,7) = '".$mth5."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m5 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub." and substr(ddate,1,7) = '".$mth6."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m6 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub." and substr(ddate,1,7) = '".$mth7."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m7 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub." and substr(ddate,1,7) = '".$mth8."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m8 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub." and substr(ddate,1,7) = '".$mth9."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m9 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub." and substr(ddate,1,7) = '".$mth10."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m10 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub." and substr(ddate,1,7) = '".$mth11."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m11 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub);
					$db->execute();
					
					$db->query("select ifnull(sum(debit-credit),0) as bal from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub." and substr(ddate,1,7) = '".$mth12."'");
					$rowt = $db->single();
					extract($rowt);
					$db->query("update ".$findb.".".$mbfile." set m12 = ".$bal." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub);
					$db->execute();
					
				}
			}
		}
	}


// Add uid
$db->query("alter table ".$findb.".".$mbfile." add `uid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY");
$db->execute();

// Add branch names
$db->query("alter table ".$findb.".".$mbfile." add Branchname varchar(25) default ''");
$db->execute();

if ($brcons != 'y' && $subcons != 'y') {
	$db->query("select uid,branch from ".$findb.".".$mbfile);
	$rows = $db->resultset();
	foreach ($rows as $row) {
		extract($row);
		$id = $uid;
		$br = $branch;
		$db->query("select branchname from ".$findb.".branch where branch = '".$br."'");
		$row = $db->single();
		extract($row);
		$bname = $branchname;
		$db->query("update ".$findb.".".$mbfile." set Branchname = '".$bname."' where uid = ".$id);
		$db->execute();
	}
}

$_SESSION['s_finheading'] = $heading;

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

$db->closeDB();


?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title>Monthly Balances</title>

<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<link rel="stylesheet" type="text/css" media="screen" href="../includes/jquery/themes/ui.multiselect.css"/> 

<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>


<script type="text/javascript" src="js/fin.js"></script>


<script type="text/javascript">

window.name = 'mthbalgrid';

function mb2xl() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('rep_mb2excel.php','tbxl','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

</script>

</head>
<body>
    <table align="center">
        <tr>
	        <td><?php include "getmthbals.php"; ?></td>
        </tr>
	</table>		

</body>
</html>