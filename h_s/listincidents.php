<?php
session_start();
$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$subscriber = $subid;




$incfile = 'ztmp'.$user_id.'_hs';

$moduledb = $_SESSION['h_sdb'];
mysql_select_db($moduledb) or die(mysql_error());

$query = "drop table if exists ".$incfile;
$result = mysql_query($query) or die(mysql_error());

$queryinc = "CREATE TABLE ".$incfile." (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `incid` int(11) NOT NULL,
  `subid` int(11) NOT NULL,
  `coyid` int(11) NOT NULL,
  `coyname` varchar(70) NOT NULL,
  `ref` varchar(20) NOT NULL,
  `date_entered` date NOT NULL,
  `date_incident` date NOT NULL,
  `time_incident` char(8) NOT NULL,
  `latitude` varchar(15) NOT NULL,
  `longitude` varchar(15) NOT NULL,
  `LTI` char(3) NOT NULL DEFAULT 'No',
  `client` varchar(70) NOT NULL,
  `accountno` int(10) NOT NULL DEFAULT '0',
  `sub` int(2) NOT NULL DEFAULT '0',
  `sub_contractor` varchar(70) NOT NULL,
  `crew` varchar(10) NOT NULL,
  `compiler` varchar(50) NOT NULL,
  `incident_type` varchar(75) NOT NULL,
  `route_id` int(11) NOT NULL DEFAULT '0',
  `forest` varchar(50) NOT NULL,
  `compartment` varchar(10) NOT NULL,
  `road` varchar(70) NOT NULL,
  `details` text NOT NULL,
  `truckno` varchar(25) NOT NULL,
  `trailerno` varchar(25) NOT NULL,
  `harm_people` varchar(20) NOT NULL DEFAULT 'No',
  `damage_property` varchar(20) NOT NULL DEFAULT 'No',
  `reocurr` varchar(20) NOT NULL DEFAULT 'No',
  `terrain` varchar(75) NOT NULL,
  `weather` varchar(75) NOT NULL,
  `temperature` varchar(75) NOT NULL,
  `wind` varchar(75) NOT NULL,
  `immediate` text NOT NULL,
  `basic1` varchar(70) NOT NULL,
  `basic2` varchar(70) NOT NULL,
  `basic3` varchar(70) NOT NULL,
  `basic4` varchar(70) NOT NULL,
  `basic5` varchar(70) NOT NULL,
  `basic6` varchar(70) NOT NULL,
  `basic7` varchar(70) NOT NULL,
  `basic8` varchar(70) NOT NULL,
  `hazards` text NOT NULL,
  `tabletid` varchar(30) NOT NULL,
  `include` char(1) NOT NULL DEFAULT 'N',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM";  

$calc = mysql_query($queryinc) or die(mysql_error());


