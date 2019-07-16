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

$query = 'select * from branch where branchname like "Tr%" order by branchname';
$result = mysql_query($query) or die(mysql_error());
// populate branches list
$branch_options = "";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	$branch_options .= "<option value=\"".$branch."\">".$branchname."</option>";
}


date_default_timezone_set($_SESSION['s_timezone']);

$edate = date("d/m/Y");
$edateh = date("Y-m-d");

$query = "select bedate from globals";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$bdateh = $bedate;
$dt = split('-',$bedate);
$y = $dt[0];
$m = $dt[1];
$d = $dt[2];
$fdt = mktime(0,0,0,$m,$d,$y);
$bdate = date("d/m/Y",$fdt);



$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Route Profitabilty Report Parameters</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script language="javascript" type="text/javascript" src="../includes/datetimepick/datetimepicker.js"></script>

<script>

	window.name = "routeprofitablity";

</script>

</head>

<body>
<form name="form1" method="post" >
<br>
  <table width="900" border="0" align="center" cellpadding="3">
  <tr>
  	<td colspan="5" align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>; font-size:14px;">Route Profitability Report Parameters</label></td>
  </tr>
  <tr>
    <td class="boxlabelleft">Between Dates</td>
    <td colspan="2"><input type="Text" id="bdate" name="bdate" maxlength="25" size="25" value="<?php echo $bdate; ?>">
    <a href="javascript:NewCal('bdate')"><img src="../includes/datetimepick/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a></td>
    <td class="boxlabelleft">and</td>
     <td id="edatecell"><input type="Text" id="edate" name="edate" maxlength="25" size="25" value="<?php echo $edate; ?>"><a href="javascript:NewCal('edate')"><img src="../includes/datetimepick/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2"><input type="button" value="Run" name="run"  onClick="routeprofitability()" ></td>
  </tr>
  </table>
</form>
</body>
</html>