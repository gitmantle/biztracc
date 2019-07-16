<?php
session_start();
require_once("../db.php");

$stkid = $_REQUEST['uid'];

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

//ini_set('display_errors', true);

$q = "select xref,generic from stkmast where itemid = ".$stkid;
$r = mysql_query($q) or die(mysql_error().' '.$q);
$row = mysql_fetch_array($r);
extract($row);
if ($xref <> 0) {
	if ($generic == 'Y') {
		$q = "select itemid,groupid,catid,item,unit,noinunit,setsell,itemcode,avgcost,deftax,supplier,xref as x from stkmast where itemid = ".$stkid;
	} else {
		$q = "select itemid,groupid,catid,item,unit,noinunit,setsell,itemcode,avgcost,deftax,supplier,xref as x from stkmast where itemid = ".$xref;
	}
	$r = mysql_query($q) or die(mysql_error().' '.$q);
	$row = mysql_fetch_array($r);
	extract($row);
	$gstkgroup = $groupid;
	$gstkcat = $catid;	
	$gstkname = $item;
	$gdefsell = $setsell;
	$gitemcode = $itemcode;
	$gorigitemcode = $itemcode;
	$gitemid = $itemid;
	$gavgcost = $avgcost;
	$ggst = $deftax;
	$gunit = $unit;
	$gnoinunit = $noinunit;
	$gsupplier_id = $supplier;
} else {
	$gitemid = 0;
	$gdefsell = 0;
}

if ($xref <> 0) {
	if ($generic == 'Y') {
		$q = "select * from stkmast where itemid = ".$xref;
	} else {
		$q = "select * from stkmast where itemid = ".$stkid;
	}
} else {
	$q = "select * from stkmast where itemid = ".$stkid;
}
$r = mysql_query($q) or die(mysql_error().' '.$q);
$row = mysql_fetch_array($r);
extract($row);
$stkgroup = $groupid;
$stkcat = $catid;
$gst = $deftax;
$tserial = $trackserial;
$tstock = $stock;
$blkd = $blocked;
$supplier_id = $supplier;

$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());


$q = "select lastname from members where member_id = ".$supplier_id;
$r = mysql_query($q) or die(mysql_error().' '.$q);
$row = mysql_fetch_array($r);
extract($row);
$supplier = $lastname.'~'.$supplier_id;
if ($supplier == '~0') {
	$supplier = '';
}

if ($xref <> 0) {
	$q = "select lastname from members where member_id = ".$gsupplier_id;
	$r = mysql_query($q) or die(mysql_error().' '.$q);
	$row = mysql_fetch_array($r);
	extract($row);
	$gsupplier = $lastname.'~'.$gsupplier_id;
	if ($gsupplier == '~0') {
		$gsupplier = '';
	}
} else {
	$gsupplier = '';
}

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());


$q = "select account from glmast where accountno = ".$sellacc." and sub = ".$sellsub;
$r = mysql_query($q) or die(mysql_error().' '.$q);
$row = mysql_fetch_array($r);
extract($row);

$sellacct = $account.'~'.$sellacc.'~'.$sellsub;

$q = "select account from glmast where accountno = ".$purchacc." and sub = ".$purchsub;
$r = mysql_query($q) or die(mysql_error().' '.$q);
$row = mysql_fetch_array($r);
extract($row);

$purchacct = $account.'~'.$purchacc.'~'.$purchsub;

// populate stock options
    $arr = array('Yes', 'No');
	$active_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $blkd) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$active_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}
	
// populate serial options
    $arr = array('No', 'Yes');
	$serial_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $tserial) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$serial_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate stocktake options
    $arr = array('Yes', 'No');
	$stocktake_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $tstock) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$stocktake_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate groups drop down
$query = "select * from stkgroup ";
$result = mysql_query($query) or die(mysql_error());
$stkgroup_options = "<option value=\"0\">Select Group</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($groupid == $stkgroup) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$stkgroup_options .= '<option value="'.$groupid.'"'.$selected.'>'.$groupname.'</option>';
}

// populate category drop down
$query = "select * from stkcategory ";
$result = mysql_query($query) or die(mysql_error());
$stkcat_options = "<option value=\"0\">Select Category</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($catid == $stkcat) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$stkcat_options .= '<option value="'.$catid.'"'.$selected.'>'.$category.'</option>';
}

