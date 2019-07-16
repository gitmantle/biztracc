<?php
session_start();

date_default_timezone_set($_SESSION['s_timezone']);

$dt = $_REQUEST['rtdate'];
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

$coyid = $_SESSION['s_coyid'];
$ddate = date("d/m/Y");
$ddateh = date("Y-m-d");
$country = $_SESSION['country'];

$db->query('select * from '.$findb.'.branch order by branchname');
$rows = $db->resultset();
// populate branches list
$branch_options = "<option value=\"\">Select Branch</option>";
foreach ($rows as $row) {
	extract($row);
	$branch_options .= "<option value=\"".$branch."\">".$branchname."</option>";
}

$db->query("select gsttype as gstinvpay from ".$findb.".globals");
$row = $db->single();
extract($row);

// populate Tax type list
$db->query("select * from ".$findb.".taxtypes");
$rows = $db->resultset();
$tax_options = "<option value=\"\">Select Tax Type</option>";
foreach ($rows as $row) {
	extract($row);
	$tax_options .= "<option value=\"".$taxpcent."#".$tax."\">".$tax.' - '.$description."</option>";
}

$db->closeDB();

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add Recurring Transaction</title>
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

	window.name = "add_rec";

function rt_add() {
	
	var ddate = "<?php echo $dt; ?>";

	var ref = document.getElementById('newref').options[document.getElementById('newref').selectedIndex].value;
	var refindex =  document.getElementById('newref').selectedIndex;
	if(ref == "") {
		alert('Please select a transaction type');
		return(false);
	}
	
	var DRaccountsList = document.getElementById('DRaccount').value;
	if(DRaccountsList == "") {
		alert('Please select an account to debit');
		return(false);
	}
	var dracc = DRaccountsList.split("~");
	var a2dr = dracc[1];
	var b2dr = dracc[3];
	var s2dr = dracc[2];
	var n2dr = dracc[0];
	
	var CRaccountsList = document.getElementById('CRaccount').value;
	if(CRaccountsList == "") {
		alert('Please select an account to credit');
		return(false);
	}
	var cracc = CRaccountsList.split("~");
	var a2cr = cracc[1];
	var b2cr = cracc[3];
	var s2cr = cracc[2];
	var n2cr = cracc[0];
	
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
	var reference = document.getElementById('newref').value;
	
	if (a2dr == 870) {
		drgst = 'Y';
	} else {
		drgst = 'N';
	}
	if (a2cr == 870) {
		crgst = 'Y';
	} else {
		crgst = 'N';
	}

	$.get("includes/ajaxrtAddTrans.php", {acc2dr:a2dr, subdr:s2dr, brdr:b2dr, acc2cr:a2cr, subcr:s2cr, brcr:b2cr, ddate:ddate, descript1:description, reference:reference, amount:amount, taxpcent:taxpcent, tax:tax, taxtype:taxtype, total:total, refindex:refindex, taxindex:taxindex, a2d:n2dr, a2c:n2cr, drgst:drgst, crgst:crgst}, function(data){});
	
	window.open("","rtgrid").jQuery("#rtlist").trigger("reloadGrid");
	this.close();

}


