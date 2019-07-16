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

$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());

// populate category drop down
$query = "select * from acccats order by acccat";
$result = mysql_query($query) or die(mysql_error());
$acat_options = "<option value=\"\">Any Category</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	$acat_options .= '<option value="'.$acccat.'">'.$acccat.'</option>';
}

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

date_default_timezone_set($_SESSION['s_timezone']);

$edate = date("d/m/Y");
$edateh = date("Y-m-d");

$query = "select bedate from globals";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$bdateh = $bedate;
$dt = explode('-',$bedate);
$y = $dt[0];
$m = $dt[1];
$d = $dt[2];
$fdt = time(0,0,0,$m,$d,$y);
$bdate = date("d/m/Y",$fdt);
$bdateh = $bedate;


$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Detail Multiple CR Account Parameters</title>
<link rel="stylesheet" type="text/css" href="../includes/flatpickr_date/dist/flatpickr.min.css">
<script src="../includes/flatpickr_date/dist/flatpickr.js"></script>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<script>

	window.name = "rep_allcr";

</script>

</head>

<body>
<form name="form1" method="post" >
	 <input type="hidden" name="det1" id="det1" value="dr">
<br>
  <table width="700" border="0" align="center" cellpadding="3">
  <tr>
  	<td colspan="5" align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>; font-size:14px;">Detail Multiple Creditor Account Parameters</label></td>
  </tr>
  <tr>
    <td class="boxlabelleft">Between Dates</td>
    <td id="bdatecell"><input type="Text" id="bdate" name="bdate" maxlength="25" size="25" value="<?php echo $bdateh; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y"></td>
    <td>and</td>
     <td id="edatecell"><input type="Text" id="edate" name="edate" maxlength="25" size="25" value="<?php echo $edateh; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y"></td>
  </tr>
  <tr>
    <td class="boxlabelleft">From Account</td>
    <td colspan="4"><input type="text" name="CRaccount" id="CRaccount" size="45" readonly="readonly" />
      <img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="crsearch" onclick="get1cr()" /></td>
  </tr>
  <tr>
    <td class="boxlabelleft">To Account</td>
    <td colspan="4"><input type="text" name="CRaccount2" id="CRaccount2" size="45" readonly="readonly" />
      <img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="crsearch2" onclick="get1cr2()" /></td>
  </tr>
  <tr>
    <td class="boxlabelleft">Category/Catgegories (if applicable)</td>
    <td colspan="4"><select name="acat" id="acat"  multiple="multiple"><?php echo $acat_options;?></select></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2"><input type="button" value="Run" name="run"  onClick="detmultcr()" ></td>
  </tr>
  </table>
  
  <div id="crselect" style="position:absolute;visibility:hidden;top:104px;left:455px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchcr" size="50" onkeypress="doSearchcr()" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="drclose" onclick="sboxhidecr()">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selectcr1.php"; ?></td>
      </tr>
    </table>
  </div>
  
  <div id="crselect2" style="position:absolute;visibility:hidden;top:150px;left:455px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchdr2" size="50" onkeypress="doSearchcr2()" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="drclose" onclick="sboxhidecr2()">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selectcr2.php"; ?></td>
      </tr>
    </table>
  </div>  

 <script>
 	document.getElementById("edate").flatpickr({
		onChange: function(dateObj, dateStr, instance) {
		}
	});
 </script>
 <script>
 	document.getElementById("bdate").flatpickr({
		onChange: function(dateObj, dateStr, instance) {
		}
	});
 </script>
      
  
</form>
</body>
</html>