<?php
session_start();
date_default_timezone_set($_SESSION['s_timezone']);

$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$bsfile = 'ztmp'.$user_id.'_bs';

$findb = $_SESSION['s_findb'];

$db->query("drop table if exists ".$findb.".".$bsfile);
$db->execute();

$db->query("create table ".$findb.".".$bsfile." (uid integer(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, Header char(30) default '',Type char(1), AccountNumber int default 0, Branch char(4) default '', Sub int default 0, AccountName varchar(45) default '', Col1 decimal(16,2) default 0, Col2 decimal(16,2) default 0, Col3 decimal(16,2) default 0, Total decimal(16,2) default 0) engine myisam"); 
$db->execute();

$coyname = $_SESSION['s_coyname'];
$todate = $_REQUEST['edate'];
$branchcode = substr($_REQUEST['branch'], 0, strlen($_REQUEST['branch'])-1); 	
if ($branchcode == '*' || $branchcode == '') {
	$branchcode = '*';
} else {
		$brcodes = "";
		$br = explode(",",$branchcode);
		foreach ($br as $value) {
			$brcodes .= "'".$value."~";
		}
		$branchcode = substr($brcodes,0,strlen($brcodes)-1);
		$branchcode = str_replace('~',chr(39).',',$branchcode).chr(39);
}

$heading = "Balance Sheet as at ".$todate;
$_SESSION['s_bsheading'] = $heading;
$fromdate = $_SESSION['s_fromdate'];
$_SESSION['s_todate'] = $todate;
$_SESSION['s_brcons'] = 'n';
$_SESSION['s_subcons'] = 'n';

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");


// populate bs table

