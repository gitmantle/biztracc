<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];

date_default_timezone_set($_SESSION['s_timezone']);

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

$hdate = date('Y-m-d');
$ttime = strftime("%H:%M", time());

$findb = $_SESSION['s_findb'];
$cltdb = $_SESSION['s_cltdb'];

$db->query("select * from ".$findb.".numbers");
$row = $db->single();
extract($row);

$db->query("select * from ".$findb.".globals");
$row = $db->single();
extract($row);
$coy = $coyname;

$mbedate = date("d/m/Y", strtotime($bedate));
$myrdate = date("d/m/Y", strtotime($yrdate));
$mlstatdt = date("d/m/Y", strtotime($lstatdt));
$mpstatdt = date("d/m/Y", strtotime($pstatdt));
$mbedateh = $bedate;
$myrdateh = $yrdate;
$mlstatdth = $lstatdt;
$mpstatdth = $pstatdt;

// populate stock options
    $arr = array('No', 'Yes');
	$stock_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $stock) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$stock_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate gst period options
    $arr = array('Not Registered','1 Month', '2 Months', '3 Months', '6 Months', 'Annually');
	$gstperiod_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $gstperiod) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$gstperiod_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate trading tax type options
    $arr = array('GST', 'VAT', 'ABN');
	$ttt_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $tradtax) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$ttt_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}
	
// populate GST payment options
    $arr = array('Invoice', 'Payment');
	$gstpayment_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $gsttype) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$gstpayment_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}
	
// populate GST payment options
    $arr = array('0', '5', '10');
	$round_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $roundto) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$round_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

$db->closeDB();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Setup</title>
<link rel="stylesheet" type="text/css" href="../includes/flatpickr_date/dist/flatpickr.min.css">
<script src="../includes/flatpickr_date/dist/flatpickr.js"></script>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script>

window.name = "hs_setup";


/**
 * DHTML date validation script. Courtesy of SmartWebby.com (http://www.smartwebby.com/dhtml/)
 */
// Declaring valid date character, minimum year and maximum year
var dtCh= "/";
var minYear=1900;
var maxYear=2100;


function isInteger(s){
	var i;
    for (i = 0; i < s.length; i++){   
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    // All characters are numbers.
    return true;
}

function stripCharsInBag(s, bag){
	var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++){   
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}

function daysInFebruary (year){
	// February has 29 days in any year evenly divisible by four,
    // EXCEPT for centurial years which are not also divisible by 400.
    return (((year % 4 == 0) && ( (!(year % 100 == 0)) || (year % 400 == 0))) ? 29 : 28 );
}
function DaysArray(n) {
	for (var i = 1; i <= n; i++) {
		this[i] = 31
		if (i==4 || i==6 || i==9 || i==11) {this[i] = 30}
		if (i==2) {this[i] = 29}
   } 
   return this
}

function isDate(dtStr){
	var daysInMonth = DaysArray(12)
	var pos1=dtStr.indexOf(dtCh)
	var pos2=dtStr.indexOf(dtCh,pos1+1)
	var strDay=dtStr.substring(0,pos1)
	var strMonth=dtStr.substring(pos1+1,pos2)
	var strYear=dtStr.substring(pos2+1)
	strYr=strYear
	if (strDay.charAt(0)=="0" && strDay.length>1) strDay=strDay.substring(1)
	if (strMonth.charAt(0)=="0" && strMonth.length>1) strMonth=strMonth.substring(1)
	for (var i = 1; i <= 3; i++) {
		if (strYr.charAt(0)=="0" && strYr.length>1) strYr=strYr.substring(1)
	}
	month=parseInt(strMonth)
	day=parseInt(strDay)
	year=parseInt(strYr)
	if (pos1==-1 || pos2==-1){
		alert("The date format should be : dd/mm/yyyy")
		return false
	}
	if (strMonth.length<1 || month<1 || month>12){
		alert("Please enter a valid month")
		return false
	}
	if (strDay.length<1 || day<1 || day>31 || (month==2 && day>daysInFebruary(year)) || day > daysInMonth[month]){
		alert("Please enter a valid day")
		return false
	}
	if (strYear.length != 4 || year==0 || year<minYear || year>maxYear){
		alert("Please enter a valid 4 digit year between "+minYear+" and "+maxYear)
		return false
	}
	if (dtStr.indexOf(dtCh,pos2+1)!=-1 || isInteger(stripCharsInBag(dtStr, dtCh))==false){
		alert("Please enter a valid date")
		return false
	}
return true
}

function ValidateDate(dt){
	if (isDate(dt)==false){
		dt.focus()
		return false
	}
    return true
 }

// function hideAll()
//  hides a bunch of divs
//

function hideAllm() {
   changeObjectVisibility("details","hidden");
   changeObjectVisibility("gst","hidden");
   changeObjectVisibility("dates","hidden");
   changeObjectVisibility("referencenos","hidden");
   changeObjectVisibility("stock","hidden");
   changeObjectVisibility("recurring","hidden");
}

function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}



