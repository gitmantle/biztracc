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

$table = 'ztmp'.$user_id.'_basgst';

// create temporary gst table
$totnoabn = 0;

$coyid = $_SESSION['s_coyid'];
$coyname = $_SESSION['s_coyname'];
$fromdate = $_REQUEST['fdt'];
$todate = $_REQUEST['edt'];
$gstno = $_REQUEST['gstno'];
if (isset($_REQUEST['sadj']) && $_REQUEST['sadj'] != '') {
	$sadj = $_REQUEST['sadj'];
} else {
	$sadj = 0;					
}
if (isset($_REQUEST['padj']) && $_REQUEST['padj'] != '') {
	$padj = $_REQUEST['padj'];
} else {
	$padj = 0;					
}
if (isset($_REQUEST['est']) && $_REQUEST['est'] != '') {
	$totest = $_REQUEST['est'];
} else {
	$totest = 0;					
}

$heading = "From ".$fromdate." to ".$todate." for ABN. ".$gstno;

$findb = $_SESSION['s_findb'];

$db->query('select taxpcent from '.$findb.'.taxtypes where uid = 1');
$row = $db->single();
extract($row);
$txpcent = $taxpcent;

$db->query("drop table if exists ".$findb.".".$table);
$db->execute();
$db->query("create table ".$findb.".".$table." (uid integer(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, box char(3), subject varchar(70), subamount decimal(16,2), amount decimal(16,2) ) engine myisam"); 
$db->execute();

$db->query("select sum(credit - debit) as gstcollected, sum(grosssales) as totalsales from ".$findb.".trmain where accountno = 870 and ddate >= '".$fromdate."' and ddate <= '".$todate."' and gstrecon != 'Y' and grosssales != 0");
$row = $db->single();
extract($row);
if (is_null($gstcollected)) {$gstcollected = 0;}
if (is_null($totalsales)) {$totsales = 0;}

$db->query("select sum(debit - credit) as gstpaid, sum(grosspurchases) as totalpurchases from ".$findb.".trmain where accountno = 870 and ddate >= '".$fromdate."' and ddate <= '".$todate."' and gstrecon != 'Y' and grosspurchases != 0");
$row = $db->single();
extract($row);
if (is_null($gstpaid)) {$totalpurchases = 0;}

$db->query("select sum(credit - debit) as totexp from ".$findb.".trmain where accountno = 870 and gsttype = 'EXP' and ddate >= '".$fromdate."' and ddate <= '".$todate."' and gstrecon != 'Y'");
$row = $db->single();
extract($row);
if (is_null($totexp)) {$totexp = 0;}

$db->query("select sum(credit) as totexp from ".$findb.".trmain where accountno = 870 and gsttype = 'N-T' and ddate >= '".$fromdate."' and ddate <= '".$todate."' and gstrecon != 'Y'");
$row = $db->single();
extract($row);

$db->query("select sum(credit - debit) as tototh from ".$findb.".trmain where accountno = 870 and gsttype = 'EXP' and ddate >= '".$fromdate."' and ddate <= '".$todate."' and gstrecon != 'Y'");
$row = $db->single();
extract($row);

$db->query("select sum(credit) as totinp from ".$findb.".trmain where accountno = 870 and gsttype = 'INP' and ddate >= '".$fromdate."' and ddate <= '".$todate."' and gstrecon != 'Y'");
$row = $db->single();
extract($row);

$db->query("select sum(debit - credit) as totcap from ".$findb.".trmain where accountno = 870 and gsttype = 'CAP' and ddate >= '".$fromdate."' and ddate <= '".$todate."' and gstrecon != 'Y'");
$row = $db->single();
extract($row);

$db->query("select sum(debit) as totpits from ".$findb.".trmain where accountno = 870 and gsttype = 'INP' and ddate >= '".$fromdate."' and ddate <= '".$todate."' and gstrecon != 'Y'");
$row = $db->single();
extract($row);

$db->query("select sum(debit) as totpout from ".$findb.".trmain where accountno = 870 and gsttype = 'N-T' and ddate >= '".$fromdate."' and ddate <= '".$todate."' and gstrecon != 'Y'");
$row = $db->single();
extract($row);

// sales

$toti = $totalsales;

