<?php
//ini_set('display_errors', true);

require_once("file:///Z|/tom/includes/accesscontrol.php");
require_once("file:///Z|/tom/includes/db2.php");
require_once("file:///Z|/tom/includes/phpfunc.php");
require_once("file:///Z|/tom/inventory/includes/inventoryclass.php");
include_once("file:///Z|/tom/inventory/includes/funcs.php");

if(isset($_REQUEST['cid'])) {
	$companydetails = array_cdetails($_REQUEST['cid']);
	$name = $companydetails['cname']." / ".$companydetails['tname'];
} else {
	$name = "Anonymous";
}


/*session_start();

$coyid = $_SESSION['coyid'];
$br = $_SESSION['branch'];
$sb = $_SESSION['subac'];
$ddate = date("Y-m-d");
$country = $_SESSION['country'];

require_once("file:///Z|/tom/db.php");
mysql_select_db($coyid) or die(mysql_error());*/



if(isset($_POST['Submit'])) {
	mysql_select_db($accdbase) or die(mysql_error());
	$oItem = new inventory;
	
	//There needs to be populated
 	$oItem->dDate = $_POST['ddate'];
 	
 	$dr_type = $_POST['dr_type'];
 	$indexs = $oItem->getIndexs();

	$reftype = ($dr_type == "I") ? "INV" : "C_S";
	$reference = $indexs[$reftype]; //$reference is used in print-section
	$oItem->sRefNo = $reference;

	
	$sql_get_accounts = "SELECT `C_S` FROM `globals`;";
	$rec_get_accounts = mysql_query($sql_get_accounts) or die("get C_S from globals: ".mysql_error());
	$c_s = mysql_fetch_row($rec_get_accounts); 
	$oItem->sAcctNo = ($reftype == "INV") ? $_GET['cid']: $c_s[0];
	$oItem->nBankAcc = $_POST['paytype'];

	$oItem->sOtherPartyRef = "";
	$oItem->sSub = "A";
	$oItem->sCompleted = "true";
	$oItem->sNotes = "";

	//Setting lines info //[0] = qty, [1] = itemuid, [2] = inctax, [3] = price, [4] = totalPrice, [5] = locactionid and [6] = array of serialnumbers
	$items = $_POST['lines'];
	$pos = 0;
	//moving the items in the lines array to the beginning of the array, so there is no gaps
	foreach($items as $i=>$item) {
		if($pos != $i) {
			$items[$pos] = $item;
			unset($items[$i]);
		}
		
		$items[$pos][0] = $items[$pos]['qty'];
		unset($items[$pos]['qty']);
		
		$items[$pos][1] = $items[$pos]['lineitem'];
		unset($items[$pos]['lineitem']);

		$items[$pos][2] = $items[$pos]['tax'] + $items[$pos]['amount'];
		unset($items[$pos]['tax']);

		$items[$pos][3] = $items[$pos]['amount'];
		unset($items[$pos]['amount']);
		
		$items[$pos][4] = $items[$pos]['total'];
		unset($items[$pos]['total']);
		
		$items[$pos][5] = "10"; //Imaging location
		
		$items[$pos][6] = $items[$pos]['serials'];
		unset($items[$pos]['serials']);
			
		$pos++;
	}
	$oItem->ItemsArray = $items;
	//print_r($oItem);
	$sResult = $oItem->addTransaction();
	
	//check transaction went through successfully
	$sc = explode('#@#',$sResult);
	$success = $sc[1];
	$id = $sc[0];
	if (strtoupper($success) == 'F') {
//mysql_select_db($dbase);
		$query = "select error from acctrans where uid = ".$id;
		$result = mysql_query($query) or die (mysql_error());
		$row = mysql_fetch_row($result);
		$error = $row[0];
		echo "<script> alert('The transaction failed - ".$error."');</script>";
//mysql_select_db($accdbase);
	}

	if ($_REQUEST['printyn'] == "on") {
		if(isset($_GET['cid'])) {
			$customer = $oItem->getCompany($_GET['cid']);
		} else {
			$customer = $oItem->getCompany($oItem->sAcctNo);			
		}
		if($reftype != "INV") {
			$_SESSION['notes'] = "Cash Sale";
		} else {
			$_SESSION['notes'] = "";
		}
		$_SESSION['toname'] = $customer['tradingname'];
		if($supplier['addline1'] == "") {
			$_SESSION['toad1'] = $customer['addline2'];
		} else {
			$_SESSION['toad1'] = $customer['addline1'] .", ".$customer['addline2'];	
		}		
		
		$_SESSION['toad2'] = $customer['city'];
		$_SESSION['toad3'] = $customer['state'];
		if($customer['pcode'] != 0) {
			$_SESSION['toad4'] = $customer['pcode'];
		}
		else {
			$_SESSION['toad4'] = " ";			
		}
		$_SESSION['ddate'] = $_REQUEST['ddate'];
		$_SESSION['reference'] = $reference;
		$_SESSION['xref'] = $_REQUEST['xref'];
		$_SESSION['totalamount'] = $_REQUEST['totalamount'];
		$_SESSION['totaltax'] = $_REQUEST['totaltax'];
		$_SESSION['gtotal'] = $_REQUEST['totalamount']+$_REQUEST['totaltax'];
		$_SESSION['header2'] = 'Tax Invoice';
		$_SESSION['template'] = 'pos_template';
		$_SESSION['remmitance'] = 'N';
		
		$linesdetails = array();
		$tradingarray = $_REQUEST['lines'];
		foreach ($tradingarray as $line) {
			//$qty, $itemuid, $tax+$price, $price, $totalPrice, $locationid
			$qty = $line['qty'];
			$inventory = new inventory;
			$inventory->getItem($line['lineitem']);
			$part = $inventory->sItemID;
			$desc = $inventory->sItemName;
			$unit = $inventory->sUnit;
			$price = $line['amount'];
			$tax = $line['tax'];
			$total = $price*$qty;
			
			$taxTotal += $tax*$qty;
			$priceTotal += $price*$qty;
			$totalTotal += ($tax+$price)*$qty;
			//$qtyTotal += $qty;
			
			$linesdetails[] = array($part,$desc,$qty,$price,$unit,$total);
			//$linesdetails[] = array($part,$desc.$desc.$desc.$desc,$qty,$price,$unit,$total);
			/*$amount = $line['amount'];
			$tax = $line['tax'];
			$total = $line['total'];
			
			$oItem->getItem($line['lineitem']);
			$lineitem = $oItem->sItemID." - ".$oItem->sItemName;
			
			$qty = $line['qty'];
			$linesdetails[] = array($lineitem,$qty,$amount,$tax,$total);*/
		}		
		$_SESSION['linedetails'] = $linesdetails;	
		
		//$_SESSION['totalline'] = array('Total','',$_REQUEST['totalamount'],$_REQUEST['totaltax'],$_REQUEST['totalamount']+$_REQUEST['totaltax']);		
			
		$host  = $_SERVER['HTTP_HOST'];
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		$extra = "printtranspdf.php";
		echo "<script> 	window.open('http://$host$uri/$extra','tradingform','toolbar=0,scrollbars=1,innerHeight=540,innerWidth=900,resizable=1,left='+5+',screenX='+5+',top='+265+',screenY='+265);</script>";

	}
} 