// function getStyleObject(string) -> returns style object
//  given a string containing the id of an object
//  the function returns the stylesheet of that object
//  or false if it can't find a stylesheet.  Handles
//  cross-browser compatibility issues.
//
function getStyleObject(objectId) {
  // checkW3C DOM, then MSIE 4, then NN 4.
  //
  if(document.getElementById && document.getElementById(objectId)) {
	return document.getElementById(objectId).style;
   }
   else if (document.all && document.all(objectId)) {  
	return document.all(objectId).style;
   } 
   else if (document.layers && document.layers[objectId]) { 
	return document.layers[objectId];
   } else {
	return false;
   }
}


function changeObjectVisibility(objectId, newVisibility) {
    // first get a reference to the cross-browser style object 
    // and make sure the object exists
    var styleObject = getStyleObject(objectId);
    if(styleObject) {
	styleObject.visibility = newVisibility;
	return true;
    } else {
	// we couldn't find the object, so we can't change its visibility
	return false;
    }
}

function switchDivm(div_id,cell) {
  var style_sheet = getStyleObject(div_id);
  
  if (style_sheet)  {
	hideAllm();
    changeObjectVisibility(div_id,"visible");
  }
}


function post() {

	//add validation here if required.
	var lname = document.getElementById('coyname').value;
	var ok = "Y";
	if (lname == "") {
		alert("Please enter a Company name.");
		ok = "N";
		return false;
	}
	
	if (ok == "Y") {
		document.getElementById('savebutton').value = "Y";
		document.getElementById('edsetup').submit();
	}
	
	
}

