<?php
session_start();

date_default_timezone_set($_SESSION['s_timezone']);

$ln = $_REQUEST['ln'];
$usersession = $_SESSION['usersession'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$rtfile = $_SESSION['s_rtfile'];

switch($rtfile) {
	case "rt1":
		$fl = "z_1rec";
		break;
	case "rt2":
		$fl = "z_2rec";
		break;
	case "rt3":
		$fl = "z_3rec";
		break;
	case "rt4":
		$fl = "z_4rec";
		break;
	case "rt5":
		$fl = "z_5rec";
		break;
	case "rt6":
		$fl = "z_6rec";
		break;
}

$findb = $_SESSION['s_findb'];

$db->query("select * from ".$findb.".".$fl." where uid = ".$ln);
$row = $db->single();
extract($row);
$xreference = $reference;
$xa2d = $a2d;
$xa2c = $a2c;
$xdescript1 = $descript1;
$xtaxtype = $taxtype;
$xamount = $amount;
$xtax = $tax;
$xtotal = $total;

// populate Tax type list
$db->query("select * from ".$findb.".taxtypes");
$rows = $db->resultset();
$tax_options = "<option value=\"\">Select Tax Type</option>";
foreach ($rows as $row) {
	extract($row);
	if ($tax == $xtaxtype) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$tax_options .= '<option value="'.$taxpcent.'#'.$tax.'"'.$selected.'>'.$tax.' - '.$description.'</option>';
}

$db->closeDB();

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Recurring Transaction</title>
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

	window.name = "ed_rec";

function rt_edit() {
	var ln = <?php echo $ln; ?>;
	var amount = toFixed(document.getElementById('newamount').value);
	
	if (amount < 0) {
		alert('Amount may not be negative');
		return false;
	}
	
	var tx = document.getElementById('newtaxtype').value;
	var txx = tx.split('#');
	var taxpcent = txx[0];
	var taxtype = txx[1];
	if(taxpcent == "") {
		alert('Please select a tax type');
		return(false);
	}
	var taxindex =  document.getElementById('newtaxtype').selectedIndex;
	var tax = toFixed(document.getElementById('newtax').value);
	
	var total = toFixed(document.getElementById('newtotal').value);
	if(total == 0) {
		alert('Please input an amount');
		return(false);
	}	
	
	var description = document.getElementById('newdescription').value;

	$.get("includes/ajaxrtEditTrans.php", {descript1:description, amount:amount, taxpcent:taxpcent, tax:tax, taxtype:taxtype, total:total, ln:ln}, function(data){});
	
	window.open("","rtgrid").jQuery("#rtlist").trigger("reloadGrid");
	this.close();

}

function add_tax(amount) {
	var tx = document.getElementById('newtaxtype').value;
	var txx = tx.split('#');
	var taxpcent = txx[0];
	var taxtype = txx[1];
	if (isNaN(taxpcent)) {
		alert('Please choose a tax type');
		return false;
	}
	var tax = toFixed(amount*taxpcent/100);
	document.getElementById('newtax').value = tax;	
	document.getElementById('newtotal').value = parseFloat(amount)+parseFloat(tax);	
}

function deduct_tax(total) {
	var tx = document.getElementById('newtaxtype').value;
	var txx = tx.split('#');
	var taxpcent = txx[0];
	var taxtype = txx[1];
	if (isNaN(taxpcent)) {
		alert('Please choose a tax type');
		return false;
	}
	var taxed = toFixed(total/(1.0 + parseFloat(taxpcent/100)));
	document.getElementById('newtax').value = parseFloat(total)-parseFloat(taxed);
	document.getElementById('newamount').value = toFixed(taxed);	
}
	

</script>
<style type="text/css">
<!--
.style2 {
	font-size: small
}
.style3 {
	font-size: x-small
}
-->
</style>
</head>
<body>
<form name="rtrans" id="rtrans" method="post" >
  <input type="hidden" name="savebutton" id="savebutton" value="N">
  <input type="hidden" name="transtype" id="transtype" value="std">
  <input type="hidden" name="gstinvpay" id="gstinvpay" value=<?php echo $gstinvpay; ?>>
  <table width="950" border="0" cellpadding="3" cellspacing="1" align="center" bgcolor="<?php echo $bgcolor; ?>">
    <tr bgcolor="<?php echo $bghead; ?>">
      <td colspan="5" align="center"><label style="color: <?php echo $thfont; ?>"><strong>Edit Transaction Line </strong></label></td>
    </tr>
    <tr>
      <td class="boxlabel" >Reference</td>
      <td colspan="4"><input type="text" name="ref" id="newref" readonly value="<?php echo $xreference; ?>"></td>
    </tr>
    <tr bgcolor="#CCCCCC">
      <td class="boxlabel" ><label style="color:#000000;">Account to Debit</label></td>
      <td colspan="4"><input type="text" name="ad" id="ad" readonly value="<?php echo $xa2d; ?>"></td>
    <tr bgcolor="#FF9933">
      <td class="boxlabel"><label style="color:#000000;">Account to Credit</label></td>
      <td colspan="4"><input type="text" name="ac" id="ac" readonly value="<?php echo $xa2c; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel">Description</td>
      <td colspan="4"><input name="description" id="newdescription" type="text" value="<?php echo $xdescript1; ?>" size="60" maxlength="60"></td>
    <tr>
      <td class="boxlabel">Type of Tax</td>
      <td><div align="left">
          <select name="taxtype" id="newtaxtype">
            <?php echo $tax_options; ?>
          </select>
        </div></td>
      <td class="boxlabel" width="174">Amount before Tax</td>
      <td ><div align="left">
          <input name="amount" id="newamount" type="text" size="17" maxlength="17" value="<?php echo $xamount; ?>" onfocus="this.select();" onBlur="add_tax(this.value);">
        </div></td>
      <td class="boxlabel">&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabel">Tax</td>
      <td><div align="left">
          <input name="tax" id="newtax" readonly type="text" size="17" maxlength="17" value="<?php echo $xtax; ?>">
        </div></td>
      <td class="boxlabel">Amount after Tax</td>
      <td ><div align="left">
          <input name="total" id="newtotal" type="text" size="17" maxlength="17" value="<?php echo $xtotal; ?>" onfocus=" this.select();" onBlur="deduct_tax(this.value);">
        </div></td>
      <td align="right">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="5"><div align="right">
          <input type="button" value="Save" name="save"  onClick="rt_edit()" >
        </div></td>
    </tr>
  </table>
</form>
</body>
</html>
