<?php
session_start();
$usersession = $_SESSION['usersession'];

$newdb_isp = 'Y';

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

$coyid = $_SESSION['s_coyid'];
$dbprefix = $_SESSION['s_dbprefix'];
$cltdb = $dbprefix.'sub'.$subscriber;
$findb = $dbprefix.'fin'.$subscriber.'_'.$coyid;
$coyname = $_SESSION['s_coyname'];

$findb = $_SESSION['s_findb'];
$cltdb = $_SESSION['s_cltdb'];

$db->query("select bedate,yrdate from ".$findb.".globals");
$row = $db->single();
extract($row);
$ybdate = $bedate;
$yedate = $yrdate;

$today = date('Y-m-d');
$new_yrdate = date('Y-m-d', strtotime('+1 year', strtotime($yedate)) );
$new_bedate = date('Y-m-d', strtotime('+1 day', strtotime($yedate)) );

$yr = substr($new_bedate,0,4);

// add last tax year to companies
$db->query('select max(coyid) as lastcoyid from companies');
$row = $db->single();
extract($row);
$new_coyid = $lastcoyid + 1;
if ($newdb_isp == 'Y') {
	$new_coyname = $coyname.' - YE'.$yr;
} else {
	$new_coyname = $coyname.' - YE'.$yr.' (empty database)';
}
$db->query('insert into companies (coyid,taxyear,coysubid,coyname) values ('.$new_coyid.',"'.$yr.'",'.$subscriber.',"'.$new_coyname.'")');
$db->execute();
$new_subdb = $dbprefix.'sub'.$subscriber."_".$yr;
$new_findb = $dbprefix.'fin'.$subscriber.'_'.$new_coyid."_".$yr;

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Year End Routine</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

</head>

<body>

<form name="form1" id="form1" method="post" action="">
  <div id="ye" style="position:absolute;visibility:visible;top:10px;left:5px;height:400px;width:600px;background-color:#FFF;overflow:scroll;">
  	<div style="position:relative;top:2px;left:275px;width:200px;font-size:12px">
    	Year End Routine
    </div>
    
    <div style="position:relative;top:10px;left:10px;width:580px;height:auto;font-size:10px">
    	<?php
		
			if ($today <= $yedate) {
				echo '<script>';
				echo 'alert("You may not run and end of year routine before the end of the tax year");';
				echo '</script>';
				return;
			}	
if ($newdb_isp == 'Y') {	//isp allows programmatic creation of new database			
			echo "Archiving database"."<br>";
			// create client database for previous tax year

			$db->query("create database if not exists ".$new_subdb." default character set = 'utf8' default collate 'utf8_general_ci'");
			$db->execute();
			$db->query("grant all on ".$new_subdb.".* to 'infinint_sagana'@'localhost' identified by 'dun480can'");
			$db->execute();
			$db->query("grant all on ".$new_subdb.".* to 'infinint_sagana'@'%' identified by 'dun480can'");
			$db->execute();
			
			$tables = array();
			$db->query('SHOW TABLES from '.$cltdb);
			$rows = $db->resultsetNum();
			foreach ($rows as $row) {
				$tables[] = $row[0];
			}
			foreach($tables as $table)  {
				if (substr($table,0,4) != 'ztmp') {
					echo "Archiving ".$table."<br>";
					$db->query('drop table if exists '.$new_subdb.'.'.$table);
					$db->execute();
					$db->query('create table '.$new_subdb.'.'.$table.' like '.$cltdb.'.'.$table);
					$db->execute();
					$db->query('insert into '.$new_subdb.'.'.$table.' select * from '.$cltdb.'.'.$table);											 
					$db->execute();
					ob_flush();
					flush();
				}
			}
			
			// create finance database for previous tax year
			
			$db->query("create database if not exists ".$new_findb." default character set = 'utf8' default collate 'utf8_general_ci'");
			$db->execute();
			$db->query("grant all on ".$new_findb.".* to 'infinint_sagana'@'localhost' identified by 'dun480can'");
			$db->execute();
			$db->query("grant all on ".$new_findb.".* to 'infinint_sagana'@'%' identified by 'dun480can'");
			$db->execute();
			
			$tables = array();
			$db->query('SHOW TABLES from '.$findb);
			$rows = $db->resultsetNum();
			foreach ($rows as $row) {
				$tables[] = $row[0];
			}
			foreach($tables as $table)  {
				if (substr($table,0,4) != 'ztmp') {
					echo "Archiving ".$table."<br>";
					$db->query('drop table if exists '.$new_findb.'.'.$table);
					$db->execute();
					$db->query('create table '.$new_findb.'.'.$table.' like '.$findb.'.'.$table);
					$db->execute();
					$db->query('insert into '.$new_findb.'.'.$table.' select * from '.$findb.'.'.$table);											 
					$db->execute();
					ob_flush();
					flush();
				}
			}
}
			$db->query('update '.$findb.'.glmast set obalm = 0, prevbal = 0, lastyear = 0');
			$db->execute();
			
