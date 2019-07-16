<?php
session_start();

$incid = $_SESSION['s_incidentid'];
$admindb = $_SESSION['s_admindb'];
$id = $_REQUEST['uid'];

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

$q = "select * from incinjuries where uid = ".$id;
$r = mysql_query($q) or die (mysql_error());
$row = mysql_fetch_array($r);
extract($row);
$id = $uid;
$iname = $name;
$iinjury1 = $injury1;
$ibody1 = $body1;
$iinjury2 = $injury2;
$ibody2 = $body2;
$iinjury3 = $injury3;
$ibody3 = $body3;
$iinjury4 = $injury4;
$ibody4 = $body4;
$iinjury5 = $injury5;
$ibody5 = $body5;
$iinjury6 = $injury6;
$ibody6 = $body6;
$itreatment = $treatment;
$iseverity = $severity;
$ilegal = $legal;
$idayslost = $dayslost;

// populate severity drop down
$query = "select incseverity from incseverity";
$result = mysql_query($query) or die(mysql_error().$query);
$severity_options = "<option value=\" \">Not applicable</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($iseverity == $incseverity) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$severity_options .= '<option value="'.$incseverity.'"'.$selected.'>'.$incseverity.'</option>';
}

// populate injury 1 drop down
    $arr = array('Amputation','Blindness - Permanent','Blindness- Temporary','Bruising','Burn','Concussion','Crush','Cut/Laceration','Deafness - Permanent','Deafness - Temporary','Death','Dehydration','Depression','Dizzyness','Faint','Foreign Object','Fracture','Heat Stroke','Internal Bleeding','Mental Strain','Poisoning - Chemical','Poisoning - Other','Puncture','Scratch/Graze','Sprain/Strain','Sting','Unconsciousness','Vomiting');
	$injury1_options = "<option value=\" \">Select</option>";
    for($i = 0; $i < count($arr); $i++)	{
			if ($arr[$i] == $iinjury1) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
		$injury1_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}
	
// populate body 1 drop down
    $arr = array('Head','Eye','Left Eye','Right Eye','Nose','Mouth','Ear','Left Ear','Right Ear','Neck','Shoulder','Left Shoulder','Right Shoulder','Arm','Left Arm','Right Arm','Wrist','Left Wrist','Right Wrist','Hand','Left hand','Right Hand','Fingers','Fingers - Left Hand','Fingers - Right Hand','Back','Upper Back','Lower Back','Chest','Stomach','Groin','Buttock','Upper Leg','Lower Leg','Upper Left Leg','Lower Left Leg','Upper Right Leg','Lower Right Leg','Ankle','Left Ankle','Right Ankle','Foot','Left Foot','Right Foot','Toes - Left Foot','Toes - Right Foot');
	$body1_options = "<option value=\" \">Select</option>";
    for($i = 0; $i < count($arr); $i++)	{
			if ($arr[$i] == $ibody1) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
		$body1_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}
	
// populate injury 2 drop down
    $arr = array('Amputation','Blindness - Permanent','Blindness- Temporary','Bruising','Burn','Concussion','Crush','Cut/Laceration','Deafness - Permanent','Deafness - Temporary','Dehydration','Depression','Dizzyness','Faint','Foreign Object','Fracture','Heat Stroke','Internal Bleeding','Mental Strain','Poisoning - Chemical','Poisoning - Other','Puncture','Scratch/Graze','Sprain/Strain','Sting','Unconsciousness','Vomiting');
	$injury2_options = "<option value=\" \">Select</option>";
    for($i = 0; $i < count($arr); $i++)	{
			if ($arr[$i] == $iinjury2) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
		$injury2_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}
	
// populate body 2 drop down
    $arr = array('Head','Eye','Left Eye','Right Eye','Nose','Mouth','Ear','Left Ear','Right Ear','Neck','Shoulder','Left Shoulder','Right Shoulder','Arm','Left Arm','Right Arm','Wrist','Left Wrist','Right Wrist','Hand','Left hand','Right Hand','Fingers','Fingers - Left Hand','Fingers - Right Hand','Back','Upper Back','Lower Back','Chest','Stomach','Groin','Buttock','Upper Leg','Lower Leg','Upper Left Leg','Lower Left Leg','Upper Right Leg','Lower Right Leg','Ankle','Left Ankle','Right Ankle','Foot','Left Foot','Right Foot','Toes - Left Foot','Toes - Right Foot');
	$body2_options = "<option value=\" \">Select</option>";
    for($i = 0; $i < count($arr); $i++)	{
			if ($arr[$i] == $ibody2) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
		$body2_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}
	
