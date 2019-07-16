<?php
session_start();
$vn = $_SESSION['s_vehicleno'];
$admindb = $_SESSION['s_admindb'];

require_once('../db.php');

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
<title>Add RUC Details</title>
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
	var licence = document.getElementById('licence').value;
	
	var ok = "Y";
	if (licence == "") {
		alert("Please enter a RUC licence number.");
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
      <td colspan="2" bgcolor="<?php echo $bghead; ?>" align="center"><label style="color: <?php echo $thfont; ?>"><strong>Add RUC Details for <?php echo $vn; ?></strong></label></td>
    </tr>
    <tr>
      <td class="boxlabel">RUC Licence</td>
      <td><input name="licence" type="text" id="licence"></td>
      </tr>
    <tr>
      <td class="boxlabel">Date Issued</td>
      <td><input type="Text" id="ddate" name="ddate" maxlength="25" size="25" value="<?php echo $ddate; ?>"><a href="javascript:NewCal('ddate')"><img src="../includes/datetimepick/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a></td>
    </tr>
    <tr>
      <td class="boxlabel">RUC from Kms</td>
      <td><input type="text" name="fromkms" id="fromkms"></td>
    </tr>
    <tr>
      <td class="boxlabel">RUC valid to Kms</td>
      <td><input type="text" name="ruckms" id="ruckms"></td>
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
		$vehicleno = $vn;
		$licence = $_REQUEST['licence'];
		$fromkms = $_REQUEST['fromkms'];
		$ruckms = $_REQUEST['ruckms'];
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
		$q = "insert into rucs (vehicleno,ruclicence,date_issued,fromkms,ruckms) values (";
		$q .= '"'.$vehicleno.'",';
		$q .= '"'.$licence.'",';
		$q .= '"'.$ddate.'",';
		$q .= $fromkms.',';
		$q .= $ruckms.')';

		$r = mysql_query($q) or die(mysql_error().$q);

	  ?>
	  <script>
	  window.open("","maintenance").jQuery("#ruclist").trigger("reloadGrid");
	  this.close();
	  </script>
	  <?php
		
			
	}

?>


</body>
</html>
