<?php
session_start();

$admindb = $_SESSION['s_admindb'];
$coyid = $_SESSION['s_coyid'];
$coyname = $_SESSION['s_coyname'];

require_once('../db.php');
mysql_select_db($admindb) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$subscriber = $subid;
$compiler = $uname;

// populate driver drop down
$query = "select uid,concat_ws(' ',ufname,ulname) as fname from users where sub_id = ".$subscriber;
$result = mysql_query($query) or die(mysql_error().$query);
$op_options = "<option value=\"0\">Select Driver</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);

		$selected = '';

	$op_options .= '<option value="'.$uid.'"'.$selected.'>'.$fname.'</option>';
}

// populate incident type down
$query = "select inctype from inctypes";
$result = mysql_query($query) or die(mysql_error().$query);
$type_options = "<option value=\" \">Select Incident Type</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);

		$selected = '';

	$type_options .= '<option value="'.$inctype.'"'.$selected.'>'.$inctype.'</option>';
}

// populate terrain drop down
$query = "select incterrain from incterrain";
$result = mysql_query($query) or die(mysql_error().$query);
$terrain_options = "<option value=\" \">Not applicable</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);

		$selected = '';

	$terrain_options .= '<option value="'.$incterrain.'"'.$selected.'>'.$incterrain.'</option>';
}

// populate weather drop down
$query = "select incweather from incweather";
$result = mysql_query($query) or die(mysql_error().$query);
$weather_options = "<option value=\" \">Not applicable</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);

		$selected = '';

	$weather_options .= '<option value="'.$incweather.'"'.$selected.'>'.$incweather.'</option>';
}

// populate temperature drop down
$query = "select inctemperature from inctemperature";
$result = mysql_query($query) or die(mysql_error().$query);
$temperature_options = "<option value=\" \">Not applicable</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);

		$selected = '';

	$temperature_options .= '<option value="'.$inctemperature.'"'.$selected.'>'.$inctemperature.'</option>';
}

// populate wind drop down
$query = "select incwind from incwind";
$result = mysql_query($query) or die(mysql_error().$query);
$wind_options = "<option value=\" \">Not applicable</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);

		$selected = '';

	$wind_options .= '<option value="'.$incwind.'"'.$selected.'>'.$incwind.'</option>';
}



$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());

// populate debtors drop down
$query = "select concat(members.firstname,' ',members.lastname) as clname,client_company_xref.drno,client_company_xref.drsub,client_company_xref.hs_contractor from members left join client_company_xref on members.member_id = client_company_xref.client_id where client_company_xref.company_id = ".$coyid." and client_company_xref.drno != 0"; 
$result = mysql_query($query) or die(mysql_error().$query);
$debtors_options = "<option value=\"\">Select Customer</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);

		$selected = '';

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

		$selected = '';

	$truck_options .= '<option value="'.$branch.'~'.$branchname.'"'.$selected.'>'.$branchname.'</option>';
}

// populate trailers drop down
$query = "select branch,branchname from branch where branchname like 'Trailer%'";
$result = mysql_query($query) or die(mysql_error().$query);
$trailer_options = "<option value=\" \">Select Trailer</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);

		$selected = '';

	$trailer_options .= '<option value="'.$branch.'~'.$branchname.'"'.$selected.'>'.$branchname.'</option>';
}

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());


// populate incident category list
    $arr = array('Health and Safety','Environmental');
	$icat_options = "<option value=\" \">Select</option>";
    for($i = 0; $i < count($arr); $i++)	{
				$selected = '';
		$icat_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate routes drop down
$query = "select uid,route,compartment from routes where uid > 1 order by route,compartment";
$result = mysql_query($query) or die(mysql_error().$query);
$route_options = "<option value=\"0\">Select Route and Compartment</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);

		$selected = '';

	$route_options .= '<option value="'.$uid.'"'.$selected.'>'.$route.'~'.$compartment.'</option>';
}

// populate contractors drop down
$query = "select uid,contractor,crew from contractors order by contractor,crew";
$result = mysql_query($query) or die(mysql_error().$query);
$contractor_options = "<option value=\"0\">Not applicable</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);

		$selected = '';

	$contractor_options .= '<option value="'.$uid.'"'.$selected.'>'.$contractor.' '.$crew.'</option>';
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
<title>Add Incident</title>
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
	var dt = SQLdate(document.getElementById('ddate').value);
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
		document.getElementById('form1').submit();
	}
	
	
}



</script>

<style type="text/css">
<!--
.style1 {font-size: large}
.italic {
	font-style: italic;
}
-->
</style>
</head>


