<?php
session_start();
$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$findb = $_SESSION['s_findb'];

date_default_timezone_set($_SESSION['s_timezone']);

$stkgroup = "";
// populate groups drop down
$db->query("select * from ".$findb.".stkmast where stock = 'Service' ");
$rows = $db->resultset();
$nonstock_options = "<option value=\"*\">Select Service Item</option>";
foreach ($rows as $row) {
	extract($row);
	$selected = '';
	$nonstock_options .= '<option value="'.$itemcode.'"'.$selected.'>'.$item.'</option>';
}

$edateh = date("Y-m-d");

$db->query("select bedate from ".$findb.".globals");
$row = $db->single();
extract($row);
$bdateh = $bedate;

$db->closeDB();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Services Sales</title>
<link rel="stylesheet" type="text/css" href="../includes/flatpickr_date/dist/flatpickr.min.css">
<script src="../includes/flatpickr_date/dist/flatpickr.js"></script>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<script>

	window.name = "strep_nonstocksales";


</script>

</head>

<body>
<form name="form1" method="post" >
<br>
  <table width="600" border="0" align="center" cellpadding="3">
  <tr>
  	<td colspan="4" align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>; font-size:14px;">Service Sales Parameters</label></td>
  </tr>
  <tr>
    <td class="boxlabelleft">Service Items</td>
    <td colspan="3"><select name="nonstock" id="nonstock"><?php echo $nonstock_options;?></select></td>
  </tr>
  <tr>
    <td class="boxlabelleft">Between Dates</td>
    <td id="bdatecell"><input type="Text" id="bdate" name="bdate" maxlength="25" size="25" value="<?php echo $bdateh; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y"></td>
    <td class="boxlabelleft">and</td>
    <td id="edatecell"><input type="Text" id="edate" name="edate" maxlength="25" size="25" value="<?php echo $edateh; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y"></td>
  </tr> 
  <tr>
    <td colspan="4"></td>
  </tr>
  <tr>
    <td colspan="4" align="right"><input type="button" value="Run" name="run"  onClick="nonstklist()" ></td>
  </tr>
  </table>
  
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