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

$db->query("select lstatdt,pstatdt,lastmedate,yrdate from ".$findb.".globals");
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
$de = explode('-',$yrdate);
$y = $de[0];
$m = $de[1];
$d = $de[2];
$yedate = $d.'/'.$m.'/'.$y;

$db->closeDB();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>End of Year Routine</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<script type="text/javascript">

	window.name = "endyear";

</script>

</head>

<body>
<form name="form1" method="post" >
  <input type="hidden" name="curdt" id="curdt" value=<?php echo $lstatdt; ?>>
  <input type="hidden" name="lstdt" id="lstdt" value=<?php echo $pstatdt; ?>>
<br>
  <table width="880" border="0" align="center" cellpadding="3">
  <tr>
  	<td colspan="2" align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>; font-size:14px;">End of Year Routine for year ending <?php echo $yedate; ?></label></td>
  </tr>
  <tr>
    <td  class="boxlabelleft"><h3>Only run this routine if you ensure the following:-</h3></td>
    </tr>
  <tr>
    <td class="boxlabelleft">You are running this after the end of the tax year you wish to archive.</td>
    <td><input type="checkbox" name="c1" id="c1" /></td>
  </tr>
  <tr>
    <td class="boxlabelleft">Your accountant/auditor has completed their inspection of your accounts.</td>
    <td><input type="checkbox" name="c2" id="c2" /></td>
    </tr>
  <tr>
    <td class="boxlabelleft">All transactions and adjusting entries have been entered.</td>
    <td><input type="checkbox" name="c3" id="c3" /></td>
  </tr>
  <tr>
    <td class="boxlabelleft">Depreciation of all fixed assets is up to date for the previous tax year.</td>
    <td><input type="checkbox" name="c4" id="c4" /></td>
  </tr>
  <tr>
    <td class="boxlabelleft">All final reports for the tax year have been printed and distributed, including</td>
  </tr>
  <tr>
    <td class="boxlabelleft">Balance Sheet, Profit and Loss, Asset Register, Debtors Listing, Creditors Listing</td>
    <td><input type="checkbox" name="c5" id="c5" /></td>
  </tr>
  <tr>
    <td class="boxlabelleft">You have made a backup of the tax year's accounts you are closing off.</td>
    <td><input type="checkbox" name="c6" id="c6" /></td>
  </tr>
  <tr>
  	<td >&nbsp;</td>
   	<td align="right"><input type="button" value="Run End of Year Routine" name="run"  onClick="yrend()" ></td>
 	</tr>
  </table>
  

  
</form>
</body>
</html>