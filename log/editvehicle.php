<?php
session_start();
$id = $_REQUEST['uid'];

require("../db.php");

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select * from vehicles where uid = ".$id;
$r = mysql_query($q) or die(mysql_error().' '.$q);
$row = mysql_fetch_array($r);
extract($row);
$t = explode(' ',$vehicleno);
$tt = $t[0];

date_default_timezone_set($_SESSION['s_timezone']);

$dt = split('-',$cofdate);
$y = $dt[0];
$m = $dt[1];
$d = $dt[2];
$fdt = mktime(0,0,0,$m,$d,$y);
$ddate = date("d/m/Y",$fdt);
$hdate = $cofdate;

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Vehicle Details</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script language="javascript" type="text/javascript" src="../includes/datetimepick/datetimepicker.js"></script>

<script>

function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

function post() {

	//add validation here if required.
	var regno = document.getElementById('regno').value;
	
	var ok = "Y";
	if (regno == "") {
		alert("Please enter a Registration Plate No.");
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
 <table width="600" border="0" align="center" cellspacing="6" bgcolor="<?php echo $bgcolor; ?>">
    <tr>
      <td colspan="2" bgcolor="<?php echo $bghead; ?>" align="center"><label style="color: <?php echo $thfont; ?>"><strong>Edit Vehicle </strong></label></td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Vehicle Registration Plate No.</label></td>
      <td><input name="regno" type="text" id="regno" value="<?php echo $regno; ?>"   ></td>
      </tr>
    <tr>
      <td class="boxlabel">Vehicle Make</td>
      <td><input type="text" name="vmake" id="vmake" value="<?php echo $make; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel">Vehicle type</td>
      <td><input type="text" name="tt" id="tt" value="<?php echo $tt; ?>" readonly></td>
    </tr>
    <tr>
      <td class="boxlabel">Fleet Number</td>
      <td><input type="text" name="fleetno" id="fleetno" value="<?php echo $vehicleno; ?>" readonly></td>
    </tr>
    <tr>
      <td class="boxlabel">Next COF due on</td>
        <td><input type="Text" id="ddate" name="ddate" maxlength="25" size="25" value="<?php echo $ddate; ?>"><a href="javascript:NewCal('ddate')"><img src="../includes/datetimepick/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a></td>
      </tr>
    <tr>
      <td class="boxlabel">Next service due at Kms</td>
      <td><input type="text" name="service" id="service" value="<?php echo $servicedue; ?>"></td>
    </tr>
	<tr>
      <td align="right" colspan="2">
        <input type="button" value="Save" name="save" onClick="post()"  >
      </td>
      </tr>
  	</table>
</form>

<?php

	if($_REQUEST['savebutton'] == "Y") {
		$regno = $_REQUEST['regno'];
		$vmake = $_REQUEST['vmake'];
		$fleetno = $_REQUEST['fleetno'];
		$tt = $_REQUEST['tt'];
		$odt = $_REQUEST['ddate'];
		$t = explode('/',$odt);
		$d = $t[0];
		if (strlen($d) == 1) {
			$d = '0'.$d;
		}
		$m = $t[1];
		if (strlen($m) == 1) {
			$m = '0'.$m;
		}
		$y = $t[2];
		$cdate = $y.'-'.$m.'-'.$d;		  
		$service = $_REQUEST['service'];
		
		$moduledb = $_SESSION['s_logdb'];
		mysql_select_db($moduledb) or die(mysql_error());

		$q = "update vehicles set ";
		$q .= "regno = '".$regno."',";
		$q .= "make = '".$vmake."',";
		$q .= "cofdate = '".$cdate."',";
		$q .= "servicedue = ".$service;
		$q .= " where uid = ".$id;

		$r = mysql_query($q) or die(mysql_error().$q);

	  ?>
	  <script>
	  window.open("","fleet").jQuery("#smlist").trigger("reloadGrid");
	  this.close();
	  </script>
	  <?php
		
			
	}

?>


</body>
</html>