<body>
<form name="form1" id="form1" method="post" >
 <input type="hidden" name="savebutton" id="savebutton" value="N">
 <table width="880" border="0" align="center" cellspacing="6" bgcolor="<?php echo $bgcolor; ?>">
    <tr>
      <td bgcolor="<?php echo $bghead; ?>" align="center"><label style="color: <?php echo $thfont; ?>;font-size:14px"><strong>Add an Incident </strong></label></td>
      <td bgcolor="<?php echo $bghead; ?>" align="left"><label style="color: <?php echo $thfont; ?>;font-size:12px"><strong>Is this a Lost Time Incident</strong></label>
        <select name="lti" id="lti">
          <option value="No">No</option>
          <option value="Yes">Yes</option>
      </select></td>
      <td bgcolor="<?php echo $bghead; ?>" align="left">Ref:
      <input type="text" name="ref" id="ref"></td>
    </tr>
    <tr>
      <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Date of Incident </label>
  <input type="Text" id="ddate" name="ddate" maxlength="25" size="25" value="<?php echo $ddate; ?>"><a href="javascript:NewCal('ddate')"><img src="../includes/datetimepick/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a></td>
      <td colspan="2" class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Time of Incident</label>
        <input name="time" type="text" id="time" size="15" >
        hh:mm am/pm</td>
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
      <input type="text" name="location" id="location" size="70"></td>
    </tr>
    <tr>
      <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Latitude of incident</label>
        <input type="text" name="lat" id="lat"></td>
      <td colspan="2" class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Longitude of incident</label>
        <input type="text" name="long" id="long"></td>
    </tr>
    <tr>
      <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Truck</label>
        <select name="truck" id="truck"><?php echo $truck_options;?>
      </select></td>
      <td colspan="2" class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Trailer</label>
        <select name="trailer" id="trailer"><?php echo $trailer_options;?>
      </select></td>
      </tr>
    <tr>
      <td colspan="3" class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Details of the incident (as comprehensive as possible)</label>
        <textarea name="details" id="details" cols="100" rows="5"></textarea></td>
      </tr>
    <tr>
      <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">What happened, or <span class="italic">could</span> have happened?</label></td>
      <td colspan="2" class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Harm to people</label>
        <select name="harm" id="harm">
          <option value="None">None</option>
          <option value="Insignificant">Insignificant</option>
          <option value="Minor">Minor</option>
          <option value="Temporary harm">Temporary harm</option>
          <option value="Serious harm">Serious harm</option>
          <option value="Fatalities">Fatalities</option>
      </select></td>
      </tr>
    <tr>
      <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Damage to property</label>
        <select name="damage" id="damage">
          <option value="None">None</option>
          <option value="Under $100">Under $100</option>
          <option value="Under $1,000">Under $1,000</option>
          <option value="Under $5,000">Uner $5,000</option>
          <option value="Under $50,000">Under $50,000</option>
          <option value="Over $50,000 +">Over $50,000</option>
      </select></td>
      <td colspan="2" class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Likelihood of incident occuring again</label>
        <select name="again" id="again">
          <option value="Rare">Rare</option>
          <option value="Possible">Possible</option>
          <option value="Moderate">Moderate</option>
          <option value="Likely">Likely</option>
          <option value="Certain">Certain</option>
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
      <td align="right" colspan="3">
        <input type="button" value="Save" name="save" onClick="post()"  >
      </td>
      </tr>
  	</table>
</form>
	<script>
	document.onkeypress = stopRKey;
    </script> 

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
		$trk = explode('~',$_REQUEST['truck']);
		$truckbr = $trk[0];
		$truck = $trk[1];
		$trl = explode('~',$_REQUEST['trailer']);
		$trailerbr = $trl[0];
		$trailer = $trl[1];
		$detail = $_REQUEST['details'];
		$harm = $_REQUEST['harm'];
		$damage = $_REQUEST['damage'];
		$occur = $_REQUEST['occur'];
		$terrain = $_REQUEST['terrain'];
		$weather = $_REQUEST['weather'];
		$temperature = $_REQUEST['temperature'];
		$wind = $_REQUEST['wind'];
		
		$q = "insert into incidents (date_entered,date_incident,ref,time_incident,latitude,longitude,LTI,client,accountno,sub,hs_contractor,sub_contractor,crew,compiler,incident_type,route_id,forest,compartment,road,details,truckno,trailerno,harm_people,damage_property,reocurr,terrain,weather,temperature,wind) values (";
		$q .= '"'.$hdate.'",';
		$q .= '"'.$ddate.'",';
		$q .= '"'.$ref.'",';
		$q .= '"'.$time.'",';
		$q .= '"'.$lat.'",';
		$q .= '"'.$long.'",';
		$q .= '"'.$lti.'",';
		$q .= '"'.$client.'",';
		$q .= $ac.',';
		$q .= $sb.',';
		$q .= $hsc.',';
		$q .= '"'.$contr.'",';
		$q .= '"'.$crw.'",';
		$q .= '"'.$compiler.'",';
		$q .= '"'.$type.'",';
		$q .= $route.',';
		$q .= '"'.$forest.'",';
		$q .= '"'.$cpt.'",';
		$q .= '"'.$loc.'",';
		$q .= '"'.$detail.'",';
		$q .= '"'.$truck.'",';
		$q .= '"'.$trailer.'",';
		$q .= '"'.$harm.'",';
		$q .= '"'.$damage.'",';
		$q .= '"'.$occur.'",';
		$q .= '"'.$terrain.'",';
		$q .= '"'.$weather.'",';
		$q .= '"'.$temperature.'",';
		$q .= '"'.$wind.'")';

		$r = mysql_query($q) or die(mysql_error().$q);
		$incid = mysql_insert_id();
		
		echo '<script>';
		echo 'var x = 0, y = 0;';	
		echo 'x = window.screenX +5;';
		echo 'y = window.screenY +200;';
	  	echo 'window.open("","incidents").jQuery("#incidentlist").trigger("reloadGrid");';
		echo "window.open('editincident.php?uid=".$incid."','edinc','toolbar=0,scrollbars=1,height=500,width=900,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);";
		echo 'this.close();';
		echo '</script>;';
				
		
			
	}

?>


</body>
</html>
