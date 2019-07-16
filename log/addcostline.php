<?php
session_start();

$costid = $_SESSION['s_costid'];

require_once('../db.php');

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

// populate items drop down
$query = "select catid,itemid,itemcode,item from stkmast where groupid = 1 order by item";
$result = mysql_query($query) or die(mysql_error().$query);
$item_options = "<option value=\"\">Select Item</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	$selected = '';
	$item_options .= '<option value="'.$catid.'~'.$itemid.'~'.$itemcode.'~'.$item.'"'.$selected.'>'.$item.'</option>';
}

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select date,truckno,trailerno from costheader where costid = ".$costid;
$r = mysql_query($q) or die(mysql_error().$q);
$row = mysql_fetch_array($r);
extract($row);

date_default_timezone_set($_SESSION['s_timezone']);

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add Cost Line</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>

<script>

function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

function toFixed(val) {
	if (isNaN(val)) {
		alert('Must be a number');
	} else {
		return parseFloat(val).toFixed(2);
	}
}

function calc() {
	var amt = document.getElementById('total').value;
	var tx = document.getElementById('gst').value;
	var gst = toFixed(parseFloat(amt)*parseFloat(tx)/100);
	document.getElementById('tax').value = gst;
	document.getElementById('totinc').value = parseFloat(amt) + parseFloat(gst);
	
}

function deduct_tax() {
	var totinc = document.getElementById('totinc').value
	var tx = document.getElementById('gst').value;
	var taxed = toFixed(parseFloat(totinc)/(1.0 + parseFloat(tx/100)));
	document.getElementById('tax').value = toFixed(parseFloat(totinc)-parseFloat(taxed));
	document.getElementById('total').value = toFixed(taxed);	
}

function post() {

	//add validation here if required.
	var ok = 'Y'
	var stkitem = document.getElementById('stkitem').value;
	var qty = document.getElementById('quantity').value;
	
	if (stkitem == "") {
		alert("Please select an item.");
		ok = "N";
		return false;
	}
	if (qty == 0) {
		alert("Please enter a quantity.");
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
<form name="form1" id="form1" method="post" >
 <input type="hidden" name="savebutton" id="savebutton" value="N">
 <table width="500" border="0" align="center" cellspacing="6" bgcolor="<?php echo $bgcolor; ?>">
    <tr>
      <td colspan="3" align="center"  bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>"><strong>Add Cost Line</strong></label></td>
    </tr>
    <tr>
      <td  class="boxlabel"><label style="color: <?php echo $tdfont; ?>" value="0" onFocus="this.select();">Quantity</label></td>
      <td colspan="2"><input name="quantity" type="text" id="quantity"  value="0" ></td>
      </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Item</label></td>
      <td colspan="2"><select name="stkitem" id="stkitem">
			<?php echo $item_options; ?>
        </select></td>
    </tr>
    <tr>
      <td  class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Unit Cost</label></td>
      <td colspan="2"><input name="unitcost" type="text" id="unitcost" value="0" onFocus="this.select();"></td>
      </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Total(Ex-GST)</label></td>
      <td colspan="2"><input name="total" type="text" id="total" value="0" onFocus="this.select();" onBlur="calc();"></td>
      </tr>
    <tr>
      <td  class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Tax</label></td>
      <td><input name="tax" type="text" id="tax" value="0" readonly></td>
      <td>GST @
      <input type="text" name="gst" id="gst" size="6" value="15.00"></td>
      </tr>
    <tr>
      <td  class="boxlabel">Total (Inc GST)</td>
      <td colspan="2"><input name="totinc" type="text" id="totinc" value="0" onFocus="this.select();" onBlur="deduct_tax();">
      check this aganst invoice</td>
    </tr>
    <tr>
      <td  class="boxlabel" id="snos">Tyre Serial Nos.</td>
      <td colspan="2"><input type="text" name="serialnos" id="serialnos" size="60"></td>
    </tr>
	<tr>
      <td colspan="3" align="right">
        <input type="button" value="Save" name="save" onClick="post()"  >
      </td>
      </tr>
  	</table>
    
    
</form>

<?php

	if($_REQUEST['savebutton'] == "Y") {
		$qty = $_REQUEST['quantity'];
		$stkitem = $_REQUEST['stkitem'];
		$stk = explode('~',$stkitem);
		$catid = $stk[0];
		$itemid = $stk[1];
		$itemcode = $stk[2];
		$item = $stk[3];
		$ucost = $_REQUEST['unitcost'];
		$tot = $_REQUEST['total'];
		$gst = $_REQUEST['tax'];
		$serialnos = $_REQUEST['serialnos'];

		$q = "insert into costlines (costid,quantity,catid,itemid,itemcode,item,unitcost,gst,total) values (";
		$q .= $costid.",";
		$q .= $qty.",";
		$q .= $catid.",";
		$q .= $itemid.",";
		$q .= "'".$itemcode."',";
		$q .= "'".$item."',";
		$q .= $ucost.",";
		$q .= $gst.",";
		$q .= $tot.")";

		$r = mysql_query($q) or die(mysql_error().$q);
		
		// tyre serial numbers
		if ($serialnos != "") {
			$sns = explode(',',$serialnos);
									
			if ($truckno == '') {
				$tr = $trailerno;
			} else {
				$tr = $truckno;
			}
			$refno = 'ORC'.$costid;
									
			foreach($sns as $tsn) {
				$sn = trim($tsn);
				$qt = "insert into tyres (itemid,itemcode,item,serialno,activity,date,vehicle,refno) values (";
				$qt .= $itemid.",";
				$qt .= "'".$itemcode."',";
				$qt .= "'".$item."',";
				$qt .= "'".$sn."',";
				$qt .= "'Fit to vehicle',";
				$qt .= "'".$date."',";
				$qt .= "'".$tr."',";
				$qt .= "'".$refno."')";
										
				$rt = mysql_query($qt) or die(mysql_error()." ".$qt);
										
			}
		}
		

	  ?>
	  <script>
	  window.open("","costs").jQuery("#costlineslist").trigger("reloadGrid");
	  this.close();
	  </script>
	  <?php
		
			
	}

?>


</body>
</html>
