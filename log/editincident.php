<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];
$incid = $_REQUEST['uid'];
$coyname = $_SESSION['s_coyname'];
$coyid = $_SESSION['s_coyid'];
$_SESSION['s_incidentid'] = $incid;

$admindb = $_SESSION['s_admindb'];
date_default_timezone_set($_SESSION['s_timezone']);

require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$subscriber = $subid;
$sname = $uname;
$cluid = $user_id;


// populate driver drop down
$query = "select uid,concat_ws(' ',ufname,ulname) as fname from users where sub_id = ".$subscriber;
$result = mysql_query($query) or die(mysql_error().$query);
$op_options = "<option value=\"0\">Select Driver</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($fname == $icompiler) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$op_options .= '<option value="'.$uid.'"'.$selected.'>'.$fname.'</option>';
}


$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select * from incidents where uid = ".$incid;
$r = mysql_query($q);
$row = mysql_fetch_array($r);
extract($row);
$itype = $incident_type;
$itime = $time_incident;
$iroute = $route_id;
$icompiler = $compiler;
$ilocation = $road;
$ilat = $latitude;
if ($ilat == '') {$ilat = '0';}
$ilong = $longitude;
if ($ilong == '') {$ilong = '0';}
$itruckno = $truckno;
$itrailerno = $trailerno;
$iclient = $client;
$isubcontractor = $sub_contractor;
$icrew = $crew;
$iforest = $forest;
$icompartment = $compartment;
$iharm = $harm_people;
$idamage = $damage_property;
$ireocurr = $reocurr;
$iterrain = $terrain;
$iweather = $weather;
$itemperature = $temperature;
$iwind = $wind;
$idt = explode('-',$date_incident);
$y = $idt[0];
$m = $idt[1];
$d = $idt[2];
$ddate = $d.'/'.$m.'/'.$y;
$hdate = $date_incident;
$ilti = $LTI;
$id = $uid;
$idetails = $details;
$iimmediate = $immediate;
$ibasic1 = $basic1;
$ibasic2 = $basic2;
$ibasic3 = $basic3;
$ibasic4 = $basic4;
$ibasic5 = $basic5;
$ibasic6 = $basic6;
$ibasic7 = $basic7;
$ibasic8 = $basic8;
$iadetails = $adetails;
$iaimmediate = $aimmediate;
$iabasic1 = $abasic1;
$iabasic2 = $abasic2;
$iabasic3 = $abasic3;
$aibasic4 = $abasic4;
$iabasic5 = $abasic5;
$iabasic6 = $abasic6;
$iabasic7 = $abasic7;
$iabasic8 = $abasic8;
$ihazards = $hazards;
$iref = $ref;

$admindb = $_SESSION['s_admindb'];
mysql_select_db($admindb) or die(mysql_error());

date_default_timezone_set($_SESSION['s_timezone']);


// populate terrain drop down
$query = "select incterrain from incterrain";
$result = mysql_query($query) or die(mysql_error().$query);
$terrain_options = "<option value=\" \">Not applicable</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($iterrain == $incterrain) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$terrain_options .= '<option value="'.$incterrain.'"'.$selected.'>'.$incterrain.'</option>';
}

// populate weather drop down
$query = "select incweather from incweather";
$result = mysql_query($query) or die(mysql_error().$query);
$weather_options = "<option value=\" \">Not applicable</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($iweather == $incweather) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$weather_options .= '<option value="'.$incweather.'"'.$selected.'>'.$incweather.'</option>';
}

// populate temperature drop down
$query = "select inctemperature from inctemperature";
$result = mysql_query($query) or die(mysql_error().$query);
$temperature_options = "<option value=\" \">Not applicable</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($itemperature == $inctemperature) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$temperature_options .= '<option value="'.$inctemperature.'"'.$selected.'>'.$inctemperature.'</option>';
}

// populate wind drop down
$query = "select incwind from incwind";
$result = mysql_query($query) or die(mysql_error().$query);
$wind_options = "<option value=\" \">Not applicable</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($iwind == $incwind) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$wind_options .= '<option value="'.$incwind.'"'.$selected.'>'.$incwind.'</option>';
}