$moduledb = $_SESSION['s_admindb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select coyid,coysubid,coyname from companies where hs_to = ".$subscriber;
$result = mysql_query($q) or die(mysql_error().$q);
while ($row = mysql_fetch_array($result)) {
	extract($row);
	$sid = $coysubid;
	$cid = $coyid;
	$cname = $coyname;
	
	$moduledb = 'log'.$sid.'_'.$cid;
	mysql_select_db($moduledb) or die(mysql_error());

	$qi = "select * from incidents where hs_contractor = ".$subscriber;
	$ri = mysql_query($qi) or die(mysql_error().$qi);
	while ($row = mysql_fetch_array($ri)) {
		extract($row);

	$moduledb = $_SESSION['h_sdb'];
	mysql_select_db($moduledb) or die(mysql_error());


		$qn = "insert into ".$incfile." (incid,subid,coyid,coyname,ref,date_entered,date_incident,time_incident,latitude,longitude,LTI,client,accountno,sub,sub_contractor,crew,compiler,incident_type,route_id,forest,compartment,road,details,truckno,trailerno,harm_people,damage_property,reocurr,terrain,weather,temperature,wind,immediate,basic1,basic2,basic3,basic4,basic5,basic6,basic7,basic8,hazards) values (";
		$qn .= $uid.',';
		$qn .= $sid.',';
		$qn .= $cid.',';
		$qn .= '"'.$cname.'",';
		$qn .= '"'.$ref.'",';
		$qn .= '"'.$date_entered.'",';
		$qn .= '"'.$date_incident.'",';
		$qn .= '"'.$time_incident.'",';
		$qn .= '"'.$latitude.'",';
		$qn .= '"'.$longitude.'",';
		$qn .= '"'.$LTI.'",';
		$qn .= '"'.$client.'",';
		$qn .= $accountno.',';
		$qn .= $sub.',';
		$qn .= '"'.$sub_contractor.'",';
		$qn .= '"'.$crew.'",';
		$qn .= '"'.$compiler.'",';
		$qn .= '"'.$incident_type.'",';
		$qn .= $route_id.',';
		$qn .= '"'.$forest.'",';
		$qn .= '"'.$compartment.'",';
		$qn .= '"'.$road.'",';
		$qn .= '"'.$details.'",';
		$qn .= '"'.$truckno.'",';
		$qn .= '"'.$trailerno.'",';
		$qn .= '"'.$harm_people.'",';
		$qn .= '"'.$damage_property.'",';
		$qn .= '"'.$reocurr.'",';
		$qn .= '"'.$terrain.'",';
		$qn .= '"'.$weather.'",';
		$qn .= '"'.$temperature.'",';
		$qn .= '"'.$wind.'",';
		$qn .= '"'.$immediate.'",';
		$qn .= '"'.$basic1.'",';
		$qn .= '"'.$basic2.'",';
		$qn .= '"'.$basic3.'",';
		$qn .= '"'.$basic4.'",';
		$qn .= '"'.$basic5.'",';
		$qn .= '"'.$basic6.'",';
		$qn .= '"'.$basic7.'",';
		$qn .= '"'.$basic8.'",';
		$qn .= '"'.$hazards.'")';
		
		$rn = mysql_query($qn) or die(mysql_error().$qn);
		
		
	}
}

date_default_timezone_set($_SESSION['s_timezone']);

$admindb = $_SESSION['s_admindb'];
mysql_select_db($admindb) or die(mysql_error());

// populate contractee drop down
$query = "select coyid, coyname from companies where hs_to = ".$subscriber;
$result = mysql_query($query) or die(mysql_error().$query);
$coyname_options = "<option value=\"\"> </option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
		$selected = '';
	$coyname_options .= '<option value="'.$coyid.'"'.$selected.'>'.$coyname.'</option>';
}


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

$moduledb = 'log'.$sid.'_'.$cid;
mysql_select_db($moduledb) or die(mysql_error());


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





$moduledb = $_SESSION['h_sdb'];
mysql_select_db($moduledb) or die(mysql_error());


date_default_timezone_set($_SESSION['s_timezone']);

$edate = date("d/m/Y");
$edateh = date("Y-m-d");
$tdate = date("d/m/Y");
$tdateh = date("Y-m-d");
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

<title>Incident</title>
<script language="javascript" type="text/javascript" src="../includes/datetimepick/datetimepicker.js"></script>

<script>

window.name = "incidents";


</script>

</head>
<body>
<form name="form1" id="form1" method="post" >

<table width="960" border="0">
<tr>
  <td ><table width="960" border="0">
    <tr></tr>
    <tr>
      <td colspan="2"><?php include "getlist.php"; ?></td>
    </tr>
    <tr>
      <td ><img src="../includes/calculator/calculator.png" width="16" height="16" alt="Calculators" title="Calculators" onclick="showCalculators();" />&nbsp; <img src="../images/todo.gif" width="16" height="16" alt="ToDo" title="ToDo List" onclick="todo()" /></td>
      <td align="right">&nbsp;&nbsp;Viewing Resolution<select name="res" id="res" ><?php echo $screen_options;?></select>
        <input type="button" name="bmaplincidents" id="bmaplincidents" value="Map Filtered Incidents" onClick="maplistincidents()"/></td>
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
      <td align="left"><label style="color: <?php echo $tdfont; ?>">Contractee</label></td>
      <td colspan="2" align="left"><select name="coyname" id="coyname"><?php echo $coyname_options;?></select></td>
      <td align="center"><input name="notcoyname" type="checkbox" id="notcoyname" /></td>
    </tr>
    <tr>
      <td align="left"><label style="color: <?php echo $tdfont; ?>">From Date</label></td>
      <td colspan="2" align="left"><input type="Text" id="fdate" name="fdate" maxlength="25" size="25" ><a href="javascript:NewCal('fdate')"><img src="../includes/datetimepick/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a></td>
      <td align="center">&nbsp;</td>
    </tr>
    <tr>
      <td align="left"><label style="color: <?php echo $tdfont; ?>">To Date</label></td>
      <td colspan="2" align="left"><input type="Text" id="tdate" name="tdate" maxlength="25" size="25" ><a href="javascript:NewCal('tdate')"><img src="../includes/datetimepick/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a></td>
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