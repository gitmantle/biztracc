<?php
session_start();
$usersession = $_SESSION['usersession'];

$_SESSION['watermark'] = 'N';

date_default_timezone_set($_SESSION['s_timezone']);

$bdate = date("d/m/Y");
$bdateh = date("Y-m-d");
			   
$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Creditors Balances at a Date</title>
<link rel="stylesheet" type="text/css" href="../includes/flatpickr_date/dist/flatpickr.min.css">
<script src="../includes/flatpickr_date/dist/flatpickr.js"></script>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script type="text/javascript">

	window.name = "rep_crbaldt";

</script>
</head>
<body>
<form name="form1" method="post" >
  <br>
  <table width="600" border="0" align="center" cellpadding="3">
    <tr>
      <td colspan="2" align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>; font-size:14px;">Creditor Balances at a Date</label></td>
    </tr>
    <tr>
      <td class="boxlabelleft">Balances as at</td>
      <td id="bdatecell"><input type="Text" id="bdate" name="bdate" maxlength="25" size="25" value="<?php echo $bdateh; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y"></td>
    </tr>
    <tr>
      <td class="boxlabelleft">From Creditor</td>
      <td ><input type="text" name="fromcr" id="fromcr" size="45" readonly >
        <img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="crsearchfrom" onclick="transvisiblecrfrom()">
    </td>
    
    </tr>
    
    <tr>
      <td class="boxlabelleft">To Creditor</td>
      <td ><input type="text" name="tocr" id="tocr" size="45" readonly >
        <img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="crsearchto" onclick="transvisiblecrto()">
        </div></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td align="right"><input type="button" value="Run" name="run"  onClick="crbaldt()" ></td>
    </tr>
  </table>
  <div id="crselectfrom" style="position:absolute;visibility:hidden;top:125px;left:500px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchcrfrom" size="50" onkeypress="doSearchcrfrom()" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="fromclose" onclick="sboxhidecrfrom()">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selectcrfrom.php"; ?></td>
      </tr>
    </table>
  </div>
  <div id="crselectto" style="position:absolute;visibility:hidden;top:150px;left:500px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchcrto" size="50" onkeypress="doSearchcrto()" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="toclose" onclick="sboxhidecrto()">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selectcrto.php"; ?></td>
      </tr>
    </table>
  </div>
  <script>
 	document.getElementById("bdate").flatpickr({
		onChange: function(dateObj, dateStr, instance) {
		}
	});
 </script>
</form>
</body>
</html>