// fixed assets
$db->query("insert into ".$findb.".".$bsfile." (Header,Type) values ('Fixed Assets','H')");
$db->execute();
$db->query("select hcode,heading as asheading from ".$findb.".assetheadings");
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	switch ($hcode) {
	case 'LD':
		if ($branchcode == '*') {
			$db->query("select sum(debit - credit) as totcost from ".$findb.".trmain where accountno >= 10000000 and accountno < 11000000 and ddate <= '".$todate."'");
			$rowcost = $db->single();
			extract($rowcost);
			if (is_null($totcost)) {$totcost = 0;}
			$db->query("select sum(credit-debit) as totdepn from ".$findb.".trmain where accountno = 702 and reference >= '10000000' and reference < '11000000' and ddate <= '".$todate."'");
			$rowdep = $db->single();
			extract($rowdep);
			if (is_null($totdepn)) {$totdepn = 0;}
			$bv = $totcost - $totdepn;
			$db->query("insert into ".$findb.".".$bsfile." (Header,Type,AccountName,Col1,Col2,Col3) values ('','D','".$asheading."',".$totcost.",".$totdepn.",".$bv.")");
			$db->execute();
		} else {
			$db->query("select sum(debit - credit) as totcost from ".$findb.".trmain where accountno >= 10000000 and accountno < 11000000 and branch in (".$branchcode.") and ddate <= '".$todate."'");
			$rowcost = $db->single();
			extract($rowcost);
			if (is_null($totcost)) {$totcost = 0;}
			$$db->query("select sum(credit-debit) as totdepn from ".$findb.".trmain where accountno = 702 and reference >= '10000000' and reference < '11000000' and branch in (".$branchcode.") and ddate <= '".$todate."'");
			$rowdep = $db->single();
			extract($rowdep);
			if (is_null($totdepn)) {$totdepn = 0;}
			$bv = $totcost - $totdepn;
			$db->query("insert into ".$findb.".".$bsfile." (Header,Type,AccountName,Col1,Col2,Col3) values ('','D','".$asheading."',".$totcost.",".$totdepn.",".$bv.")");
			$db->execute();
		}
		break;
	case 'BL':
		if ($branchcode == '*') {
			$db->query("select sum(debit - credit) as totcost from ".$findb.".trmain where accountno >= 11000000 and accountno < 12000000 and ddate <= '".$todate."'");
			$rowcost = $db->single();
			extract($rowcost);
			if (is_null($totcost)) {$totcost = 0;}
			$db->query("select sum(credit-debit) as totdepn from ".$findb.".trmain where accountno = 702 and reference >= '11000000' and reference < '12000000' and ddate <= '".$todate."'");
			$rowdep = $db->single();
			extract($rowdep);
			if (is_null($totdepn)) {$totdepn = 0;}
			$bv = $totcost - $totdepn;
			$db->query("insert into ".$findb.".".$bsfile." (Header,Type,AccountName,Col1,Col2,Col3) values ('','D','".$asheading."',".$totcost.",".$totdepn.",".$bv.")");
			$db->execute();
		} else {
			$db->query("select sum(debit - credit) as totcost from ".$findb.".trmain where accountno >= 11000000 and accountno < 12000000 and branch in (".$branchcode.") and ddate <= '".$todate."'");
			$rowcost = $db->single();
			extract($rowcost);
			if (is_null($totcost)) {$totcost = 0;}
			$db->query("select sum(credit-debit) as totdepn from ".$findb.".trmain where accountno = 702 and reference >= '11000000' and reference < '12000000' and branch in (".$branchcode.") and ddate <= '".$todate."'");
			$rowdep = $db->single();
			extract($rowdep);
			if (is_null($totdepn)) {$totdepn = 0;}
			$bv = $totcost - $totdepn;
			$db->query("insert into ".$findb.".".$bsfile." (Header,Type,AccountName,Col1,Col2,Col3) values ('','D','".$asheading."',".$totcost.",".$totdepn.",".$bv.")");
			$db->execute();
		}
		break;		
	case 'MV':
		if ($branchcode == '*') {
			$db->query("select sum(debit - credit) as totcost from ".$findb.".trmain where accountno >= 12000000 and accountno < 13000000 and ddate <= '".$todate."'");
			$rowcost = $db->single();
			extract($rowcost);
			if (is_null($totcost)) {$totcost = 0;}
			$db->query("select sum(credit-debit) as totdepn from ".$findb.".trmain where accountno = 702 and reference >= '12000000' and reference < '13000000' and ddate <= '".$todate."'");
			$rowdep = $db->single();
			extract($rowdep);
			if (is_null($totdepn)) {$totdepn = 0;}
			$bv = $totcost - $totdepn;
			$db->query("insert into ".$findb.".".$bsfile." (Header,Type,AccountName,Col1,Col2,Col3) values ('','D','".$asheading."',".$totcost.",".$totdepn.",".$bv.")");
			$db->execute();
		} else {
			$db->query("select sum(debit - credit) as totcost from ".$findb.".trmain where accountno >= 12000000 and accountno < 13000000 and branch in (".$branchcode.") and ddate <= '".$todate."'");
			$rowcost = $db->single();
			extract($rowcost);
			if (is_null($totcost)) {$totcost = 0;}
			$db->query("select sum(credit-debit) as totdepn from ".$findb.".trmain where accountno = 702 and reference >= '12000000' and reference < '13000000' and branch in (".$branchcode.") and ddate <= '".$todate."'");
			$rowdep = $db->single();
			extract($rowdep);
			if (is_null($totdepn)) {$totdepn = 0;}
			$bv = $totcost - $totdepn;
			$db->query("insert into ".$findb.".".$bsfile." (Header,Type,AccountName,Col1,Col2,Col3) values ('','D','".$asheading."',".$totcost.",".$totdepn.",".$bv.")");
			$db->execute();
		}
		break;			
	case 'PE':
		if ($branchcode == '*') {
			$db->query("select sum(debit - credit) as totcost from ".$findb.".trmain where accountno >= 13000000 and accountno < 14000000 and ddate <= '".$todate."'");
			$rowcost = $db->single();
			extract($rowcost);
			if (is_null($totcost)) {$totcost = 0;}
			$db->query("select sum(credit-debit) as totdepn from ".$findb.".trmain where accountno = 702 and reference >= '13000000' and reference < '14000000' and ddate <= '".$todate."'");
			$rowdep = $db->single();
			extract($rowdep);
			if (is_null($totdepn)) {$totdepn = 0;}
			$bv = $totcost - $totdepn;
			$db->query("insert into ".$findb.".".$bsfile." (Header,Type,AccountName,Col1,Col2,Col3) values ('','D','".$asheading."',".$totcost.",".$totdepn.",".$bv.")");
			$db->execute();
		} else {
			$db->query("select sum(debit - credit) as totcost from ".$findb.".trmain where accountno >= 13000000 and accountno < 14000000 and branch in (".$branchcode.") and ddate <= '".$todate."'");
			$rowcost = $db->single();
			extract($rowcost);
			if (is_null($totcost)) {$totcost = 0;}
			$db->query("select sum(credit-debit) as totdepn from ".$findb.".trmain where accountno = 702 and reference >= '13000000' and reference < '14000000' and branch in (".$branchcode.") and ddate <= '".$todate."'");
			$rowdep = $db->single();
			extract($rowdep);
			if (is_null($totdepn)) {$totdepn = 0;}
			$bv = $totcost - $totdepn;
			$db->query("insert into ".$findb.".".$bsfile." (Header,Type,AccountName,Col1,Col2,Col3) values ('','D','".$asheading."',".$totcost.",".$totdepn.",".$bv.")");
			$db->execute();
		}
		break;			
	case 'FF':
		if ($branchcode == '*') {
			$db->query("select sum(debit - credit) as totcost from ".$findb.".trmain where accountno >= 14000000 and accountno < 15000000 and ddate <= '".$todate."'");
			$rowcost = $db->single();
			extract($rowcost);
			if (is_null($totcost)) {$totcost = 0;}
			$db->query("select sum(credit-debit) as totdepn from ".$findb.".trmain where accountno = 702 and reference >= '14000000' and reference < '15000000' and ddate <= '".$todate."'");
			$rowdep = $db->single();
			extract($rowdep);
			if (is_null($totdepn)) {$totdepn = 0;}
			$bv = $totcost - $totdepn;
			$db->query("insert into ".$findb.".".$bsfile." (Header,Type,AccountName,Col1,Col2,Col3) values ('','D','".$asheading."',".$totcost.",".$totdepn.",".$bv.")");
			$db->execute();
		} else {
			$db->query("select sum(debit - credit) as totcost from ".$findb.".trmain where accountno >= 14000000 and accountno < 15000000 and branch in (".$branchcode.") and ddate <= '".$todate."'");
			$rowcost = $db->single();
			extract($rowcost);
			if (is_null($totcost)) {$totcost = 0;}
			$db->query("select sum(credit-debit) as totdepn from ".$findb.".trmain where accountno = 702 and reference >= '14000000' and reference < '15000000' and branch  in (".$branchcode.") and ddate <= '".$todate."'");
			$rowdep = $db->single();
			extract($rowdep);
			if (is_null($totdepn)) {$totdepn = 0;}
			$bv = $totcost - $totdepn;
			$db->query("insert into ".$findb.".".$bsfile." (Header,Type,AccountName,Col1,Col2,Col3) values ('','D','".$asheading."',".$totcost.",".$totdepn.",".$bv.")");
			$db->execute();
		}
		break;				
	case 'MS':
		if ($branchcode == '*') {
			$db->query("select sum(debit - credit) as totcost from ".$findb.".trmain where accountno >= 15000000 and accountno < 16000000 and ddate <= '".$todate."'");
			$rowcost = $db->single();
			extract($rowcost);
			if (is_null($totcost)) {$totcost = 0;}
			$db->query("select sum(credit-debit) as totdepn from ".$findb.".trmain where accountno = 702 and reference >= '15000000' and reference < '16000000' and ddate <= '".$todate."'");
			$rowdep = $db->single();
			extract($rowdep);
			if (is_null($totdepn)) {$totdepn = 0;}
			$bv = $totcost - $totdepn;
			$db->query("insert into ".$findb.".".$bsfile." (Header,Type,AccountName,Col1,Col2,Col3) values ('','D','".$asheading."',".$totcost.",".$totdepn.",".$bv.")");
			$db->execute();
		} else {
			$db->query("select sum(debit - credit) as totcost from ".$findb.".trmain where accountno >= 15000000 and accountno < 16000000 and branch in (".$branchcode.") and ddate <= '".$todate."'");
			$rowcost = $db->single();
			extract($rowcost);
			if (is_null($totcost)) {$totcost = 0;}
			$db->query("select sum(credit-debit) as totdepn from ".$findb.".trmain where accountno = 702 and reference >= '15000000' and reference < '16000000' and branch in (".$branchcode.") and ddate <= '".$todate."'");
			$rowdep = $db->single();
			extract($rowdep);
			if (is_null($totdepn)) {$totdepn = 0;}
			$bv = $totcost - $totdepn;
			$db->query("insert into ".$findb.".".$bsfile." (Header,Type,AccountName,Col1,Col2,Col3) values ('','D','".$asheading."',".$totcost.",".$totdepn.",".$bv.")");
			$db->execute();
		}
		break;				
	case 'S1':
		if ($branchcode == '*') {
			$db->query("select sum(debit - credit) as totcost from ".$findb.".trmain where accountno >= 16000000 and accountno < 17000000 and ddate <= '".$todate."'");
			$rowcost = $db->single();
			extract($rowcost);
			if (is_null($totcost)) {$totcost = 0;}
			$db->query("select sum(credit-debit) as totdepn from ".$findb.".trmain where accountno = 702 and reference >= '16000000' and reference < '17000000' and ddate <= '".$todate."'");
			$rowdep = $db->single();
			extract($rowdep);
			if (is_null($totdepn)) {$totdepn = 0;}
			$bv = $totcost - $totdepn;
			$db->query("insert into ".$findb.".".$bsfile." (Header,Type,AccountName,Col1,Col2,Col3) values ('','D','".$asheading."',".$totcost.",".$totdepn.",".$bv.")");
			$db->execute();
		} else {
			$db->query("select sum(debit - credit) as totcost from ".$findb.".trmain where accountno >= 16000000 and accountno < 17000000 and branch in (".$branchcode.") and ddate <= '".$todate."'");
			$rowcost = $db->single();
			extract($rowcost);
			if (is_null($totcost)) {$totcost = 0;}
			$db->query("select sum(credit-debit) as totdepn from ".$findb.".trmain where accountno = 702 and reference >= '16000000' and reference < '17000000' and branch in (".$branchcode.") and ddate <= '".$todate."'");
			$rowdep = $db->single();
			extract($rowdep);
			if (is_null($totdepn)) {$totdepn = 0;}
			$bv = $totcost - $totdepn;
			$db->query("insert into ".$findb.".".$bsfile." (Header,Type,AccountName,Col1,Col2,Col3) values ('','D','".$asheading."',".$totcost.",".$totdepn.",".$bv.")");
			$db->execute();
		}
		break;				
	case 'S2':
		if ($branchcode == '*') {
			$db->query("select sum(debit - credit) as totcost from ".$findb.".trmain where accountno >= 17000000 and accountno < 18000000 and ddate <= '".$todate."'");
			$rowcost = $db->single();
			extract($rowcost);
			if (is_null($totcost)) {$totcost = 0;}
			$db->query("select sum(credit-debit) as totdepn from ".$findb.".trmain where accountno = 702 and reference >= '17000000' and reference < '18000000' and ddate <= '".$todate."'");
			$rowdep = $db->single();
			extract($rowdep);
			if (is_null($totdepn)) {$totdepn = 0;}
			$bv = $totcost - $totdepn;
			$db->query("insert into ".$findb.".".$bsfile." (Header,Type,AccountName,Col1,Col2,Col3) values ('','D','".$asheading."',".$totcost.",".$totdepn.",".$bv.")");
			$db->execute();
		} else {
			$db->query("select sum(debit - credit) as totcost from ".$findb.".trmain where accountno >= 17000000 and accountno < 18000000 and branch in (".$branchcode.") and ddate <= '".$todate."'");
			$rowcost = $db->single();
			extract($rowcost);
			if (is_null($totcost)) {$totcost = 0;}
			$db->query("select sum(credit-debit) as totdepn from ".$findb.".trmain where accountno = 702 and reference >= '17000000' and reference < '18000000' and branch in (".$branchcode.") and ddate <= '".$todate."'");
			$rowdep = $db->single();
			extract($rowdep);
			if (is_null($totdepn)) {$totdepn = 0;}
			$bv = $totcost - $totdepn;
			$db->query("insert into ".$findb.".".$bsfile." (Header,Type,AccountName,Col1,Col2,Col3) values ('','D','".$asheading."',".$totcost.",".$totdepn.",".$bv.")");
			$db->execute();
		}
		break;			
	case 'S3':
		if ($branchcode == '*') {
			$db->query("select sum(debit - credit) as totcost from ".$findb.".trmain where accountno >= 18000000 and accountno < 19000000 and ddate <= '".$todate."'");
			$rowcost = $db->single();
			extract($rowcost);
			if (is_null($totcost)) {$totcost = 0;}
			$db->query("select sum(credit-debit) as totdepn from ".$findb.".trmain where accountno = 702 and reference >= '18000000' and reference < '19000000' and ddate <= '".$todate."'");
			$rowdep = $db->single();
			extract($rowdep);
			if (is_null($totdepn)) {$totdepn = 0;}
			$bv = $totcost - $totdepn;
			$db->query("insert into ".$findb.".".$bsfile." (Header,Type,AccountName,Col1,Col2,Col3) values ('','D','".$asheading."',".$totcost.",".$totdepn.",".$bv.")");
			$db->execute();
		} else {
			$db->query("select sum(debit - credit) as totcost from ".$findb.".trmain where accountno >= 18000000 and accountno < 19000000 and branch in (".$branchcode.") and ddate <= '".$todate."'");
			$rowcost = $db->single();
			extract($rowcost);
			if (is_null($totcost)) {$totcost = 0;}
			$db->query("select sum(credit-debit) as totdepn from ".$findb.".trmain where accountno = 702 and reference >= '18000000' and reference < '19000000' and branch in (".$branchcode.") and ddate <= '".$todate."'");
			$rowdep = $db->single();
			extract($rowdep);
			if (is_null($totdepn)) {$totdepn = 0;}
			$bv = $totcost - $totdepn;
			$db->query("insert into ".$findb.".".$bsfile." (Header,Type,AccountName,Col1,Col2,Col3) values ('','D','".$asheading."',".$totcost.",".$totdepn.",".$bv.")");
			$db->execute();
		}
		break;		
	case 'S4':
		if ($branchcode == '*') {
			$db->query("select sum(debit - credit) as totcost from ".$findb.".trmain where accountno >= 19000000 and accountno < 20000000 and ddate <= '".$todate."'");
			$rowcost = $db->single();
			extract($rowcost);
			if (is_null($totcost)) {$totcost = 0;}
			$db->query("select sum(credit-debit) as totdepn from ".$findb.".trmain where accountno = 702 and reference >= '19000000' and reference < '20000000' and ddate <= '".$todate."'");
			$rowdep = $db->single();
			extract($rowdep);
			if (is_null($totdepn)) {$totdepn = 0;}
			$bv = $totcost - $totdepn;
			$db->query("insert into ".$findb.".".$bsfile." (Header,Type,AccountName,Col1,Col2,Col3) values ('','D','".$asheading."',".$totcost.",".$totdepn.",".$bv.")");
			$db->execute();
		} else {
			$db->query("select sum(debit - credit) as totcost from ".$findb.".trmain where accountno >= 19000000 and accountno < 20000000 and branch in (".$branchcode.") and ddate <= '".$todate."'");
			$rowcost = $db->single();
			extract($rowcost);
			if (is_null($totcost)) {$totcost = 0;}
			$db->query("select sum(credit-debit) as totdepn from ".$findb.".trmain where accountno = 702 and reference >= '19000000' and reference < '20000000' and branch in (".$branchcode.") and ddate <= '".$todate."'");
			$rowdep = $db->single();
			extract($rowdep);
			if (is_null($totdepn)) {$totdepn = 0;}
			$bv = $totcost - $totdepn;
			$db->query("insert into ".$findb.".".$bsfile." (Header,Type,AccountName,Col1,Col2,Col3) values ('','D','".$asheading."',".$totcost.",".$totdepn.",".$bv.")");
			$db->execute();
		}
		break;			
			
	}

}

