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

$bx = $_REQUEST['bx'];
$fromdate = $_REQUEST['fdate'];
$todate = $_REQUEST['tdate'];

$_SESSION['s_gstbox'] = $bx;
$_SESSION['s_gstfdate'] = $fromdate;
$_SESSION['s_gsttdate'] = $todate;

$findb = $_SESSION['s_findb'];
$cltdb = $_SESSION['s_cltdb'];
$coyid = $_SESSION['s_coyid'];

$db->query('select taxpcent from '.$findb.'.taxtypes where uid = 1');
$row = $db->single();
extract($row);
$txpcent = $taxpcent;

$glfile = 'ztmp'.$user_id.'_gst'.$bx;

$db->query("drop table if exists ".$findb.".".$glfile);
$db->execute();

$db->query("create table ".$findb.".".$glfile." ( ddate date, account varchar(50),accountno int(11),branch char(4),sub int(11),amount decimal(16,2), reference varchar(15),taxpcent decimal(5,2) default 0,gsttype char(3),descript1 varchar(50)) engine myisam"); 
$db->execute();

if ($bx == 1) {
	$db->query("select ddate,accno,br,subbr,credit as gstcollected,reference,descript1,taxpcent,gsttype from ".$findb.".trmain where accountno = 870 and ddate >= '".$fromdate."' and ddate <= '".$todate."' and gstrecon != 'Y' and (credit > 0)");
	$rows = $db->resultset();
	foreach ($rows as $row) {
		extract($row);
		if ($gstcollected != 0) {
			$db->query("insert into ".$findb.".".$glfile." (ddate,accountno,branch,sub,amount,reference,taxpcent,gsttype,descript1) values (:ddate,:accountno,:branch,:sub,:amount,:reference,:taxpcent,:gsttype,:descript1)");
			$db->bind(':ddate',$ddate);
			$db->bind(':accountno', $accno);
			$db->bind(':branch', $br);
			$db->bind(':sub', $subbr);
			$db->bind(':amount', $gstcollected);
			$db->bind(':reference', $reference);
			$db->bind(':taxpcent', $taxpcent);
			$db->bind(':gsttype', $gsttype);
			$db->bind(':descript1', $descript1);
			$db->execute();
		}
	}
	
}

if ($bx == 4) {
	$db->query("select ddate,accno,br,subbr,debit as gstpaid,reference,descript1,taxpcent,gsttype from ".$findb.".trmain where accountno = 870 and ddate >= '".$fromdate."' and ddate <= '".$todate."' and gstrecon != 'Y' and (debit > 0)");
	$rows = $db->resultset();
	foreach ($rows as $row) {
		extract($row);
		if ($gstpaid != 0) {
			$db->query("insert into ".$findb.".".$glfile." (ddate,accountno,branch,sub,amount,reference,taxpcent,gsttype,descript1) values (:ddate,:accountno,:branch,:sub,:amount,:reference,:taxpcent,:gsttype,:descript1)");
			$db->bind(':ddate',$ddate);
			$db->bind(':accountno', $accno);
			$db->bind(':branch', $br);
			$db->bind(':sub', $subbr);
			$db->bind(':amount', $gstpaid);
			$db->bind(':reference', $reference);
			$db->bind(':taxpcent', $taxpcent);
			$db->bind(':gsttype', $gsttype);
			$db->bind(':descript1', $descript1);
			$db->execute();
		}
	}
}
	
if ($bx == 6) {
	$db->query("select ddate,accno,br,subbr,grosssales - credit as netsales,reference,descript1,taxpcent,gsttype from ".$findb.".trmain where accountno = 870 and ddate >= '".$fromdate."' and ddate <= '".$todate."' and gstrecon != 'Y' and (credit > 0)");
	$rows = $db->resultset();
	foreach ($rows as $row) {
		extract($row);
		if ($netsales != 0) {
			$db->query("insert into ".$findb.".".$glfile." (ddate,accountno,branch,sub,amount,reference,taxpcent,gsttype,descript1) values (:ddate,:accountno,:branch,:sub,:amount,:reference,:taxpcent,:gsttype,:descript1)");
			$db->bind(':ddate',$ddate);
			$db->bind(':accountno', $accno);
			$db->bind(':branch', $br);
			$db->bind(':sub', $subbr);
			$db->bind(':amount', $netsales);
			$db->bind(':reference', $reference);
			$db->bind(':taxpcent', $taxpcent);
			$db->bind(':gsttype', $gsttype);
			$db->bind(':descript1', $descript1);
			$db->execute();
		}
	}	
}
	
if ($bx == 7) {
	$db->query("select ddate,accno,br,subbr,grosspurchases - debit as netpurchases,reference,descript1,taxpcent,gsttype from ".$findb.".trmain where accountno = 870 and ddate >= '".$fromdate."' and ddate <= '".$todate."' and gstrecon != 'Y' and (debit > 0)");
	$rows = $db->resultset();
	foreach ($rows as $row) {
		extract($row);
		if ($netpurchases != 0) {
			$db->query("insert into ".$findb.".".$glfile." (ddate,accountno,branch,sub,amount,reference,taxpcent,gsttype,descript1) values (:ddate,:accountno,:branch,:sub,:amount,:reference,:taxpcent,:gsttype,:descript1)");
			$db->bind(':ddate',$ddate);
			$db->bind(':accountno', $accno);
			$db->bind(':branch', $br);
			$db->bind(':sub', $subbr);
			$db->bind(':amount', $netpurchases);
			$db->bind(':reference', $reference);
			$db->bind(':taxpcent', $taxpcent);
			$db->bind(':gsttype', $gsttype);
			$db->bind(':descript1', $descript1);
			$db->execute();
		}
	}	

}


// add uid field
$db->query("alter table ".$findb.".".$glfile." add `uid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY");
$db->execute();

// add account details
$db->query("select uid,accountno,branch,sub from ".$findb.".".$glfile);
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	$id = $uid;
	if ($accountno < 1000) {
		$db->query("select account from ".$findb.".glmast where accountno = ".$accountno." and branch = '".$branch."' and sub = ".$sub);
		$row = $db->single();
		extract($row);
	}
	if ($accountno >= 20000000 && $accountno < 30000000) {
		$db->query("select member as account from ".$cltdb.".client_company_xref where crno = ".$accountno." and crsub = ".$sub." and company_id = ".$coyid);
		$row = $db->single();
		extract($row);
	}
	if ($accountno >= 20000000 && $accountno >= 30000000) {
		$db->query("select member as account from ".$cltdb.".client_company_xref where drno = ".$accountno." and drsub = ".$sub." and company_id = ".$coyid);
		$row = $db->single();
		extract($row);
	}
	$db->query("update ".$findb.".".$glfile." set account = '".$account."' where uid = ".$id);
	$db->execute();
	
}

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

$db->closeDB();

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>VAT Transaction Report</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>


<style type="text/css">
<!--
.style2 {font-size: 12px}
-->
</style>
</head>

<body>


<form name="form1" method="post" action="">

<table width="600" border="0" cellpadding="1" cellspacing="1" align="center">
<tr>
      <td><?php include "getviewgstUK.php"; ?></td>
</tr>

</table>
</form>

</body>
</html>