// populate basic cause 1 drop down
$query = "select incbasic from incbasic";
$result = mysql_query($query) or die(mysql_error().$query);
$basic1_options = "<option value=\" \">Not applicable</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($ibasic1 == $incbasic) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$basic1_options .= '<option value="'.$incbasic.'"'.$selected.'>'.$incbasic.'</option>';
}

// populate basic cause 2 drop down
$query = "select incbasic from incbasic";
$result = mysql_query($query) or die(mysql_error().$query);
$basic2_options = "<option value=\" \">Not applicable</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($ibasic2 == $incbasic) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$basic2_options .= '<option value="'.$incbasic.'"'.$selected.'>'.$incbasic.'</option>';
}

// populate basic cause 3 drop down
$query = "select incbasic from incbasic";
$result = mysql_query($query) or die(mysql_error().$query);
$basic3_options = "<option value=\" \">Not applicable</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($ibasic3 == $incbasic) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$basic3_options .= '<option value="'.$incbasic.'"'.$selected.'>'.$incbasic.'</option>';
}

// populate basic cause 4 drop down
$query = "select incbasic from incbasic";
$result = mysql_query($query) or die(mysql_error().$query);
$basic4_options = "<option value=\" \">Not applicable</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($ibasic4 == $incbasic) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$basic4_options .= '<option value="'.$incbasic.'"'.$selected.'>'.$incbasic.'</option>';
}

// populate basic cause 5 drop down
$query = "select incbasic from incbasic";
$result = mysql_query($query) or die(mysql_error().$query);
$basic5_options = "<option value=\" \">Not applicable</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($ibasic5 == $incbasic) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$basic5_options .= '<option value="'.$incbasic.'"'.$selected.'>'.$incbasic.'</option>';
}

// populate basic cause 6 drop down
$query = "select incbasic from incbasic";
$result = mysql_query($query) or die(mysql_error().$query);
$basic6_options = "<option value=\" \">Not applicable</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($ibasic6 == $incbasic) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$basic6_options .= '<option value="'.$incbasic.'"'.$selected.'>'.$incbasic.'</option>';
}

// populate basic cause 7 drop down
$query = "select incbasic from incbasic";
$result = mysql_query($query) or die(mysql_error().$query);
$basic7_options = "<option value=\" \">Not applicable</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($ibasic7 == $incbasic) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$basic7_options .= '<option value="'.$incbasic.'"'.$selected.'>'.$incbasic.'</option>';
}

// populate basic cause 8 drop down
$query = "select incbasic from incbasic";
$result = mysql_query($query) or die(mysql_error().$query);
$basic8_options = "<option value=\" \">Not applicable</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($ibasic8 == $incbasic) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$basic8_options .= '<option value="'.$incbasic.'"'.$selected.'>'.$incbasic.'</option>';
}

// populate admin basic cause 1 drop down
$query = "select incbasic from incbasic";
$result = mysql_query($query) or die(mysql_error().$query);
$abasic1_options = "<option value=\" \">Not applicable</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($iabasic1 == $incbasic) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$abasic1_options .= '<option value="'.$incbasic.'"'.$selected.'>'.$incbasic.'</option>';
}

// populate admin basic cause 2 drop down
$query = "select incbasic from incbasic";
$result = mysql_query($query) or die(mysql_error().$query);
$abasic2_options = "<option value=\" \">Not applicable</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($iabasic2 == $incbasic) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$abasic2_options .= '<option value="'.$incbasic.'"'.$selected.'>'.$incbasic.'</option>';
}

// populate admin basic cause 3 drop down
$query = "select incbasic from incbasic";
$result = mysql_query($query) or die(mysql_error().$query);
$abasic3_options = "<option value=\" \">Not applicable</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($iabasic3 == $incbasic) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$abasic3_options .= '<option value="'.$incbasic.'"'.$selected.'>'.$incbasic.'</option>';
}

// populate admin basic cause 4 drop down
$query = "select incbasic from incbasic";
$result = mysql_query($query) or die(mysql_error().$query);
$abasic4_options = "<option value=\" \">Not applicable</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($iabasic4 == $incbasic) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$abasic4_options .= '<option value="'.$incbasic.'"'.$selected.'>'.$incbasic.'</option>';
}

// populate admin basic cause 5 drop down
$query = "select incbasic from incbasic";
$result = mysql_query($query) or die(mysql_error().$query);
$abasic5_options = "<option value=\" \">Not applicable</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($iabasic5 == $incbasic) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$abasic5_options .= '<option value="'.$incbasic.'"'.$selected.'>'.$incbasic.'</option>';
}