// populate tax types drop down
$query = "select * from taxtypes ";
$result = mysql_query($query) or die(mysql_error());
$tax_options = "<option value=\"0\">Select Category</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($uid == $gst) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$tax_options .= '<option value="'.$uid.'"'.$selected.'>'.$description.'</option>';
}

// populate generic tax types drop down
$query = "select * from taxtypes ";
$result = mysql_query($query) or die(mysql_error());
$gtax_options = "<option value=\"0\">Select Category</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($uid == $ggst) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$gtax_options .= '<option value="'.$uid.'"'.$selected.'>'.$description.'</option>';
}

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Stock Item</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<link rel="stylesheet" type="text/css" media="screen" href="../includes/jquery/themes/ui.multiselect.css"/> 

<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.core.js"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.position.js"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.menu.js"></script>
<script type="text/javascript" src="../includes/jquery/external/jquery.bgiframe-2.1.1.js"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script>
	function getpurch() {
		document.getElementById('purchselect').style.visibility = 'visible';
		$.get("selectpurch.php", {}, function(data){$("#selectpurchlist").trigger("reloadGrid")});
		document.getElementById('searchpurch').value = "";
		document.getElementById('searchpurch').focus();
	}
	function getsell() {
		document.getElementById('sellselect').style.visibility = 'visible';
		$.get("selectinc.php", {}, function(data){$("#selectinclist").trigger("reloadGrid")});
		document.getElementById('searchsell').value = "";
		document.getElementById('searchsell').focus();
	}
	
	
	function purchhide() {
		document.getElementById('purchselect').style.visibility = 'hidden';											
	}

	function sellhide() {
		document.getElementById('sellselect').style.visibility = 'hidden';											
	}

	function gridReload1purch(){ 
		var p_mask = jQuery("#searchpurch").val(); 
		p_mask = p_mask.toUpperCase();
		jQuery('#selectpurchlist').setGridParam({url:'selectpurch.php?gl_mask='+p_mask}).trigger("reloadGrid"); 
	} 
	
	function doSearchpurch(){ 
			var timeoutHnd = setTimeout(gridReload1purch,500); 
		} 
	
	function gridReload1sell(){ 
		var s_mask = jQuery("#searchsell").val(); 
		s_mask = s_mask.toUpperCase();
		jQuery('#selectinclist').setGridParam({url:'selectinc.php?gl_mask='+s_mask}).trigger("reloadGrid"); 
	} 
	
	function doSearchsell(){ 
			var timeoutHnd = setTimeout(gridReload1sell,500); 
	} 

function setsupselect(supplier,num) {
	if (num == 1) {
	 	document.getElementById('supplier').value = supplier;
		document.getElementById('supselect1').style.visibility = 'hidden';
	} else {
	 	document.getElementById('gsupplier').value = supplier;
		document.getElementById('supselect2').style.visibility = 'hidden';
	}
}



function supvisible1() {
		document.getElementById('supselect1').style.visibility = 'visible';											
		document.getElementById('searchsup1').value = "";
		document.getElementById('searchsup1').focus();
}

function supvisible2() {
		document.getElementById('supselect2').style.visibility = 'visible';											
		document.getElementById('searchsup2').value = "";
		document.getElementById('searchsup2').focus();
}
	
function gridReloadsup1(){ 
	var cr_mask = jQuery("#searchsup1").val(); 
	cr_mask = cr_mask.toUpperCase();
	jQuery("#selectcrlist").setGridParam({url:"selectcr.php?cr_mask="+cr_mask}).trigger("reloadGrid"); 
} 

function gridReloadsup2(){ 
	var cr_mask = jQuery("#searchsup2").val(); 
	cr_mask = cr_mask.toUpperCase();
	jQuery("#selectcrlist2").setGridParam({url:"selectcr2.php?cr_mask="+cr_mask}).trigger("reloadGrid"); 
} 

function doSearchsup1(){ 
		var timeoutHnd = setTimeout(gridReloadsup1,500); 
	} 

function doSearchsup2(){ 
		var timeoutHnd = setTimeout(gridReloadsup2,500); 
	} 	
	
function sboxhidesup1() {
	document.getElementById('supselect1').style.visibility = 'hidden';											
}

