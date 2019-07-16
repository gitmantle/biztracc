<?php
session_start();
$usersession = $_SESSION['usersession'];

$_SESSION['watermark'] = 'N';

include_once("../includes/DBClass.php");
$db = new DBClass();

$findb = $_SESSION['s_findb'];

date_default_timezone_set($_SESSION['s_timezone']);

$edate = date("d/m/Y");
$edateh = date("Y-m-d");

$db->query("select lstatdt,pstatdt,lastmedate from ".$findb.".globals");
$row = $db->single();
extract($row);
$curdat = $lstatdt;
$monthend = $lastmedate;
$dt = explode('-',$curdat);
$y = $dt[0];
$m = $dt[1];
$d = $dt[2];
$stmtdate = $d.'/'.$m.'/'.$y;
$dt = explode('-',$monthend);
$y = $dt[0];
$m = $dt[1];
$d = $dt[2];
$medate = $d.'/'.$m.'/'.$y;

$db->closeDB();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>End of Month Routine</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<script type="text/javascript">

	window.name = "endmonth";

</script>

</head>

<body>
<form name="form1" method="post" >
  <input type="hidden" name="curdt" id="curdt" value=<?php echo $lstatdt; ?>>
  <input type="hidden" name="lstdt" id="lstdt" value=<?php echo $pstatdt; ?>>
<br>
  <table width="880" border="0" align="center" cellpadding="3">
  <tr>
  	<td colspan="3" align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>; font-size:14px;">End of Month Routine</label></td>
  </tr>
  <tr>
    <td colspan="3" class="boxlabelleft">The last time the Month End routine was run was on <?php echo $medate; ?>. Only continue if you have completed your statement run for the month and if required, saved a copy of your statements.</td>
    </tr>
  <tr>
    <td colspan="3" class="boxlabelleft">Ensure you have backed up your financials before running this routine.</td>
  </tr>
  <tr>
    <td colspan="3"class="boxlabelleft">This routine will recalculate all aged balances to coincide with your Statement dates.</td>
    </tr>
  <tr>
  	<td colspan="2">&nbsp;</td>
   	<td align="right"><input type="button" value="Run End of Month Routine" name="run"  onClick="mthend()" ></td>
 	</tr>
  </table>
  

  
</form>
</body>
</html>