// populate admin basic cause 6 drop down
$query = "select incbasic from incbasic";
$result = mysql_query($query) or die(mysql_error().$query);
$abasic6_options = "<option value=\" \">Not applicable</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($iabasic6 == $incbasic) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$abasic6_options .= '<option value="'.$incbasic.'"'.$selected.'>'.$incbasic.'</option>';
}

// populate admin basic cause 7 drop down
$query = "select incbasic from incbasic";
$result = mysql_query($query) or die(mysql_error().$query);
$abasic7_options = "<option value=\" \">Not applicable</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($iabasic7 == $incbasic) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$abasic7_options .= '<option value="'.$incbasic.'"'.$selected.'>'.$incbasic.'</option>';
}

// populate admin basic cause 8 drop down
$query = "select incbasic from incbasic";
$result = mysql_query($query) or die(mysql_error().$query);
$abasic8_options = "<option value=\" \">Not applicable</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($iabasic8 == $incbasic) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$abasic8_options .= '<option value="'.$incbasic.'"'.$selected.'>'.$incbasic.'</option>';
}



$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());

// populate debtors drop down
$query = "select concat(members.firstname,' ',members.lastname) as clname,client_company_xref.drno,client_company_xref.drsub,client_company_xref.hs_contractor from members left join client_company_xref on members.member_id = client_company_xref.client_id where client_company_xref.company_id = ".$coyid." and client_company_xref.drno != 0"; 
$result = mysql_query($query) or die(mysql_error().$query);
$debtors_options = "<option value=\"\">Select Customer</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if (trim($iclient) == trim($clname)) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$debtors_options .= '<option value="'.trim($clname).'~'.$drno.'~'.$drsub.'~'.$hs_contractor.'"'.$selected.'>'.trim($clname).'</option>';
}

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

// populate trucks drop down
$query = "select branch,branchname from branch where branchname like 'Truck%'";
$result = mysql_query($query) or die(mysql_error().$query);
$truck_options = "<option value=\" \">Select Truck</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($itruckno == $branchname) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$truck_options .= '<option value="'.$branch.'~'.$branchname.'"'.$selected.'>'.$branchname.'</option>';
}

// populate trailers drop down
$query = "select branch,branchname from branch where branchname like 'Trailer%'";
$result = mysql_query($query) or die(mysql_error().$query);
$trailer_options = "<option value=\" \">Select Trailer</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($itrailerno == $branchname) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$trailer_options .= '<option value="'.$branch.'~'.$branchname.'"'.$selected.'>'.$branchname.'</option>';
}

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());



// populate type list
    $arr = array('Health and Safety','Environmental');
	$icat_options = "";
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $inctype) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$icat_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}


// populate routes drop down
$query = "select uid,route,compartment from routes where uid > 1 order by route,compartment";
$result = mysql_query($query) or die(mysql_error().$query);
$route_options = "<option value=\"0\">Select Route and Compartment</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($iroute == $uid) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$route_options .= '<option value="'.$uid.'"'.$selected.'>'.$route.'~'.$compartment.'</option>';
}

// populate contractors drop down
$query = "select uid,contractor,crew from contractors order by contractor,crew";
$result = mysql_query($query) or die(mysql_error().$query);
$contractor_options = "<option value=\"0\">Not applicable</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($isubcontractor == $contractor && $icrew == $crew) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$contractor_options .= '<option value="'.$uid.'"'.$selected.'>'.$contractor.' '.$crew.'</option>';
}


// populate LTI list
    $arr = array('No','Yes');
	$lti_options = "";
    for($i = 0; $i < count($arr); $i++)	{
			if ($arr[$i] == $ilti) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}	
		$lti_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate harm list
    $arr = array('None','Insignificant','Minor','Temporary harm','Serious harm','Fatalities');
	$harm_options = "";
    for($i = 0; $i < count($arr); $i++)	{
			if ($arr[$i] == $iharm) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}	
		$harm_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate damage list
    $arr = array('None','Under $100','Under $1,000','Under $5,000','Under $50,000','Over $50,000');
	$damage_options = "";
    for($i = 0; $i < count($arr); $i++)	{
			if ($arr[$i] == $idamage) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}	
		$damage_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate reocurr list
    $arr = array('Rare','Possible','Moderate','Likely','Certain');
	$reocurr_options = "";
    for($i = 0; $i < count($arr); $i++)	{
			if ($arr[$i] == $ireocurr) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}	
		$reocurr_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}



