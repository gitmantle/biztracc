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




$moduledb = $_SESSION['h_sdb'];
mysql_select_db($moduledb) or die(mysql_error());





date_default_timezone_set($_SESSION['s_timezone']);

$edate = date("d/m/Y");
$edateh = date("Y-m-d");
$bdate = date("d/m/Y", strtotime('today - 30 days'));
$bdateh = date("Y-m-d", strtotime('today - 30 days'));

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
      <td colspan="2"><?php include "getincidents.php"; ?></td>
    </tr>
    <tr>
      <td ><img src="../includes/calculator/calculator.png" width="16" height="16" alt="Calculators" title="Calculators" onclick="showCalculators();" />&nbsp; <img src="../images/todo.gif" width="16" height="16" alt="ToDo" title="ToDo List" onclick="todo()" /></td>
      <td align="right">&nbsp;Map all incidents that occured between
        <input type="Text" id="bdate" name="bdate" maxlength="25" size="25" value="<?php echo $bdate; ?>"><a href="javascript:NewCal('bdate')"><img src="../includes/datetimepick/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
        and
        <input type="Text" id="edate" name="edate" maxlength="25" size="25" value="<?php echo $edate; ?>"><a href="javascript:NewCal('edate')"><img src="../includes/datetimepick/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
        &nbsp;Viewing Resolution<select name="res" id="res" ><?php echo $screen_options;?></select>
        <input type="button" name="bmapincidents" id="bmapincidents" value="Map Incidents" onClick="mapincidents()"/></td>
    </tr>
  </table></td>
</tr>
</table>
</form>
</body>

</html>