function sboxhidesup2() {
	document.getElementById('supselect2').style.visibility = 'hidden';											
}



	function setpurch(acc) {
	
	var a = acc.split('~');
	var ac = a[0];
	var sb = a[1];
	var acname = a[2];
	var purch = acname+'~'+ac+'~'+sb;
	
	document.getElementById('purchac').value = purch;
	document.getElementById('purchac').style.visibility = 'visible';
	document.getElementById('purchselect').style.visibility = 'hidden';

	}

	function setinc(acc) {
	
	var a = acc.split('~');
	var ac = a[0];
	var sb = a[1];
	var acname = a[2];
	var inc = acname+'~'+ac+'~'+sb;
	
	document.getElementById('sellac').value = inc;
	document.getElementById('sellac').style.visibility = 'visible';
	document.getElementById('sellselect').style.visibility = 'hidden';

	}

function post() {

	//add validation here if required.
	var itemcode = document.getElementById('stkcode').value;
	var grp = document.getElementById('stkgroup').value;
	var cat = document.getElementById('stkcat').value;
	var pac = document.getElementById('purchac').value;
	var sac = document.getElementById('sellac').value;
	var gst = document.getElementById('stkgst').value;
	var noin = document.getElementById('noinunit').value;
	var supl = document.getElementById('supplier').value;
	var gitemcode = document.getElementById('gstkcode').value;
	var ggrp = document.getElementById('gstkgroup').value;
	var gcat = document.getElementById('gstkcat').value;
	var gnoin = document.getElementById('gnoinunit').value;
	var gsupl = document.getElementById('gsupplier').value;
	
	var ok = "Y";
	if (itemcode == "") {
		alert("Please enter an item code.");
		ok = "N";
		return false;
	}
	if (grp == 0) {
		alert("Please select a group.");
		ok = "N";
		return false;
	}
	if (cat == 0) {
		alert("Please select a category.");
		ok = "N";
		return false;
	}
	if (pac == "" || pac == '~0~0') {
		alert("Please select a default purchase acount.");
		ok = "N";
		return false;
	}
	if (sac == "" || sac == '~0~0') {
		alert("Please select a default sales account.");
		ok = "N";
		return false;
	}
	if (gst == 0) {
		alert("Please select a default tax type.");
		ok = "N";
		return false;
	}
	if (noin == 0) {
		alert("Please enter a quantity per unit/pack.");
		ok = "N";
		return false;
	}
	if (supl == '') {
		alert("Please enter supplier.");
		ok = "N";
		return false;
	}
	
	
	// generics
	if (gitemcode != "") {
	
		if (ggrp == 0) {
			alert("Please select a group for generic item.");
			ok = "N";
			return false;
		}
		if (gcat == 0) {
			alert("Please select a category for generic item.");
			ok = "N";
			return false;
		}
		if (gnoin == 0) {
			alert("Please enter a quantity per unit/pack for generic item.");
			ok = "N";
			return false;
		}
		if (gsupl == '') {
			alert("Please enter supplier for generic item.");
			ok = "N";
			return false;
		}
	}
	
	if (ok == "Y") {
		document.getElementById('savebutton').value = "Y";
		document.getElementById('form1').submit();
	}
	
}

</script>


<style type="text/css">
<!--
.style1 {font-size: large}
-->
</style>
</head>