// populate injury 3 drop down
    $arr = array('Amputation','Blindness - Permanent','Blindness- Temporary','Bruising','Burn','Concussion','Crush','Cut/Laceration','Deafness - Permanent','Deafness - Temporary','Dehydration','Depression','Dizzyness','Faint','Foreign Object','Fracture','Heat Stroke','Internal Bleeding','Mental Strain','Poisoning - Chemical','Poisoning - Other','Puncture','Scratch/Graze','Sprain/Strain','Sting','Unconsciousness','Vomiting');
	$injury3_options = "<option value=\" \">Select</option>";
    for($i = 0; $i < count($arr); $i++)	{
			if ($arr[$i] == $iinjury3) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
		$injury3_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}
	
// populate body 3 drop down
    $arr = array('Head','Eye','Left Eye','Right Eye','Nose','Mouth','Ear','Left Ear','Right Ear','Neck','Shoulder','Left Shoulder','Right Shoulder','Arm','Left Arm','Right Arm','Wrist','Left Wrist','Right Wrist','Hand','Left hand','Right Hand','Fingers','Fingers - Left Hand','Fingers - Right Hand','Back','Upper Back','Lower Back','Chest','Stomach','Groin','Buttock','Upper Leg','Lower Leg','Upper Left Leg','Lower Left Leg','Upper Right Leg','Lower Right Leg','Ankle','Left Ankle','Right Ankle','Foot','Left Foot','Right Foot','Toes - Left Foot','Toes - Right Foot');
	$body3_options = "<option value=\" \">Select</option>";
    for($i = 0; $i < count($arr); $i++)	{
			if ($arr[$i] == $ibody3) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
		$body3_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}
	
// populate injury 4 drop down
    $arr = array('Amputation','Blindness - Permanent','Blindness- Temporary','Bruising','Burn','Concussion','Crush','Cut/Laceration','Deafness - Permanent','Deafness - Temporary','Dehydration','Depression','Dizzyness','Faint','Foreign Object','Fracture','Heat Stroke','Internal Bleeding','Mental Strain','Poisoning - Chemical','Poisoning - Other','Puncture','Scratch/Graze','Sprain/Strain','Sting','Unconsciousness','Vomiting');
	$injury4_options = "<option value=\" \">Select</option>";
    for($i = 0; $i < count($arr); $i++)	{
			if ($arr[$i] == $iinjury4) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
		$injury4_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}
	
// populate body 4 drop down
    $arr = array('Head','Eye','Left Eye','Right Eye','Nose','Mouth','Ear','Left Ear','Right Ear','Neck','Shoulder','Left Shoulder','Right Shoulder','Arm','Left Arm','Right Arm','Wrist','Left Wrist','Right Wrist','Hand','Left hand','Right Hand','Fingers','Fingers - Left Hand','Fingers - Right Hand','Back','Upper Back','Lower Back','Chest','Stomach','Groin','Buttock','Upper Leg','Lower Leg','Upper Left Leg','Lower Left Leg','Upper Right Leg','Lower Right Leg','Ankle','Left Ankle','Right Ankle','Foot','Left Foot','Right Foot','Toes - Left Foot','Toes - Right Foot');
	$body4_options = "<option value=\" \">Select</option>";
    for($i = 0; $i < count($arr); $i++)	{
			if ($arr[$i] == $ibody4) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
		$body4_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}
	
// populate injury 5 drop down
    $arr = array('Amputation','Blindness - Permanent','Blindness- Temporary','Bruising','Burn','Concussion','Crush','Cut/Laceration','Deafness - Permanent','Deafness - Temporary','Dehydration','Depression','Dizzyness','Faint','Foreign Object','Fracture','Heat Stroke','Internal Bleeding','Mental Strain','Poisoning - Chemical','Poisoning - Other','Puncture','Scratch/Graze','Sprain/Strain','Sting','Unconsciousness','Vomiting');
	$injury5_options = "<option value=\" \">Select</option>";
    for($i = 0; $i < count($arr); $i++)	{
			if ($arr[$i] == $iinjury5) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
		$injury5_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}
	
