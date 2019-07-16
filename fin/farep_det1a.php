<?php
session_start();
$usersession = $_SESSION['usersession'];

$_SESSION['s_select'] = '~~';

include_once("../includes/DBClass.php");
$db = new DBClass();

$findb = $_SESSION['s_findb'];

$db->query('select * from '.$findb.'.branch order by branchname');
$rows = $db->resultset();
// populate branches list
$branch_options = "<option value=\"*\">All Branches</option>";
foreach ($rows as $row) {
	extract($row);
	$branch_options .= "<option value=\"".$branch."\">".$branchname."</option>";
}


date_default_timezone_set($_SESSION['s_timezone']);

$edate = date("d/m/Y");
$edateh = date("Y-m-d");

$db->query("select bedate from ".$findb.".globals");
$row = $db->single();
extract($row);
$bdate = date("d/m/Y", strtotime($bedate));
$bdateh = $bedate;

$db->closeDB();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Detail One Asset Parameters</title>
<link rel="stylesheet" type="text/css" href="../includes/flatpickr_date/dist/flatpickr.min.css">
<script src="../includes/flatpickr_date/dist/flatpickr.js"></script>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<script>

	window.name = "rep_det1a";

</script>

</head>

<body>
<form name="form1" method="post" >
	 <input type="hidden" name="det1" id="det1" value="as">
<br>
  <table width="700" border="0" align="center" cellpadding="3">
  <tr>
  	<td colspan="5" align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>; font-size:14px;">Detail One Asset Parameters</label></td>
  </tr>
  <tr>
    <td class="boxlabelleft">Between Dates</td>
    <td id="bdatecell"><input type="Text" id="bdate" name="bdate" maxlength="25" size="25" value="<?php echo $bdateh; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y"></td>
    <td>and</td>
     <td id="edatecell"><input type="Text" id="edate" name="edate" maxlength="25" size="25" value="<?php echo $edateh; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y"></td>
  </tr>
  <tr>
    <td class="boxlabelleft">Account</td>
    <td colspan="4"><input type="text" name="ASaccount" id="ASaccount" size="45" readonly="readonly" />
      <img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="glsearch" onclick="get1as()" /></td>
  </tr>
  <tr>
    <td class="boxlabelleft"> Opening Balance</td>
    <td colspan="4"><select name="lob" id="lob">
      <option value="Y">Yes</option>
      <option value="N">No</option>
    </select></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2"><input type="button" value="Run" name="run"  onClick="det1as()" ></td>
  </tr>
  </table>
  
  <div id="asselect" style="position:absolute;visibility:hidden;top:104px;left:455px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchas" size="50" onkeypress="doSearchas()" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="drclose" onclick="sboxhideas()">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selectas.php"; ?></td>
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