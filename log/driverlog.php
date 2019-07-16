<?php
session_start();
$usersession = $_SESSION['usersession'];
$cid = $_SESSION['s_coyid'];

$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";

$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$sid = $subid;

date_default_timezone_set($_SESSION['s_timezone']);

$edate = date("d/m/Y");
$edateh = date("Y-m-d");
$bdate = date("d/m/Y", strtotime('today - 30 days'));
$bdateh = date("Y-m-d", strtotime('today - 30 days'));

$query = "select users.uid,concat(users.ufname,' ',users.ulname) as uname from users,access where users.uid = access.staff_id and access.module = 'prc' and access.subid = ".$sid." and access.coyid = ".$cid;
$result = mysql_query($query) or die(mysql_error().' '.$query);
// populate branches list
$driver_options = "";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	$driver_options .= "<option value=\"".$uid."\">".$uname."</option>";
}



$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Driver Log</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script language="javascript" type="text/javascript" src="../includes/datetimepick/datetimepicker.js"></script>

<script>

	window.name = "driverlog";


</script>

</head>

<body>
<form name="form1" method="post" >
<br>
  <table width="900" border="0" align="center" cellpadding="3">
  <tr>
  	<td colspan="5" align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>; font-size:14px;">Driver Log</label></td>
  </tr>
  <tr>
    <td class="boxlabelleft">Between Dates</td>
    <td colspan="2"><input type="Text" id="bdate" name="bdate" maxlength="25" size="25" value="<?php echo $bdate; ?>">
    <a href="javascript:NewCal('bdate')"><img src="../includes/datetimepick/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a></td>
    <td class="boxlabelleft">and</td>
     <td id="edatecell"><input type="Text" id="edate" name="edate" maxlength="25" size="25" value="<?php echo $edate; ?>"><a href="javascript:NewCal('edate')"><img src="../includes/datetimepick/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a></td>
  </tr>
  <tr>
   	<td class="boxlabelleft">Drivers</td>
    <td><select name="driver" id="driver" multiple="multiple"><?php echo $driver_options;?></select></td>
    <td colspan="3" class="boxlabelleft" valign="middle"><p>hold Ctrl while selecting to select multiple drivers.</p>
      <p>&nbsp;</p></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2"><input type="button" value="Run" name="run"  onClick="dlog()" ></td>
  </tr>
  </table>
</form>
</body>
</html>