// populate body 5 drop down
    $arr = array('Head','Eye','Left Eye','Right Eye','Nose','Mouth','Ear','Left Ear','Right Ear','Neck','Shoulder','Left Shoulder','Right Shoulder','Arm','Left Arm','Right Arm','Wrist','Left Wrist','Right Wrist','Hand','Left hand','Right Hand','Fingers','Fingers - Left Hand','Fingers - Right Hand','Back','Upper Back','Lower Back','Chest','Stomach','Groin','Buttock','Upper Leg','Lower Leg','Upper Left Leg','Lower Left Leg','Upper Right Leg','Lower Right Leg','Ankle','Left Ankle','Right Ankle','Foot','Left Foot','Right Foot','Toes - Left Foot','Toes - Right Foot');
	$body5_options = "<option value=\" \">Select</option>";
    for($i = 0; $i < count($arr); $i++)	{
			if ($arr[$i] == $ibody5) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
		$body5_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}
	
// populate injury 6 drop down
    $arr = array('Amputation','Blindness - Permanent','Blindness- Temporary','Bruising','Burn','Concussion','Crush','Cut/Laceration','Deafness - Permanent','Deafness - Temporary','Dehydration','Depression','Dizzyness','Faint','Foreign Object','Fracture','Heat Stroke','Internal Bleeding','Mental Strain','Poisoning - Chemical','Poisoning - Other','Puncture','Scratch/Graze','Sprain/Strain','Sting','Unconsciousness','Vomiting');
	$injury6_options = "<option value=\" \">Select</option>";
    for($i = 0; $i < count($arr); $i++)	{
			if ($arr[$i] == $iinjury6) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
		$injury6_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}
	
// populate body 6 drop down
    $arr = array('Head','Eye','Left Eye','Right Eye','Nose','Mouth','Ear','Left Ear','Right Ear','Neck','Shoulder','Left Shoulder','Right Shoulder','Arm','Left Arm','Right Arm','Wrist','Left Wrist','Right Wrist','Hand','Left hand','Right Hand','Fingers','Fingers - Left Hand','Fingers - Right Hand','Back','Upper Back','Lower Back','Chest','Stomach','Groin','Buttock','Upper Leg','Lower Leg','Upper Left Leg','Lower Left Leg','Upper Right Leg','Lower Right Leg','Ankle','Left Ankle','Right Ankle','Foot','Left Foot','Right Foot','Toes - Left Foot','Toes - Right Foot');
	$body6_options = "<option value=\" \">Select</option>";
    for($i = 0; $i < count($arr); $i++)	{
			if ($arr[$i] == $ibody6) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
		$body6_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}
	
	
	
// populate legal drop down
$query = "select inclegal from inclegal";
$result = mysql_query($query) or die(mysql_error().$query);
$legal_options = "<option value=\" \">Not applicable</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($ilegal == $inclegal) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$legal_options .= '<option value="'.$inclegal.'"'.$selected.'>'.$inclegal.'</option>';
}

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add People Injured</title>
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

