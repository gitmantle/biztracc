<?php
ini_set('display_errors', true);

session_start();
$curdate = $_REQUEST['curdate'];
$lstdate = $_REQUEST['lstdate'];
$period = $_REQUEST['period'];
$range = $_REQUEST['range'];
$fromdr = $_REQUEST['fromdr'];
$todr = $_REQUEST['todr'];
$fdr = explode('~',$fromdr);
$fromdr = $fdr[1];
$tdr = explode('~',$todr);
$todr = $tdr[1];
$coyid = $_SESSION['s_coyid'];
$comment = $_REQUEST['comment'];
$creditstat = $_REQUEST['creditstat'];
$dbprefix = $_SESSION['s_dbprefix'];

$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$drfile = 'ztmp'.$user_id.'_statements';
$subdb = $dbprefix.'sub'.$subid.'.';

$findb = $_SESSION['s_findb'];

$db->query("drop table if exists ".$findb.".".$drfile);
$db->execute();

$db->query("create table ".$findb.".".$drfile." (debtor varchar(80) default '', account int(11) default 0, sub int(11) default 0, member_id int(11) default 0, current decimal(16,2) default 0, d30 decimal(16,2) default 0,d60 decimal(16,2) default 0,d90 decimal(16,2) default 0, d120 decimal(16,2) default 0, billing int(11) default 0, email int(11) default 0, sendby char(5) default '')  engine myisam");
$db->execute();

if ($period == 'c') {
	$db->query("insert into ".$findb.".".$drfile." select concat(".$subdb."members.firstname,' ',".$subdb."members.lastname) as dr, ".$subdb."client_company_xref.drno,".$subdb."client_company_xref.drsub, ".$subdb."client_company_xref.client_id, 0, 0, 0, 0, 0, ".$subdb."client_company_xref.billing, ".$subdb."client_company_xref.email,".$subdb."client_company_xref.sendstatement from ".$subdb."client_company_xref,".$subdb."members where (".$subdb."members.member_id = ".$subdb."client_company_xref.client_id) and (".$subdb."client_company_xref.drno <> 0) and (".$subdb."client_company_xref.drno >= ".$fromdr." and ".$subdb."client_company_xref.drno <= ".$todr.") and (".$subdb."client_company_xref.company_id = ".$coyid.") and ((".$subdb."client_company_xref.sendstatement = 'Post' and ".$subdb."client_company_xref.billing > 0 ) or (".$subdb."client_company_xref.sendstatement = 'Email' and ".$subdb."client_company_xref.email > 0) )");
} else {
	$db->query("insert into ".$findb.".".$drfile." select concat(".$subdb."members.firstname,' ',".$subdb."members.lastname) as dr, ".$subdb."client_company_xref.drno,".$subdb."client_company_xref.drsub, ".$subdb."client_company_xref.client_id, ".$subdb."client_company_xref.d30, ".$subdb."client_company_xref.d60, ".$subdb."client_company_xref.d90, ".$subdb."client_company_xref.d120, 0,".$subdb."client_company_xref.billing, ".$subdb."client_company_xref.email,".$subdb."client_company_xref.sendstatement from ".$subdb."client_company_xref,".$subdb."members where (".$subdb."members.member_id = ".$subdb."client_company_xref.client_id) and (".$subdb."client_company_xref.drno <> 0) and (".$subdb."client_company_xref.drno >= ".$fromdr." and ".$subdb."client_company_xref.drno <= ".$todr.") and (".$subdb."client_company_xref.company_id = ".$coyid.") and ((".$subdb."client_company_xref.sendstatement = 'Post' and ".$subdb."client_company_xref.billing > 0 ) or (".$subdb."client_company_xref.sendstatement = 'Email' and ".$subdb."client_company_xref.email > 0) )");
}

$db->execute();

// add uid field
$db->query("alter table ".$findb.".".$drfile." add `uid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY");
$db->execute();