<body>
<div id="bwin">
<form name="form1" id="form1" method="post" >
 <input type="hidden" name="savebutton" id="savebutton" value="N">
  <table width="960" border="0" align="center">
    <tr>
      <td colspan="6"><div align="center" class="style1"><u>Edit Stock Item </u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">Item Code</td>
      <td><input name="stkcode" type="text" id="stkcode"  size="30" maxlength="30" readonly value="<?php echo $itemcode; ?>"></td>
      <td class="boxlabel">Item Description</td>
      <td colspan="3"><input name="stkname" type="text" id="stkname"  size="60" maxlength="100" value="<?php echo $item; ?>"></td>
      </tr>
    <tr>
      <td class="boxlabel">Group</td>
      <td><select name="stkgroup" id="stkgroup"><?php echo $stkgroup_options;?>
      </select></td>
      <td class="boxlabel">Category</td>
      <td colspan="3"><select name="stkcat" id="stkcat"><?php echo $stkcat_options;?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabel">Unit/Pack</td>
      <td class="boxlabelleft"><input type="text" name="stkunit" id="stkunit" value="<?php echo $unit; ?>" size="15">
        of
        <input type="text" name="noinunit" id="noinunit" value="<?php echo $noinunit; ?>" size="10">
        </td>
      <td class="boxlabel">Default GST/VAT</td>
      <td colspan="3"><select name="stkgst" id="stkgst"><?php echo $tax_options;?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabel">Average Unit Cost</td>
      <td><input type="text" name="avgcost" id="avgcost" value="<?php echo $avgcost; ?>" readonly></td>
      <td class="boxlabel">Default Selling Price</td>
      <td colspan="3"><input type="text" name="defsell" id="defsell" value="<?php echo $setsell; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel">Default purchase acc.</td>
      <td><input type="text" name="purchac" id="purchac" size="35" readonly value="<?php echo $purchacct; ?>">&nbsp;<img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="drsearch" onclick="getpurch()"> </td>
      <td class="boxlabel">Default selling acc.</td>
      <td><input type="text" name="sellac" id="sellac" size="35" readonly value="<?php echo $sellacct; ?>">&nbsp;<img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="drsearch" onclick="getsell()"> </td>
    </tr>
    <tr>
      <td class="boxlabel">Active</td>
      <td><select name="stkactive" id="stkactive"><?php echo $active_options;?>
      </select></td>
      <td class="boxlabel">Bar Code No.</td>
      <td colspan="3"><input type="text" name="barno" id="barno" value="<?php echo $barno; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel">Include in Stocktake</td>
      <td><select name="stkstock" id="stkstock">
        <?php echo $stocktake_options;?>
      </select></td>
      <td class="boxlabel">Supplier</td>
      <td colspan="3"><input type="text" name="supplier" id="supplier" size="45" readonly value ="<?php echo $supplier; ?>">
          <img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="crsearch" onclick="supvisible1()"></td>
    </tr>
    <tr>
      <td colspan="4" ><hr size="4" width="900" align="center" /> </td>
    </tr>
    <tr>
      <td colspan="4" ><div align="center" class="style1"><u>Generic Equivalent</u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">Generic Item Code</td>
      <td><input name="gstkcode" type="text" id="gstkcode"  size="30" maxlength="30" value="<?php echo $gitemcode; ?>"></td>
      <td class="boxlabel">Generic Item Description</td>
      <td colspan="3"><input name="gstkname" type="text" id="gstkname" size="60" value="<?php echo $gstkname; ?>"></td>
      </tr>
    <tr>
      <td class="boxlabel">Group</td>
      <td><select name="gstkgroup" id="gstkgroup"><?php echo $stkgroup_options;?>
      </select></td>
      <td class="boxlabel">Category</td>
      <td colspan="3"><select name="gstkcat" id="gstkcat"><?php echo $stkcat_options;?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabel">Unit/Pack</td>
      <td class="boxlabelleft">
        <input type="text" name="gstkunit" id="gstkunit" value="<?php echo $gunit; ?>" size="15">
		of
		<input type="text" name="gnoinunit" id="gnoinunit" value="<?php echo $gnoinunit; ?>" size="10">
      </td>
      <td class="boxlabel">Default GST/VAT</td>
      <td colspan="3"><select name="gstkgst" id="gstkgst">
        <?php echo $gtax_options;?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabel">Average Unit Cost</td>
      <td><input type="text" name="gavgcost" id="gavgcost" readonly value="<?php echo $gavgcost; ?>"></td>
      <td class="boxlabel">Default Selling Price</td>
      <td colspan="3"><input type="text" name="gdefsell" id="gdefsell" value="<?php echo $gdefsell; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel">Supplier</td>
      <td colspan="3"><input type="text" name="gsupplier" id="gsupplier" size="45" readonly value ="<?php echo $gsupplier; ?>">
          <img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="crsearch" onclick="supvisible2()"></td>
	  <td colspan="2" align="right"><input type="button" value="Save" name="save"  onClick="post()"  ></td>
	  </tr>
  </table>
  
  <div id="purchselect" style="position:absolute;visibility:hidden;top:130px;left:169px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchpurch" size="50" onkeypress="doSearchpurch()" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="glclose" onclick="purchhide()"></td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selectpurch.php"; ?></td>
      </tr>
    </table>
  </div>
  
  <div id="sellselect" style="position:absolute;visibility:hidden;top:130px;left:525px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchsell" size="50" onkeypress="doSearchsell()" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="drclose" onclick="sellhide()"></td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selectinc.php"; ?></td>
      </tr>
    </table>
  </div>
  
   <div id="supselect1" style="position:absolute;visibility:hidden;top:156px;left:500px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchsup1" size="50" onkeypress="doSearchsup1()" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="stkclose" onclick="sboxhidesup1()">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selectsupplier1.php"; ?></td>
      </tr>
    </table>
  </div>
 
   <div id="supselect2" style="position:absolute;visibility:hidden;top:156px;left:80px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchsup2" size="50" onkeypress="doSearchsup2()" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="stkclose" onclick="sboxhidesup2()">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selectsupplier2.php"; ?></td>
      </tr>
    </table>
  </div>
   