// calculate total book value
$db->query("select sum(Col1 - Col2) as totbv from ".$findb.".".$bsfile);
$rowbv = $db->single();
extract($rowbv);
$totalbv = $totbv;

$db->query("update ".$findb.".".$bsfile." set Total = ".$totalbv." where Header = 'Fixed Assets'");
$db->execute(); 	
			
// current assets
$db->query("insert into ".$findb.".".$bsfile." (Header,Type) values ('Current Assets','H')");
$db->execute(); 
if ($branchcode == '*') {
	$db->query("select accountno as ano,branch as bno,sub as sno,account from ".$findb.".glmast where accountno > 702 and accountno < 851 and sub = 0 order by accountno,branch,sub");
} else {
	$db->query("select accountno as ano,branch as bno,sub as sno,account from ".$findb.".glmast where branch in (".$branchcode.") and accountno > 702 and accountno < 851 and sub = 0 order by accountno,branch,sub");
}
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	$db->query("insert into ".$findb.".".$bsfile." (Type,AccountNumber,Branch,Sub,AccountName) values ('D',".$ano.",'".$bno."',".$sno.",'".$account."')");
	$db->execute(); 
}



// current liabilities
$db->query("insert into ".$findb.".".$bsfile." (Header,Type) values ('Current Liabilities','H')");
$db->execute(); 
if ($branchcode == '*') {
	$db->query("select accountno as ano,branch as bno,sub as sno,account from ".$findb.".glmast where accountno >= 850 and accountno < 901 and sub = 0 order by accountno,branch,sub");
} else {
	$db->query("select accountno as ano,branch as bno,sub as sno,account from ".$findb.".glmast where branch in (".$branchcode.") and accountno >= 850 and accountno < 901 and sub = 0 order by accountno,branch,sub");
}
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	$db->query("insert into ".$findb.".".$bsfile." (Type,AccountNumber,Branch,Sub,AccountName) values ('D',".$ano.",'".$bno."',".$sno.",'".$account."')");
	$db->execute(); 
}

