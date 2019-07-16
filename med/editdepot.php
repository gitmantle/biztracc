<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];
$admindb = $_SESSION['s_admindb'];
date_default_timezone_set($_SESSION['s_timezone']);
$induid = $_REQUEST['uid'];

require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$subscriber = $subid;

$moduledb = $_SESSION['s_prcdb'];
mysql_select_db($moduledb) or die(mysql_error());


$query = "select * from depots where depot_id = ".$induid;
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$rt = explode('-',$route);
$rt1 = $rt[0];
$rt2 = $rt[1];
$rt3 = $rt[2];
$rt4 = $rt[3];
$rt5 = $rt[4];
$rt6 = $rt[5];

?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Depot</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<script>
function sameadd() {
	document.getElementById('pad1').value = document.getElementById('sad1').value;
	document.getElementById('pad2').value = document.getElementById('sad2').value;
	document.getElementById('ptown').value = document.getElementById('stown').value;
	document.getElementById('ppostcode').value = document.getElementById('spostcode').value;
	document.getElementById('pcountry').value = document.getElementById('scountry').value;
}

function post() {

	//add validation here if required.
	var dep = document.getElementById('depot').value;
	var n1 = document.getElementById('troute1').value;
	var n2 = document.getElementById('troute2').value;
	var n3 = document.getElementById('troute3').value;
	var n4 = document.getElementById('troute4').value;
	var n5 = document.getElementById('troute5').value;
	var n6 = document.getElementById('troute6').value;
	
	var ok = "Y";
	if (dep == '') {
		alert("Please enter a depot.");
		ok = "N";
		return false;
	}
	if (isNaN(n1) || isNaN(n3) || isNaN(n5) || isNaN(n2) || isNaN(n4) || isNaN(n6) ) {
		alert("Please enter a Route ID in the format 99-999-99-999-99-999.");
		ok = "N";
		return false;
	}
	if (n1.length < 2 || n3.length < 2 || n5.length < 2 || n2.length < 3 || n4.length < 3 || n6.length < 3) {
		alert("Please enter a Route ID in the format 99-999-99-999-99-999.");
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
<div id="lwin">
<form name="form1" id="form1" method="post" >
 <input type="hidden" name="savebutton" id="savebutton" value="N">
  <table width="750" border="0" align="center">
    <tr>
      <td colspan="4"><div align="center" class="style1"><u>Edit Depot </u></div></td>
    </tr>
    <tr>
      <td width="106" class="boxlabel"><div align="right">Depot</div></td>
      <td colspan="3"><input name="depot" type="text" id="depot"  size="45" maxlength="45" value="<?php echo $depot; ?>"></td>
      </tr>
    <tr>
      <td class="boxlabel">Route ID</td>
      <td colspan="3"><input type="text" name="troute1" id="troute1" size="2" maxlength="2" value="<?php echo $rt1; ?>" onfocus="this.select();">
     	-
        <input type="text" name="troute2" id="troute2" size="3" maxlength="3" value="<?php echo $rt2; ?>" onfocus="this.select();" >
        -
        <input type="text" name="troute3" id="troute3" size="2" maxlength="2" value="<?php echo $rt3; ?>" onfocus="this.select();" >
        -
        <input type="text" name="troute4" id="troute4" size="3" maxlength="3" value="<?php echo $rt4; ?>" onfocus="this.select();" >
        -
        <input type="text" name="troute5" id="troute5" size="2" maxlength="2" value="<?php echo $rt5; ?>" onfocus="this.select();" >
        -
        <input type="text" name="troute6" id="troute6" size="3" maxlength="3" value="<?php echo $rt6; ?>" onfocus="this.select();" > &nbsp;
        Format 99-999-99-999-99-999 (see manual for details)</td>
    </tr>
    <tr>
      <td class="boxlabel">Contact Person</td>
      <td colspan="4"><input name="contact" type="text" id="contact"  size="45" maxlength="45" value="<?php echo $contact; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel">Address</td>
      <td class="boxlabelleft">Street</td>
      <td class="boxlabelleft">Post Box</td>
      <td align="right"><input type="Button" name="same" id="same" value="Same as Street" onclick="sameadd();"></td>
    </tr>
    <tr>
      <td class="boxlabel">Line 1</td>
      <td><input type="text" name="sad1" id="sad1" size="50" value="<?php echo $sad1; ?>"></td>
      <td colspan="2"><input type="text" name="pad1" id="pad1" size="50" value="<?php echo $pad1; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel">Line 2</td>
      <td><input type="text" name="sad2" id="sad2" size="50" value="<?php echo $sad2; ?>"></td>
      <td colspan="2"><input type="text" name="pad2" id="pad2" size="50" value="<?php echo $pad2; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel">Town</td>
      <td><input type="text" name="stown" id="stown" size="50" value="<?php echo $stown; ?>"></td>
      <td colspan="2"><input type="text" name="ptown" id="ptown" size="50" value="<?php echo $ptown; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel">Postcode</td>
      <td><input type="text" name="spostcode" id="spostcode" size="15" value="<?php echo $spostcode; ?>"></td>
      <td colspan="2"><input type="text" name="ppostcode" id="ppostcode" size="15" value="<?php echo $ppostcode; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel">Country</td>
      <td><input type="text" name="scountry" id="scountry" size="50" value="<?php echo $scountry; ?>"></td>
      <td colspan="2"><input type="text" name="pcountry" id="pcountry" size="50" value="<?php echo $pcountry; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel">Phone</td>
      <td><input type="text" name="phone" id="phone" size="50" value="<?php echo $phone; ?>"></td>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabel">Mobile</td>
      <td><input type="text" name="mobile" id="mobile" size="50" value="<?php echo $mobile; ?>"></td>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabel">Email</td>
      <td><input type="text" name="email" id="email" size="50" value="<?php echo $email; ?>"></td>
      <td colspan="2">&nbsp;</td>
    </tr>	<tr>
      <td>&nbsp;</td>
      <td colspan="3" align="right"><input type="button" value="Save" name="save" onClick="post()" ></td>
      </tr>
  </table>
</form>
</div>

<?php

	if($_REQUEST['savebutton'] == "Y") {
		
				$troute = $_REQUEST['troute1'].'-'.$_REQUEST['troute2'].'-'.$_REQUEST['troute3'].'-'.$_REQUEST['troute4'].'-'.$_REQUEST['troute5'].'-'.$_REQUEST['troute6'];


				$sSQLString = "update depots set ";
				$sSQLString .= 'depot = "'.ucwords($_REQUEST['depot']).'",';
				$sSQLString .= 'sad1 = "'.ucwords($_REQUEST['sad1']).'",';
				$sSQLString .= 'sad2 = "'.ucwords($_REQUEST['sad2']).'",';
				$sSQLString .= 'stown = "'.ucwords($_REQUEST['stown']).'",';
				$sSQLString .= 'spostcode = "'.$_REQUEST['spostcode'].'",';
				$sSQLString .= 'scountry = "'.ucwords($_REQUEST['scountry']).'",';
				$sSQLString .= 'pad1 = "'.ucwords($_REQUEST['pad1']).'",';
				$sSQLString .= 'pad2 = "'.ucwords($_REQUEST['pad2']).'",';
				$sSQLString .= 'ptown = "'.$ptown.'",';
				$sSQLString .= 'ppostcode = "'.ucwords($_REQUEST['ptown']).'",';
				$sSQLString .= 'pcountry = "'.ucwords($_REQUEST['pcountry']).'",';
				$sSQLString .= 'phone = "'.$_REQUEST['phone'].'",';
				$sSQLString .= 'mobile = "'.$_REQUEST['mobile'].'",';
				$sSQLString .= 'email = "'.$_REQUEST['email'].'",';
				$sSQLString .= 'contact = "'.ucwords($_REQUEST['contact']).'",';
				$sSQLString .= 'route = "'.$troute.'"';
				$sSQLString .= ' where depot_id = '.$induid;
				
				$result = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
	
				?>
				<script>
				window.open("","updtdepot").jQuery("#depotlist").trigger("reloadGrid");
				this.close();
				</script>
				<?php
		
	}

?>



</body>
</html>