//TODO: Get tax list

include_once "../inventory/includes/inventoryclass.php";
$inventory = new inventory;
$tax_options = "<option value=\"\">Select Tax Type</option>";
foreach($inventory->getTaxTypes() as $tax) {
	extract($tax);
	$tax_options .= "<option value=\"".$value."\">".$tax.' - '.$description."</option>";
}

$itemoptions = "<option value=\"\">Select Item</option>";
$locations = $inventory->getItems("", "", "", "");
if($locations) {
	foreach($locations as $item) {
		//print_r($item);
		if($item['stockOnHand'] > 0) {
			$itemoptions .= "<option value=\"".$item['uid']."#@#".number_format($item['sellPrice'],2)."#@#".$item['itemid']."\">".$item['itemname']." (".floatval($item['stockOnHand']).")</option>";
		}
	}
}

mysql_select_db($dbase);
/*$coyid = $_SESSION['coyid'];
$br = $_SESSION['branch'];
$sb = $_SESSION['subac'];
$ddate = date("Y-m-d");
$country = $_SESSION['country'];


mysql_select_db("devtom") or die(mysql_error());



// populate Tax type list
switch ($country) {
	case 'nz':
		$query = "select * from nztaxtypes";
		break;
	case 'au':
		$query = "select * from autaxtypes";
		break;
}
$result = mysql_query($query) or die(mysql_error());
$tax_options = "<option value=\"\">Select Tax Type</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	$tax_options .= "<option value=\"".$taxpcent."\">".$tax.' - '.$description."</option>";
}

// populate list with income accounts
$query = "select accountno,branch,sub,account from glmast where ctrlacc = 'N' and accountno > 0 and accountno < 101 order by accountno,branch,sub";
$result = mysql_query($query) or die($query);
$CRaccountsList = "<option value=\"\">Select Account</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	$CRaccountsList .= "<option value=\"".$accountno."-".$branch."-".$sub."\">".$accountno."-".$branch."-".$sub."  ".$account."</option>";
}

// populate list with cash on hand accounts
$query = "select accountno,branch,sub,account from glmast where accountno = 755 order by accountno,branch,sub";
$result = mysql_query($query) or die($query);
$DRaccountsList = "<option value=\"\">Select Account</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	$DRaccountsList .= "<option value=\"".$accountno."-".$branch."-".$sub."\">".$accountno."-".$branch."-".$sub."</option>";
}*/