$db->query("insert into ".$findb.".".$bsfile." (Header,Type) values ('Net Current Assets/Liabilities','B')");
$db->execute();

// equity
$db->query("insert into ".$findb.".".$bsfile." (Header,Type) values ('Equity','H')");
$db->execute(); 
if ($branchcode == '*') {
	$db->query("select accountno as ano,branch as bno,sub as sno,account from ".$findb.".glmast where accountno >= 901 and accountno < 1000 and sub = 0 order by accountno,branch,sub");
} else {
	$db->query("select accountno as ano,branch as bno,sub as sno,account from ".$findb.".glmast where branch in (".$branchcode.") and accountno >= 901 and accountno < 1000 and sub = 0 order by accountno,branch,sub");
}
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	$db->query("insert into ".$findb.".".$bsfile." (Type,AccountNumber,Branch,Sub,AccountName) values ('D',".$ano.",'".$bno."',".$sno.",'".$account."')");
	$db->execute(); 
}

// calulate profit/loss
if ($branchcode == '*') {
	$db->query("select sum(credit - debit) as pl from ".$findb.".trmain where accountno <= 700 and ddate <= '".$todate."'");
} else {
	$db->query("select sum(credit - debit) as pl from ".$findb.".trmain where accountno <= 700 and branch in (".$branchcode.") and ddate <= '".$todate."'");
}
$row = $db->single();
extract($row);
if (is_null($pl)) {$pl = 0;}
$profitloss = $pl;
if ($profitloss >= 0) {
	$db->query("insert into ".$findb.".".$bsfile." (Type,AccountName,Col2) values ('D','Current Profit',".$profitloss.")");
} else {
	$db->query("insert into ".$findb.".".$bsfile." (Type,AccountName,Col2) values ('D','Current Loss',".$profitloss.")");
}
$db->execute();

