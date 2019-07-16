<?php
session_start();
require("../db.php");

$admindb = $_SESSION['s_admindb'];
mysql_select_db($admindb) or die(mysql_error());


// populate terrain drop down
$query = "select incterrain from incterrain";
$result = mysql_query($query) or die(mysql_error().$query);
$terrain_options = "<option value=\"\"> </option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
		$selected = '';
	$terrain_options .= '<option value="'.$incterrain.'"'.$selected.'>'.$incterrain.'</option>';
}

// populate weather drop down
$query = "select incweather from incweather";
$result = mysql_query($query) or die(mysql_error().$query);
$weather_options = "<option value=\"\"> </option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
		$selected = '';
	$weather_options .= '<option value="'.$incweather.'"'.$selected.'>'.$incweather.'</option>';
}

// populate temperature drop down
$query = "select inctemperature from inctemperature";
$result = mysql_query($query) or die(mysql_error().$query);
$temperature_options = "<option value=\"\"> </option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
		$selected = '';
	$temperature_options .= '<option value="'.$inctemperature.'"'.$selected.'>'.$inctemperature.'</option>';
}

// populate wind drop down
$query = "select incwind from incwind";
$result = mysql_query($query) or die(mysql_error().$query);
$wind_options = "<option value=\"\"> </option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
		$selected = '';
	$wind_options .= '<option value="'.$incwind.'"'.$selected.'>'.$incwind.'</option>';
}

// populate basic cause 1 drop down
$query = "select incbasic from incbasic";
$result = mysql_query($query) or die(mysql_error().$query);
$basic1_options = "<option value=\"\"> </option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
		$selected = '';
	$basic1_options .= '<option value="'.$incbasic.'"'.$selected.'>'.$incbasic.'</option>';
}

// populate basic cause 2 drop down
$query = "select incbasic from incbasic";
$result = mysql_query($query) or die(mysql_error().$query);
$basic2_options = "<option value=\"\"> </option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
		$selected = '';
	$basic2_options .= '<option value="'.$incbasic.'"'.$selected.'>'.$incbasic.'</option>';
}