if ($newdb_isp == 'Y') {	//isp allows programmatic creation of new database
			$db->query('update '.$new_subdb.'.client_company_xref set company_id = '.$new_coyid.' where company_id = '.$coyid);
			$db->execute();
}

			// sort out staff access for archived databases
			
			echo "Assign access rights to users"."<br>";
			$db->query('select staff_id,module,usergroup,branch from access where subid = '.$subscriber.' and coyid = '.$coyid);
			$rows = $db->resultset();
			foreach ($rows as $row) {
				extract($row);
				$stf = $staff_id;
				$mod = $module;
				$ugp = $usergroup;
				$br = $branch;
				$db->query('insert into access (staff_id,subid,coyid,module,usergroup,branch) values ('.$stf.','.$subscriber.','.$new_coyid.',"'.$mod.'",'.$ugp.',"'.$br.'")');
				$db->execute();
			}


			// Carry forward unreconciled bank transactions

			echo "Carrying forward unreconciled bank transactions"."<br>";
			$db->query('insert into '.$findb.'.trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,descript1,taxpcent,gsttype,reconciled,temprecon) select"' .$new_bedate.'",accountno,branch,sub,accno,br,subbr,debit,credit,descript1,taxpcent,gsttype,reconciled,temprecon from '.$findb.'.trmain where accountno >= 751 and accountno <= 800 and reconciled = "N" and ddate <= "'.$yedate.'"');
			$db->execute();
			
			echo "Calculating profit/loss for previous tax year per branch"."<br>";
			$db->query('select branch from '.$findb.'.branch');
			$rows = $db->resultset();
			foreach ($rows as $row) {
				extract($row);
				$br = $branch;
				$db->query('select sum(debit-credit) as pl from '.$findb.'.trmain where accountno < 701 and ddate <= "'.$yedate.'" and branch = "'.$br.'"');
				$row = $db->single();
				extract($row);
				if (is_null($pl)) {
					$pl = 0;
				}
				if ($pl < 0 ) {	//ie a profit
					$pl = $pl * -1;
					$db->query('insert into '.$findb.'.trmain (ddate,accountno,branch,sub,accno,br,subbr,credit,reference,descript1) values ("'.$new_bedate.'",998,"'.$br.'",0,999,"'.$br.'",0,'.$pl.',"OB","Previous Tax Year PL")');
					$db->execute();
				} else {	// ie a loss
					$db->query('insert into '.$findb.'.trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,reference,descript1) values ("'.$new_bedate.'",998,"'.$br.'",0,999,"'.$br.'",0,'.$pl.',"OB","Previous Tax Year PL")');
					$db->execute();
				}
			}
			
			echo "populate lastyear for accounts < 701"."<br>";
			$db->query('select accountno,branch,sub,account from '.$findb.'.glmast where accountno < 701');
			$rows = $db->resultset();
			foreach ($rows as $row) {
				extract($row);
				$a = $accountno;
				$b = $branch;
				$s = $sub;
				$aname = $account;
				$bal = 0;
				$db->query('select sum(debit - credit) as bl from '.$findb.'.trmain where accountno = '.$a.' and branch = "'.$b.'" and sub = '.$s.' and ddate <= "'.$yedate.'"');
				$row = $db->single();
				extract($row);
				$bal = $bl;
				if (is_null($bal)) {
					$bal = 0;
				}

				if ($bal != 0) {
					$db->query('update '.$findb.'.glmast set lastyear = '.$bal.' where accountno = '.$a.' and branch = "'.$b.'" and sub = '.$s);
					$db->execute();
				}
			}
			
			echo "Calculating GL opening balances"."<br>";
			$db->query('select accountno,branch,sub,account from '.$findb.'.glmast where accountno > 700');
			$rows = $db->resultset();
			foreach ($rows as $row) {
				extract($row);
				$a = $accountno;
				$b = $branch;
				$s = $sub;
				$aname = $account;
				$bal = 0;
				echo "Calculating opening balance for ".$aname."<br>";
				if ($accountno >= 701 && $accountno <= 750) {
					$db->query('select sum(debit - credit) as bl from '.$findb.'.trmain where accountno = '.$a.' and branch = "'.$b.'" and sub = '.$s.' and ddate <= "'.$yedate.'"');
					$row = $db->single();
					extract($row);
					$bal = $bl;
					if (is_null($bal)) {
						$bal = 0;
					}
					
					if ($bal > 0) {
						$db->query('insert into '.$findb.'.trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,reference,descript1) values ("'.$new_bedate.'",'.$a.',"'.$b.'",'.$s.',999,"'.$b.'",0,'.$bal.',"OB","Opening Balance")');
						$db->execute();
						$db->query('update '.$findb.'.glmast set lastyear = '.$bal.' where accountno = '.$a.' and branch = "'.$b.'" and sub = '.$s);
						$db->execute();
					}
					if ($bal < 0) {
						$blc = $bal;
						$bal = $bal* - 1;
						$db->query('insert into '.$findb.'.trmain (ddate,accountno,branch,sub,accno,br,subbr,credit,reference,descript1) values ("'.$new_bedate.'",'.$a.',"'.$b.'",'.$s.',999,"'.$b.'",0,'.$bal.',"OB","Opening Balance")');
						$db->execute();
						$db->query('update '.$findb.'.glmast set lastyear = '.$blc.' where accountno = '.$a.' and branch = "'.$b.'" and sub = '.$s);
						$db->execute();
					}
				}
				if ($accountno >= 751 && $accountno <= 800) {
					$db->query('select sum(debit - credit) as bal from '.$findb.'.trmain where accountno = '.$a.' and branch = "'.$b.'" and sub = '.$s.' and reconciled = "Y" and ddate <= "'.$yedate.'"');
					$row = $db->single();
					extract($row);
					if (is_null($bal)) {
						$bal = 0;
					}
					
					if ($bal > 0) {
						$db->query('insert into '.$findb.'.trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,reference,descript1) values ("'.$new_bedate.'",'.$a.',"'.$b.'",'.$s.',999,"'.$b.'",0,'.$bal.',"OB","Opening Balance")');
						$db->execute();
						}
					if ($bal < 0) {
						$bal = $bal* - 1;
						$db->query('insert into '.$findb.'.trmain (ddate,accountno,branch,sub,accno,br,subbr,credit,reference,descript1) values ("'.$new_bedate.'",'.$a.',"'.$b.'",'.$s.',999,"'.$b.'",0,'.$bal.',"OB","Opening Balance")');
						$db->execute();
						
					}
					
					$db->query('select sum(debit - credit) as blc from '.$findb.'.trmain where accountno = '.$a.' and branch = "'.$b.'" and sub = '.$s.' and ddate <= "'.$yedate.'"');
					$row = $db->single();
					extract($row);
					if (is_null($blc)) {
						$blc = 0;
					}
					
					$db->query('update '.$findb.'.glmast set lastyear = '.$blc.' where accountno = '.$a.' and branch = "'.$b.'" and sub = '.$s);
					$db->execute();
				}
				if ($accountno >= 801 && $accountno < 1000) {
					$bal = 0;
					$db->query('select sum(debit - credit) as bl from '.$findb.'.trmain where accountno = '.$a.' and branch = "'.$b.'" and sub = '.$s.' and ddate <= "'.$yedate.'"');
					$row = $db->single();
					extract($row);
					$bal = $bl;
					if (is_null($bal)) {
						$bal = 0;
					}
					
					if ($bal > 0) {
						$db->query('insert into '.$findb.'.trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,reference,descript1) values ("'.$new_bedate.'",'.$a.',"'.$b.'",'.$s.',999,"'.$b.'",0,'.$bal.',"OB","Opening Balance")');
						$db->execute();
						$db->query('update '.$findb.'.glmast set lastyear = '.$bal.' where accountno = '.$a.' and branch = "'.$b.'" and sub = '.$s);
						$db->execute();
					}
					if ($bal < 0) {
						$blc = $bal;
						$bal = $bal* - 1;
						$db->query('insert into '.$findb.'.trmain (ddate,accountno,branch,sub,accno,br,subbr,credit,reference,descript1) values ("'.$new_bedate.'",'.$a.',"'.$b.'",'.$s.',999,"'.$b.'",0,'.$bal.',"OB","Opening Balance")');
						$db->execute();
						
						$db->query('update '.$findb.'.glmast set lastyear = '.$blc.' where accountno = '.$a.' and branch = "'.$b.'" and sub = '.$s);
						$db->execute();
					}
				}
			}
			
			echo "Caclulating Debtors and Creditors opening balances"."<br>";
			
			$db->query('select drno,drsub,member from '.$cltdb.'.client_company_xref where drno > 0 and company_id = '.$coyid);
			$rows = $db->resultset();
			foreach ($rows as $row) {
				extract($row);
				$a = $drno;
				$s = $drsub;
				$aname = $member;
				$bal = 0;
				echo "Calculating opening balance for ".$aname."<br>";
				
				$db->query('select sum(debit - credit) as bl from '.$findb.'.trmain where accountno = '.$a.' and sub = '.$s.' and ddate <= "'.$yedate.'"');
				$row = $db->single();
				extract($row);
					$bal = $bl;
					if (is_null($bal)) {
						$bal = 0;
					}
					
				if ($bal > 0) {
					$db->query('insert into '.$findb.'.trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,reference,descript1) values ("'.$new_bedate.'",'.$a.',"1000",'.$s.',999,"1000",0,'.$bal.',"OB","Opening Balance")');
					$db->execute();
				}
				if ($bal < 0) {
					$bal = $bal* - 1;
					$db->query('insert into '.$findb.'.trmain (ddate,accountno,branch,sub,accno,br,subbr,credit,reference,descript1) values ("'.$new_bedate.'",'.$a.',"1000",'.$s.',999,"1000",0,'.$bal.',"OB","Opening Balance")');
					$db->execute();
				}

			}
			
			$db->query('select crno,crsub,member from '.$cltdb.'.client_company_xref where crno > 0 and company_id = '.$coyid);
			$rows = $db->resultset();
			foreach ($rows as $row) {
				extract($row);
				$a = $drno;
				$s = $drsub;
				$aname = $member;
				$bal = 0;
				echo "Calculating opening balance for ".$aname."<br>";
				
				$db->query('select sum(debit - credit) as bl from '.$findb.'.trmain where accountno = '.$a.' and sub = '.$s.' and ddate <= "'.$yedate.'"');
				$row = $db->single();
				extract($row);
					$bal = $bl;
					if (is_null($bal)) {
						$bal = 0;
					}
					
				if ($bal > 0) {
					$db->query('insert into '.$findb.'.trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,reference,descript1) values ("'.$new_bedate.'",'.$a.',"1000",'.$s.',999,"1000",0,'.$bal.',"OB","Opening Balance")');
					$db->execute();
				}
				if ($bal < 0) {
					$bal = $bal* - 1;
					$$db->query('insert into '.$findb.'.trmain (ddate,accountno,branch,sub,accno,br,subbr,credit,reference,descript1) values ("'.$new_bedate.'",'.$a.',"1000",'.$s.',999,"1000",0,'.$bal.',"OB","Opening Balance")');
					$db->execute();
				}
				
			}
			
			echo "Caclulating Fixed Assets opening balances"."<br>";
			$db->query('select accountno,branch,sub,asset from '.$findb.'.fixassets');
			$rows = $db->resultset();
			foreach ($rows as $row) {
				extract($row);
				$a = $accountno;
				$b = $branch;
				$s = $sub;
				$aname = $asset;
				$bal = 0;
				echo "Calculating opening balance for ".$aname."<br>";
				$db->query('select sum(debit - credit) as bl from '.$findb.'.trmain where accountno = '.$a.' and branch = "'.$b.'" and sub = '.$s.' and ddate <= "'.$yedate.'"');
				$row = $db->single();
				extract($row);
					$bal = $bl;
					if (is_null($bal)) {
						$bal = 0;
					}
					
				if ($bal > 0) {
					$db->query('insert into '.$findb.'.trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,reference,descript1) values ("'.$new_bedate.'",'.$a.',"'.$b.'",'.$s.',999,"'.$b.'",0,'.$bal.',"OB","Opening Balance")');
					$db->execute();
				}
				if ($bal < 0) {
					$bal = $bal* - 1;
					$db->query('insert into '.$findb.'.trmain (ddate,accountno,branch,sub,accno,br,subbr,credit,reference,descript1) values ("'.$new_bedate.'",'.$a.',"'.$b.'",'.$s.',999,"'.$b.'",0,'.$bal.',"OB","Opening Balance")');
					$db->execute();
				}

			}
			
			echo "Deleting previous tax year transactions"."<br>";
			$db->query('delete from '.$findb.'.trmain where ddate <= "'.$yedate.'"');
			$db->execute();
			$db->query('delete from '.$findb.'.invtrans where ref_no in (select ref_no from '.$findb.'.invhead where ddate < "'.$ybdate.'")');
			$db->execute();
			$db->query('delete from '.$findb.'.invhead where ddate < "'.$ybdate.'"');
			$db->execute();
			
			echo "calculating opening balances for stock transactions."."<br>";
			$db->query('select itemcode,item,groupid,catid from '.$findb.'.stkmast');
			$rows = $db->resultset();
			foreach ($rows as $row) {
				extract($row);
				$a = $itemcode;
				$g = $groupid;
				$c = $catid;
				$aname = $item;
				$bal = 0;
				echo "Calculating opening balance for ".$aname."<br>";
				$db->query('select sum(increase - decrease) as bl from '.$findb.'.stktrans where itemcode = "'.$a.'" and ddate <= "'.$yedate.'"');
				$row = $db->single();
				extract($row);
				$bal = $bl;
				if (is_null($bal)) {
					$bal = 0;
				}
					
				if ($bal > 0) {
					$db->query('insert into '.$findb.'.stktrans (groupid,catid,itemcode,item,ddate,increase) values ('.$g.','.$c.',"'.$a.'","'.$aname.'","'.$new_bedate.'",'.$bal.')');
					$db->execute();
				}
				if ($bal < 0) {
					$bal = $bal* - 1;
					$db->query('insert into '.$findb.'.stktrans (groupid,catid,itemcode,item,ddate,decrease) values ('.$g.','.$c.',"'.$a.'","'.$aname.'","'.$new_bedate.'",'.$bal.')');
					$db->execute();
				}
			}
			
			echo "Deleting last tax year stock transactions."."<br>";
			$db->query('delete from '.$findb.'.stktrans where ddate <= "'.$yedate.'"');
			$db->execute();
		
			// reset dates
			$db->query('update '.$findb.'.globals set bedate = "'.$new_bedate.'", yrdate = "'.$new_yrdate.'"');
			$db->execute();
if ($newdb_isp == 'Y') {			
			// delete no current menu options
			$db->query('delete from '.$new_findb.'.c_menu where `current` = "N"');
			$db->execute();
}
			//*************************************************************************************************
			// Recalculate balances
			//*************************************************************************************************

			echo 'Recalculating balances'.'<br>';
			
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
			
			$db->closeDB();
			echo 'End of Year Routine complete.';
			
			
			echo '<script>';
			echo 'alert("End of Year Routine complete.");';
			echo 'this.close();';
			echo '</script>';
			
		
		?>
    
    </div>

</div>

</form>

</body>
</html
