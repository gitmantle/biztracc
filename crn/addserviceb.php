<?php
session_start();

$vn = $_SESSION['s_vehicleno'];

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
<title>Add Service B</title>
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
	var nextdue = document.getElementById('nextdue').value;
	
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
	if (nextdue == "") {
		alert("Please enter Kms when next B service due.");
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
      <td colspan="4" bgcolor="<?php echo $bghead; ?>" align="center"><label style="color: <?php echo $thfont; ?>"><strong> B Service for <?php echo $vn; ?></strong></label></td>
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
    <tr bgcolor="#FFFF33">
      <td colspan="4" align="center">This check sheet is to be used in conjunction with manufacturers specifications and service requirements</td>
    </tr>
    <tr>
      <td class="boxlabelleft">CHECK</td>
      <td class="boxlabelleft">OK / Done</td>
      <td class="boxlabelleft">FRONT &amp; REAR SUSPENSION</td>
      <td class="boxlabelleft">OK / Done</td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Cooling system and test conditioner</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="lspecies" id="lspecies">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
        </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Spring leaves &amp; clamps</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="suspension1" id="suspension1">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Air filter</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="check2" id="check2">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Air bags - chafing / mounting</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="suspension2" id="suspension2">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Power steering oil level</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="check3" id="check3">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Air bags - leveling valve</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="suspension3" id="suspension3">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Transmission oil level</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="check4" id="check4">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Brackets, shackles, pins &amp; bushes</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="suspension4" id="suspension4">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Diff &amp; final drives oil level</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="check5" id="check5">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">U bolts &amp; centre bolts</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="suspension5" id="suspension5">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Clutch &amp; brake fluid level</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="check6" id="check6">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Torque rods &amp; panhard rods</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="suspension6" id="suspension6">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Battery condition &amp; level</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="check7" id="check7">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Spring mounts &amp; hangars</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="suspension7" id="suspension7">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">&nbsp;</td>
      <td class="boxlabelleft" bgcolor="#99FFFF">&nbsp;</td>
      <td class="boxlabelleft" >AIR SYSTEM</td>
      <td class="boxlabelleft" >&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabelleft" >GREASE</td>
      <td class="boxlabelleft" >&nbsp;</td>
      <td class="boxlabelleft" bgcolor="#99FF99">Air leaks - applied &amp; released</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="air1" id="air1">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Turntable plate, pivots &amp; jaws</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="grease1" id="grease1">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Tank &amp; mounting</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="air2" id="air2">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Driveline</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="grease2" id="grease2">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Hose &amp; pipework chafing</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="air3" id="air3">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Kingpins</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="grease3" id="grease3">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft" >FUEL SYSTEM</td>
      <td class="boxlabelleft" >&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Steering joints</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="grease4" id="grease4">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Leaks</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="fuel1" id="fuel1">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Supsension pivots</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="grease5" id="grease5">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Pipework tank &amp; mounting</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="fuel2" id="fuel2">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Clutch &amp; throttle linkage</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="grease6" id="grease6">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft" >MUDGUARDS &amp; BODY</td>
      <td class="boxlabelleft" >&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Lubricate doors &amp; hinges</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="grease7" id="grease7">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Mountings &amp; brackets</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="body1" id="body1">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Any other pivots &amp; linkages</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="grease8" id="grease8">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Mudflaps</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="body2" id="body2">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft">CHASSIS</td>
      <td class="boxlabelleft">&nbsp;</td>
      <td class="boxlabelleft" bgcolor="#99FF99">Body cracks</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="body3" id="body3">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Check &amp; adjust front wheel bearings</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="chassis1" id="chassis1">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Mezz floor security &amp; condition</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="body4" id="body4">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Check king pins</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="chassis2" id="chassis2">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft">TURNTABLE / RINGFEEDER</td>
      <td class="boxlabelleft">&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Check &amp; adjust brakes</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="chassis3" id="chassis3">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Mounting bolts</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="turn1" id="turn1">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Check &amp; adjust clutch</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="chassis4" id="chassis4">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Cracks</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="turn2" id="turn2">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Cross members - cracks / loose</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="chassis5" id="chassis5">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Fifth wheel service per schedule</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="turn3" id="turn3">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft">VISUAL INSPECTION OF</td>
      <td class="boxlabelleft">&nbsp;</td>
      <td class="boxlabelleft" bgcolor="#99FF99">Ringfeeder operation</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="turn4" id="turn4">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Engine oil leaks</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="visual1" id="visual1">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft">ELECTRICAL</td>
      <td class="boxlabelleft">&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Transmission oil leaks</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="visual2" id="visual2">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Headlamps - both beams</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="electric1" id="electric1">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Diff &amp; axle oil leaks</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="visual3" id="visual3">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Park lights</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="electric2" id="electric2">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Power steering system &amp; steering box</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="visual4" id="visual4">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Turn indicators - front, side &amp; rear</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="electric3" id="electric3">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">All drive belts</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="visual5" id="visual5">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Roof lights</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="electric4" id="electric4">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft">EXHAUST</td>
      <td class="boxlabelleft">&nbsp;</td>
      <td class="boxlabelleft" bgcolor="#99FF99">Tail lights</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="electric5" id="electric5">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Leaks</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="exhaust1" id="exhaust1">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Brake lights</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="electric6" id="electric6">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Pipwork &amp; clamps</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="exhaust2" id="exhaust2">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Number plate light</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="electric7" id="electric7">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Muffler &amp; mountings</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="exhaust3" id="exhaust3">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Reflectors</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="electric8" id="electric8">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft">DRIVE LINE</td>
      <td class="boxlabelleft">&nbsp;</td>
      <td class="boxlabelleft">GENERAL</td>
      <td class="boxlabelleft">&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Universal joints</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="drive1" id="drive1">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Wheels, nuts / studs</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="gen1" id="gen1">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Hangar bearing</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="drive2" id="drive2">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Tyres - damage, wear, match</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="gen2" id="gen2">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Diff flange</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="drive3" id="drive3">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Engine mounts</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="gen3" id="gen3">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft">STEERING</td>
      <td class="boxlabelleft">&nbsp;</td>
      <td class="boxlabelleft" bgcolor="#99FF99">Door hinges, catches &amp; locks</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="gen4" id="gen4">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Steering box adjustment</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="steering1" id="steering1">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Wiper blades &amp; arms</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="gen5" id="gen5">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Steering box mounting</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="steering2" id="steering2">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Driver controls &amp; instruments</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="gen6" id="gen6">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Tie rod &amp; drag link ends</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="steering3" id="steering3">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Tail lift &amp; hoist hydraulic oil level</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="gen7" id="gen7">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft">REPLACE</td>
      <td class="boxlabelleft">&nbsp;</td>
      <td class="boxlabelleft" bgcolor="#99FF99">Tail lift &amp; hoist operation</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="gen8" id="gen8">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Engine oil &amp; filter</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="replace1" id="replace1">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">&nbsp;</td>
      <td class="boxlabelleft" bgcolor="#99FF99">&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Fuel filter</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="replace2" id="replace2">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
      <td class="boxlabelleft">ROAD TEST</td>
      <td class="boxlabelleft">&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabelleft">COMMENTS</td>
      <td class="boxlabelleft">&nbsp;</td>
      <td class="boxlabelleft" bgcolor="#FF9900">Fill out lube sticker - use SPEEDO Km</td>
      <td class="boxlabelleft" bgcolor="#FF9900"><select name="road1" id="road1">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td colspan="4" bgcolor="#99FFFF" class="boxlabelleft"><textarea name="comments" id="comments" cols="110" rows="2"></textarea></td>
    </tr>
	<tr>
      <td colspan="3" class="boxlabelleft">Serviceman
        <input type="text" name="serviceman" id="serviceman">
        Next B Service due at Kms
        <input type="text" name="nextdue" id="nextdue"></td>
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
		$check1 = $_REQUEST['check1'];
		$check2 = $_REQUEST['check2'];
		$check3 = $_REQUEST['check3'];
		$check4 = $_REQUEST['check4'];
		$check5 = $_REQUEST['check5'];
		$check6 = $_REQUEST['check6'];
		$check7 = $_REQUEST['check7'];
		$grease1 = $_REQUEST['grease1'];
		$grease2 = $_REQUEST['grease2'];
		$grease3 = $_REQUEST['grease3'];
		$grease4 = $_REQUEST['grease4'];
		$grease5 = $_REQUEST['grease5'];
		$grease6 = $_REQUEST['grease6'];
		$grease7 = $_REQUEST['grease7'];
		$grease8 = $_REQUEST['grease8'];
		$chassis1 = $_REQUEST['chassis1'];
		$chassis2 = $_REQUEST['chassis2'];
		$chassis3 = $_REQUEST['chassis3'];
		$chassis4 = $_REQUEST['chassis4'];
		$chassis5 = $_REQUEST['chassis5'];
		$visual1 = $_REQUEST['visual1'];
		$visual2 = $_REQUEST['visual2'];
		$visual3 = $_REQUEST['visual3'];
		$visual4 = $_REQUEST['visual4'];
		$visual5 = $_REQUEST['visual5'];
		$exhaust1 = $_REQUEST['exhaust1'];
		$exhaust2 = $_REQUEST['exhaust2'];
		$exhaust3 = $_REQUEST['exhaust3'];
		$drive1 = $_REQUEST['drive1'];
		$drive2 = $_REQUEST['drive2'];
		$drive3 = $_REQUEST['drive3'];
		$steering1 = $_REQUEST['steering1'];
		$steering2 = $_REQUEST['steering2'];
		$steering3 = $_REQUEST['steering3'];
		$suspension1 = $_REQUEST['suspension1'];
		$suspension2 = $_REQUEST['suspension2'];
		$suspension3 = $_REQUEST['suspension3'];
		$suspension4 = $_REQUEST['suspension4'];
		$suspension5 = $_REQUEST['suspension5'];
		$suspension6 = $_REQUEST['suspension6'];
		$suspension7 = $_REQUEST['suspension7'];
		$air1 = $_REQUEST['air1'];
		$air2 = $_REQUEST['air2'];
		$air3 = $_REQUEST['air3'];
		$fuel1 = $_REQUEST['fuel1'];
		$fuel2 = $_REQUEST['fuel2'];
		$body1 = $_REQUEST['body1'];
		$body2 = $_REQUEST['body2'];
		$body3 = $_REQUEST['body3'];
		$body4 = $_REQUEST['body4'];
		$turn1 = $_REQUEST['turn1'];
		$turn2 = $_REQUEST['turn2'];
		$turn3 = $_REQUEST['turn3'];
		$turn4 = $_REQUEST['turn4'];
		$electric1 = $_REQUEST['electric1'];
		$electric2 = $_REQUEST['electric2'];
		$electric3 = $_REQUEST['electric3'];
		$electric4 = $_REQUEST['electric4'];
		$electric5 = $_REQUEST['electric5'];
		$electric6 = $_REQUEST['electric6'];
		$electric7 = $_REQUEST['electric7'];
		$electric8 = $_REQUEST['electric8'];
		$gen1 = $_REQUEST['gen1'];
		$gen2 = $_REQUEST['gen2'];
		$gen3 = $_REQUEST['gen3'];
		$gen4 = $_REQUEST['gen4'];
		$gen5 = $_REQUEST['gen5'];
		$gen6 = $_REQUEST['gen6'];
		$gen7 = $_REQUEST['gen7'];
		$gen8 = $_REQUEST['gen8'];
		$replace1 = $_REQUEST['replace1'];
		$replace2 = $_REQUEST['replace2'];
		$road1 = $_REQUEST['road1'];
		$comments = $_REQUEST['comments'];
		$serviceman = addslashes($_REQUEST['serviceman']);
		$nextdue = $_REQUEST['nextdue'];
		
		include_once("../includes/DBClass.php");
		$db = new DBClass();

		$q = "insert into ".$crndb.".service (vehicleno,date,hubodometer,speedo,jobno,workshop,service_type,servicedue) values ('".$vn."','".$ddate."',".$hubo.",".$speedo.",'".$jobno."','".$workshop."','B',".$nextdue.")";
		$db->query($q);
		$db->execute();
		$sid = $db->lastInsertId();

		$q = "insert into ".$crndb.".serviceb (service_id,workshop,jobno,ddate,hubo,speedo,check1,check2,check3,check4,check5,check6,check7,grease1,grease2,grease3,grease4,grease5,grease6,grease7,grease8,chassis1,chassis2,chassis3,chassis4,chassis5,visual1,visual2,visual3,visual4,visual5,exhaust1,exhaust2,exhaust3,drive1,drive2,drive3,steering1,steering2,steering3,suspension1,suspension2,suspension3,suspension4,suspension5,suspension6,suspension7,air1,air2,air3,fuel1,fuel2,body1,body2,body3,body4,turn1,turn2,turn3,turn4,electric1,electric2,electric3,electric4,electric5,electric6,electric7,electric8,gen1,gen2,gen3,gen4,gen5,gen6,gen7,gen8,replace1,replace2,road1,comments,serviceman,servicedue) values (".$sid.",'".$workshop."','".$jobno."','".$ddate."',".$hubo.",".$speedo.",'".$check1."','".$check2."','".$check3."','".$check4."','".$check5."','".$check6."','".$check7."','".$grease1."','".$grease2."','".$grease3."','".$grease4."','".$grease5."','".$grease6."','".$grease7."','".$grease8."','".$chassis1."','".$chassis2."','".$chassis3."','".$chassis4."','".$chassis5."','".$visual1."','".$visual2."','".$visual3."','".$visual4."','".$visual5."','".$exhaust1."','".$exhaust2."','".$exhaust3."','".$drive1."','".$drive2."','".$drive3."','".$steering1."','".$steering2."','".$steering3."','".$suspension1."','".$suspension2."','".$suspension3."','".$suspension4."','".$suspension5."','".$suspension6."','".$suspension7."','".$air1."','".$air2."','".$air3."','".$fuel1."','".$fuel2."','".$body1."','".$body2."','".$body3."','".$body4."','".$turn1."','".$turn2."','".$turn3."','".$turn4."','".$electric1."','".$electric2."','".$electric3."','".$electric4."','".$electric5."','".$electric6."','".$electric7."','".$electric8."','".$gen1."','".$gen2."','".$gen3."','".$gen4."','".$gen5."','".$gen6."','".$gen7."','".$gen8."','".$replace1."','".$replace2."','".$road1."','".$comments."','".$serviceman."','".$nextdue."')";

		$db->query($q);
		$db->execute();

		$qdb->query("update ".$crndb.".vehicles set lastserviced = '".$ddate."', servicedue = ".$nextdue." where vehicleno = '".$vn."'");
		$db->execute();
		
		$db->closeDB();

?>
	  <script>
	  window.open("","maintenance").jQuery("#servicelist").trigger("reloadGrid");
	  this.close();
	  </script>
	  <?php
		
			
	}

?>


</body>
</html>