$db->query("insert into ".$findb.".".$bsfile." (Header,Type) values ('Net Equity','B')");
$db->execute(); 

// calculate current asset balances
$db->query("select uid, AccountNumber as ano, Branch as bno, Sub as sno from ".$findb.".".$bsfile." where Type = 'D' and AccountNumber > 702 and AccountNumber < 851");
$rows = $db->resultset();

$period = " and ddate <= '".$todate."'";

foreach ($rows as $row) {
	extract($row);
	$recuid = $uid;
	$db->query("select sum(debit - credit) as tot from ".$findb.".trmain where accountno = ".$ano." and branch = '".$bno."'".$period);
	$row1 = $db->single();
	extract($row1);
	if (is_null($tot)) {$tot = 0;}
	$db->query("update ".$findb.".".$bsfile." set Col1 = ".$tot." where uid = ".$recuid);
	$db->execute(); 
}	

// calculate current liability balances
$db->query("select uid, AccountNumber as ano, Branch as bno, Sub as sno from ".$findb.".".$bsfile." where Type = 'D' and AccountNumber > 850 and AccountNumber < 901");
$rows = $db->resultset();

$period = " and ddate <= '".$todate."'";

foreach ($rows as $row) {
	extract($row);
	$recuid = $uid;
	$db->query("select sum(debit - credit) as tot from ".$findb.".trmain where accountno = ".$ano." and branch = '".$bno."'".$period);
	$row1 = $db->single();
	extract($row1);
	if (is_null($tot)) {$tot = 0;}
	$db->query("update ".$findb.".".$bsfile." set Col1 = ".($tot)." where uid = ".$recuid);
	$db->execute(); 
}	