$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Incident</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script language="javascript" type="text/javascript" src="../includes/datetimepick/datetimepicker.js"></script>
<script>

window.name = "editincident";

/**
 * DHTML date validation script. Courtesy of SmartWebby.com (http://www.smartwebby.com/dhtml/)
 */

// function hideAll()
//  hides a bunch of divs
//

function hideAllm() {
   changeObjectVisibility("general","hidden");
   changeObjectVisibility("details","hidden");
   changeObjectVisibility("people","hidden");
   changeObjectVisibility("injuries","hidden");
   changeObjectVisibility("damage","hidden");
   changeObjectVisibility("causes","hidden");
   changeObjectVisibility("action","hidden");
   changeObjectVisibility("pictures","hidden");
}

function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}



// function getStyleObject(string) -> returns style object
//  given a string containing the id of an object
//  the function returns the stylesheet of that object
//  or false if it can't find a stylesheet.  Handles
//  cross-browser compatibility issues.
//
function getStyleObject(objectId) {
  // checkW3C DOM, then MSIE 4, then NN 4.
  //
  if(document.getElementById && document.getElementById(objectId)) {
	return document.getElementById(objectId).style;
   }
   else if (document.all && document.all(objectId)) {  
	return document.all(objectId).style;
   } 
   else if (document.layers && document.layers[objectId]) { 
	return document.layers[objectId];
   } else {
	return false;
   }
}


function changeObjectVisibility(objectId, newVisibility) {
    // first get a reference to the cross-browser style object 
    // and make sure the object exists
    var styleObject = getStyleObject(objectId);
    if(styleObject) {
	styleObject.visibility = newVisibility;
	return true;
    } else {
	// we couldn't find the object, so we can't change its visibility
	return false;
    }
}

function switchDivm(div_id,cell) {
  var style_sheet = getStyleObject(div_id);
  
  if (style_sheet)  {
	hideAllm();
    changeObjectVisibility(div_id,"visible");
  }
}