?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title>Point of Sale</title>

<link rel="stylesheet" href="file:///Z|/style.css" media="screen" type="text/css">
<LINK REL="Stylesheet" HREF="file:///Z|/tom/includes/dhtmlgoodies_calendar/dhtmlgoodies_calendar.css" TYPE="text/css">
<script src="file:///Z|/tom/includes/dhtmlgoodies_calendar/dhtmlgoodies_calendar.js"></script>
<script src="file:///Z|/tom/includes/accajax.js"></script>

<script src="file:///Z|/tom/inventory/includes/ajax.js"></script>

<script>
var line = 0;
function addtrans() {

	var amount = toFixed(document.getElementById('newamount').value);
	var taxtype = document.getElementById('newtaxtype').options[document.getElementById('newtaxtype').selectedIndex].text.substring(0,3);
	var taxpcent = document.getElementById('newtaxtype').value;
	if(taxpcent == "") {
		alert('Please select a tax type');
		return(false);
	}
	var taxindex =  document.getElementById('newtaxtype').selectedIndex;
	var tax = toFixed(document.getElementById('newtax').value);
	var qty = document.getElementById('qty').value;
	var total = toFixed(document.getElementById('newtotal').value*qty);
	if(total == 0) {
		alert('Please input an amount');
		return(false);
	}	

	var lineitem = document.getElementById('newitem').value;
	var lineitemArr = lineitem.split("#@#", 3);
	var itemcode = lineitemArr[2];
	var itemuid = lineitemArr[0];
	var itemdesc = document.getElementById('newitem').options[document.getElementById('newitem').selectedIndex].text;
	
	
	
	var newLine = document.getElementById('lines').innerHTML;
	
	newLine += "<tr id=\"line" + line + "\">" +
		"<input type=\"hidden\" name=\"lines[" + (line) +"][taxindex]\"  id=\"lines[" + (line) +"][taxindex]\" value=\"" + taxindex + "\">" +	
		"<input type=\"hidden\" name=\"lines[" + (line) +"][taxtype]\"  id=\"lines[" + (line) +"][taxtype]\" value=\"" + taxtype + "\">" +	
		"<input type=\"hidden\" name=\"lines[" + (line) +"][taxpcent]\"  id=\"lines[" + (line) +"][taxpcent]\" value=\"" + taxpcent + "\">" +
		"<input type=\"hidden\" name=\"lines[" + (line) +"][lineitem]\" id=\"lines[" + (line) +"][lineitem]\" value=\"" + itemuid + "\">" +
		"<td width=\"100px\">" +
			"<input value=\"" + itemcode + "\" style=\"text-align: left; width:100%\" readOnly=\"true\">" +
		"</td>" +	
		"<td width=\"243px\">" +
			"<input value=\"" + itemdesc + "\" style=\"text-align: left; width:100%\" readOnly=\"true\">" +
		"</td>" +	
		"<td width=\"60px\" >" +
			"<input name=\"lines[" + (line) +"][qty]\" id=\"lines[" + (line) +"][qty]\" value=\"" + qty + "\" style=\"text-align: right\" size=\"6\" readOnly=\"true\">" +
		"</td>" +
		"<td width=\"103px\" >" +
			"<input name=\"lines[" + (line) +"][amount]\" id=\"lines[" + (line) +"][amount]\" value=\"" + amount + "\" style=\"text-align: right\" size=\"16\" readOnly=\"true\">" +
		"</td>" +
		"<td width=\"85px\" >" +
			"<input name=\"lines[" + (line) +"][tax]\" id=\"lines[" + (line) +"][tax]\" value=\"" + tax + "\" style=\"text-align: right\" size=\"12\" readOnly=\"true\">" +
		"</td>" +		
		"<td width=\"112px\" >" +
			"<input name=\"lines[" + (line) +"][total]\" id=\"lines[" + (line) +"][total]\" value=\"" + total + "\" style=\"text-align: right\" size=\"16\" readOnly=\"true\">" +
		"</td>" +			
		"<td width=\"36px\">" +
			 "<img src=\"../images/edit_icon.jpg\" style=\"cursor: pointer\" onclick=\"editline('" + line + "'); return false;\">" +
		"</td>" +			
		"<td width=\"20px\">" +
			 "<img src=\"../images/delete_icon.gif\" style=\"cursor: pointer\" onclick=\"deleteline('" + line + "'); return false;\">" +
//			 "<input type=\"button\" value=\"Edit\" onclick=\"editline('" + line + "'); return false;\">" +
//			 "<input type=\"button\" value=\"Delete\" onclick=\"deleteline('" + line + "'); return false;\">" +
		"</td>" + 
		"<td width=\"67px\">" +
			 "<div id=\"serialLines" + line + "\"></div>" +
		"</td>" +
	"</tr>";
//alert("serialLines" + line);

	document.getElementById('lines').innerHTML = newLine;

	clickedLine = line;
	ajaxGenerateSerialInputs(itemuid, qty, true);

	var sumamount = 0;
	var sumtax = 0;
	for(var i = 0; i <= line; i++) {
		if( document.getElementById('lines[' + i + '][amount]')) {
		  var qty = document.getElementById('lines[' + i + '][qty]').value
		  sumamount += parseFloat(document.getElementById('lines[' + i + '][amount]').value * qty);
		  sumtax += parseFloat(document.getElementById('lines[' + i + '][tax]').value * qty);
		}
	}
	var sumtotal = parseFloat(sumamount) + parseFloat(sumtax);
	document.getElementById('totalamount').value = toFixed(sumamount);	
	document.getElementById('totaltax').value = toFixed(sumtax);	
	document.getElementById('gtotal').value = toFixed(sumtotal);	
	
	line++;
	
	clear_new_line_fields();
		
	document.getElementById('newddate').focus();	
	
}

