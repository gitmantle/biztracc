<?php
session_start();
//ini_set('display_errors', true);

date_default_timezone_set($_SESSION['s_timezone']);

require("../db.php");

$id = $_REQUEST['uid'];
$cname = $_REQUEST['cname'];

$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());

$query = "select client_id,priceband,billing,email,sendstatement,blocked from client_company_xref where uid = ".$id; 
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$sendby = $sendstatement;
$em = $email;
$bill = $billing;
$pb = $priceband;
$mid = $client_id;
$blk = $blocked;

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

// populate price band  drop down
$query = "select * from stkpricepcent";
$result = mysql_query($query) or die(mysql_error());
$priceband_options = "<option value=\"0\">Select Price Band</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($uid == $pb) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$priceband_options .= "<option value=\"".$uid."\" ".$selected.">".$priceband."</option>";
}

$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());

// populate method list
    $arr = array('Post','Email');
	$method_options = "";
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $sendby) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$method_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate address list
$query = "select * from addresses where address_type_id = 4 and member_id = ".$mid;
$result = mysql_query($query) or die(mysql_error());
$billing_options = "<option value=\"0\">Select Billing Address</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	$ad = trim($street_no.' '.$ad1.' '.$ad2.' '.$suburb.' '.$town.' '.$postcode);
	if ($address_id == $bill) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$billing_options .= "<option value=\"".$address_id."\" ".$selected.">".$ad."</option>";
}

// populate comms list
$query = "select * from comms where comms_type_id = 4 and member_id = ".$mid;
$result = mysql_query($query) or die(mysql_error());
$comms_options = "<option value=\"0\">Select Email Address</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($comms_id == $em) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$comms_options .= "<option value=\"".$comms_id."\" ".$selected.">".$comm."</option>";
}

// populate blokced list
    $arr = array('No','Yes');
	$blk_options = "";
    for($i = 0; $i < count($arr); $i++)	{
			if ($arr[$i] == $blk) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}	
		$blk_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}


$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");


?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Financials</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>



</head>


<script>
	 
function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

function pe() {
	var smethod = document.getElementById('lmethod').value;
	if (smethod == 'Post') {
		document.getElementById('b').style.visibility = "visible";
		document.getElementById('e').style.visibility = "hidden";
	} else {
		document.getElementById('b').style.visibility = "hidden";
		document.getElementById('e').style.visibility = "visible";
	}
}

function post() {

	//add validation here if required.
	var meth = document.getElementById('lmethod').value;
	var em = document.getElementById('lemail').value;
	var bl = document.getElementById('lbilling').value;
	var pb = document.getElementById('lpb').value;
	
	var ok = "Y";
	if (pb == '0') {
		alert("Please enter a price band.");
		ok = "N";
		return false;
	}
	if (meth == 'Email' && em == '0') {
		alert("Please select an email address. If none are available, add them through the Communications tab.");
	}
	if (meth == 'Post' && bl == '0') {
		alert("Please select a postal billing address. If none are available, add them through the Addresses tab.");
	}
	
	
	if (ok == "Y") {
		document.getElementById('savebutton').value = "Y";
		document.getElementById('form1').submit();
	}
}

</script>

<body>
<div id="lwin">
<form name="form1" id="form1" method="post" >
 <input type="hidden" name="savebutton" id="savebutton" value="N">
  <table width="700" border="0" align="center" bgcolor="<?php echo $bgcolor; ?>">
    <tr>
      <td colspan="2" bgcolor="<?php echo $bghead; ?>" align="center"><label style="color: <?php echo $thfont; ?>"><strong>Edit Statement Parameters</strong></label></td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Send Statement by:-</label></td>
      <td><select name="lmethod" id="lmethod" onchange="pe()"><?php echo $method_options; ?></select></td>
    </tr>
    <tr id="b">
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Send Statement by Post to Address:-</label></td>
      <td><select name="lbilling" id="lbilling"><?php echo $billing_options; ?></select></td>
    </tr>
    <tr id="e">
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Send Statement by Email to Address:-</label></td>
      <td><select name="lemail" id="lemail"><?php echo $comms_options; ?></select></td>
    </tr>
    <tr>
      <td class="boxlabel">Blocked</td>
      <td ><select name="blk" id="blk"><?php echo $blk_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabel">Price Band:-</td>
      <td ><select name="lpb" id="lpb"><?php echo $priceband_options; ?></select></td>
    </tr>
	<tr>
      <td align="right" colspan="2">
        <input type="button" value="Save" name="save" onClick="post()"  >
      </td>
      </tr>
  </table>
</form>
</div>

	<script>
		document.onkeypress = stopRKey;
    </script> 

<?php

	if($_REQUEST['savebutton'] == "Y") {
		
		$pband = $_REQUEST['lpb'];
		$sendby = $_REQUEST['lmethod'];
		if ($sendby == 'Post') {
			$billad = $_REQUEST['lbilling'];
			$emailad = 0;
		} else {
			$billad = 0;
			$emailad = $_REQUEST['lemail'];
		}
		$blkd = $_REQUEST['blk'];
		
		$q = "update client_company_xref set ";
		$q .= "sendstatement = '".$sendby."',";
		$q .= "billing = ".$billad.",";
		$q .= "email = ".$emailad.",";
		$q .= "blocked = '".$blkd."',";
		$q .= "priceband = ".$pband;
		$q .= " where uid = ".$id;
		
		$r = mysql_query($q) or die(mysql_error().$q);
		
		if ($_SESSION['s_sup'] == 'Member') {
			?>	
				<script>
				window.open("","editmembers").jQuery("#mfinancialslist").trigger("reloadGrid");
				this.close();
				</script>
			<?php
		} else {
			?>	
				<script>
				window.open("","editsuppliers").jQuery("#mfinancialslist").trigger("reloadGrid");
				this.close();
				</script>
			<?php
		}
	}
?>


</body>
</html>
