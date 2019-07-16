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

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

// populate trucks drop down
$query = "select branch,branchname from branch where branchname like 'Truck%'";
$result = mysql_query($query) or die(mysql_error().$query);
$truck_options = "<option value=\"*\">All Trucks</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);

		$selected = '';

	$truck_options .= '<option value="'.$branchname.'"'.$selected.'>'.$branchname.'</option>';
}

// populate from time list
    $arr = array('01:00','02:00','03:00','04:00','05:00','06:00','07:00','08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00','21:00','22:00','23:00');
	$fromtime_options = "<option value=\"00:00\">00:00</option>";
    for($i = 0; $i < count($arr); $i++)	{
				$selected = '';
		$fromtime_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate time list
    $arr = array('01:00','02:00','03:00','04:00','05:00','06:00','07:00','08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00','21:00','22:00','23:00','24:00');
	$time_options = "<option value=\"24:00\">24:00</option>";
    for($i = 0; $i < count($arr); $i++)	{
				$selected = '';
		$time_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate resolution list
    $arr = array('1280x800','1366x768','1280x1024','1920x1080');
	$screen_options = "<option value=\"1024 x 768\">1024 x 768</option>";
    for($i = 0; $i < count($arr); $i++)	{
				$selected = '';
		$screen_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

date_default_timezone_set($_SESSION['s_timezone']);

$edate = date("d/m/Y");
$edateh = date("Y-m-d");


$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Vehicle Locations</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script language="javascript" type="text/javascript" src="../includes/datetimepick/datetimepicker.js"></script>

<script>

	window.name = "vehiclelocs";

</script>

</head>

<body>
<form name="form1" method="post" >
<br>
  <table width="500" border="0" align="center" cellpadding="3">
  <tr>
  	<td colspan="3" align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>; font-size:14px;">Vehicle Locations</label></td>
  </tr>
  <tr>
    <td class="boxlabelleft">From</td>
    <td ><select name="from" id="from" >
      <?php echo $fromtime_options;?>
    </select></td>
  </tr>
  <tr>
    <td class="boxlabelleft">Up to</td>
    <td ><select name="time" id="time" >
      <?php echo $time_options;?>
    </select></td>
  </tr>
  <tr>
    <td class="boxlabelleft">On</td>
    <td id="bdatecell"><input type="Text" id="edate" name="edate" maxlength="25" size="25" value="<?php echo $edate; ?>"><a href="javascript:NewCal('edate')"><img src="../includes/datetimepick/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a></td>
  </tr>
  <tr>
   	<td class="boxlabelleft">Trucks</td>
    <td><select name="trucks" id="trucks" multiple="multiple">
      <?php echo $truck_options;?>
    </select></td>
  </tr>
  <tr>
  	<td class="boxlabelleft">Viewing Resolution</td>
    <td class="boxlabelleft"><select name="res" id="res" >
      <?php echo $screen_options;?>
    </select></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="right"><input type="button" value="Run" name="run"  onClick="mapvehicles()" ></td>
    </tr>
  </table>
</form>
</body>
</html>