function post() {

	//add validation here if required.
	var name = document.getElementById('name').value;
	
	var ok = "Y";
	if (name == "") {
		alert("Please enter a name.");
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
      <td colspan="2" bgcolor="<?php echo $bghead; ?>" align="center"><label style="color: <?php echo $thfont; ?>"><strong>Edit Person Injured </strong></label></td>
    </tr>
    <tr>
      <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Name</label></td>
      <td><input name="name" type="text" id="name" size="50" value="<?php echo $iname; ?>"></td>
      </tr>
    <tr>
      <td class="boxlabelleft">Injuries</td>
      <td class="boxlabelleft">Part of Body</td>
    </tr>
    <tr>
      <td class="boxlabelleft"><select name="injury1" id="injury1">
      	<?php echo $injury1_options; ?>
      </select></td>
      <td><select name="body1" id="body1">
      	<?php echo $body1_options; ?>
      </select></td>
      </tr>
    <tr>
      <td class="boxlabelleft"><select name="injury2" id="injury2">
      	<?php echo $injury2_options; ?>
      </select></td>
      <td><select name="body2" id="body2">
      	<?php echo $body2_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft"><select name="injury3" id="injury3">
      	<?php echo $injury3_options; ?>
      </select></td>
      <td><select name="body3" id="body3">
      	<?php echo $body3_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft"><select name="injury4" id="injury4">
      	<?php echo $injury4_options; ?>
      </select></td>
      <td><select name="body4" id="body4">
      	<?php echo $body4_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft"><select name="injury5" id="injury5">
      	<?php echo $injury5_options; ?>
      </select></td>
      <td><select name="body5" id="body5">
      	<?php echo $body5_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft"><select name="injury6" id="injury6">
      	<?php echo $injury6_options; ?>
      </select></td>
      <td><select name="body6" id="body6">
      	<?php echo $body6_options; ?>
      </select></td>
    </tr>
	<tr>
	  <td class="boxlabelleft">Treatment</td>
	  <td><textarea name="treatment" id="treatment" cols="50" rows="5"><?php echo $itreatment; ?></textarea></td>
    </tr>
	<tr>
	  <td class="boxlabelleft">Severity</td>
	  <td class="boxlabelleft"><select name="severity" id="severity">
      	<?php echo $severity_options; ?>
      </select></td>
    </tr>
	<tr>
	  <td class="boxlabelleft">Legal Category</td>
	  <td class="boxlabelleft"><select name="legal" id="legal">
      	<?php echo $legal_options; ?>
      </select></td>
    </tr>
	<tr>
	  <td class="boxlabelleft">Days Lost (if applicable)</td>
	  <td class="boxlabelleft"><input type="text" name="dayslost" id="dayslost" size="5" value="<?php echo $idayslost; ?>"></td>
    </tr>
	<tr>
      <td colspan="2" align="right">
        <input type="button" value="Save" name="save" onClick="post()"  >
      </td>
      </tr>
  	</table>
</form>

<?php

	if($_REQUEST['savebutton'] == "Y") {
		$name = $_REQUEST['name'];
		$injury1 = $_REQUEST['injury1'];
		$body1 = $_REQUEST['body1'];
		$injury2 = $_REQUEST['injury2'];
		$body2 = $_REQUEST['body2'];
		$injury3 = $_REQUEST['injury3'];
		$body3 = $_REQUEST['body3'];
		$injury4 = $_REQUEST['injury4'];
		$body4 = $_REQUEST['body4'];
		$injury5 = $_REQUEST['injury5'];
		$body5 = $_REQUEST['body5'];
		$injury6 = $_REQUEST['injury6'];
		$body6 = $_REQUEST['body6'];
		$treatment = $_REQUEST['treatment'];
		$severity = $_REQUEST['severity'];
		$legal = $_REQUEST['legal'];
		if ($_REQUEST['dayslost'] == "") {
			$dayslost = 0;
		}else {
			$dayslost = $_REQUEST['dayslost'];
		}

		$q = "update incinjuries set ";
		$q .= 'name = "'.$name.'",';
		$q .= 'injury1 = "'.$injury1.'",';
		$q .= 'body1 = "'.$body1.'",';
		$q .= 'injury2 = "'.$injury2.'",';
		$q .= 'body2 = "'.$body2.'",';
		$q .= 'injury3 = "'.$injury3.'",';
		$q .= 'body3 = "'.$body3.'",';
		$q .= 'injury4 = "'.$injury4.'",';
		$q .= 'body4 = "'.$body4.'",';
		$q .= 'injury5 = "'.$injury5.'",';
		$q .= 'body5 = "'.$body5.'",';
		$q .= 'injury6 = "'.$injury6.'",';
		$q .= 'body6 = "'.$body6.'",';
		$q .= 'treatment = "'.$treatment.'",';
		$q .= 'severity = "'.$severity.'",';
		$q .= 'legal = "'.$legal.'",';
		$q .= 'dayslost = '.$dayslost;
		$q .= ' where uid = '.$id;

		$r = mysql_query($q) or die(mysql_error().$q);

	  ?>
	  <script>
	  window.open("","editincident").jQuery("#iinjurylist").trigger("reloadGrid");
	  //alert('If you do not see the edited changes, click the Reload Grid icon on the bottom bar of the grid');
	  this.close();
	  </script>
	  <?php
		
			
	}

?>


</body>
</html>