function add_tax(amount) {
	var taxpcent = document.getElementById('newtaxtype').value;
	if (isNaN(taxpcent)) {
		alert('Please choose a tax type');
		return false;
	}
	var tax = toFixed(amount*taxpcent/100);
	document.getElementById('newtax').value = tax;	
	document.getElementById('newtotal').value = toFixed(parseFloat(amount)+parseFloat(tax));	
}

function deduct_tax(total) {
	var taxpcent = document.getElementById('newtaxtype').value;
	if (isNaN(taxpcent)) {
		alert('Please choose a tax type');
		return false;
	}
	var taxed = toFixed(total/(1.0 + parseFloat(taxpcent/100)));
	document.getElementById('newtax').value = parseFloat(total)-parseFloat(taxed);
	document.getElementById('newamount').value = toFixed(taxed);	
}
	
function clear_new_line_fields()
{
	document.getElementById('newamount').value = 0;
	document.getElementById('newtaxtype').selectedIndex = 0;
	document.getElementById('newtax').value = 0;
	document.getElementById('newtotal').value = 0;
	document.getElementById('newitem').value = '';
}	
	
function editline(lineno) {
	var amount = document.getElementById('lines[' + lineno + '][amount]').value;
	var taxindex = document.getElementById('lines[' + lineno + '][taxindex]').value;
	var tax = document.getElementById('lines[' + lineno + '][tax]').value;
	var total = document.getElementById('lines[' + lineno + '][total]').value;
	var description = document.getElementById('lines[' + lineno + '][lineitem]').value;

	document.getElementById('newtaxtype').selectedIndex = taxindex;
	document.getElementById('newtax').value = tax;
	document.getElementById('newamount').value = amount;
	document.getElementById('newtotal').value = total;
	document.getElementById('newitem').value = description;
		
	deleteline(lineno);
}

