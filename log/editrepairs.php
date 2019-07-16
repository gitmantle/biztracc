<?php
session_start();

$vn = $_SESSION['s_vehicleno'];
$sid = $_REQUEST['id'];

require_once('../db.php');

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select * from vehicles where vehicleno = '".$vn."'";
$r = mysql_query($q) or die (mysql_error());
$row = mysql_fetch_array($r);
extract($row);
$cd = explode('-',$cofdate);
$cof = $cd[2].'/'.$cd[1].'/'.$cd[0];
$mk = $make;


$q = "select * from repairs where service_id = ".$sid;
$r = mysql_query($q) or die (mysql_error());
$row = mysql_fetch_array($r);
extract($row);
$sd = explode('-',$ddate);
$servdate = $sd[2].'/'.$sd[1].'/'.$sd[0];


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
<title>Edit Repair</title>
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


function ajaxGetWSstaff(wshop) {
	var ws = wshop.split('~');
	var id = ws[0];
	//populate workshop staff list
		$.get("includes/ajaxGetWSstaff.php", {id: id}, function(data){
			$("#serviceman").append(data);
		});	
}

function post() {

	//add validation here if required.
	var wshop = document.getElementById('wshop').value;
	var jobno = document.getElementById('jobno').value;
	var serviceman = document.getElementById('serviceman').value;
	
	var ok = "Y";
	if (wshop == 0) {
		alert("Please select a workshop.");
		ok = "N";
		return false;
	}
	if (jobno == "") {
		alert("Please enter a job number.");
		ok = "N";
		return false;
	}
	if (serviceman == "") {
		alert("Please select a serviceman.");
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
 <table width="970" border="0" align="center" cellspacing="4" bgcolor="<?php echo $bgcolor; ?>">
    <tr>
      <td colspan="4" bgcolor="<?php echo $bghead; ?>" align="center"><label style="color: <?php echo $thfont; ?>"><strong> Repair for <?php echo $vn; ?></strong></label></td>
    </tr>
    <tr>
      <td class="boxlabelleft">Workshop
      <input type="text" name="wshop" id="wshop" value="<?php echo $workshop; ?>" readonly></td>
      <td class="boxlabelleft">Reg No
      <input type="text" name="regno" id="regno" value="<?php echo $regno; ?>" readonly></td>
      <td class="boxlabelleft">Job No
        <input type="text" name="jobno" id="jobno" value="<?php echo $jobno; ?>" readonly></td>
      <td class="boxlabelleft">Job Date
      <input type="text" name="ddate" id="ddate" value="<?php echo $servdate; ?>" readonly></td>
    </tr>
    <tr>
      <td class="boxlabelleft">Make
      <input type="text" name="make" id="make" value="<?php echo $mk; ?>" readonly></td>
      <td class="boxlabelleft">COF
      <input type="text" name="cofdue" id="cofdue" readonly value="<?php echo $cof; ?>"></td>
      <td class="boxlabelleft">Hub Km
      <input type="text" name="hubo" id="hubo"  readonly value="<?php echo $hubo; ?>"></td>
      <td class="boxlabelleft">Speedo Km
      <input type="text" name="speedo" id="speedo"  readonly value="<?php echo $speedo; ?>"></td>
    </tr>
 </table>
 <table width="970" border="0" align="center" cellspacing="4" bgcolor="<?php echo $bgcolor; ?>">
    <tr>
      <td class="boxlabelleft">DEFECTS</td>
      <td class="boxlabelleft">REPAIRS</td>
    </tr>
    <tr>
      <td bgcolor="#99FFFF" class="boxlabelleft"><textarea name="defect1" id="defect1" cols="50" rows="2"><?php echo $defect1; ?></textarea></td>
      <td bgcolor="#99FF99" class="boxlabelleft"><textarea name="repair1" id="repair1" cols="50" rows="2"><?php echo $repair1; ?></textarea></td>
    </tr>
    <tr>
      <td bgcolor="#99FFFF" class="boxlabelleft"><textarea name="defect2" id="defect2" cols="50" rows="2"><?php echo $defect2; ?></textarea></td>
      <td bgcolor="#99FF99" class="boxlabelleft"><textarea name="repair2" id="repair2" cols="50" rows="2"><?php echo $repair2; ?></textarea></td>
    </tr>
    <tr>
      <td bgcolor="#99FFFF" class="boxlabelleft"><textarea name="defect3" id="defect3" cols="50" rows="2"><?php echo $defect3; ?></textarea></td>
      <td bgcolor="#99FF99" class="boxlabelleft"><textarea name="repair3" id="repair3" cols="50" rows="2"><?php echo $repair3; ?></textarea></td>
    </tr>
    <tr>
      <td bgcolor="#99FFFF" class="boxlabelleft"><textarea name="defect4" id="defect4" cols="50" rows="2"><?php echo $defect4; ?></textarea></td>
      <td bgcolor="#99FF99" class="boxlabelleft"><textarea name="repair4" id="repair4" cols="50" rows="2"><?php echo $repair4; ?></textarea></td>
    </tr>
    <tr>
      <td bgcolor="#99FFFF" class="boxlabelleft"><textarea name="defect5" id="defect5" cols="50" rows="2"><?php echo $defect5; ?></textarea></td>
      <td bgcolor="#99FF99" class="boxlabelleft"><textarea name="repair5" id="repair5" cols="50" rows="2"><?php echo $repair5; ?></textarea></td>
    </tr>
    <tr>
      <td bgcolor="#99FFFF" class="boxlabelleft"><textarea name="defect6" id="defect6" cols="50" rows="2"><?php echo $defect6; ?></textarea></td>
      <td bgcolor="#99FF99" class="boxlabelleft"><textarea name="repair6" id="repair6" cols="50" rows="2"><?php echo $repair6; ?></textarea></td>
    </tr>
    <tr>
      <td bgcolor="#99FFFF" class="boxlabelleft"><textarea name="defect7" id="defect7" cols="50" rows="2"><?php echo $defect7; ?></textarea></td>
      <td bgcolor="#99FF99" class="boxlabelleft"><textarea name="repair7" id="repair7" cols="50" rows="2"><?php echo $repair7; ?></textarea></td>
    </tr>
    <tr>
      <td bgcolor="#99FFFF" class="boxlabelleft"><textarea name="defect8" id="defect8" cols="50" rows="2"><?php echo $defect8; ?></textarea></td>
      <td bgcolor="#99FF99" class="boxlabelleft"><textarea name="repair8" id="repair8" cols="50" rows="2"><?php echo $repair8; ?></textarea></td>
    </tr>
    <tr>
      <td bgcolor="#99FFFF" class="boxlabelleft"><textarea name="defect9" id="defect9" cols="50" rows="2"><?php echo $defect9; ?></textarea></td>
      <td bgcolor="#99FF99" class="boxlabelleft"><textarea name="repair9" id="repair9" cols="50" rows="2"><?php echo $repair9; ?></textarea></td>
    </tr>
    <tr>
      <td bgcolor="#99FFFF" class="boxlabelleft"><textarea name="defect10" id="defect10" cols="50" rows="2"><?php echo $defect10; ?></textarea></td>
      <td bgcolor="#99FF99" class="boxlabelleft"><textarea name="repair10" id="repair10" cols="50" rows="2"><?php echo $repair10; ?></textarea></td>
    </tr>
	<tr>
      <td class="boxlabelleft">Serviceman
      <input type="text" name="serviceman" id="serviceman" value="<?php echo $serviceman; ?>" readonly></td>
      <td align="right"><input type="button" value="Save" name="save" onClick="post()"  ></td>
      </tr>
  	</table>
</form>

<?php

	if($_REQUEST['savebutton'] == "Y") {
		$wshop = $_REQUEST['wshop'];
		$w = explode('~',$wshop);
		$workshop = addslashes($w[1]);
		$jobno = $_REQUEST['jobno'];
		$ddate = $_REQUEST['ddateh'];
		$hubo = $_REQUEST['hubo'];
		$speedo = $_REQUEST['speedo'];
		$defect1 = $_REQUEST['defect1'];
		$defect2 = $_REQUEST['defect2'];
		$defect3 = $_REQUEST['defect3'];
		$defect4 = $_REQUEST['defect4'];
		$defect5 = $_REQUEST['defect5'];
		$defect6 = $_REQUEST['defect6'];
		$defect7 = $_REQUEST['defect7'];
		$defect8 = $_REQUEST['defect8'];
		$defect9 = $_REQUEST['defect9'];
		$defect10 = $_REQUEST['defect10'];
		$repair1 = $_REQUEST['repair1'];
		$repair2 = $_REQUEST['repair2'];
		$repair3 = $_REQUEST['repair3'];
		$repair4 = $_REQUEST['repair4'];
		$repair5 = $_REQUEST['repair5'];
		$repair6 = $_REQUEST['repair6'];
		$repair7 = $_REQUEST['repair7'];
		$repair8 = $_REQUEST['repair8'];
		$repair9 = $_REQUEST['repair9'];
		$repair10 = $_REQUEST['repair10'];
		$serviceman = addslashes($_REQUEST['serviceman']);
		
		$q = "update repairs set ";
		$q .= "defect1 = '".$defect1."',defect2 = '".$defect2."',defect3 = '".$defect3."',defect4 = '".$defect4."',defect5 = '".$defect5."',defect6 = '".$defect6."',defect7 = '".$defect7."',defect8 = '".$defect8."',defect9 = '".$defect9."',defect10 = '".$defect10."',";
		$q .= "repair1 = '".$repair1."',repair2 = '".$repair2."',repair3 = '".$repair3."',repair4 = '".$repair4."',repair5 = '".$repair5."',repair6 = '".$repair6."',repair7 = '".$repair7."',repair8 = '".$repair8."',repair9 = '".$repair9."',repair10 = '".$repair10."',";
		$q .= "serviceman = '".$serviceman."'";
		$q .= " where service_id = ".$sid;
		
		$r = mysql_query($q) or die(mysql_error().$q);

	  ?>
	  <script>
	  window.open("","maintenance").jQuery("#repairlist").trigger("reloadGrid");
	  this.close();
	  </script>
	  <?php
		
			
	}

?>


</body>
</html>
