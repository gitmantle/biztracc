<?php
session_start();
$dlist = $_SESSION['s_distlist'];

if ($dlist == '') {
	echo '<script>';
	echo 'alert("Please select a distribution list");';
	echo '</script>';
	return;
}

$usersession = $_SESSION['usersession'];

require("../db.php");
$admindb = $_SESSION['s_admindb'];
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$mdb = $_SESSION['s_prcdb'];
$cdb = $_SESSION['s_cltdb'];
$fdb = $_SESSION['s_findb'];

//**********************************************************************************************
// get data for lack of stock and purchase orders
//**********************************************************************************************
$moduledb = $_SESSION['s_prcdb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select distperiod, processed, startdate from distlist where uid = ".$dlist;
$r = mysql_query($q) or die(mysql_error().' '.$q);
$row = mysql_fetch_array($r);
extract($row);
$proc = $processed;
$s = explode('-',$startdate);
$stdate = $s[1].'-'.$s[2];

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

$potable = 'ztmp'.$user_id.'_po';

// get stock required for period and stock on hand
$query = "drop table if exists ".$potable;
$result = mysql_query($query) or die(mysql_error());

$query = "create table ".$potable." ( uid int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, unitsrequired decimal(9,2) default 0, unitsonhand decimal(9,2) default 0,unit varchar(20) default '', noinunit decimal(9,2) default 0,medicine varchar(70) default '',itemcode varchar(25) default '',supplier_id int(11) default 0, supplier varchar(80) default '',phone varchar(30) default '', mobile varchar(30) default '', email varchar(80) default '', toorder decimal(9,2) default 0 )  engine myisam";
$calc = mysql_query($query) or die(mysql_error());

$qm = "select sum(".$mdb.".distmeds.noofunits) as required, ".$fdb.".stkmast.itemcode, ".$fdb.".stkmast.item, ".$fdb.".stkmast.unit, ".$fdb.".stkmast.noinunit, ".$fdb.".stkmast.supplier, sum(".$fdb.".stkmast.onhand) as available, sum(".$fdb.".stkmast.uncosted) as uncosted, sum(".$fdb.".stkmast.onorder) as ordered, ".$cdb.".members.lastname from ".$mdb.".distmeds, ".$fdb.".stkmast, ".$cdb.".members,".$mdb.".distdetail where ".$fdb.".stkmast.itemid = ".$mdb.".distmeds.medicineid and ".$cdb.".members.member_id = ".$fdb.".stkmast.supplier and ".$mdb.".distmeds.distlist_id = ".$dlist." and ".$mdb.".distmeds.distdetail_id = ".$mdb.".distdetail.uid and ".$mdb.".distdetail.checked = 'Yes' group by ".$fdb.".stkmast.itemcode";
$rm = mysql_query($qm) or die(mysql_error().' '.$qm);
while ($row = mysql_fetch_array($rm)) {
	extract($row);
	if ($available < $required) {  // not enough stock
		$placeorder = $required - $available - $uncosted;
		$qi = "insert into ".$potable." (unitsrequired,unitsonhand,unit,noinunit,medicine,itemcode,supplier,supplier_id,toorder) values (".$required.",".$available.",'".$unit."',".$noinunit.",'".$item."','".$itemcode."','".$lastname."',".$supplier.",".$placeorder.")";
		$ri = mysql_query($qi) or die(mysql_error().' '.$qi);
	}
}

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select uid,supplier_id from ".$potable;
$r = mysql_query($q) or die(mysql_error().' '.$q);
$nsrows = mysql_num_rows($r);
while ($row = mysql_fetch_array($r)) {
	extract($row);
	$mid = $supplier_id;
	$id = $uid;
	
	$moduledb = $_SESSION['s_cltdb'];
	mysql_select_db($moduledb) or die(mysql_error());

	$qp = "select concat(country_code,' (',area_code,') ',comm) as ph from comms where member_id = ".$mid." and comms_type_id = 2 and preferred = 'Y'";
	$rp = mysql_query($qp) or die(mysql_error().' '.$qp);
	$numrows = mysql_num_rows($rp);
	if ($numrows == 0) {
		$moduledb = $_SESSION['s_cltdb'];
		mysql_select_db($moduledb) or die(mysql_error());

		$qp = "select concat(country_code,' (',area_code,') ',comm) as ph from comms where member_id = ".$mid." and comms_type_id = 2 limit 1";
		$rp = mysql_query($qp) or die(mysql_error().' '.$qp);
		$nrows = mysql_num_rows($rp);
		if ($nrows == 1) {
			$row = mysql_fetch_array($rp);
			extract($row);
			$p = $ph;
		} else {
			$p = '';	
		}
	} else {
		$row = mysql_fetch_array($rp);
		extract($row);
		$p = $ph;
	}
	
	$moduledb = $_SESSION['s_findb'];
	mysql_select_db($moduledb) or die(mysql_error());

	$qu = "update ".$potable." set phone = '".$p."' where uid = ".$id;
	$ru = mysql_query($qu) or die(mysql_error().' '.$qu);
	
	$moduledb = $_SESSION['s_cltdb'];
	mysql_select_db($moduledb) or die(mysql_error());

	$qm = "select comm as mob from comms where member_id = ".$mid." and comms_type_id = 3 and preferred = 'Y'";
	$rm = mysql_query($qm) or die(mysql_error().' '.$qm);
	$numrows = mysql_num_rows($rm);
	if ($numrows == 0) {
		$qm = "select comm as mob from comms where member_id = ".$mid." and comms_type_id = 3 limit 1";
		$rm = mysql_query($qm) or die(mysql_error().' '.$qm);
		$nrows = mysql_num_rows($rm);
		if ($nrows == 1) {
			$row = mysql_fetch_array($rm);
			extract($row);
			$m = $mob;
		} else {
			$m = '';	
		}
	} else {
		$row = mysql_fetch_array($rm);
		extract($row);
		$m = $mob;
	}
 
	$moduledb = $_SESSION['s_findb'];
	mysql_select_db($moduledb) or die(mysql_error());

	$qu = "update ".$potable." set mobile = '".$m."' where uid = ".$id;
	$ru = mysql_query($qu) or die(mysql_error().' '.$qu);

	$moduledb = $_SESSION['s_cltdb'];
	mysql_select_db($moduledb) or die(mysql_error());

	$qe = "select comm as em from comms where member_id = ".$mid." and comms_type_id = 4 and preferred = 'Y'";
	$re = mysql_query($qe) or die(mysql_error().' '.$qe);
	$numrows = mysql_num_rows($re);
	if ($numrows == 0) {
		$qes = "select comm as em from comms where member_id = ".$mid." and comms_type_id = 4 limit 1";
		$res = mysql_query($qes) or die(mysql_error().' '.$qes);
		$nrows = mysql_num_rows($res);
		if ($nrows == 1) {
			$row = mysql_fetch_array($res);
			extract($row);
			$e = $em;
		} else {
			$e = '';	
		}
	} else {
		$row = mysql_fetch_array($res);
		extract($row);
		$e = $em;
	}
	
	$moduledb = $_SESSION['s_findb'];
	mysql_select_db($moduledb) or die(mysql_error());

	$qu = "update ".$potable." set email = '".$e."' where uid = ".$id;
	$ru = mysql_query($qu) or die(mysql_error().' '.$qu);		

}

//**********************************************************************************************
// get data for statements
//**********************************************************************************************

$moduledb = $_SESSION['s_prcdb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select period,enddate from periods where startdate = '".$stdate."'";
$r = mysql_query($q) or die(mysql_error().' '.$q);
$row = mysql_fetch_array($r);
extract($row);
$edate = $enddate;
$p = $period;
$y = date("Y");
if ($p == 1) {
	$y = $y - 1;
	$qp = "select startdate as s from periods where period = 13";
	$rp = mysql_query($qp) or die(mysql_error().' '.$qp);
	$row = mysql_fetch_array($rp);
	extract($row);
	$curdate = $y.'-'.$s;
	$lstdate = $y.'-'.$edate;
} else {
	$p = $p - 1;
	$qp = "select startdate as s from periods where period = ".$p;
	$rp = mysql_query($qp) or die(mysql_error().' '.$qp);
	$row = mysql_fetch_array($rp);
	extract($row);
	$curdate = $y.'-'.$s;
	$lstdate = $y.'-'.$edate;
}

$drlist = '';
$q = "select  ".$cdb.".client_company_xref.drno from ".$cdb.".client_company_xref,".$mdb.".distdetail where ".$cdb.".client_company_xref.client_id = ".$mdb.".distdetail.member_id and ".$mdb.".distdetail.distlist_id = ".$dlist." and (".$mdb.".distdetail.balance + ".$mdb.".distdetail.ordered) < 0 and ".$mdb.".distdetail.checked = 'Yes'";
$r = mysql_query($q) or die(mysql_error().' '.$q);
while ($row = mysql_fetch_array($r)) {
	extract($row);	
	$drlist .= $drno.',';
}
$drlist = rtrim($drlist,',');

if ($drlist == '') {
	echo '<script>';
	echo 'alert("There are no clients with sufficient funds who have been fully checked and marked as such.");';
	echo '</script>';
	return;
}

$period = 'f';
$range = 'z';
$coyid = $_SESSION['s_coyid'];
$comment = '';
$creditstat = 'Yes';
$overdue = 'No';


$fromdate = date("d/m/Y", strtotime("$curdate"));
$todate = date("d/m/Y", strtotime("$lstdate"));
$fdate = date("Y-m-d", strtotime("$fromdate"));
$tdate = date("Y-m-d", strtotime("$todate"));

$drfile = 'ztmp'.$user_id.'_statements';

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

$query = "drop table if exists ".$drfile;
$result = mysql_query($query) or die(mysql_error());

$query = "create table ".$drfile." (debtor varchar(80) default '', account int(11) default 0, sub int(11) default 0, member_id int(11) default 0, current decimal(16,2) default 0, d30 decimal(16,2) default 0,d60 decimal(16,2) default 0,d90 decimal(16,2) default 0, d120 decimal(16,2) default 0, billing int(11) default 0, email int(11) default 0, sendby char(5) default '')  engine myisam";
$calc = mysql_query($query) or die(mysql_error());

$q = "insert into ".$drfile." select concat(".$cdb.".members.firstname,' ',".$cdb.".members.lastname) as dr, ".$cdb.".client_company_xref.drno,".$cdb.".client_company_xref.drsub, ".$cdb.".client_company_xref.client_id, ".$cdb.".client_company_xref.current, ".$cdb.".client_company_xref.d30, ".$cdb.".client_company_xref.d60, ".$cdb.".client_company_xref.d90, ".$cdb.".client_company_xref.d120, ".$cdb.".client_company_xref.billing, ".$cdb.".client_company_xref.email,".$cdb.".client_company_xref.sendstatement from ".$cdb.".client_company_xref,".$cdb.".members where (".$cdb.".members.member_id = ".$cdb.".client_company_xref.client_id) and (".$cdb.".client_company_xref.drno <> 0) and (".$cdb.".client_company_xref.drno in (".$drlist.")) and (".$cdb.".client_company_xref.company_id = ".$coyid.") and ((".$cdb.".client_company_xref.sendstatement = 'Post' and ".$cdb.".client_company_xref.billing > 0 ) or (".$cdb.".client_company_xref.sendstatement = 'Email' and ".$cdb.".client_company_xref.email > 0) )";

$r = mysql_query($q) or die(mysql_error().' '.$q);

if ($creditstat == 'No') {
	$q = "delete from ".$drfile." where (current + d30 + d60 + d90 + d120) < 0";	
	$r = mysql_query($q) or die(mysql_error().' '.$q);
}

switch ($range) {
	case 'z':
		$q = "delete from ".$drfile." where (current + d30 + d60 + d90 + d120) = 0";	
		$r = mysql_query($q) or die(mysql_error().' '.$q);
		break;
	case 't':
	// determine if there were transactions to that account for the period
		$q = "select uid,account,sub,curent,d30,d60,d90,d120 from ".$drfile;
		$r = mysql_query($q) or die(mysql_error().' '.$q);
		while ($row = mysql_fetch_array($r)) {
			extract($row);
			$ac = $account;
			$sb = $sub;
			$id = $uid;
			$bal = $current+$d30+$d60+$d90+$d120;
			$qt = "select uid from trmain where (ddate >= '".$fdate."' and ddate <= '".$tdate."') and accountno = ".$ac." and sub = ".$sb;
			$rt = mysql_query($qt) or die(mysql_error().' '.$qt);
			if (mysql_num_rows($rt) == 0 && $bal == 0) {
				$q = "delete from ".$drfile." where uid = ".$id;	
				$r = mysql_query($q) or die(mysql_error().' '.$q);
			}
		}
		break;
}


// add uid and address fields
$q = "alter table ".$drfile." add `uid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY";
$r = mysql_query($q) or die(mysql_error().' '.$q);
$q = "alter table ".$drfile." add `street_no` varchar(45),add ad1 varchar(45),add ad2 varchar(45),add suburb varchar(45),add town varchar(45),add postcode varchar(15),add country varchar(45),add address varchar(100)";
$r = mysql_query($q) or die(mysql_error().' '.$q);

// add addresses
$q = "select uid,sendby,billing,email from ".$drfile;
$r = mysql_query($q) or die(mysql_error().' '.$q);
while ($row = mysql_fetch_array($r)) {
	extract($row);
	$adid = $billing;
	$cmid = $email;
	$method = $sendby;
	$id = $uid;
	if ($method == 'Post') {
		$qa = "select ".$cdb.".addresses.street_no,".$cdb.".addresses.ad1,".$cdb.".addresses.ad2,".$cdb.".addresses.suburb,".$cdb.".addresses.town,".$cdb.".addresses.postcode,".$cdb.".addresses.country from ".$cdb.".addresses where ".$cdb.".addresses.address_id = ".$adid;
		$ra = mysql_query($qa) or die(mysql_error().' '.$qa);
		$rowa = mysql_fetch_array($ra);
		extract($rowa);
		$ad = trim($street_no.' '.$ad1.' '.$ad2.' '.$suburb.' '.$town.' '.$postcode);
		$qau = "update ".$drfile." set street_no = '".$street_no."', ad1 = '".$ad1."', ad2 = '".$ad2."', suburb = '".$suburb."', town = '".$town."', postcode = '".$postcode."', country = '".$country."', address = '".$ad."' where uid = ".$id;
		$rau = mysql_query($qau) or die(mysql_error().' '.$qau);
	} else {
		$qe = "select ".$cdb."comms.comm from ".$cdb."comms where ".$cdb."comms.comms_id = ".$cmid;
		$re = mysql_query($qe) or die(mysql_error().' '.$qe);
		$rowe = mysql_fetch_array($re);
		extract($rowe);
		$ad = $comm;
		$qau = "update ".$drfile." set address = '".$ad."' where uid = ".$id;
		$rau = mysql_query($qau) or die(mysql_error().' '.$qau);
	}
}


$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Distribution Processing</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script language="javascript" type="text/javascript" src="../includes/datetimepick/datetimepicker.js"></script>

<script type="text/javascript">

window.name = 'distprocess';

function distinv() {
	var dlist = <?php echo $dlist; ?>;
	$.get("includes/ajaxDistInvoice.php", {dlist: dlist}, 
		  function(data){alert(data);$("#binvoice").hide();$("#distlist").trigger("reloadGrid")																				
	});
	document.getElementById('bdepot').style.visibility = 'visible';
	document.getElementById('bstatement').style.visibility = 'visible';
	document.getElementById('bdepotlabel').style.visibility = 'visible';
	document.getElementById('bdosage').style.visibility = 'visible';
}

function printpos() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('../fin/rep_pos2pdfp.php','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}


function distdepot() {
	var dlist = <?php echo $dlist; ?>;
	
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +375;
	window.open('includes/ajaxDistDepot.php?dlist='+dlist,'enk','toolbar=0,scrollbars=1,height=470,width=800,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
	//$.get("includes/ajaxDistDepot.php", {dlist: dlist}, function(data){});
}

function runstatementsp() {
	var x = 0, y = 0; // default values	
	var comment = "<?php echo $comment; ?>";
	var overdue = "<?php echo $overdue; ?>";
	var fromdate = "<?php echo $fromdate; ?>";
	var todate = "<?php echo $todate; ?>";
	var period = "<?php echo $period; ?>";
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('../fin/rep_stat2pdfp.php?overdue='+overdue+'&period='+period+'&fromdate='+fromdate+'&todate='+todate+'&comment='+comment,'statpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addlabels() {
	var period = "<?php echo $period; ?>";
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addlabels2pdf.php?period='+period,'adlpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function dosagelabels() {
	
}

</script>
</head>
<body>
<form>
<table width="980" align="center" bgcolor="<?php echo $bgcolor; ?>">
	<tr>
    	<td colspan="2" align="center" bgcolor="<?php echo $bghead; ?>"><h3><label style="color: <?php echo $thfont; ?>">Distribution Process for Period <?php echo $distperiod; ?></label></h3></td>
	</tr>
	<tr>
    	<td colspan="2"><?php include "getpo.php"; ?></td>
	</tr>
	<tr>
    	<td colspan="2" class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Once you have sufficient stock, this process will invoice all members who have been checked and have sufficient credit to cover the period's medicine order. It will then provide facility to print all relevant reports and lables. <br />
   	  This process can only be reversed by going through each member's account individually and passing credit notes. <br />
   	  PLEASE ENSURE YOU ARE CONFIDENT YOU WISH TO PROCEED BEFORE PRESSING Create Invoices.</label></td>
	</tr>
	<tr>
    	<td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Invoice Members</label></td>
    	<td><input type="button" name="binvoice" id="binvoice" value="Create Invoices" style="width:250px" onclick="distinv()"/></td>
	</tr>
	<tr>
    	<td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Print Member Requirements per Depot</label></td>
    	<td><input type="button" name="bdepot" id="bdepot" value=" Print Depot Lists" style="width:250px" onclick="distdepot()"/></td>
	</tr>
	<tr>
    	<td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Print Member's Statements</label></td>
    	<td><input type="button" name="bstatement" id="bstatement" value="Print Statements" style="width:250px" onclick="runstatementsp()"/></td>
	</tr>
	<tr>
    	<td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Print Depot Address Labels</label></td>
    	<td><input type="button" name="bdepotlabel" id="bdepotlabel" value="Print Address Labels" style="width:250px" onclick="addlabels()"/></td>
	</tr>
	<tr>
    	<td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Print Dosage Instruction Labels</label></td>
    	<td><input type="button" name="bdosage" id="bdosage" value="Print Dosage Labels" style="width:250px" onclick="dosagelabels()"/></td>
	</tr>

</table>

<script>
	var nr = <?php echo $nsrows; ?>;
	var pr = "<?php echo $proc; ?>";
	
	if (nr == 0) {
		document.getElementById('binvoice').style.visibility = 'visible';
	} else {
		document.getElementById('binvoice').style.visibility = 'hidden';
	}
	if (pr == 'No') {
		document.getElementById('bdepot').style.visibility = 'hidden';
		document.getElementById('bstatement').style.visibility = 'hidden';
		document.getElementById('bdepotlabel').style.visibility = 'hidden';
		document.getElementById('bdosage').style.visibility = 'hidden';
	} else {
		document.getElementById('bdepot').style.visibility = 'visible';
		document.getElementById('bstatement').style.visibility = 'visible';
		document.getElementById('bdepotlabel').style.visibility = 'visible';
		document.getElementById('bdosage').style.visibility = 'visible';
		document.getElementById('binvoice').style.visibility = 'hidden';
	}
</script>

</form>
</body>
</html>