<?php
session_start();
$usersession = $_SESSION['usersession'];

$stkid = $_REQUEST['uid'];
$coyid = $_SESSION['s_coyid'];
$findb = $_SESSION['s_findb'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

//ini_set('display_errors', true);

$db->query("select * from ".$findb.".stkmast where itemid = ".$stkid);
$row = $db->single();
extract($row);
$stkgroup = $groupid;
$stkcat = $catid;
$gst = $deftax;
$tserial = $trackserial;
$tstock = $stock;
$blkd = $active;
$itempic = 'itempics/'.$picture;

$db->query("select account from ".$findb.".glmast where accountno = ".$sellacc." and sub = ".$sellsub);
$row = $db->single();
extract($row);

$sellacct = $account.'~'.$sellacc.'~'.$sellsub;

$db->query("select account from ".$findb.".glmast where accountno = ".$purchacc." and sub = ".$purchsub);
$row = $db->single();
extract($row);

$purchacct = $account.'~'.$purchacc.'~'.$purchsub;

// populate active options
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

// populate batch options
    $arr = array('No', 'Yes');
	$batch_options = '';
    for($i = 0; $i < count($arr); $i++)	{
			$selected = '';
		$batch_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}
	
// populate stocktake options
    $arr = array('Stock', 'Service');
	$stocktake_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $tstock) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$stocktake_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}


// populate category drop down
$db->query("select * from ".$findb.".stkcategory where catid < 3");
$rows = $db->resultset();
$stkcat_options = "<option value=\"0\">Select Category</option>";
foreach ($rows as $row) {
	extract($row);
	if ($catid == $stkcat) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$stkcat_options .= '<option value="'.$catid.'"'.$selected.'>'.$category.'</option>';
}

// populate tax types drop down
$db->query("select * from ".$findb.".taxtypes ");
$rows = $db->resultset();
$tax_options = "<option value=\"0\">Select Category</option>";
foreach ($rows as $row) {
	extract($row);
	if ($uid == $gst) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$tax_options .= '<option value="'.$uid.'"'.$selected.'>'.$description.'</option>';
}

$db->closeDB();

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
		jQuery('#selectsrvinclist').setGridParam({url:'selectsrvinc.php?gl_mask='+s_mask}).trigger("reloadGrid"); 
	} 
	
	function doSearchsell(){ 
			var timeoutHnd = setTimeout(gridReload1sell,500); 
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
	var cat = document.getElementById('stkcat').value;
	var pac = document.getElementById('purchac').value;
	var sac = document.getElementById('sellac').value;
	var gst = document.getElementById('stkgst').value;
	
	var ok = "Y";
	if (itemcode == "") {
		alert("Please enter an item code.");
		ok = "N";
		return false;
	}
	if (cat == 0) {
		alert("Please select a category.");
		ok = "N";
		return false;
	}
	if (pac == "" || pac == "~0~0") {
		alert("Please select a default purchase acount.");
		ok = "N";
		return false;
	}
	if (sac == "" || sac == "~0~0") {
		alert("Please select a default sales account.");
		ok = "N";
		return false;
	}
	if (gst == 0) {
		alert("Please select a default tax type.");
		ok = "N";
		return false;
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
<form name="form1" id="form1" method="post" enctype="multipart/form-data">
 <input type="hidden" name="savebutton" id="savebutton" value="N">
  <table width="980" border="0" align="center">
    <tr>
      <td colspan="6"><div align="center" class="style1"><u>Edit Service/Disbursement </u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">Service/Disbursement Code</td>
      <td><input name="stkcode" type="text" id="stkcode"  size="30" maxlength="30" readonly value="<?php echo $itemcode; ?>"></td>
      <td class="boxlabel">Item Description</td>
      <td colspan="3"><input name="stkname" type="text" id="stkname"  size="55" maxlength="100" value="<?php echo $item; ?>"></td>
      </tr>
    <tr>
      <td class="boxlabel">&nbsp;</td>
      <td>&nbsp;</td>
      <td class="boxlabel">Category</td>
      <td colspan="3"><select name="stkcat" id="stkcat"><?php echo $stkcat_options;?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabel">Default Selling Price</td>
      <td><input type="text" name="defsell" id="defsell" value="<?php echo $setsell; ?>"></td>
      <td class="boxlabel">per Unit</td>
      <td colspan="3"><input type="text" name="stkunit" id="stkunit" value="<?php echo $unit; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel">Default purchase acc.</td>
      <td><input type="text" name="purchac" id="purchac" size="35" readonly value="<?php echo $purchacct; ?>">
        <img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="drsearch2" onclick="getpurch()"></td>
      <td class="boxlabel">Default selling acc.</td>
      <td colspan="3"><input type="text" name="sellac" id="sellac" size="35" readonly value="<?php echo $sellacct; ?>">
        <img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="drsearch" onclick="getsell()"></td>
    </tr>
    <tr>
      <td class="boxlabel">Active</td>
      <td><select name="stkactive" id="stkactive">
        <?php echo $active_options;?>
      </select></td>
      <td class="boxlabel">Default GST/VAT</td>
      <td colspan="3"><select name="stkgst" id="stkgst"><?php echo $tax_options;?>
      </select></td>
    </tr>
	  <td>&nbsp;</td>
	  <td colspan="3" align="right"><input type="button" value="Save" name="save"  onClick="post()"  ></td>
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
        <td colspan="2"><?php include "selectsrvinc.php"; ?></td>
      </tr>
    </table>
  </div>
  
  
</form>
</div>

<?php


	if($_REQUEST['savebutton'] == "Y") {
	
			$stkgroup = 1;
			$stkcat = $_REQUEST['stkcat'];
			$stkname = ucwords(trim($_REQUEST['stkname']));
			$stkcode = strtoupper($_REQUEST['stkcode']);
			$barno = '';
			$stkunit = $_REQUEST['stkunit'];
			$sellac = $_REQUEST['sellac'];
			$purchac = $_REQUEST['purchac'];
			$defsell = $_REQUEST['defsell'];
			$deftax = $_REQUEST['stkgst'];
			$active = $_REQUEST['stkactive'];
			$serial = 'No';
			$stocktake = 'Service';
			$batch = '';
			$technotes = '';
			$advertising = '';
			
			include_once("includes/accaddacc.php");
			$oAcc = new accaddacc;
			
			$oAcc->uid = $stkid;
			$oAcc->groupid = $stkgroup;
			$oAcc->catid = $stkcat;
			$oAcc->item = $stkname;
			$oAcc->itemcode = $stkcode;
			$oAcc->barno = $barno;
			$oAcc->unit = $stkunit;
			$oAcc->sellacc = $sellac;
			$oAcc->purchacc = $purchac;
			$oAcc->setsell = $defsell;
			$oAcc->deftax = $deftax;
			$oAcc->blocked = $active;
			$oAcc->trackserial = $serial;
			$oAcc->trackstock = $stocktake;
			$oAcc->batch = $batch;
			$oAcc->technotes = $technotes;
			$oAcc->advertising = $advertising;
									
			$oAcc->EditItem();
			

			?>
				<script>
				window.open("","updtsrvitems").jQuery("#srvlist").trigger("reloadGrid");
				this.close()
				</script>
			<?php
	}
?>
 

</body>
</html>