function addgst() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +275;
	window.open('hs_addgst.php','adgst','toolbar=0,scrollbars=1,height=350,width=800,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editgst(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +275;
	window.open('hs_editgst.php?uid='+uid,'edgst','toolbar=0,scrollbars=1,height=350,width=800,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

</script>
<!-- Deluxe Tabs -->
<noscript>
<a href="http://deluxe-tabs.com">Javascript Tabs Menu by Deluxe-Tabs.com</a>
</noscript>
<script type="text/javascript" src="tabs/client_tabs.files/dtabs.js"></script>
<!-- (c) 2009, http://deluxe-tabs.com -->
<style type="text/css">
<!--
.style1 {
	font-size: large
}
.star {
	color: #F00;
}
-->
</style>
</head><body>
<form name="edsetup" id="edsetup" method="post">
  <input type="hidden" name="savebutton" id="savebutton" value="N">
  <table width="880" border="0" align="left">
    <tr bgcolor="<?php echo $bghead; ?>">
      <td class="boxlabelleft"><label style="color: <?php echo $thfont; ?> "><strong>Setup accounting details and parameters for <?php echo $coyname; ?></strong></label></td>
    </tr>
  </table>
  <table width="880" border="0" align="left">
    <tr>
      <td><script type="text/javascript" src="tabs/setup_tabs.js"></script></td>
    </tr>
    <tr>
      <td><div id="details" style="position:absolute;visibility:hidden;top:50px;left:3px;height:300px;width:860px;background-color:<?php echo $bgcolor; ?>;" >
          <table width="700" border="0" align="center" cellpadding="3" cellspacing="1">
            <tr>
              <td class="boxlabel">Company name</td>
              <td colspan="2"><input type="text" name="coyname" id="coyname" value="<?php echo $coyname; ?>" size="45" maxlength="45">
                <span class="star">*</span></td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td class="boxlabel">Type of Business - line 1 </td>
              <td><input type="text" name="bustype1"  value="<?php echo $bustype1; ?>" size="40" ></td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td class="boxlabel">line 2</td>
              <td><input name="bustype2" type="text" size="40" value="<?php echo $bustype2; ?>"></td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr valign="top">
              <td class="boxlabel">Main Branch</td>
              <td ><input name="branchname" value="<?php echo $branchname; ?>" type="text" size="45" maxlength="45"></td>
              <td class="boxlabel">Branch code</td>
              <td class="boxlabelleft" >1000</td>
            </tr>
            <tr>
              <td class="boxlabel">Street address</td>
              <td><input name="ad1" type="text" size="30" value="<?php echo $ad1; ?>"></td>
              <td class="boxlabel">PO Box</td>
              <td><input name="boxno" type="text" size="15" value="<?php echo $boxno; ?>"></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td><input name="ad2" type="text" size="30" value="<?php echo $ad2; ?>"></td>
              <td class="boxlabel">Post Office</td>
              <td><input name="po" type="text" size="15" value="<?php echo $po; ?>"></td>
            </tr>
            <tr>
              <td class="boxlabel">City</td>
              <td><input name="ad3" type="text" size="30" value="<?php echo $ad3; ?>"></td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td class="boxlabel">Phone</td>
              <td><input name="telno" type="text" size="15" value="<?php echo $telno; ?>"></td>
            </tr>
            <tr>
              <td class="boxlabel">email</td>
              <td><input name="email" type="text" size="40" value="<?php echo $email; ?>"></td>
              <td class="boxlabel">Fax</td>
              <td><input name="faxno" type="text" size="15" value="<?php echo $faxno; ?>"></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td align="right"><input type="button" value="Save" name="save" onclick="post()"></td>
            </tr>
          </table>
        </div>
        <div id="gst"  style="position:absolute;visibility:visible;top:50px;left:3px;height:360px;width:860px;background-color:<?php echo $bgcolor; ?>;" >
          <table width="780" border="0" align="center" cellpadding="3" cellspacing="1">
			<tr>
            	<td class="boxlabel">Type of Trading Tax</td>
                <td colspan="4"><select name="ltt" id="ltt"><?php echo $ttt_options; ?></select></td>
            </tr>
          	<tr>
            <td class="boxlabel">GST/VAT/ABN Number</td>
              <td colspan="4"><input name="gstno" value="<?php echo $gstno;?>" type="text" id="gstno" size="15" maxlength="15"></td>
            </tr>
            <tr>
              <td class="boxlabel">GST/VAT/ABN Pay Period</td>
              <td colspan="4"><select name="gstperiod">
                  <?php echo $gstperiod_options; ?></select></td>
            </tr>
            <tr>
              <td class="boxlabel">Pay GST/VAT/ABN on </td>
              <td colspan="4"><select name="paygst" id="paygst">
					<?php echo $gstpayment_options; ?></select></td>
            </tr>
            <tr>
              <td class="boxlabel">GST/VAT/ABN Types</td>
              <td colspan="4"><?php include "getgst.php"; ?></td>
            </tr>
            <tr>
              <td colspan="2">&nbsp;</td>
              <td>&nbsp;</td>
              <td align="right"><input type="submit" name="save" value="Save" onclick="post()">
            </tr>
          </table>
        </div>
        <div id="dates" style="position:absolute;visibility:hidden;top:50px;left:3px;height:300px;width:860px;background-color:<?php echo $bgcolor; ?>;" >
          <table width="700" border="0" align="center" cellpadding="3" cellspacing="1">
            <tr>
              <td class="boxlabel">Enter the first date of the financial year </td>
              <td colspan="2"><input type="Text" id="bedate" name="bedate" maxlength="25" size="25" value="<?php echo $mbedateh; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y"></td>
            </tr>
            <tr>
              <td class="boxlabel">Enter the last date of the financial year </td>
              <td colspan="2"><input type="Text" id="yrdate" name="yrdate" maxlength="25" size="25" value="<?php echo $myrdateh; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y"></td>
            <tr>
              <td class="boxlabel">Enter the date for the last Statement run </td>
              <td colspan="2"><input type="Text" id="lstatdt" name="lstatdt" maxlength="25" size="25" value="<?php echo $mlstatdth; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y"></td>
            </tr>
            <tr>
              <td class="boxlabel">Enter the date for the previous Statement run </td>
              <td colspan="2"><input type="Text" id="pstatdt" name="pstatdt" maxlength="25" size="25" value="<?php echo $mpstatdt; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y"></td>
            </tr>
            <tr>
              <td align="right" nowrap>&nbsp;</td>
              <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
              <td class="boxlabel" nowrap>Allow transactions in new tax year before finalising previous tax year? </td>
              <td colspan="2"><table width="200">
                  <tr>
                    <td width="91"><label>
                        <input type="radio" name="allowtrans" <?php if($allowtrans == "Y") {echo "CHECKED";}?> value="Y">
                        Yes</label></td>
                    <td width="97"><input type="radio" name="allowtrans" <?php if($allowtrans == "N") {echo "CHECKED";}?> value="N">
                      No</td>
                  </tr>
                </table></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td align="right"><input type="button" value="Save" name="save" onclick="post()"></td>
            </tr>
          </table>
        </div>
        <div id="referencenos" style="position:absolute;visibility:hidden;top:50px;left:3px;height:400px;width:860px;background-color:<?php echo $bgcolor; ?>;" >
          <table width="750" border="0" align="center" cellpadding="3" cellspacing="1">
            <tr>
              <td class="boxlabelleft">Invoice</td>
              <td><input name="inv" type="text" value="<?php echo $inv; ?>" size="15">
                </td>
              <td class="boxlabelleft">Purchase - Non Stock</td>
              <td><input name="pur" type="text" value="<?php echo $pur; ?>" size="15"></td>
            </tr>
            <tr>
              <td class="boxlabelleft">Cash Sale -Stock</td>
              <td>
                  <input name="c_s" type="text" value="<?php echo $c_s; ?>" size="15">
                </td>
              <td class="boxlabelleft">Cheque</td>
              <td><input name="chq" type="text" value="<?php echo $chq; ?>" size="15"></td>
            </tr>
            <tr>
              <td  class="boxlabelleft">Goods Received</td>
              <td>
                  <input name="grn" type="text" value="<?php echo $grn; ?>" size="15">
                </td>
              <td class="boxlabelleft">Deposit</td>
              <td><input name="dep" type="text" value="<?php echo $dep; ?>" size="15"></td>
            </tr>
            <tr>
              <td class="boxlabelleft">Credit Note - Stock </td>
              <td>
                  <input name="crn" type="text" value="<?php echo $crn; ?>" size="15">
                </td>
              <td class="boxlabelleft">Petty Cash </td>
              <td><input name="p_c" type="text" value="<?php echo $p_c; ?>" size="15"></td>
            </tr>
            <tr>
              <td class="boxlabelleft">Goods Returned</td>
              <td>
                  <input name="ret" type="text" value="<?php echo $ret; ?>" size="15">
                </td>
              <td class="boxlabelleft">Requisition</td>
              <td><input name="req" type="text" value="<?php echo $req; ?>" size="15"></td>
            </tr>
            <tr>
              <td class="boxlabelleft">Credit Card </td>
              <td>
                  <input name="crd" type="text" value="<?php echo $crd; ?>" size="15">
                </td>
              <td class="boxlabelleft">Adjustment</td>
              <td><input name="adj" type="text" value="<?php echo $adj; ?>" size="15"></td>
            </tr>
            <tr>
              <td class="boxlabelleft">Electronic Banking In</td>
              <td >
                  <input name="ebi" type="text" value="<?php echo $ebi; ?>" size="15">
                </td>
              <td class="boxlabelleft">Transfer</td>
              <td><input name="tsf" type="text" value="<?php echo $tsf; ?>" size="15"></td>
            </tr>
            <tr>
              <td class="boxlabelleft">Electronic Banking Out</td>
              <td><input name="ebo" type="text" value="<?php echo $ebo; ?>" size="15"></td>
              <td class="boxlabelleft">Journal</td>
              <td><input name="jnl" type="text" value="<?php echo $jnl; ?>" size="15"></td>
            </tr>
            <tr>
              <td class="boxlabelleft">Receipt</td>
              <td><input name="rec" type="text" value="<?php echo $rec; ?>" size="15"></td>
              <td class="boxlabelleft">Other</td>
              <td><input name="oth" type="text" value="<?php echo $oth; ?>" size="15"></td>
            </tr>
            <tr>
              <td class="boxlabelleft">Payment</td>
              <td><input name="pay" type="text" value="<?php echo $pay; ?>" size="15"></td>
              <td class="boxlabelleft">Credit Note - Non Stock </td>
              <td><input name="c_n" type="text" value="<?php echo $c_n; ?>" size="15"></td>
            </tr>
            <tr>
              <td class="boxlabelleft">Sale - Non Stock</td>
              <td><input name="sal" type="text" value="<?php echo $sal; ?>" size="15"></td>
              <td class="boxlabelleft">Goods Returned - Non Stock</td>
              <td><input name="r_t" type="text" value="<?php echo $r_t; ?>" size="15"></td>
            </tr>
            <tr>
              <td class="boxlabelleft">Direct Debit</td>
              <td><input name="dd" type="text" value="<?php echo $d_d; ?>" size="15"></td>
              <td class="boxlabelleft">Debit Card</td>
              <td><input name="dc" type="text" value="<?php echo $d_c; ?>" size="15"></td>
            </tr>
            <tr>
              <td class="boxlabelleft">Stop Order</td>
              <td><input name="so" type="text" value="<?php echo $spo; ?>" size="15"></td>
              <td>&nbsp;</td>
              <td align="right"><input type="button" value="Save" name="save2" onClick="post()"></td>
            </tr>
          </table>
        </div>
        <div id="stock" style="position:absolute;visibility:hidden;top:50px;left:3px;height:300px;width:860px;background-color:<?php echo $bgcolor; ?>;" >
          <table width="700" border="0" align="center" cellpadding="3" cellspacing="1">
            <tr>
              <td class="boxlabel" nowrap>Trading bank account </td>
              <td><input name="tbankac" id="tbankac" type="text" size="4" maxlength="3" value="<?php echo $trdbankacc; ?>"></td>
              <td class="boxlabel">Branch</td>
              <td><input name="tbankbr" id="tbankbr" type="text" size="4" maxlength="3" value="<?php echo $trdbankbr; ?>"></td>
              <td class="boxlabel">Sub</td>
              <td><input name="tbanksb" id="tbanksb" type="text" size="3" maxlength="2" value="<?php echo $trdbanksub; ?>"></td>
            </tr>
            <tr>
              <td class="boxlabel" nowrap>Cash on hand account </td>
              <td><input name="cohac" id="cohac" type="text" size="4" maxlength="3" value="<?php echo $cashacc; ?>"></td>
              <td class="boxlabel">Branch</td>
              <td><input name="cohbr" id="cohbr" type="text" size="4" maxlength="4" value="<?php echo $cashbr; ?>"></td>
              <td class="boxlabel">Sub</td>
              <td><input name="cohsb" id="cohsb" type="text" size="3" maxlength="2" value="<?php echo $cashsb; ?>"></td>
            </tr>
            <tr>
              <td class="boxlabel" nowrap>Credit card account </td>
              <td><input name="crdac" id="crdac" type="text" size="4" maxlength="3" value="<?php echo $credcardacc; ?>"></td>
              <td class="boxlabel">Branch</td>
              <td><input name="crdbr" id="crdbr" type="text" size="4" maxlength="4" value="<?php echo $credcardbr; ?>"></td>
              <td class="boxlabel">Sub</td>
              <td><input name="crdsb" id="crdsb" type="text" size="3" maxlength="2" value="<?php echo $credcardsub; ?>"></td>
            </tr>
            <tr>
              <td class="boxlabel" nowrap>% markup on Cash Sales</td>
              <td><input name="mkup" id="mkup" type="text" size="10" maxlength="5" value="<?php echo $c_s_markup; ?>"></td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td class="boxlabel" nowrap>For Cash Sales round to nearest:-</td>
              <td colspan="3"><select name="rnd" id="rnd">
					<?php echo $round_options; ?></select>
              &nbsp; cents (select 0 for None)</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td align="right" colspan="6">
                  <input type="button" value="Save" name="save" onclick="post()">
                </td>
            </tr>
          </table>
        </div>
        <div id="recurring" style="position:absolute;visibility:hidden;top:50px;left:3px;height:300px;width:860px;background-color:<?php echo $bgcolor; ?>;" >
          <table width="700" border="0" align="center" cellpadding="3" cellspacing="1">
            <tr>
              <td class="boxlabel">Name of first recurring transaction file </td>
              <td colspan="2"><input name="z_rt1" type="text" value="<?php echo $z_rt1; ?>" size = "45" maxlength="45"></td>
            </tr>
            <tr>
              <td class="boxlabel">Name of second recurring transaction file</td>
              <td colspan="2"><input name="z_rt2" type="text" value="<?php echo $z_rt2; ?>"  size = "45" maxlength="45"></td>
            </tr>
            <tr>
              <td class="boxlabel">Name of third recurring transaction file</td>
              <td colspan="2"><input name="z_rt3" type="text" value="<?php echo $z_rt3; ?>"  size = "45" maxlength="45"></td>
            </tr>
            <tr>
              <td class="boxlabel">Name of fourth recurring transaction file </td>
              <td colspan="2"><input name="z_rt4" type="text" value="<?php echo $z_rt4; ?>" size = "45" maxlength="45"></td>
            </tr>
            <tr>
              <td class="boxlabel">Name of fifth recurring transaction file</td>
              <td colspan="2"><input name="z_rt5" type="text" value="<?php echo $z_rt5; ?>"  size = "45" maxlength="45"></td>
            </tr>
            <tr>
              <td class="boxlabel">Name of sixth recurring transaction file</td>
              <td colspan="2"><input name="z_rt6" type="text" value="<?php echo $z_rt6; ?>"  size = "45" maxlength="45"></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td align="right"><input type="button" value="Save" name="save" onclick="post()"></td>
            </tr>
          </table>
        </div></td>
    </tr>
  </table>
  <script>
		//hideAllm();
		switchDivm('details','ge');
    </script>
  <script>document.onkeypress = stopRKey;</script>
  
  <script>
 	document.getElementById("bedate").flatpickr({
		onChange: function(dateObj, dateStr, instance) {
		}
	});
 </script>
 <script>
 	document.getElementById("yrdate").flatpickr({
		onChange: function(dateObj, dateStr, instance) {
		}
	});
 </script>
 <script>
 	document.getElementById("lstatdt").flatpickr({
		onChange: function(dateObj, dateStr, instance) {
		}
	});
 </script>
 <script>
 	document.getElementById("pstatdt").flatpickr({
		onChange: function(dateObj, dateStr, instance) {
		}
	});
 </script>
             
  
</form>
<?php

	if($_REQUEST['savebutton'] == "Y") {
		
		$scoyname = $_REQUEST['coyname'];
		$sbustype1 = $_REQUEST['bustype1'];
		$sbustype2 = $_REQUEST['bustype2'];
		$sad1 = $_REQUEST['ad1'];
		$sad2 = $_REQUEST['ad2'];
		$sad3 = $_REQUEST['ad3'];
		$sboxno = $_REQUEST['boxno'];
		$spo = $_REQUEST['po'];
		$stelno = $_REQUEST['telno'];
		$semail = $_REQUEST['email'];
		$sfaxno = $_REQUEST['faxno'];
		$sgstno = $_REQUEST['gstno'];
		$sgstpcent = 0;
		$sgstperiod = $_REQUEST['gstperiod'];
		$spaygst = $_REQUEST['paygst'];
		$srnd = $_REQUEST['rnd'];
		$ltt = $_REQUEST['ltt'];
		
		  $ddate = $_REQUEST['bedate'];		  
		  $sbedate = $ddate;
		

		  $ddate = $_REQUEST['yrdate'];		  
		  $syrdate = $ddate;
		

		  $ddate = $_REQUEST['lstatdt'];		  
		  $slstatdt = $ddate;
		
		
		  $ddate = $_REQUEST['pstatdt'];		  
		  $spstatdt = $ddate;
		
		$sallowtrans = $_REQUEST['allowtrans'];
		$sinv = $_REQUEST['inv'];
		$schq = $_REQUEST['chq'];
		$sc_s = $_REQUEST['c_s'];
		$sdep = $_REQUEST['dep'];
		$sgrn = $_REQUEST['grn'];
		$sp_c = $_REQUEST['p_c'];
		$scrn = $_REQUEST['crn'];
		$sreq = $_REQUEST['req'];
		$sret = $_REQUEST['ret'];
		$sadj = $_REQUEST['adj'];
		$scrd = $_REQUEST['crd'];
		$stsf = $_REQUEST['tsf'];
		$sebi = $_REQUEST['ebi'];
		$sebo = $_REQUEST['ebo'];
		$sjnl = $_REQUEST['jnl'];
		$srec = $_REQUEST['rec'];
		$soth = $_REQUEST['oth'];
		$spay = $_REQUEST['pay'];
		$sc_n = $_REQUEST['c_n'];
		$ssal = $_REQUEST['sal'];
		$sr_t = $_REQUEST['r_t'];
		$spur = $_REQUEST['pur'];
		$sd_d = $_REQUEST['dd'];
		$sd_c = $_REQUEST['dc'];
		$sspo = $_REQUEST['so'];
		$sbranchname = $_REQUEST['branchname'];
		$trbankac = $_REQUEST['tbankac'];
		$trbankbr = $_REQUEST['tbankbr'];
		$trbanksb = $_REQUEST['tbanksb'];
		$cohac = $_REQUEST['cohac'];
		$cohbr = $_REQUEST['cohbr'];
		$cohsb = $_REQUEST['cohsb'];
		$crdac = $_REQUEST['crdac'];
		$crdbr = $_REQUEST['crdbr'];
		$crdsb = $_REQUEST['crdsb'];
		$sz_rt1 = $_REQUEST['z_rt1'];
		$sz_rt2 = $_REQUEST['z_rt2'];
		$sz_rt3 = $_REQUEST['z_rt3'];
		$sz_rt4 = $_REQUEST['z_rt4'];
		$sz_rt5 = $_REQUEST['z_rt5'];
		$sz_rt6 = $_REQUEST['z_rt6'];
		$mkup = $_REQUEST['mkup'];


		include_once("../includes/DBClass.php");
		$db = new DBClass();
		
		$db->query("update ".$findb.".globals set setupf = :setupf, coyname = :coyname, bustype1 = :bustype1, bustype2 = :bustype2, ad1 = :ad1, ad2 = :ad2, ad3 = :ad3, boxno = :boxno, po = :po, telno = :telno, email = :email, faxno = :faxno, gstno = :gstno, gstpcent = :gstpcent, gsttype =:gsttype, gstperiod = :gstperiod, bedate = :bedate, yrdate = :yrdate, lstatdt = :lstatdt, pstatdt = :pstatdt, allowtrans = :allowtrans, branchname = :branchname, trdbankacc = :trdbankacc, trdbankbr = :trdbankbr, trdbanksub = :trdbanksub, cashacc = :cashacc, cashbr = :cashbr, cashsb = :cashsb, credcardacc = :credcardacc, credcardbr = :credcardbr, credcardsub = :credcardsub, z_rt1 = :z_rt1, z_rt2 = :z_rt2, z_rt3 = :z_rt3, z_rt4 = :z_rt4, z_rt5 = :z_rt5, z_rt6 = :z_rt6, c_s_markup = :c_s_markup, roundto = :roundto, tradtax = :tradtax");
		$db->bind(':setupf',  'Y');
		$db->bind(':coyname', $scoyname);
		$db->bind(':bustype1', $sbustype1);
		$db->bind(':bustype2', $sbustype2);
		$db->bind(':ad1', $sad1);
		$db->bind(':ad2', $sad2);
		$db->bind(':ad3', $sad3);
		$db->bind(':boxno', $sboxno);
		$db->bind(':po', $spo);
		$db->bind(':telno', $stelno);
		$db->bind(':email', $semail);
		$db->bind(':faxno', $sfaxno);
		$db->bind(':gstno', $sgstno);
		$db->bind(':gstpcent', $sgstpcent);
		$db->bind(':gsttype', $spaygst);
		$db->bind(':gstperiod', $sgstperiod);
		$db->bind(':bedate', $sbedate);
		$db->bind(':yrdate', $syrdate);
		$db->bind(':lstatdt', $slstatdt);
		$db->bind(':pstatdt', $spstatdt);
		$db->bind(':allowtrans', $sallowtrans);
		$db->bind(':branchname', $sbranchname);
		$db->bind(':trdbankacc', $trbankac);
		$db->bind(':trdbankbr', strtoupper($trbankbr));
		$db->bind(':trdbanksub', $trbanksb);
		$db->bind(':cashacc', $cohac);
		$db->bind(':cashbr', strtoupper($cohbr));
		$db->bind(':cashsb', $cohsb);
		$db->bind(':credcardacc', $crdac);
		$db->bind(':credcardbr', strtoupper($crdbr));
		$db->bind(':credcardsub', $crdsb);
		$db->bind(':z_rt1', $sz_rt1);
		$db->bind(':z_rt2', $sz_rt2);
		$db->bind(':z_rt3', $sz_rt3);
		$db->bind(':z_rt4', $sz_rt4);
		$db->bind(':z_rt5', $sz_rt5);
		$db->bind(':z_rt6', $sz_rt6);
		$db->bind(':c_s_markup', $mkup);
		$db->bind(':roundto', $srnd);
		$db->bind(':tradtax', $ltt);

		$db->execute();
		
		$db->query("update ".$findb.".numbers set inv = :inv, chq = :chq, c_s = :c_s, dep = :dep, grn = :grn, p_c = :p_c, req = :req, ret = :ret, adj = :adj, crd = :crd, tsf = :tsf, ebi = :ebi, ebo = :ebo, jnl = :jnl, rec = :rec, oth = :oth, pay = :pay, c_n = :c_n, r_t = :r_t, sal = :sal, pur = :pur, d_d = :d_d, d_c = :d_c, spo = :spo");
		$db->bind(':inv', $sinv);
		$db->bind(':chq', $schq);
		$db->bind(':c_s', $sc_s);
		$db->bind(':dep', $sdep);
		$db->bind(':grn', $sgrn);
		$db->bind(':p_c', $sp_c);
		$db->bind(':req', $sreq);
		$db->bind(':ret', $sret);
		$db->bind(':adj', $sadj);
		$db->bind(':crd', $scrd);
		$db->bind(':tsf', $stsf);
		$db->bind(':ebi', $sebi);
		$db->bind(':ebo', $sebo);
		$db->bind(':jnl', $sjnl);
		$db->bind(':rec', $srec);
		$db->bind(':oth', $soth);
		$db->bind(':pay', $spay);
		$db->bind(':c_n', $sc_n);
		$db->bind(':r_t', $sr_t);
		$db->bind(':sal', $ssal);
		$db->bind(':pur', $spur);
		$db->bind(':d_d', $sd_d);
		$db->bind(':d_c', $sd_c);
		$db->bind(':spo', $sspo);
		
		$db->execute();
		
		$hdate = date('Y-m-d');
		$ttime = strftime("%H:%M", time());
		
		$db->query("insert into ".$cltdb.".audit (ddate,ttime,user_id,uname,sub_id,member_id,action) values (:ddate,:ttime,:user_id,:uname,:sub_id,:member_id,:action)");
		$db->bind(':ddate', $hdate);
		$db->bind(':ttime', $ttime);
		$db->bind(':user_id', $user_id);
		$db->bind(':uname', $sname);
		$db->bind(':sub_id', $subscriber);
		$db->bind(':member_id', $user_id);
		$db->bind(':action', 'Edit Setup for '.$coy);
		
		$db->execute();
		$db->closeDB();

		echo '<script>';
		echo 'this.close();';
		echo '</script>';
			
	}

?>
</body>
</html>