function showtax() {
	var dr = document.getElementById('DRaccount').value;
	var adr = dr.split('~');
	var a2dr = adr[1];
	var cr = document.getElementById('CRaccount').value;
	var acr = cr.split('~');
	var a2cr = acr[1];
	var gstinvpay = document.getElementById('gstinvpay').value;
	var ledger = document.getElementById('newbgacc2dr').value;
	if (gstinvpay == 'Invoice') {
		if ((a2dr <= 700 && a2cr > 700) || (a2cr <= 700 && a2dr > 700) || (ledger == 'AS')) {
			document.getElementById('newtaxtype').style.visibility = 'visible';
			document.getElementById('newamount').style.visibility = 'visible';
			document.getElementById('newtax').style.visibility = 'visible';
			document.getElementById('newtotal').style.visibility = 'visible';
		} else {
			document.getElementById('newtaxtype').style.visibility = 'hidden';
			document.getElementById('newtaxtype').selectedIndex = 4;
			document.getElementById('newamount').style.visibility = 'hidden';
			document.getElementById('newtax').style.visibility = 'visible';
			document.getElementById('newtotal').style.visibility = 'visible';
		}
	} else {
		if (((a2dr > 750 && a2dr <= 800) && (a2cr <= 700 || a2cr > 5000)) || (a2cr > 750 && a2cr <= 800) && (a2dr <= 700 || a2dr > 5000)) {
			document.getElementById('newtaxtype').style.visibility = 'visible';
			document.getElementById('newamount').style.visibility = 'visible';
			document.getElementById('newtax').style.visibility = 'visible';
			document.getElementById('newtotal').style.visibility = 'visible';
		} else {
			document.getElementById('newtaxtype').style.visibility = 'hidden';
			document.getElementById('newtaxtype').selectedIndex = 4;
			document.getElementById('newamount').style.visibility = 'hidden';
			document.getElementById('newtax').style.visibility = 'visible';
			document.getElementById('newtotal').style.visibility = 'visible';
		}
	}
	
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
      <td colspan="5"><label style="color: <?php echo $thfont; ?>"><strong>New Transaction Line </strong></label></td>
    </tr>
    <tr>
      <td class="boxlabel" >Reference</td>
      <td colspan="4"><div align="left">
        <select name="ref" id="newref" onChange="ajaxGetRef(this.value); return false;">
          <option value="">Select type</option>
          <option value="CHQ">Cheque</option>
          <option value="DEP">Deposit</option>
          <option value="INV">Invoice</option>
          <option value="C_S">Cash Sale</option>
          <option value="C_P">Cash Purchase</option>
          <option value="PAY">Payment</option>
          <option value="GRN">Goods Received</option>
          <option value="PUR">Credit Purchase</option>
          <option value="SAL">Credit Sale</option>
          <option value="CRD">Credit Card</option>
          <option value="EBK">Electronic Banking</option>
          <option value="REC">Receipt</option>
          <option value="C_N">Credit Note</option>
          <option value="R_T">Return</option>
          <option value="P_C">Petty Cash</option>
          <option value="ADJ">Adjustment</option>
          <option value="TSF">Transfer</option>
          <option value="OTH">Other</option>
        </select>
      </div></td>
    </tr>
    <tr bgcolor="#CCCCCC">
      <td class="boxlabel" ><label style="color:#000000;">Account to Debit</label></td>
      <td><div align="left">
          <select name="bgacc2dr" id="newbgacc2dr" onchange="ajaxGetACList(this.value,'dr'); return false;">
            <option>Select Ledger</option>
            <option value="GL">General Ledger</option>
            <option value="DR">Debtors Ledger</option>
            <option value="CR">Creditors Ledger</option>
          </select>
        </div></td>
      <td colspan="3"><div align="left">
          <input type="text" name="DRaccount" id="DRaccount" size="45" readonly >
          <img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="drsearch" onclick="sboxvisibledr()"> </div></td>
    </tr>
    <tr bgcolor="#FF9933">
      <td class="boxlabel"><label style="color:#000000;">Account to Credit</label></td>
      <td><div align="left">
          <select name="bgacc2cr" id="newbgacc2cr" onchange="ajaxGetACList(this.value,'cr'); return false;">
            <option>Select Ledger</option>
            <option value="GL">General Ledger</option>
            <option value="DR">Debtors Ledger</option>
            <option value="CR">Creditors Ledger</option>
          </select>
        </div></td>
      <td colspan="3"><div align="left">
          <input type="text" name="CRaccount" id="CRaccount" size="45" readonly >
          <img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="crsearch" onclick="sboxvisiblecr()"> </div></td>
    </tr>
    <tr>
      <td class="boxlabel">Description</td>
      <td colspan="4">
          <input name="description" id="newdescription" type="text" onFocus="showtax()" size="60" maxlength="60">
        </td>
    <tr>
      <td class="boxlabel">Type of Tax</td>
      <td><div align="left">
          <select name="taxtype" id="newtaxtype">
            <?php echo $tax_options; ?>
          </select>
        </div></td>
      <td class="boxlabel" width="174">Amount before Tax</td>
      <td ><div align="left">
          <input name="amount" id="newamount" type="text" size="17" maxlength="17" onfocus="this.select();" onBlur="add_tax(this.value);">
        </div></td>
      <td class="boxlabel">&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabel">Tax</td>
      <td><div align="left">
          <input name="tax" id="newtax" readonly type="text" size="17" maxlength="17">
        </div></td>
      <td class="boxlabel">Amount after Tax</td>
      <td ><div align="left">
          <input name="total" id="newtotal" value="0" type="text" size="17" maxlength="17" onfocus="this.select();" onBlur="deduct_tax(this.value);">
        </div></td>
      <td align="right">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="5"><div align="right">
          <input type="button" value="Add" name="save"  onClick="rt_add()" >
        </div></td>
    </tr>
  </table>
  <script>
	document.getElementById('drsearch').style.visibility = 'hidden';
	document.getElementById('crsearch').style.visibility = 'hidden';
	document.getElementById('DRaccount').style.visibility = 'hidden';
	document.getElementById('CRaccount').style.visibility = 'hidden';
	document.getElementById('newtaxtype').style.visibility = 'hidden';
	document.getElementById('newamount').style.visibility = 'hidden';
	document.getElementById('newtax').style.visibility = 'hidden';
	document.getElementById('newtotal').style.visibility = 'hidden';
	</script>
    
  <div id="glselect" style="position:absolute;visibility:hidden;top:104px;left:455px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchgl" size="50" onkeypress="doSearchgl()" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="glclose" onclick="sboxhidegl()">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selectgl.php"; ?></td>
      </tr>
    </table>
  </div>
  
  <div id="drselect" style="position:absolute;visibility:hidden;top:104px;left:455px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchdr" size="50" onkeypress="doSearchdr()" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="drclose" onclick="sboxhidedr()">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selectdr.php"; ?></td>
      </tr>
    </table>
  </div>
  
  <div id="crselect" style="position:absolute;visibility:hidden;top:104px;left:455px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchcr" size="50" onkeypress="doSearchcr()" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="drclose" onclick="sboxhidecr()">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selectcr.php"; ?></td>
      </tr>
    </table>
  </div>
  
  
</form>
</body>
</html>