function mapinc() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	var id = <?php echo $incid; ?>;
	var coord = <?php echo $ilat; ?>+','+<?php echo $ilong; ?>;
	window.open('map.php?id='+id+'&coord='+coord,'goog','toolbar=0,scrollbars=1,height=360,width=560,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addincaction() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addincaction.php','adact','toolbar=0,scrollbars=1,height=350,width=700,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editincaction(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editincaction.php?uid='+uid,'edact','toolbar=0,scrollbars=1,height=350,width=700,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addipeople() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addipeople.php','adpl','toolbar=0,scrollbars=1,height=380,width=730,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editipeople(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editipeople.php?uid='+uid,'edpl','toolbar=0,scrollbars=1,height=380,width=730,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addiinjury() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addiinjuries.php','adin','toolbar=0,scrollbars=1,height=500,width=750,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editiinjury(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editiinjuries.php?uid='+uid,'edin','toolbar=0,scrollbars=1,height=500,width=750,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addidamage() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addidamage.php','addm','toolbar=0,scrollbars=1,height=200,width=750,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editidamage(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editidamage.php?uid='+uid,'eddm','toolbar=0,scrollbars=1,height=200,width=750,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}



function post() {

	//add validation here if required.
	var dt = document.getElementById('ddate').value;
	var time = document.getElementById('time').value;
	var route = document.getElementById('route').value;
	var type = document.getElementById('inctype').value;
	var detail = document.getElementById('details').value;
	var client = document.getElementById('client').value;
	
	var ok = "Y";
	if (dt == "") {
		alert("Please enter a date.");
		ok = "N";
		return false;
	}
	if (time == "") {
		alert("Please enter a time.");
		ok = "N";
		return false;
	}
	if (route == "") {
		alert("Please enter a route/forest.");
		ok = "N";
		return false;
	}
	if (client == "") {
		alert("Please enter a client.");
		ok = "N";
		return false;
	}
	if (type == "") {
		alert("Please enter an incident type.");
		ok = "N";
		return false;
	}
	if (detail == "") {
		alert("Please enter the incident details.");
		ok = "N";
		return false;
	}
	
	if (ok == "Y") {
		document.getElementById('savebutton').value = "Y";
		document.getElementById('in').submit();
	}
	
	
}


	

</script>
<!-- Deluxe Tabs -->
<noscript>
<a href="http://deluxe-tabs.com">Javascript Tabs Menu by Deluxe-Tabs.com</a>
</noscript>
<script type="text/javascript" src="tabs/client_tabs.files/dtabs.js"></script>
<!-- (c) 2009, http://deluxe-tabs.com -->
<style type="text/css">
<!--
.style1 {
	font-size: large
}
.star {
	color: #F00;
}
-->
</style>
</head><body>
<form name="in" id="in" method="post">
  <input type="hidden" name="savebutton" id="savebutton" value="N">
  <table width="890" border="0" align="left">
  	<tr>
    	<td bgcolor="<?php echo $bghead; ?>" align="center"><label style="color: <?php echo $thfont; ?>;font-size:14px"><strong>Incident # <?php echo $id; ?> - <?php echo $ddate; ?></strong></label></td>
    </tr>
    <tr>
      <td><script type="text/javascript" src="tabs/incident_tabs.js"></script></td>
    </tr>
    <tr>
      <td><div id="general" style="position:absolute;visibility:hidden;top:45px;left:3px;height:490px;width:880px;background-color:<?php echo $bgcolor; ?>;" >
          <table width="870" border="0" align="center" cellspacing="6" bgcolor="<?php echo $bgcolor; ?>">
            <tr>
              <td bgcolor="<?php echo $bghead; ?>" align="center"><label style="color: <?php echo $thfont; ?>;font-size:14px"><strong>Incident # <?php echo $id; ?></strong></label></td>
              <td bgcolor="<?php echo $bghead; ?>" align="left"><label style="color: <?php echo $thfont; ?>;font-size:12px"><strong>Is this a Lost Time Incident</strong></label>
                <select name="lti" id="lti">
                  <?php echo $lti_options; ?>
                </select></td>
              <td bgcolor="<?php echo $bghead; ?>" align="left">Ref:
              <input type="text" name="ref" id="ref" value="<?php echo $iref; ?>"></td>
            </tr>
            <tr>
              <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Date of Incident </label>
                <input type="Text" id="ddate" name="ddate" maxlength="25" size="25" value="<?php echo $ddate; ?>"><a href="javascript:NewCal('ddate')"><img src="../includes/datetimepick/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a></td>
              <td colspan="2" class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Time of Incident</label>
                <input name="time" type="text" id="time" size="15" value="<?php echo $itime; ?>">hh:mm am/pm</td>
            </tr>
            <tr>
              <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Name of Client</label>
                <select name="client" id="client">
                  <?php echo $debtors_options; ?>
                </select></td>
              <td colspan="2" class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Name of Sub-Contractor </label>
                <select name="subcontractor" id="subcontractor">
                  <?php echo $contractor_options; ?>
                </select></td>
            </tr>
            <tr>
              <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Route/Forest</label>
                <select name="route" id="route">
                  <?php echo $route_options;?>
                </select></td>
              <td colspan="2" class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Incident Category</label>
                <select name="inctype" id="inctype">
                  <?php echo $icat_options; ?>
                </select></td>
            </tr>
            <tr>
              <td colspan="3" class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Location of Incident</label>
                <input type="text" name="location" id="location" size="70" value="<?php echo $ilocation; ?>">
                <img src="../images/map.gif" title="Map of Location" width="15" height="15" onClick="mapinc()"></td>
            </tr>
            <tr>
              <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Latitude of incident</label>
                <input type="text" name="lat" id="lat" value="<?php echo $ilat; ?>"></td>
              <td colspan="2" class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Longitude of incident</label>
                <input type="text" name="long" id="long" value="<?php echo $ilong; ?>"></td>
            </tr>
            <tr>
              <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Truck</label>
                <select name="truck" id="truck">
                  <?php echo $truck_options;?>
                </select></td>
              <td colspan="2" class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Trailer</label>
                <select name="trailer" id="trailer">
                  <?php echo $trailer_options;?>
                </select></td>
            </tr>
            <tr>
              <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">What happened, or <span class="italic">could</span> have happened?</label></td>
              <td colspan="2" class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Harm to people</label>
                <select name="harm" id="harm">
                  <?php echo $harm_options; ?>
                </select></td>
            </tr>
            <tr>
              <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Damage to property</label>
                <select name="dmg" id="dmg">
                  <?php echo $damage_options; ?>
                </select></td>
              <td colspan="2" class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Likelihood of incident occuring again</label>
                <select name="again" id="again">
                  <?php echo $reocurr_options; ?>
                </select></td>
            <tr>
              <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Conditions (if applicable)</label></td>
              <td colspan="2" class="boxlabelleft">&nbsp;</td>
            <tr>
              <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Terrain</label>
                <select name="terrain" id="terrain">
                  <?php echo $terrain_options; ?>
                </select></td>
              <td colspan="2" class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Weather</label>
                <select name="weather" id="weather">
                  <?php echo $weather_options; ?>
                </select></td>
            </tr>
            <tr>
              <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Temperature</label>
                <select name="temperature" id="temperature">
                  <?php echo $temperature_options; ?>
                </select></td>
              <td colspan="2" class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Wind</label>
                <select name="wind" id="wind">
                  <?php echo $wind_options; ?>
                </select></td>
            </tr>
            <tr>
              <td align="right" colspan="3"><input type="button" value="Save" name="save" onClick="post()"  ></td>
            </tr>
          </table>
        </div>
        <div id="details" style="position:absolute;visibility:hidden;top:45px;left:3px;height:490px;width:880px;background-color:<?php echo $bgcolor; ?>;" >
          <table width="870" border="0" align="center" cellspacing="6" bgcolor="<?php echo $bgcolor; ?>">
            <tr>
              <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Details of the incident (as comprehensive as possible) - from incident site.</label>
                <textarea name="details" id="details" cols="100" rows="10" readonly><?php echo $idetails; ?></textarea></td>
            </tr>
            <tr>
              <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Details of the incident (as comprehensive as possible) - from Administration.</label>
                <textarea name="adetails" id="adetails" cols="100" rows="10" ><?php echo $iadetails; ?></textarea></td>
            </tr>
            <tr>
              <td align="right" ><input type="button" value="Save" name="save" onClick="post()"  ></td>
            </tr>
          </table>
        </div>
        <div id="people"  style="position:absolute;visibility:hidden;top:45px;left:3px;height:490px;width:880px;background-color:<?php echo $bgcolor; ?>;" >
          <table width="890" border="0" align="center" cellpadding="3" cellspacing="1">
            <tr>
              <td><?php include "getincpeople.php"; ?></td>
            </tr>
          </table>
        </div>
        <div id="injuries" style="position:absolute;visibility:hidden;top:45px;left:3px;height:490px;width:880px;background-color:<?php echo $bgcolor; ?>;" >
          <table width="890" border="0" align="center" cellpadding="3" cellspacing="1">
            <tr>
              <td><?php include "getincinjuries.php"; ?></td>
            </tr>
          </table>
        </div>
        <div id="damage" style="position:absolute;visibility:hidden;top:45px;left:3px;height:490px;width:880px;background-color:<?php echo $bgcolor; ?>;" >
          <table width="890" border="0" align="center" cellpadding="3" cellspacing="1">
            <tr>
              <td><?php include "getincdamage.php"; ?></td>
            </tr>
          </table>
        </div>
        <div id="causes" style="position:absolute;visibility:hidden;top:45px;left:3px;height:490px;width:880px;background-color:<?php echo $bgcolor; ?>;" >
          <table width="800" border="0" align="center" cellpadding="3" cellspacing="1">
            <tr>
              <td class="boxlabelleft">Immediate causes Site</td>
              <td><textarea name="immediate" id="immediate" cols="60" rows="3" readonly><?php echo $immediate; ?></textarea></td>
            </tr>
            <tr>
              <td class="boxlabelleft">Immediate causes Admin</td>
              <td><textarea name="aimmediate" id="aimmediate" cols="60" rows="3"><?php echo $iaimmediate; ?></textarea></td>
            </tr>  
            <tr>
              <td class="boxlabelleft">Basic causes Site</td>
              <td>
              	<table>
                	<tr>
                    	<td><input type="text" name="basic1" size="50" value="<?php echo $ibasic1; ?>" readonly></td>
                    	<td><input type="text" name="basic2" size="50" value="<?php echo $ibasic2; ?>" readonly></td>
                    </tr>
                	<tr>
                    	<td><input type="text" name="basic3" size="50" value="<?php echo $ibasic3; ?>" readonly></td>
                    	<td><input type="text" name="basic4" size="50" value="<?php echo $ibasic4; ?>" readonly></td>
                    </tr>
                	<tr>
                    	<td><input type="text" name="basic5" size="50" value="<?php echo $ibasic5; ?>" readonly></td>
                    	<td><input type="text" name="basic6" size="50" value="<?php echo $ibasic6; ?>" readonly></td>
                    </tr>
                	<tr>
                    	<td><input type="text" name="basic7" size="50" value="<?php echo $ibasic7; ?>" readonly></td>
                    	<td><input type="text" name="basic8" size="50" value="<?php echo $ibasic8; ?>" readonly></td>
                    </tr>
                
                </table>
              </td>
            </tr>
            <tr>
              <td class="boxlabelleft">Basic causes Admin
              (as many as apply)</td>
              <td>
                <select name="abasic1" id="abasic1">
                	<?php echo $abasic1_options; ?>
                </select>
                <select name="abasic2" id="abasic2">
                	<?php echo $abasic2_options; ?>
                </select>
                <select name="abasic3" id="abasic3">
                	<?php echo $abasic3_options; ?>
                </select>
                <select name="abasic4" id="abasic4">
                	<?php echo $abasic4_options; ?>
                </select>
                <select name="abasic5" id="abasic5">
                	<?php echo $abasic5_options; ?>
                </select>
                <select name="abasic6" id="abasic6">
                	<?php echo $abasic6_options; ?>
                </select>
                <select name="abasic7" id="abasic7">
                	<?php echo $abasic7_options; ?>
                </select>
                <select name="abasic8" id="abasic8">
                	<?php echo $abasic8_options; ?>
                </select>
              </td>
            </tr>
            <tr>
              <td class="boxlabelleft">Contributing hazards</td>
              <td><textarea name="hazards" id="hazards" cols="60" rows="3"><?php echo $hazards; ?></textarea></td>
            </tr>
            <tr>
              <td align="right" colspan="2"><input type="button" value="Save" name="save" onClick="post()"  ></td>
            </tr>
          </table>
        </div>
        <div id="action" style="position:absolute;visibility:hidden;top:45px;left:3px;height:490px;width:880px;background-color:<?php echo $bgcolor; ?>;" >
          <table width="890" border="0" align="center" cellpadding="3" cellspacing="1">
            <tr>
              <td><?php include "getincactions.php"; ?></td>
            </tr>
          </table>
        </div>
        <div id="pictures" style="position:absolute;visibility:hidden;top:45px;left:3px;height:490px;width:880px;background-color:<?php echo $bgcolor; ?>;" >
          
			<?php
            
            include("class.image.php") ;
            
            
            $image = new AffichImage() ;
            
            //define the width of the thumbnail
            $lon = 100 ;
            //define the height of the thumbnail
            $lar = 100 ;
            //define the number of thumbnails per line
            $lin = 10 ;
            //define the directory where to look for images
            $dir = "ws/incidents" ;
            
            
            $image->showDir($incid, $dir, -1, $lar, $lon, $lin);
            
            ?>		  

        </div>
      </td>
    </tr>
  </table>
  <script>
		//hideAllm();
		switchDivm('general','gn');
    </script>
  <script>document.onkeypress = stopRKey;</script>
</form>
<?php

	if($_REQUEST['savebutton'] == "Y") {
		$ref = $_REQUEST['ref'];
		$lti = $_REQUEST['lti'];
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
		$time = $_REQUEST['time'];
		$loc = $_REQUEST['location'];
		$lat = $_REQUEST['lat'];
		$long = $_REQUEST['long'];
		$cl = explode('~',$_REQUEST['client']);
		$client = $cl[0];
		$ac = $cl[1];
		$sb = $cl[2];
		$hsc = $cl[3];
		$ctr = $_REQUEST['subcontractor'];
		$qf = "select contractor,crew from contractors where uid = ".$ctr;
		$rf = mysql_query($qf);
		$row = mysql_fetch_array($rf) or die(mysql_error());
		extract($row);
		$contr = $contractor;
		$crw = $crew;
		$route = $_REQUEST['route'];
		$qf = "select forest,compartment from routes where uid = ".$route;
		$rf = mysql_query($qf);
		$row = mysql_fetch_array($rf) or die(mysql_error());
		extract($row);
		$frt = $forest;
		$cpt = $compartment;
		$type = $_REQUEST['inctype'];
		$trk = split('~',$_REQUEST['truck']);
		$truckbr = $trk[0];
		$truck = $trk[1];
		$trl = split('~',$_REQUEST['trailer']);
		$trailerbr = $trl[0];
		$trailer = $trl[1];
		$detail = $_REQUEST['details'];
		$adetail = $_REQUEST['adetails'];
		$harm = $_REQUEST['harm'];
		$damage = $_REQUEST['dmg'];
		$occur = $_REQUEST['again'];
		$terrain = $_REQUEST['terrain'];
		$weather = $_REQUEST['weather'];
		$temperature = $_REQUEST['temperature'];
		$wind = $_REQUEST['wind'];
		$b1 = $_REQUEST['abasic1'];
		$b2 = $_REQUEST['abasic2'];
		$b3 = $_REQUEST['abasic3'];
		$b4 = $_REQUEST['abasic4'];
		$b5 = $_REQUEST['abasic5'];
		$b6 = $_REQUEST['abasic6'];
		$b7 = $_REQUEST['abasic7'];
		$b8 = $_REQUEST['abasic8'];
		$immediate = $_REQUEST['immediate'];
		$aimmediate = $_REQUEST['aimmediate'];
		$hazards = $_REQUEST['hazards'];

		$q = "update incidents set ";
		$q .= 'date_incident = "'.$ddate.'",';
		$q .= 'ref = "'.$ref.'",';
		$q .= 'time_incident = "'.$time.'",';
		$q .= 'latitude = "'.$lat.'",';
		$q .= 'longitude = "'.$long.'",';
		$q .= 'LTI = "'.$lti.'",';
		$q .= 'client = "'.$client.'",';
		$q .= 'accountno = '.$ac.',';
		$q .= 'hs_contractor = '.$hsc.',';
		$q .= 'sub = '.$sb.',';
		$q .= 'sub_contractor = "'.$contr.'",';
		$q .= 'crew = "'.$crw.'",';
		$q .= 'compiler = "'.$compiler.'",';
		$q .= 'incident_type = "'.$type.'",';
		$q .= 'route_id = '.$route.',';
		$q .= 'forest = "'.$forest.'",';
		$q .= 'compartment = "'.$cpt.'",';
		$q .= 'road = "'.$loc.'",';
		$q .= 'adetails = "'.$adetail.'",';
		$q .= 'truckno = "'.$truck.'",';
		$q .= 'trailerno = "'.$trailer.'",';
		$q .= 'harm_people = "'.$harm.'",';
		$q .= 'damage_property = "'.$damage.'",';
		$q .= 'reocurr = "'.$occur.'",';
		$q .= 'terrain = "'.$terrain.'",';
		$q .= 'weather = "'.$weather.'",';
		$q .= 'temperature = "'.$temperature.'",';
		$q .= 'wind = "'.$wind.'",';
		$q .= 'abasic1 = "'.$b1.'",';
		$q .= 'abasic2 = "'.$b2.'",';
		$q .= 'abasic3 = "'.$b3.'",';
		$q .= 'abasic4 = "'.$b4.'",';
		$q .= 'abasic5 = "'.$b5.'",';
		$q .= 'abasic6 = "'.$b6.'",';
		$q .= 'abasic7 = "'.$b7.'",';
		$q .= 'abasic8 = "'.$b8.'",';
		$q .= 'aimmediate = "'.$aimmediate.'",';
		$q .= 'hazards = "'.$hazards.'"';
		$q .= ' where uid = '.$incid;
		$r = mysql_query($q) or die(mysql_error().$q);
		$incid = mysql_insert_id();
		
		echo '<script>';
	  	echo 'window.open("","incidents").jQuery("#incidentlist").trigger("reloadGrid");';
		echo 'this.close();';
		echo '</script>;';
		
		

			
	}

?>
</body>
</html>
