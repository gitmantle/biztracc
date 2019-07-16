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

$table = 'ztmp'.$user_id.'_gst';

// create temporary gst table
$coyid = $_SESSION['s_coyid'];
$coyname = $_SESSION['s_coyname'];
$fromdate = $_REQUEST['fdt'];
$todate = $_REQUEST['edt'];
$gstno = $_REQUEST['gstno'];

$findb = $_SESSION['s_findb'];

$db->query("select tradtax from ".$findb.".globals");
$row = $db->single();
extract($row);

$heading = "From ".$fromdate." to ".$todate." for ".$tradtax." Registraton No. ".$gstno;
$commit = "Commit ".$tradtax." Figures";

$db->query('select taxpcent from '.$findb.'.taxtypes where uid = 1');
$row = $db->single();
extract($row);
$txpcent = $taxpcent;

$db->query("drop table if exists ".$findb.".".$table);
$db->execute();

$db->query("create table ".$findb.".".$table." (uid integer(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, subject varchar(100), box char(2), amount decimal(16,2) default 0 ) engine myisam"); 
$db->execute();

$db->query("select sum(credit - debit) as gstcollected, sum(grosssales) as totalsales from ".$findb.".trmain where accountno = 870 and ddate >= '".$fromdate."' and ddate <= '".$todate."' and gstrecon != 'Y' and grosssales != 0");
$row = $db->single();
extract($row);
if (is_null($gstcollected)) {$gstcollected = 0;}
if (is_null($totalsales)) {$totalsales = 0;}
$netsales = $totalsales - $gstcollected;

$db->query("select sum(debit - credit) as gstpaid, sum(grosspurchases) as totalpurchases from ".$findb.".trmain where accountno = 870 and ddate >= '".$fromdate."' and ddate <= '".$todate."' and gstrecon != 'Y' and grosspurchases != 0");
$row = $db->single();
extract($row);
if (is_null($gstpaid)) {$gstpaid = 0;}
if (is_null($totalpurchases)) {$totalpurchases = 0;}
$netpurchases = $totalpurchases - $gstpaid;

$db->query("insert into ".$findb.".".$table." (subject,box,amount) values ('Vat due in this period on sales and other outputs',' 1',".$gstcollected.")");
$db->execute();

$db->query("insert into ".$findb.".".$table." (subject,box,amount) values ('Vat due in this period on acquisitions from EC',' 2',0)");
$db->execute();

$totvat = $gstcollected + 0;
$db->query("insert into ".$findb.".".$table." (subject,box,amount) values ('TOTAL VAT due - sum of boxes 1 and 2',' 3',".$totvat.")");
$db->execute();

$db->query("insert into ".$findb.".".$table." (subject,box,amount) values ('Vat reclaimed in this period on purchases and other inputs (including acquisitions from EC)',' 4',".$gstpaid.")");
$db->execute();

$topay = $gstcollected - $gstpaid;
$db->query("insert into ".$findb.".".$table." (subject,box,amount) values ('Net VAT to be paid to Customs or reclaimed','5',".$topay.")");
$db->execute();

$db->query("insert into ".$findb.".".$table." (subject,box,amount) values ('Total value of sales and all other outputs EXCL any VAT','6',".$netsales.")");
$db->execute();

$db->query("insert into ".$findb.".".$table." (subject,box,amount) values ('Total value of purchases and all other inputs EXCL VAT','7',".$netpurchases.")");
$db->execute();

$db->query("insert into ".$findb.".".$table." (subject,box,amount) values ('Total value of all supplies of goods and related costs, excluding any VAT to other EC Member States','8',0)");
$db->execute();

$db->query("insert into ".$findb.".".$table." (subject,box,amount) values ('Total value of acquisitions of goods and related costs excluding any VAT from other EC Member States','9',0)");
$db->execute();

$db->closeDB();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");


?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>GST Report</title>
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
	
	function gst2pdf() {
		var heading = '<?php echo $heading; ?>';
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +265;	
		window.open('gst2pdf.php?gstfile='+gstfile+'&heading='+heading,'gstpdf'+gstfile,'toolbar=0,scrollbars=1,innerHeight=540,innerWidth=900,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	}
	
	function showCalculators() {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
		window.open('../includes/calculators.php','calc','toolbar=0,scrollbars=1,height=500,width=950,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	}

	function viewgstUK(b) {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +265;	
		window.open('viewgstUK.php?gstfile='+gstfile+'&bx='+b+'&fdate='+fdate+'&tdate='+tdate,'vgst'+b,'toolbar=0,scrollbars=1,innerHeight=500,innerWidth=900,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	}
	
	function commitgst() {
		var tdt = document.getElementById('tdate').value;
		var fdt = document.getElementById('fdate').value;
		var r = confirm('If you require a printout of  this form press Cancel, return to the previous screen and obtain the printout. If you have a printout or do not require one, press OK to commit the GST figures')
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

<table width="600" border="0" cellpadding="1" cellspacing="1" align="center">
<tr>
  <td align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>; font-size: 14px;"><?php echo $coyname; ?></label></td>
</tr>
<tr>
  <td align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>; font-size: 14px;"><?php echo $heading; ?></label></td>
</tr>
<tr>
      <td><?php include "getGSTlistingUK.php"; ?></td>
</tr>
<tr>
	<td align="right" bgcolor="<?php echo $bghead; ?>"><input name="Submit" type="button" value="<?php echo $commit; ?>" onClick="commitgst()"></td>
</tr>

</table>
</form>

</body>
</html>