function deleteline(lineno) {
	var sumamount = 0;
	var sumtax = 0;
	document.getElementById('line' + lineno ).innerHTML = ''; 
	for(var i = 0; i <= line; i++) {
		if( document.getElementById('lines[' + i + '][amount]')) {
		  sumamount += parseFloat(document.getElementById('lines[' + i + '][amount]').value);
		  sumtax += parseFloat(document.getElementById('lines[' + i + '][tax]').value);
		}
	}
	var sumtotal = parseFloat(sumamount) + parseFloat(sumtax);
	document.getElementById('totalamount').value = toFixed(sumamount);	
	document.getElementById('totaltax').value = toFixed(sumtax);	
	document.getElementById('gtotal').value = toFixed(sumtotal);	

	document.getElementById('newitem').focus();	
	return false;
}


function toFixed(val) {
	if (isNaN(val)) {
		alert('Must be a number');
	} else {
		return parseFloat(val).toFixed(2);
	}
}

function checktax(taxpcent) {
	var amount = document.getElementById('newamount').value;
	if (isNaN(taxpcent)) {
		alert('Please choose a tax type');
		return false;
	}
	var tax = toFixed(amount*taxpcent/100);
	document.getElementById('newtax').value = tax;	
	document.getElementById('newtotal').value = parseFloat(amount)+parseFloat(tax);		
}

function postTrans() {

	//add validation here if required.
	var paytype = document.getElementById('paytype').value;
	var dr_type_c = document.getElementById('dr_type_C').checked;
	//alert(dr_type);
	//var dr = document.getElementById('newDRaccountsList').value;
	//var cr = document.getElementById('newCRaccountsList').value;
	var ok = 'N';
	
	if (paytype == '' && dr_type_c) {
		alert('Please select a Method of Payment');
		document.getElementById('paytype').focus();
		ok = 'N'
	} else {
		ok = 'Y'
	}	
	/*if (ok == 'Y') {
		if (dr == '') {
			alert('Please select an account to debit');
			document.getElementById('newDRaccountsList').focus();
			ok = 'N'
		} else {
			ok = 'Y'
		}		
	}
	if (ok == 'Y') {
		if (cr == '') {
			alert('Please select an account to credit');
			document.getElementById('newCRaccountsList').focus();
			ok = 'N'
		} else {
			ok = 'Y'
		}		
	}*/		
	if (ok == 'Y') {
		//alert('post line:'+line);
		for(var i = -1; i < line-1; i++) {
			//alert(i);
			if(document.getElementById('lines[' + i + '][6]')) {
				var newStr = '';
				var j = 0;
				for(xxx in document.getElementById('lines[' + i + '][6]').options) {
					//alert(j);
					if(document.getElementById('lines[' + i + '][6]').options[xxx].value && xxx != 0) {
						newStr += "<input type=\"hidden\" name=\"lines[" + (i + 1) + "][serials][" + j + "]\" value=\"" + document.getElementById('lines[' + i + '][6]').options[xxx].value + "\">\n";
						j++;
					}
					//alert(document.getElementById('lines[' + i + '][6]').options[xxx].value);					
				}
				document.getElementById('serialLines' + (i + 1)).innerHTML = newStr;				
			}
		}
		var printpdf = document.getElementById('printyn').value;
		document.getElementById('trans').submit();
		
		/*if (printpdf == 'on') {
			tr2pdf();
		}*/
	}
}

function tr2pdf() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +265;	
	window.open('printtrans.php','transpdf','toolbar=0,scrollbars=1,innerHeight=540,innerWidth=900,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function transferSell(val) {
	var varArr = val.split("#@#", 2);
	var sellPrice = varArr[1];
	document.getElementById('newamount').value = sellPrice;
	add_tax(sellPrice);
}
</script>

<script>
		pathToImages = '../images/';
</script>

<style type="text/css">
<!--
.style1 {font-size: large}
.style2 {font-size: x-large}
-->
</style>
</head>

<body>

