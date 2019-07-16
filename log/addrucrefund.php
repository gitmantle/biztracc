<?php
session_start();

$rlicence = $_REQUEST['rlic'];
$vn = $_SESSION['s_vehicleno'];

require_once('../db.php');


$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

// populate routes drop down
$query = "select uid,route,compartment from routes where uid > 1 order by route,compartment";
$result = mysql_query($query) or die(mysql_error().$query);
$route_options = "<option value=\"0\">Select Route and Compartment</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);

		$selected = '';

	$route_options .= '<option value="'.$uid.'"'.$selected.'>'.$route.'~'.$compartment.'</option>';
}

date_default_timezone_set($_SESSION['s_timezone']);
$ddate = date("d/m/Y");
$hdate = date("Y-m-d");

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add RUC Refund</title>
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
	var docket = document.getElementById('docno').value;
	var route = document.getElementById('route').value;
	
	var ok = "Y";
	if (docket == "") {
		alert("Please enter a Docket No.");
		ok = "N";
		return false;
	}
	if (route == 0) {
		alert("Please select a Route.");
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
 <table width="690" border="0" align="center" cellspacing="6" bgcolor="<?php echo $bgcolor; ?>">
    <tr>
      <td colspan="2" bgcolor="<?php echo $bghead; ?>" class="style1" align="center"><label style="color: <?php echo $thfont; ?>"><strong>Add RUC Refund for <?php echo $vn; ?></strong></label></td>
    </tr>
    <tr>
      <td colspan="2" bgcolor="<?php echo $bghead; ?>" class="style1" align="left">Administrators may use this screen to add a record to the table that stores private road distances associated with a docket/route. If the docket already exists in the system (check under the Dockets-&gt;Update Dockets menu), check in the Reports-&gt;Road User Charges that there is no entry for the docket details you wish to add before you proceed.</td>
    </tr>
    <tr>
      <td class="boxlabel">RUC Licence</td>
      <td><input name="licence" type="text" id="licence" readonly value="<?php echo $rlicence; ?>"></td>
      </tr>
    <tr>
      <td class="boxlabel">Docket Number</td>
      <td><input type="text" name="docno" id="docno"></td>
    </tr>
    <tr>
      <td class="boxlabel">Date</td>
      <td ><input type="Text" id="ddate" name="ddate" maxlength="25" size="25" value="<?php echo $ddate; ?>"><a href="javascript:NewCal('ddate')"><img src="../includes/datetimepick/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a></td>
    </tr>
    <tr>
      <td class="boxlabel">Route</td>
      <td><select name="route" id="route"><?php echo $route_options;?></select></td>
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
		$ddate = $y.'-'.$m.'-'.$d;	
		$licence = $_REQUEST['licence'];
		$route = $_REQUEST['route'];
		$docket = $_REQUEST['docno'];
		
		$q = "select regno from vehicles where vehicleno = '".$vn."'";
		$r = mysql_query($q) or die(mysql_error().$q);
		$row = mysql_fetch_array($r);
		extract($row);
		$rno = $regno;
		
		// get the private milage for this route
		$qpvt = "select private as dprivate from routes where uid = ".$route;
		$rpvt = mysql_query($qpvt) or die(mysql_error()." ".$qpvt);
		$row = mysql_fetch_array($rpvt);
		extract($row);
		if (substr($vn,0,5) == 'Truck') {
			$pvtkms = $dprivate * 2;
		} else {
			$pvtkms = $dprivate;
		}
		
		$qir = "insert into ruckms (ddate,vehicle,regno,ruclicence,docket_no,routeid,private) values (";
		$qir .= "'".$ddate."',";
		$qir .= "'".$vn."',";
		$qir .= "'".$rno."',";
		$qir .= "'".$licence."',";
		$qir .= $docket.",";
		$qir .= $route.",";
		$qir .= $pvtkms.")";
		$rir = mysql_query($qir) or die(mysql_error()." ".$qir);


	  ?>
	  <script>
	  this.close();
	  </script>
	  <?php
		
			
	}

?>


</body>
</html>
