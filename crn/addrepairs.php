<?php
session_start();

$vn = $_SESSION['s_vehicleno'];
$wsid = $_SESSION['s_workshop'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$crndb = $_SESSION['s_crndb'];

// populate workshop  drop down
$db->query("select uid,workshop from ".$crndb.".workshop");
$rows = $db->resultset();
$wshop_options = "<option value=\"0\">Select Workshop</option>";
foreach ($rows as $row) {
	extract($row);

		$selected = '';

	$wshop_options .= '<option value="'.$uid.'~'.$workshop.'"'.$selected.'>'.$workshop.'</option>';
}

$db->query("select regno, make, cofdate from ".$crndb.".vehicles where vehicleno = '".$vn."'");
$row = $db->single();
extract($row);

$cd = explode('-',$cofdate);
$cof = $cd[2].'/'.$cd[1].'/'.$cd[0];

date_default_timezone_set($_SESSION['s_timezone']);
$ddate = date("d/m/Y");
$hdate = date("Y-m-d");

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

$db->closeDB();

?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add Repair</title>
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
        <select name="wshop" id="wshop" onchange="ajaxGetWSstaff(this.value); return false;">
      	<?php echo $wshop_options; ?>
      </select></td>
      <td class="boxlabelleft">Reg No
      <input type="text" name="regno" id="regno" value="<?php echo $regno; ?>" readonly></td>
      <td class="boxlabelleft">Job No
        <input type="text" name="jobno" id="jobno"></td>
      <td class="boxlabelleft">Job Date
      <input type="Text" id="ddate" name="ddate" maxlength="25" size="25" value="<?php echo $ddate; ?>"><a href="javascript:NewCal('ddate')"><img src="../includes/datetimepick/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a></td>
    </tr>
    <tr>
      <td class="boxlabelleft">Make
      <input type="text" name="make" id="make" value="<?php echo $make; ?>" readonly></td>
      <td class="boxlabelleft">COF
      <input type="text" name="cofdue" id="cofdue" readonly value="<?php echo $cof; ?>"></td>
      <td class="boxlabelleft">Hub Km
      <input type="text" name="hubo" id="hubo" value="0" onFocus="this.select();"></td>
      <td class="boxlabelleft">Speedo Km
      <input type="text" name="speedo" id="speedo" value="0" onFocus="this.select();"></td>
    </tr>
 </table>
 <table width="970" border="0" align="center" cellspacing="4" bgcolor="<?php echo $bgcolor; ?>">
    <tr>
      <td class="boxlabelleft">DEFECTS</td>
      <td class="boxlabelleft">REPAIRS</td>
    </tr>
    <tr>
      <td bgcolor="#99FFFF" class="boxlabelleft"><textarea name="defect1" id="defect1" cols="50" rows="2"></textarea></td>
      <td bgcolor="#99FF99" class="boxlabelleft"><textarea name="repair1" id="repair1" cols="50" rows="2"></textarea></td>
    </tr>
    <tr>
      <td bgcolor="#99FFFF" class="boxlabelleft"><textarea name="defect2" id="defect2" cols="50" rows="2"></textarea></td>
      <td bgcolor="#99FF99" class="boxlabelleft"><textarea name="repair2" id="repair2" cols="50" rows="2"></textarea></td>
    </tr>
    <tr>
      <td bgcolor="#99FFFF" class="boxlabelleft"><textarea name="defect3" id="defect3" cols="50" rows="2"></textarea></td>
      <td bgcolor="#99FF99" class="boxlabelleft"><textarea name="repair3" id="repair3" cols="50" rows="2"></textarea></td>
    </tr>
    <tr>
      <td bgcolor="#99FFFF" class="boxlabelleft"><textarea name="defect4" id="defect4" cols="50" rows="2"></textarea></td>
      <td bgcolor="#99FF99" class="boxlabelleft"><textarea name="repair4" id="repair4" cols="50" rows="2"></textarea></td>
    </tr>
    <tr>
      <td bgcolor="#99FFFF" class="boxlabelleft"><textarea name="defect5" id="defect5" cols="50" rows="2"></textarea></td>
      <td bgcolor="#99FF99" class="boxlabelleft"><textarea name="repair5" id="repair5" cols="50" rows="2"></textarea></td>
    </tr>
    <tr>
      <td bgcolor="#99FFFF" class="boxlabelleft"><textarea name="defect6" id="defect6" cols="50" rows="2"></textarea></td>
      <td bgcolor="#99FF99" class="boxlabelleft"><textarea name="repair6" id="repair6" cols="50" rows="2"></textarea></td>
    </tr>
    <tr>
      <td bgcolor="#99FFFF" class="boxlabelleft"><textarea name="defect7" id="defect7" cols="50" rows="2"></textarea></td>
      <td bgcolor="#99FF99" class="boxlabelleft"><textarea name="repair7" id="repair7" cols="50" rows="2"></textarea></td>
    </tr>
    <tr>
      <td bgcolor="#99FFFF" class="boxlabelleft"><textarea name="defect8" id="defect8" cols="50" rows="2"></textarea></td>
      <td bgcolor="#99FF99" class="boxlabelleft"><textarea name="repair8" id="repair8" cols="50" rows="2"></textarea></td>
    </tr>
    <tr>
      <td bgcolor="#99FFFF" class="boxlabelleft"><textarea name="defect9" id="defect9" cols="50" rows="2"></textarea></td>
      <td bgcolor="#99FF99" class="boxlabelleft"><textarea name="repair9" id="repair9" cols="50" rows="2"></textarea></td>
    </tr>
    <tr>
      <td bgcolor="#99FFFF" class="boxlabelleft"><textarea name="defect10" id="defect10" cols="50" rows="2"></textarea></td>
      <td  colspan="2" bgcolor="#99FF99" class="boxlabelleft"><textarea name="repair10" id="repair10" cols="50" rows="2"></textarea></td>
    </tr>
	<tr>
      <td class="boxlabelleft">Serviceman
                <input type="text" name="serviceman" id="serviceman"></td>
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
		
		include_once("../includes/DBClass.php");
		$db = new DBClass();
		
		$db->query("insert into ".$crn.".service (vehicleno,date,hubodometer,speedo,jobno,workshop,service_type) values ('".$vn."','".$ddate."',".$hubo.",".$speedo.",'".$jobno."','".$workshop."','R')");
		$db->query($q);
		$db->execute();
		$sid = $db->lastInsertId();

		$q = "insert into repairs (service_id,workshop,jobno,ddate,hubo,speedo,defect1,defect2,defect3,defect4,defect5,defect6,defect7,defect8,defect9,defect10,repair1,repair2,repair3,repair4,repair5,repair6,repair7,repair8,repair9,repair10,serviceman) values (".$sid.",'".$workshop."','".$jobno."','".$ddate."',".$hubo.",".$speedo.",'".$defect1."','".$defect2."','".$defect3."','".$defect4."','".$defect5."','".$defect6."','".$defect7."','".$defect8."','".$defect9."','".$defect10."','".$repair1."','".$repair2."','".$repair3."','".$repair4."','".$repair5."','".$repair6."','".$repair7."','".$repair8."','".$repair9."','".$repair10."','".$serviceman."')";

		$db->query($q);
		$db->execute();
		
		$db->colsoeDB();
		
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