// calculate aged balances synched from statement dates

$lastcur = date("Y-m-d", strtotime($lstdate));
$lastd30 = date("Y-m-d", strtotime($lastcur." -1 month"));
$lastd60 = date("Y-m-d", strtotime($lastd30." -1 month"));
$lastd90 = date("Y-m-d", strtotime($lastd60." -1 month"));
$lastd120 = date("Y-m-d", strtotime($lastd90." -1 month"));

$db->query("select uid, account, sub from ".$findb.".".$drfile);
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	$drac = $account;
	$drsb = $sub;
	$id = $uid;
	
	// recalcualte 120 day plus balances
	$db->query("select sum(debit-credit) as bal from ".$findb.".trmain where accountno = ".$drac." and sub = ".$drsb." and ddate <= '".$lastd120."'");
	$row = $db->single();
	extract($row);
	if (is_null($bal)) {$bal = 0;}
	$db->query("update ".$findb.".".$drfile." set d120 = ".$bal." where uid = ".$id);
	$db->execute();
	
	// recalcualte 90 day balances
	$db->query("select sum(debit-credit) as bal from ".$findb.".trmain where accountno = ".$drac." and sub = ".$drsb." and ddate > '".$lastd120."' and ddate <= '".$lastd90."'");
	$row = $db->single();
	extract($row);
	if (is_null($bal)) {$bal = 0;}
	$db->query("update ".$findb.".".$drfile." set d90 = ".$bal." where uid = ".$id);
	$db->execute();

	// recalcualte 60 day balances
	$db->query("select sum(debit-credit) as bal from ".$findb.".trmain where accountno = ".$drac." and sub = ".$drsb." and ddate > '".$lastd90."' and ddate <= '".$lastd60."'");
	$row = $db->single();
	extract($row);
	if (is_null($bal)) {$bal = 0;}
	$db->query("update ".$findb.".".$drfile." set d60 = ".$bal." where uid = ".$id);
	$db->execute();

	// recalcualte 30 day balances
	$db->query("select sum(debit-credit) as bal from ".$findb.".trmain where accountno = ".$drac." and sub = ".$drsb." and ddate > '".$lastd60."' and ddate <= '".$lastd30."'");
	$row = $db->single();
	extract($row);
	if (is_null($bal)) {$bal = 0;}
	$db->query("update ".$findb.".".$drfile." set d30 = ".$bal." where uid = ".$id);
	$db->execute();

	// recalcualte current balances
	$db->query("select sum(debit-credit) as bal from ".$findb.".trmain where accountno = ".$drac." and sub = ".$drsb." and ddate > '".$lastd30."' and ddate <= '".$lstdate."'");
	$row = $db->single();
	extract($row);
	if (is_null($bal)) {$bal = 0;}
	$db->query("update ".$findb.".".$drfile." set current = ".$bal." where uid = ".$id);
	$db->execute();
	
}

if ($creditstat == 'No') {
	$db->query("delete from ".$findb.".".$drfile." where (current + d30 + d60 + d90 + d120) < 0");	
	$db->execute();
}

switch ($range) {
	case 'z':
		$db->query("delete from ".$findb.".".$drfile." where (current + d30 + d60 + d90 + d120) = 0");	
		$db->execute();
		break;
	case 't':
	// determine if there were transactions to that account for the period
		$db->query("select uid,account,sub,current,d30,d60,d90,d120 from ".$findb.".".$drfile);
		$rows = $db_trd->resultset();
		foreach ($rows as $row) {
			extract($row);
			$ac = $account;
			$sb = $sub;
			$id = $uid;
			$bal = $current+$d30+$d60+$d90+$d120;
			$db->query("select uid from ".$findb.".trmain where (ddate >= '".$curdate."' and ddate <= '".$lstdate."') and accountno = ".$ac." and sub = ".$sb);
			$rows = $db_trd->resultset();
			$rt = count($rows);
			if ($rt == 0 && $bal == 0) {
				$db->query("delete from ".$findb.".".$drfile." where uid = ".$id);	
				$db->execute();
			}
		}
		break;
}


