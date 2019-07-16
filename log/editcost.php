<?php
session_start();

$admindb = $_SESSION['s_admindb'];
$coyid = $_SESSION['s_coyid'];
$costuid = $_REQUEST['uid'];

require_once('../db.php');
mysql_select_db($admindb) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$subscriber = $subid;

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select * from costlines where uid = ".$costuid;
$r = mysql_query($q) or die(mysql_error().$q);
$row = mysql_fetch_array($r);
extract($row);
$cid = 'ORC'.$costid;
$icode = $itemcode;

$q = "select serialno from tyres where refno = '".$cid."' and itemcode = '".$icode."'";
$r = mysql_query($q) or die(mysql_error().$q);
$sno = "";
$numrows = mysql_num_rows($r);
if ($numrows > 0) { 
	while ($row = mysql_fetch_array($r)) {
		extract($row);
		$sno .= $serialno.",";
	}
	$sno = substr($sno,0,-1);
}

date_default_timezone_set($_SESSION['s_timezone']);

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Cost</title>
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

function calc() {
	var amt = document.getElementById('total').value;
	var tx = document.getElementById('gst').value;
	var gst = toFixed(parseFloat(amt)*parseFloat(tx)/100);
	document.getElementById('tax').value = gst;
	document.getElementById('totinc').value = parseFloat(amt) + parseFloat(gst);
	
}

function toFixed(val) {
	if (isNaN(val)) {
		alert('Must be a number');
	} else {
		return parseFloat(val).toFixed(2);
	}
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
 <input type="hidden" name="sno" id="sno" value="<?php echo $sno; ?>">
 <table width="550" border="0" align="center" cellspacing="6" bgcolor="<?php echo $bgcolor; ?>">
    <tr>
      <td colspan="3" align="center"  bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>"><strong>Edit Cost</strong></label></td>
    </tr>
    <tr>
      <td  class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Quantity</label></td>
      <td colspan="2"><input name="quantity" type="text" id="quantity"  value="<?php echo $quantity; ?>" ></td>
      </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Item</label></td>
      <td colspan="2"><input name="item" type="text" id="item" value="<?php echo $item; ?>" size="60" readonly ></td>
    </tr>
    <tr>
      <td  class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Unit Cost</label></td>
      <td colspan="2"><input name="unitcost" type="text" id="unitcost" value="<?php echo $unitcost; ?>" onFocus="this.select();"></td>
      </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Total (Ex-GST)</label></td>
      <td colspan="2"><input name="total" type="text" id="total" value="<?php echo $total; ?>" onFocus="this.select();" onBlur="calc();"></td>
      </tr>
    <tr>
      <td  class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Tax</label></td>
      <td><input name="tax" type="text" id="tax" value="<?php echo $gst; ?>" readonly></td>
      <td>GST @ 
      <input type="text" name="gst" id="gst" size="6" value="15.00"></td>
      </tr>
    <tr>
      <td  class="boxlabel">Total (Inc GST)</td>
      <td colspan="2"><input name="totinc" type="text" id="totinc" value="<?php echo $total+$gst; ?>" onFocus="this.select();" onBlur="deduct_tax();"> 
      check this aganst invoice</td>
    </tr>
    <tr>
      <td  class="boxlabel" id="snos">Serial Nos.</td>
      <td colspan="2"><input type="text" name="serialnos" id="serialnos" size="60" value="<?php echo $sno; ?>" readonly></td>
    </tr>
	<tr>
      <td colspan="3" align="right">
        <input type="button" value="Save" name="save" onClick="post()"  >
      </td>
      </tr>
  	</table>
    
    <script>
		var sn = document.getElementById('sno').value;
		if (sn != '') {
			document.getElementById('snos').style="visible";
			document.getElementById('serialnos').style="visible";
		} else {
			document.getElementById('snos').style="hidden";
			document.getElementById('serialnos').style="hidden";
		}
	</script>
    
    
    
</form>

<?php

	if($_REQUEST['savebutton'] == "Y") {
		$qty = $_REQUEST['quantity'];
		$ucost = $_REQUEST['unitcost'];
		$tot = $_REQUEST['total'];
		$gst = $_REQUEST['tax'];

		$q = "update costlines set ";
		$q .= "quantity = ".$qty.",";
		$q .= "unitcost = ".$ucost.",";
		$q .= "total = ".$tot.",";
		$q .= "gst = ".$gst;
		$q .= " where uid = ".$costuid;

		$r = mysql_query($q) or die(mysql_error().$q);

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