// calculate equity balances
$db->query("select uid, AccountNumber as ano, Branch as bno, Sub as sno from ".$findb.".".$bsfile." where Type = 'D' and AccountNumber > 900 and AccountNumber < 1000");
$rows = $db->resultset();

$period = " and ddate <= '".$todate."'";

foreach ($rows as $row) {
	extract($row);
	$recuid = $uid;
	$db->query("select sum(debit - credit) as tot from ".$findb.".trmain where accountno = ".$ano." and branch = '".$bno."'".$period);
	$row1 = $db->single();	extract($row1);
	if (is_null($tot)) {$tot = 0;}
	$db->query("update ".$findb.".".$bsfile." set Col1 = ".($tot*-1)." where uid = ".$recuid);
	$db->execute(); 
}	


$db->query("select sum(Col1) as totass from ".$findb.".".$bsfile." where AccountNumber >= 700 and AccountNumber < 851");
$row = $db->single();
extract($row);
if (empty($totass)) {
	$totass = 0;
}
$db->query("update ".$findb.".".$bsfile." set Total = ".$totass." where Header = 'Current Assets'");
$db->execute();
$db->query("select sum(Col1) as totlia from ".$findb.".".$bsfile." where AccountNumber > 850 and AccountNumber < 901");
$row = $db->single();
extract($row);
if (empty($totlia)) {
	$totlia = 0;
}
$db->query("update ".$findb.".".$bsfile." set Total = ".$totlia." where Header = 'Current Liabilities'");
$db->execute();
$db->query("select sum(Col1) as toteq from ".$findb.".".$bsfile." where AccountNumber > 900 and Accountnumber < 1000");
$row = $db->single();
extract($row);
if (empty($toteq)) {
	$toteq = 0;
}
$db->query("update ".$findb.".".$bsfile." set Total = ".$toteq." where Header = 'Equity'");
$db->execute();

