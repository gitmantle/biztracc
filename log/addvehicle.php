<?php
session_start();

$admindb = $_SESSION['s_admindb'];

require_once('../db.php');
mysql_select_db($admindb) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$subscriber = $subid;


$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());


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
<title>Add Vehicle Details</title>
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
	var fleetno = document.getElementById('fleetno').value;
	
	var ok = "Y";
	if (regno == "") {
		alert("Please enter a Registration Plate No.");
		ok = "N";
		return false;
	}
	if (fleetno == " ") {
		alert("Please select a Fleet Number.");
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
      <td colspan="2" bgcolor="<?php echo $bghead; ?>" align="center"><label style="color: <?php echo $thfont; ?>"><strong>Add Vehicle </strong></label></td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Vehicle Registration Plate No.</label></td>
      <td><input name="regno" type="text" id="regno"></td>
      </tr>
    <tr>
      <td class="boxlabel">Vehicle Make</td>
      <td><input type="text" name="vmake" id="vmake"></td>
    </tr>
    <tr>
      <td class="boxlabel">Vehicle type</td>
      <td><select name="tt" id="tt">
        <option value="Truck">Truck</option>
        <option value="Trailer">Trailer</option>
      </select></td> 
    </tr>
    <tr>
      <td class="boxlabel">Fleet Number</td>
      <td><input type="text" name="fleetno" id="fleetno"></td>
    </tr>
    <tr>
      <td colspan="2" class="boxlabel">A Branch/Cost Centre will be automatically created in the accounts for this vehicle.</td>
    </tr>
    <tr>
      <td class="boxlabel">Next COF due on</td>
        <td><input type="Text" id="ddate" name="ddate" maxlength="25" size="25" value="<?php echo $ddate; ?>"><a href="javascript:NewCal('ddate')"><img src="../includes/datetimepick/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a></td>
      </tr>
    <tr>
      <td class="boxlabel">Next service due at Kms</td>
      <td><input type="text" name="service" id="service"></td>
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
		
		$brname = $tt.' '.$fleetno;
		
		$moduledb = $_SESSION['s_findb'];
		mysql_select_db($moduledb) or die(mysql_error());

		$q = "insert into branch (branchname) values ('".$brname."')";		
		$r = mysql_query($q) or die(mysql_error().$q);
		$brno = mysql_insert_id(); 
		$branchcode = str_pad($brno,4,"0",STR_PAD_LEFT);
		
		$bru = "update branch set branch = '".$branchcode."' where uid = ".$brno;
		$brr = mysql_query($bru) or die($bru);
		
		$cc = $branchcode.'~'.$brname;
		
		// get array of system accounts
		$acquery = "select account,accountno,sub,blocked from glmast where branch = '0001' and system = 'Y'";
		$acresult = mysql_query($acquery) or die($acquery);
	
		while ($accrow  = mysql_fetch_array($acresult)) {
			extract($accrow);
			$mname = $account;
			$macno = $accountno;
			$msub = $sub;
			$mblocked = $blocked;
			$iquery = "insert into glmast (account,accountno,branch,sub,blocked,system,ctrlacc) values ";
			$iquery .= "('".$mname."',";
			$iquery .= $macno.",'";
			$iquery .= $branchcode."',";
			$iquery .= $msub.",'";
			$iquery .= $mblocked."',";
			$iquery .= "'Y',";
			$iquery .= "'Y')";
			
			$iresult = mysql_query($iquery) or die($iquery);		
		}

$moduledb = $_SESSION['s_logdb'];
		mysql_select_db($moduledb) or die(mysql_error());

		$q = "insert into vehicles (regno,cost_centre,branch,vehicleno,make,cofdate) values (";
		$q .= '"'.$regno.'",';
		$q .= '"'.$cc.'",';
		$q .= '"'.$branchcode.'",';
		$q .= '"'.$brname.'",';
		$q .= '"'.$vmake.'",';
		$q .= '"'.$cdate.'")';

		$r = mysql_query($q) or die(mysql_error().' '.$q);

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