<div id="tabs">
	<form name="trans" id="trans" method="post" >
	<input type="hidden" name="Submit" value="true">	

	<table width="890" border="0" cellpadding="3" cellspacing="1" align="center">
	<tr bgcolor="#6699FF">
	  <td colspan="7"> <div class="style1 style1">
	    <div align="center"><u>Point of Sale </u></div>
	  </div></td>
	  <td width="143"><?php echo date("d-m-Y"); ?><input type="hidden" name="ddate" id="newddate" value="<?php echo date("d-m-Y"); ?>"></td>
	</tr>
	<tr>
	  <td width="366" colspan="3"><div align="center"><span class="style2"> <?php echo $name; ?></span></div></td>
	  <td colspan="3">
	    <label>
	    <input type="radio" name="dr_type" id="dr_type_C" value="C" checked="checked">
  Cash Sale</label>
	    <label>
	    <input type="radio" name="dr_type" id="dr_type_I" value="I">
  Invoice</label>
      </td>
      <td>Pay&nbsp;by</td>
	  <td><select name="paytype" id="paytype">
        <option value="">Select payment type</option>
        <option value="CSH">Cash</option>
        <option value="CRD">Credit Card</option>
        <option value="EFT">EFTPOS</option>
        <option value="CHQ">Cheque</option>
            </select></td>
	</tr>
	</table>
	
	<table width="890" border="0" cellpadding="3" cellspacing="1" align="center">
	<tr bgcolor="#6699FF">
	  <td colspan="3"><strong>New Transaction Line Details </strong></td>
	  <td colspan="4">
          <div align="right">
            <input type="button" name="add" id="add" value="Add" onclick="addtrans()">
          </div></td>
	</tr>	  
	<tr>
	  <td colspan="2">Item</td>
	  <td><select name="lineitem" id="newitem" onChange="transferSell(this.value);"><?php echo $itemoptions; ?></select>	    </td>
	  <td><div align="right">Quantity</div></td>
	  <td width="182">
	    <div align="left">
	      <input name="textfield" type="text" size="10" id="qty">
	      </div></td>
	  <td colspan="2">&nbsp;</td>
	</tr>
	<tr>
	  <td colspan="2">Type&nbsp;of&nbsp;Tax </td>
	  <td width="223"><select name="taxtype" id="newtaxtype" onChange="checktax(this.value);">
        <?php echo $tax_options; ?>
      </select></td>
	  <td width="242"><div align="right">Amount before Tax</div></td>
	  <td colspan="2"><input name="amount" id="newamount" type="text" size="17" maxlength="17" onBlur="add_tax(this.value);"></td>
	  <td width="121">&nbsp;</td>
	</tr>
	<tr>
	  <td colspan="2">Tax</td>
	  <td><input name="tax" id="newtax" readonly type="text" size="17" maxlength="17"></td>
	  <td><div align="right">Amount after Tax</div></td>
	  <td colspan="2"><input name="total" id="newtotal" value="0" type="text" size="17" maxlength="17" onBlur="deduct_tax(this.value);"></td>
	  <td>&nbsp;</td>
	</tr>
	</table>	

	
	
	<table id="lines" width="890" border="0" cellpadding="3" cellspacing="1" align="center">
	<tr bgcolor="#6699FF">
	  <td width="100" align="left"><strong>Item Code</strong></td>
	  <td width="243" align="left"><strong>Description</strong></td>
	  <td width="60" align="left"><strong>Qty.</strong></td>
	  <td width="103" align="left"><strong>Amount</strong></td>
	  <td width="85" align="left"><strong>Tax</strong></td>
	  <td width="112"><div align="left"><strong>Line Total</strong></div></td>
	  <td width="36"><strong>Print</strong>	    </td>
	<td width="20"><input type="checkbox" name="printyn" id="printyn" checked></td>
	  <td width="67"><div align="right">
	    <input name="Submit" type="button" onClick="postTrans();" value="Post">
	    </div></td>
	</tr>
	</table>
	
	<table id="lines" width="890" border="0" cellpadding="3" cellspacing="1" align="center">
	<tr>
	  <td width="62" align="right">&nbsp;</td>
	  <td width="143" align="right"><div align="left">
	  </div></td>
	  <td width="201" align="right"><strong>Totals</strong></td>
	  <td width="107" align="right"><div align="left">
	    <input name="totalamount" id="totalamount" type="text" size="10" readonly>
	    </div></td>
	  <td width="88" align="right"><div align="left">
	    <input name="totaltax" id="totaltax" type="text" size="10" readonly>
	    </div></td>
	  <td width="99"><div align="left">
	    <input name="gtotal" id="gtotal" type="text" size="10" readonly>
	    </div></td>	
	  <td width="64">&nbsp;</td>
	  <td width="69">&nbsp;</td>	  
	</tr>
	</table>
	</form>
</div>
</body>
</html>