$netass = round($totalbv + $totass + $totlia,2);

if ($netass < 0) {
	$db->query("update ".$findb.".".$bsfile." set Header = 'Net Current Liabilities', Total = ".($netass*-1)." where Header = 'Net Current Assets/Liabilities'");
} else {
	$db->query("update ".$findb.".".$bsfile." set Header = 'Net Current Assets', Total = ".$netass." where Header = 'Net Current Assets/Liabilities'");
}	
$db->execute(); 

$totneq = $toteq + $profitloss;
if (empty($totneq)) {
	$totneq = 0;
}
if($totneq < 0) {
	$totneq = $totneq * -1;
}
$db->query("update ".$findb.".".$bsfile." set Total = ".$totneq." where Header = 'Net Equity'");
$db->execute(); 	

$db->closeDB();

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Balance Sheet</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<link rel="stylesheet" type="text/css" media="screen" href="../includes/jquery/themes/ui.multiselect.css"/> 

<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>


<script>

function viewac(acno,br,sb) {
	var fdt = "<?php echo $fromdate; ?>";
	var edt = "<?php echo $todate; ?>";
	var ob = "N";
	
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	jQuery.ajaxSetup({async:false});
	$.get("../ajax/ajaxUpdtGLAcc.php", {vac: acno, vbr: br, vsb: sb, fdt: fdt, edt: edt, ob: ob}, function(data){
	});
	jQuery.ajaxSetup({async:true});
	viewac2();
}

function viewac2() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('rep_view1gl.php','vac','toolbar=0,scrollbars=1,height=600,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function bs2xl() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('rep_bs2excel.php','bsxl','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

function bs2pdf() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('rep_bs2pdf.php','bspdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

</script>



<style type="text/css">
<!--
.style2 {font-size: 12px}
-->
</style>
</head>

<body>

<body>
    <table align="center">
        <tr>
	        <td><?php include "getbs.php"; ?></td>
        </tr>
	</table>		

</body>
</html>