if (is_null($toti)) {$toti = 0;}
$db->query("insert into ".$findb.".".$table." (subject,box,amount) values ('Total sales (including any Tax)','G1',".$toti.")");
$db->execute();

if (is_null($totexp)) {$totexp = 0;}
$db->query("insert into ".$findb.".".$table." (subject,box,subamount) values ('Export sales','G2',".$totexp.")");
$db->execute();

if (is_null($tototh)) {$tototh = 0;}
$db->query("insert into ".$findb.".".$table." (subject,box,subamount) values ('Other Tax-free sales','G3',".$tototh.")");
$db->execute();

if (is_null($totinp)) {$totinp = 0;}
$db->query("insert into ".$findb.".".$table." (subject,box,subamount) values ('Input taxed sales','G4',".$totinp.")");
$db->execute();

$oth = $totexp + $tototh + $totinp;
$db->query("insert into ".$findb.".".$table." (subject,box,amount) values ('G2 + G3 + G4','G5',".$oth.")");
$db->execute();

$difs = $toti - $oth;
$db->query("insert into ".$findb.".".$table." (subject,box,amount) values ('Total sales subject to Tax (G1 - G5)','G6',".$difs.")");
$db->execute();

$db->query("insert into ".$findb.".".$table." (subject,box,amount) values ('Adjustments - if applicable)','G7',".$sadj.")");
$db->execute();

$difa = $difs + $sadj;
$db->query("insert into ".$findb.".".$table." (subject,box,amount) values ('Total sales subject to Tax after adjustment (G6 + G7)','G8',".$difa.")");
$db->execute();

$gstcollected = $difa / ((100 + $txpcent)/10);

$gstc = $gstcollected;
if (is_null($gstc)) {$gstc = 0;}
$db->query("insert into ".$findb.".".$table." (subject,box,amount) values ('Tax on sales','G9',".$gstc.")");
$db->execute();

// purchases

$totp = $totalpurchases;

if (is_null($totcap)) {$totcap = 0;}
$db->query("insert into ".$findb.".".$table." (subject,box,subamount) values ('Capital purchases - inc Tax','G10',".$totcap.")");
$db->execute();

$totncap = $totalpurchases - $totcap;
$db->query("insert into ".$findb.".".$table." (subject,box,subamount) values ('Non capital purchases - inc Tax','G11',".$totncap.")");
$db->execute();

$tpurchases = $totcap + $totncap;
$db->query("insert into ".$findb.".".$table." (subject,box,amount) values ('G10 + G11','G12',".$tpurchases.")");
$db->execute();

if (is_null($totpits)) {$totpits = 0;}
$db->query("insert into ".$findb.".".$table." (subject,box,subamount) values ('Purchases for making input taxed sales','G13',".$totpits.")");
$db->execute();

if (is_null($totpout)) {$totpout = 0;}
$db->query("insert into ".$findb.".".$table." (subject,box,subamount) values ('Purchases without Tax in the price','G14',".$totpout.")");
$db->execute();

$db->query("insert into ".$findb.".".$table." (subject,box,subamount) values ('Estimated purchases for private use','G15',".$totest.")");
$db->execute();

$poth = $totpits + $totpout + $totest;
$db->query("insert into ".$findb.".".$table." (subject,box,amount) values ('G2 + G3 + G4','G16',".$poth.")");
$db->execute();

if (is_null($totp)) {$totp = 0;}
$totp = $tpurchases - $totp;
$db->query("insert into ".$findb.".".$table." (subject,box,amount) values ('Total Purchases subject to Tax (G12 - G16)','G17',".$totp.")");
$db->execute();

$db->query("insert into ".$findb.".".$table." (subject,box,amount) values ('Adjustments - if applicable','G18',".$padj.")");
$db->execute();

$difb = $totp + $padj;
$db->query("insert into ".$findb.".".$table." (subject,box,amount) values ('Total purchases subject to Tax after adjustment (G17 + G18)','G19',".$difb.")");
$db->execute();

$gstpaid = $difb / ((100 + $txpcent)/10);

$gstp = $gstpaid;
if (is_null($gstp)) {$gstp = 0;}
$db->query("insert into ".$findb.".".$table." (subject,box,amount) values ('Tax on purchases','G20',".$gstp.")");
$db->execute();

// Payroll section

