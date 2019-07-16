<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];
$icid = $_REQUEST['uid'];
$_SESSION['s_incidentid'] = $icid;

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

$incfile = 'ztmp'.$user_id.'_hs';

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

$moduledb = $_SESSION['h_sdb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select incid,subid,coyid from ".$incfile." where uid = ".$icid;
$r = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($r);
extract($row);

$moduledb = 'log'.$subid.'_'.$coyid;
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
$isubcontractor = $sub_contractor.' '.$crew;
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
$ihazards = $hazards;
$iref = $ref;


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
<script>

window.name = "editincident";

	 //$(document).ready(function(){
		//$('#ddate').datepicker({ dateFormat: "dd/mm/yy", yearRange: "-5:+5", showOn: "button", buttonImage: "../images/calendar.gif", buttonImageOnly: true, altField: "#ddateh", altFormat: "yy-mm-dd"});
	 //});

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
	var dt = document.getElementById('ddateh').value;
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
  <input type="hidden" name="ddateh" id="ddateh" value="<?php echo $hdate; ?>">
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
              <input type="text" name="lti" id="lti" value="<?php echo $ilti; ?>" readonly></td>
              <td bgcolor="<?php echo $bghead; ?>" align="left">Ref:
              <input type="text" name="ref" id="ref" value="<?php echo $iref; ?>" readonly></td>
            </tr>
            <tr>
              <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Date of Incident </label>
                <input name="ddate" type="text" id="ddate" readonly value="<?php echo $ddate; ?>"></td>
              <td colspan="2" class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Time of Incident</label>
                <input name="time" type="text" id="time" size="15" value="<?php echo $itime; ?>" readonly>hh:mm am/pm</td>
            </tr>
            <tr>
              <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Name of Client</label>
              <input type="text" name="client" id="client" value="<?php echo $iclient; ?>" readonly></td>
              <td colspan="2" class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Name of Sub-Contractor </label>
              <input type="text" name="subcontractor" id="subcontractor" value="<?php echo $isubcontractor; ?>" readonly></td>
            </tr>
            <tr>
              <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Route/Forest</label>
              <input type="text" name="route" id="route" value="<?php echo $iroute; ?>" readonly></td>
              <td colspan="2" class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Incident Category</label>
              <input type="text" name="inctype" id="inctype" value="<?php echo $itype; ?>" readonly></td>
            </tr>
            <tr>
              <td colspan="3" class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Location of Incident</label>
                <input type="text" name="location" id="location" size="70" value="<?php echo $ilocation; ?>" readonly>
                <img src="../images/map.gif" title="Map of Location" width="15" height="15" onClick="mapinc()"></td>
            </tr>
            <tr>
              <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Latitude of incident</label>
                <input type="text" name="lat" id="lat" value="<?php echo $ilat; ?>" readonly></td>
              <td colspan="2" class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Longitude of incident</label>
                <input type="text" name="long" id="long" value="<?php echo $ilong; ?>" readonly></td>
            </tr>
            <tr>
              <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Truck</label>
              <input type="text" name="truck" id="truck" value="<?php echo $itruckno; ?>" readonly></td>
              <td colspan="2" class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Trailer</label>
              <input type="text" name="trailer" id="trailer" value="<?php echo $itrailerno; ?>" readonly></td>
            </tr>
            <tr>
              <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">What happened, or <span class="italic">could</span> have happened?</label></td>
              <td colspan="2" class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Harm to people</label>
              <input type="text" name="harm" id="harm" value="<?php echo $iharm; ?>" readonly></td>
            </tr>
            <tr>
              <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Damage to property</label>
              <input type="text" name="dmg" id="dmg" value="<?php echo $idamage; ?>" readonly></td>
              <td colspan="2" class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Likelihood of incident occuring again</label>
              <input type="text" name="again" id="again" value="<?php echo $ireocurr; ?>" readonly></td>
            <tr>
              <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Conditions (if applicable)</label></td>
              <td colspan="2" class="boxlabelleft">&nbsp;</td>
            <tr>
              <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Terrain</label>
              <input type="text" name="terrain" id="terrain" value="<?php echo $iterrain; ?>" readonly></td>
              <td colspan="2" class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Weather</label>
              <input type="text" name="weather" id="weather" value="<?php echo $iweather; ?>" readonly></td>
            </tr>
            <tr>
              <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Temperature</label>
              <input type="text" name="temperature" id="temperature" value="<?php echo $itemperature; ?>" readonly></td>
              <td colspan="2" class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Wind</label>
              <input type="text" name="wind" id="wind" value="<?php echo $iwind; ?>" readonly></td>
            </tr>
          </table>
        </div>
        <div id="details" style="position:absolute;visibility:hidden;top:45px;left:3px;height:490px;width:880px;background-color:<?php echo $bgcolor; ?>;" >
          <table width="870" border="0" align="center" cellspacing="6" bgcolor="<?php echo $bgcolor; ?>">
            <tr>
              <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Details of the incident (as comprehensive as possible)</label>
                <textarea name="details" id="details" cols="100" rows="20" readonly><?php echo $idetails; ?></textarea></td>
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
              <td class="boxlabelleft">Immediate causes</td>
              <td colspan="2"><textarea name="immediate" id="immediate" cols="60" rows="5" readonly><?php echo $immediate; ?></textarea></td>
            </tr>
            <tr>
              <td class="boxlabelleft">Basic causes</td>
              <td><input type="text" name="basic1" id="basic1" value="<?php echo $ibasic1; ?>" readonly size="40"></td>
              <td><input type="text" name="basic2" id="basic2" value="<?php echo $ibasic2; ?>" readonly size="40"></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td><input type="text" name="basic3" id="basic3" value="<?php echo $ibasic3; ?>" readonly size="40"></td>
              <td><input type="text" name="basic4" id="basic4" value="<?php echo $ibasic4; ?>" readonly size="40"></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td><input type="text" name="basic5" id="basic5" value="<?php echo $ibasic5; ?>" readonly size="40"></td>
              <td><input type="text" name="basic6" id="basic6" value="<?php echo $ibasic6; ?>" readonly size="40"></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td><input type="text" name="basic7" id="basic7" value="<?php echo $ibasic7; ?>" readonly size="40"></td>
              <td><input type="text" name="basic8" id="basic8" value="<?php echo $ibasic8; ?>" readonly size="40"></td>
            </tr>
            <tr>
              <td class="boxlabelleft">Contributing hazards</td>
              <td colspan="2"><textarea name="hazards" id="hazards" cols="60" rows="5" readonly><?php echo $hazards; ?></textarea></td>
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
            $dir = "../log/ws/incidents" ;
            
            
            $image->showDir($subid,$coyid,$incid, $dir, -1, $lar, $lon, $lin);
            
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

</body>
</html>