// populate type list
    $arr = array('','Health and Safety','Environmental');
	$icat_options = "";
    for($i = 0; $i < count($arr); $i++)	{
			$selected = '';
		$icat_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

// populate contractors drop down
$query = "select uid,contractor,crew from contractors order by contractor,crew";
$result = mysql_query($query) or die(mysql_error().$query);
$contractor_options = "<option value=\"\"></option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);

		$selected = '';

	$contractor_options .= '<option value="'.$contractor.'~'.$crew.'"'.$selected.'>'.$contractor.' '.$crew.'</option>';
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


// populate LTI list
    $arr = array('','No','Yes');
	$lti_options = "";
    for($i = 0; $i < count($arr); $i++)	{
				$selected = '';
		$lti_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate harm list
    $arr = array('','None','Insignificant','Minor','Temporary harm','Serious harm','Fatalities');
	$harm_options = "";
    for($i = 0; $i < count($arr); $i++)	{
				$selected = '';
		$harm_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate damage list
    $arr = array('','None','Under $100','Under $1,000','Under $5,000','Under $50,000','Over $50,000');
	$damage_options = "";
    for($i = 0; $i < count($arr); $i++)	{
				$selected = '';
		$damage_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate reocurr list
    $arr = array('','Rare','Possible','Moderate','Likely','Certain');
	$reocurr_options = "";
    for($i = 0; $i < count($arr); $i++)	{
				$selected = '';
		$reocurr_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}







$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

date_default_timezone_set($_SESSION['s_timezone']);

$edate = date("d/m/Y");
$edateh = date("Y-m-d");
$bdate = date("d/m/Y", strtotime('today - 30 days'));
$bdateh = date("Y-m-d", strtotime('today - 30 days'));
$bdate = date("d/m/Y", strtotime('today - 30 days'));
$bdateh = date("Y-m-d", strtotime('today - 30 days'));
$fdate = '00/00/00';
$fdateh = '0000-00-00';

// populate resolution list
    $arr = array('1280x800','1366x768','1280x1024','1920x1080');
	$screen_options = "<option value=\"1024 x 768\">1024 x 768</option>";
    for($i = 0; $i < count($arr); $i++)	{
				$selected = '';
		$screen_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>Incident Register</title>
<script language="javascript" type="text/javascript" src="../includes/datetimepick/datetimepicker.js"></script>

<script>

window.name = "incidents";

</script>

</head>
<body>
<form name="form1" id="form1" method="post" >

<table width="960" border="0">
<tr>
  <td ><table width="970" border="0">
    <tr></tr>
    <tr>
      <td colspan="2"><?php include "getincidents.php"; ?></td>
    </tr>
    <tr>
      <td ><img src="../includes/calculator/calculator.png" width="16" height="16" alt="Calculators" title="Calculators" onclick="showCalculators();" />&nbsp; <img src="../images/todo.gif" width="16" height="16" alt="ToDo" title="ToDo List" onclick="todo()" /></td>
      <td align="right">&nbsp;
Viewing Resolution
  <select name="res" id="res" >
    <?php echo $screen_options;?>
  </select>&nbsp;
  <input type="Text" id="bdate" name="bdate" maxlength="25" size="25" value="<?php echo $bdate; ?>"><a href="javascript:NewCal('bdate')"><img src="../includes/datetimepick/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
        and
        <input type="Text" id="edate" name="edate" maxlength="25" size="25" value="<?php echo $edate; ?>"><a href="javascript:NewCal('edate')"><img src="../includes/datetimepick/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
        &nbsp;
<input type="button" name="bmapincidents" id="bmapincidents" value="Map incidents between dates" onClick="mapincidents()"/>
        <input type="button" name="map_lincs" id="map_lincs" value="Map filtered incidents" onClick="maplistincidents()"/></td>
    </tr>
  </table></td>
</tr>
</table>

<div id="filterpage" style="position:absolute;visibility:hidden;top:90px;left:136px;height:430px;width:600px;background-color:<?php echo $bgcolor; ?>;border-width:7px; border-color:<?php echo $bgcolor; ?>; border-style:outset;">
  <table width="580" align="left" border="0" cellspacing="1" cellpadding="1">
    <tr>
      <th align="left" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>">Filter by:-</label></th>
      <td colspan="2" bgcolor="<?php echo $bghead; ?>">&nbsp;</td>
      <th align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>">Not</label></th>
    </tr>
    <tr>
      <td align="left"><label style="color: <?php echo $tdfont; ?>">Sub Contractor</label></td>
      <td colspan="2" align="left"><select name="coyname" id="coyname"><?php echo $contractor_options;?></select></td>
      <td align="center"><input name="notcoyname" type="checkbox" id="notcoyname" /></td>
    </tr>
    <tr>
      <td align="left"><label style="color: <?php echo $tdfont; ?>">From Date</label></td>
      <td colspan="2" align="left"><input type="Text" id="fdate" name="fdate" maxlength="25" size="25" value="<?php echo $fdate; ?>"><a href="javascript:NewCal('fdate')"><img src="../includes/datetimepick/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a></td>
      <td align="center">&nbsp;</td>
    </tr>
    <tr>
      <td align="left"><label style="color: <?php echo $tdfont; ?>">To Date</label></td>
      <td colspan="2" align="left"><input type="Text" id="tdate" name="tdate" maxlength="25" size="25" value="<?php echo $tdate; ?>"><a href="javascript:NewCal('tdate')"><img src="../includes/datetimepick/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a></td>
      <td align="center">&nbsp;</td>
    </tr>
    <tr>
      <td align="left"><label style="color: <?php echo $tdfont; ?>">Lost time injury</label></td>
      <td colspan="2" align="left"><select name="lti" id="lti"><?php echo $lti_options;?></select></td>
      <td align="center"><input name="notlti" type="checkbox" id="notlti" /></td>
    </tr>
    <tr>
      <td align="left"><label style="color: <?php echo $tdfont; ?>">Incident type</label></td>
      <td colspan="2" align="left"><select name="type" id="type"><?php echo $icat_options;?></select></td>
      <td align="center"><input name="nottype" type="checkbox" id="nottype" /></td>
    </tr>
    <tr>
      <td align="left"><label style="color: <?php echo $tdfont; ?>">Harm to people</label></td>
      <td colspan="2" align="left"><select name="harm" id="harm"><?php echo $harm_options;?></select></td>
      <td align="center"><input name="notharm" type="checkbox" id="notharm" /></td>
    </tr>
    <tr>
      <td align="left"><label style="color: <?php echo $tdfont; ?>">Damage to property</label></td>
      <td colspan="2" align="left"><select name="damage" id="damage"><?php echo $damage_options;?></select></td>
      <td align="center"><input name="notdamage" type="checkbox" id="notdamage" /></td>
    </tr>
    <tr>
      <td align="left"><label style="color: <?php echo $tdfont; ?>">Reocurrence</label></td>
      <td colspan="2" align="left"><select name="reocurr" id="reocurr"><?php echo $reocurr_options;?></select></td>
      <td align="center"><input name="notreocurr" type="checkbox" id="notreocurr" /></td>
    </tr>
    <tr>
      <td align="left"><label style="color: <?php echo $tdfont; ?>">Terrain</label></td>
      <td colspan="2" align="left"><select name="terrain" id="terrain"><?php echo $terrain_options;?></select></td>
      <td align="center"><input name="notterrain" type="checkbox" id="notterrain" /></td>
    </tr>
    <tr>
      <td align="left"><label style="color: <?php echo $tdfont; ?>">Weather</label></td>
      <td colspan="2" align="left"><select name="weather" id="weather"><?php echo $weather_options;?></select></td>
      <td align="center"><input name="notweather" type="checkbox" id="notweather" /></td>
    </tr>
    <tr>
      <td align="left"><label style="color: <?php echo $tdfont; ?>">Temperature</label></td>
      <td colspan="2" align="left"><select name="temperature" id="temperature"><?php echo $temperature_options;?></select></td>
      <td align="center"><input name="nottemperature" type="checkbox" id="nottemperature" /></td>
    </tr>
    <tr>
      <td align="left"><label style="color: <?php echo $tdfont; ?>">Wind</label></td>
      <td colspan="2" align="left"><select name="wind" id="wind"><?php echo $wind_options;?></select></td>
      <td align="center"><input name="notwind" type="checkbox" id="notwind" /></td>
    </tr>
    <tr>
      <td align="left"><label style="color: <?php echo $tdfont; ?>">1st Basic cause</label></td>
      <td colspan="2" align="left"><select name="basic1" id="basic1"><?php echo $basic1_options;?></select></td>
      <td align="center">&nbsp;</td>
    </tr>
     <tr>
      <td align="left"><label style="color: <?php echo $tdfont; ?>">2nd Basic cause</label></td>
      <td colspan="2" align="left"><select name="basic2" id="basic2"><?php echo $basic2_options;?></select></td>
      <td align="center">&nbsp;</td>
    </tr>
    <tr>
      <td align="right" bgcolor="<?php echo $bghead; ?>"><input type="button" name="bfilter" id="bfilter" value="Filter" onclick="filterinc()"/></td>
      <td align="right" bgcolor="<?php echo $bghead; ?>"><input type="button" name="bclose" id="bclose" value="Close" onclick="closefilters()"/></td>
      <td colspan="2" bgcolor="<?php echo $bghead; ?>">&nbsp;</td>
    </tr>
    
    
    
  </table>

</div>


</form>
</body>

</html>