$db->query("select sum(debit - credit) as totpay from ".$findb.".trmain where accountno = 500 and sub = 1 and ddate >= '".$fromdate."' and ddate <= '".$todate."'");
$row = $db->single();
extract($row);

$db->query("select sum(debit - credit) as totpaye from ".$findb.".trmain where accountno = 500 and sub = 2 and ddate >= '".$fromdate."' and ddate <= '".$todate."'");
$row = $db->single();
extract($row);

$db->query("select sum(debit - credit) as totothpay from ".$findb.".trmain where accountno = 500 and sub = 3 and ddate >= '".$fromdate."' and ddate <= '".$todate."'");
$row = $db->single();
extract($row);

$totalpay = $totpay + $totpaye + $totothpay;
if (is_null($totalpay)) {$totalpay = 0;}
$db->query("insert into ".$findb.".".$table." (subject,box,amount) values ('Total pay including deductions','W1',".$totalpay.")");
$db->execute();

if (is_null($totpaye)) {$totpaye = 0;}
$db->query("insert into ".$findb.".".$table." (subject,box,amount) values ('PAYG withheld','W2',".$totpaye.")");
$db->execute();

if (is_null($totnoabn)) {$totnoabn = 0;}
$db->query("insert into ".$findb.".".$table." (subject,box,amount) values ('Amounts withheld where no ABN','W4',".$totnoabn.")");
$db->execute();

if (is_null($totothpay)) {$totothpay = 0;}
$db->query("insert into ".$findb.".".$table." (subject,box,amount) values ('Other amounts withheld not in W2 or W4','W3',".$totothpay.")");
$db->execute();

$totwithheld = $totpaye + $totnoabn + $totothpay;
if (is_null($totwithheld)) {$totwithheld = 0;}
$db->query("insert into ".$findb.".".$table." (subject,box,amount) values ('Total amounts withheld','W3',".$totwithheld.")");
$db->execute();

$db->closeDB();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>BAS GST Calculation Worksheet</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>


<script type="text/javascript">
	var fdate = '<?php echo $fromdate; ?>';	
	var tdate = '<?php echo $todate; ?>';	
	var gstfile = '<?php echo $table; ?>';
	
	function bas2pdf() {
		var heading = '<?php echo $heading; ?>';
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +265;	
		window.open('bas2pdf.php?gstfile='+gstfile+'&heading='+heading,'gstpdf'+gstfile,'toolbar=0,scrollbars=1,innerHeight=540,innerWidth=900,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	}
	
	function showCalculators() {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
		window.open('../includes/calculators.php','calc','toolbar=0,scrollbars=1,height=500,width=950,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	}

	function viewgst(b) {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +265;	
		window.open('viewgst.php?gstfile='+gstfile+'&bx='+b+'&fdate='+fdate+'&tdate='+tdate,'vgst'+b,'toolbar=0,scrollbars=1,innerHeight=500,innerWidth=900,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	}
	
	function commitgst() {
		var tdt = document.getElementById('tdate').value;
		var fdt = document.getElementById('fdate').value;
		var r = confirm('If you require a printout of  this form, please do it now before continuing with committing the Tax figures')
		if (r == true) {
			$.get("gstrecon.php", {fdate:fdt,tdate:tdt}, function(data){
				alert(data);
				this.close();
			});
		} else {
			return false;
		}
	}
	

</script>

<style type="text/css">
<!--
.style2 {font-size: 12px}
-->
</style>
</head>

<body>


<form name="form1" method="post" action="">
  <input type="hidden" name="fdate" id="fdate" value=<?php echo $fromdate; ?>>
  <input type="hidden" name="tdate" id="tdate" value=<?php echo $todate; ?>>

<table width="700" border="0" cellpadding="1" cellspacing="1" align="center">
<tr>
  <td align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>; font-size: 14px;"><?php echo $coyname; ?></label></td>
</tr>
<tr>
  <td align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>; font-size: 14px;"><?php echo $heading; ?></label></td>
</tr>
<tr>
      <td><?php include "getBAScalc.php"; ?></td>
</tr>
<tr>
	<td align="right" bgcolor="<?php echo $bghead; ?>"><input name="Submit" type="button" value="Commit Tax figures" onClick="commitgst()"></td>
</tr>

</table>
</form>

</body>
</html>