// add address fields
$db->query("alter table ".$findb.".".$drfile." add `street_no` varchar(45),add ad1 varchar(45),add ad2 varchar(45),add suburb varchar(45),add town varchar(45),add postcode varchar(15),add country varchar(45),add address varchar(100)");
$db->execute();

// add addresses
$db->query("select uid,sendby,billing,email from ".$findb.".".$drfile);
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	$adid = $billing;
	$cmid = $email;
	$method = $sendby;
	$id = $uid;
	if ($method == 'Post') {
		$db->query("select ".$subdb."addresses.street_no,".$subdb."addresses.ad1,".$subdb."addresses.ad2,".$subdb."addresses.suburb,".$subdb."addresses.town,".$subdb."addresses.postcode,".$subdb."addresses.country from ".$subdb."addresses where ".$subdb."addresses.address_id = ".$adid);
		$rowa = $db->single();
		extract($rowa);
		$ad = trim($street_no.' '.$ad1.' '.$ad2.' '.$suburb.' '.$town.' '.$postcode);
		$db->query("update ".$findb.".".$drfile." set street_no = '".$street_no."', ad1 = '".$ad1."', ad2 = '".$ad2."', suburb = '".$suburb."', town = '".$town."', postcode = '".$postcode."', country = '".$country."', address = '".$ad."' where uid = ".$id);
		$db->execute();
	} else {
		$db->query("select ".$subdb."comms.comm from ".$subdb."comms where ".$subdb."comms.comms_id = ".$cmid);
		$rowe = $db->single();
		extract($rowe);
		$ad = $comm;
		$db->query("update ".$findb.".".$drfile." set address = '".$ad."' where uid = ".$id);
		$db->execute();
	}
}

$db->closeDB();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>Statements</title>
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

<script>

window.name = "statements";

function runstatementsp() {
	var x = 0, y = 0; // default values	
	var comment = "<?php echo $comment; ?>";
	var fromdate = "<?php echo $curdate; ?>";
	var todate = "<?php echo $lstdate; ?>";
	var period = "<?php echo $period; ?>";
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('rep_stat2pdfp.php?period='+period+'&fromdate='+fromdate+'&todate='+todate+'&comment='+comment,'statpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	$.get("includes/ajaxUpdtStatDate.php", {todate:todate}, function(data){});
}
function runstatementse() {
	var x = 0, y = 0; // default values	
	var comment = "<?php echo $comment; ?>";
	var fromdate = "<?php echo $curdate; ?>";
	var todate = "<?php echo $lstdate; ?>";
	var period = "<?php echo $period; ?>";
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('rep_stat2pdfe.php?period='+period+'&fromdate='+fromdate+'&todate='+todate+'&comment='+comment,'statpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	$.get("includes/ajaxUpdtStatDate.php", {todate:todate}, function(data){});
}

</script>

</head>
<body>
  <table width="950" border="0">
    <tr>
    <tr>
      <td><?php include "getstpost.php"; ?></td>
    </tr>
	<tr>
    	<td align="right"><input type="button" name="prun" id="prun" value="Run Postal Statements" onclick="runstatementsp()"/></td>
    </tr>
    <tr>
      <td><?php include "getstemail.php"; ?></td>
    </tr>
	<tr>
    	<td align="right"><input type="button" name="erun" id="erun" value="Run Email Statements" onclick="runstatementse()"/></td>
    </tr>
    <tr>
      <td ><img src="../includes/calculator/calculator.png" width="16" height="16" alt="Calculators" title="Calculators" onclick="showCalculators();">&nbsp; <img src="../images/todo.gif" width="16" height="16" alt="ToDo" title="ToDo List" onClick="todo()"></td>
    </tr>
  </table>
</body>

</html>