</form>
</div>

<?php
	if($_REQUEST['savebutton'] == "Y") {
		
			$stkgroup = $_REQUEST['stkgroup'];
			$stkcat = $_REQUEST['stkcat'];
			$stkname = ucwords(trim($_REQUEST['stkname']));
			$stkcode = strtoupper($_REQUEST['stkcode']);
			$barno = $_REQUEST['barno'];
			$stkunit = $_REQUEST['stkunit'];
			$noinunit = $_REQUEST['noinunit'];
			$sellac = $_REQUEST['sellac'];
			$purchac = $_REQUEST['purchac'];
			$defsell = $_REQUEST['defsell'];
			$deftax = $_REQUEST['stkgst'];
			$active = $_REQUEST['stkactive'];
			$serial = 'No';
			$stocktake = $_REQUEST['stkstock'];
			$s = explode('~',$_REQUEST['supplier']);
			$supplier = $s[1];
			$stocktake = $_REQUEST['stkstock'];
			
			$gstkgroup = $_REQUEST['gstkgroup'];
			$gstkcat = $_REQUEST['gstkcat'];
			$gstkname = ucwords(trim($_REQUEST['gstkname']));
			$gstkcode = strtoupper($_REQUEST['gstkcode']);
			$gdefsell = $_REQUEST['gdefsell'];
			$gdeftax = $_REQUEST['gstkgst'];
			$gstkunit = $_REQUEST['gstkunit'];
			$gnoinunit = $_REQUEST['gnoinunit'];
			$gs = explode('~',$_REQUEST['gsupplier']);
			$gsupplier = $gs[1];
			
			
			include_once("includes/addmed.php");
			$oAcc = new addmed;
			
			
			$q = "select xref,generic from stkmast where itemid = ".$stkid;
			$r = mysql_query($q) or die(mysql_error().' '.$q);
			$row = mysql_fetch_array($r);
			extract($row);
			if ($xref <> 0) {
				if ($generic == 'Y') {
					$standardid = $xref;
					$genericid = $stkid;
				} else {
					$standardid = $stkid;
					$genericid = $xref;
				}
			} else {
				$standardid = $stkid;
				$genericid = $xref;
			}
			
			$oAcc->uid = $standardid;
			$oAcc->groupid = $stkgroup;
			$oAcc->catid = $stkcat;
			$oAcc->item = $stkname;
			$oAcc->itemcode = $stkcode;
			$oAcc->barno = $barno;
			$oAcc->unit = $stkunit;
			$oAcc->noinunit = $noinunit;
			$oAcc->sellacc = $sellac;
			$oAcc->purchacc = $purchac;
			$oAcc->setsell = $defsell;
			$oAcc->deftax = $deftax;
			$oAcc->blocked = $active;
			$oAcc->trackserial = $serial;
			$oAcc->trackstock = $stocktake;
			$oAcc->supplier = $supplier;
			
			$oAcc->ggroupid = $gstkgroup;
			$oAcc->gcatid = $gstkcat;
			$oAcc->gitem = $gstkname;
			$oAcc->gitemcode = $gstkcode;
			$oAcc->gsetsell = $gdefsell;
			$oAcc->gorigitemcode = $gorigitemcode;
			$oAcc->gitemid = $genericid;
			$oAcc->gdeftax = $gdeftax;
			$oAcc->gunit = $gstkunit;
			$oAcc->gnoinunit = $gnoinunit;
			$oAcc->gsupplier = $gsupplier;

			$oAcc->EditItem();
	
			?>
				<script>
				window.open("","updtstkitems").jQuery("#stklist").trigger("reloadGrid");
				this.close()
				</script>
			<?php
	
	}
?>
 

</body>
</html>

