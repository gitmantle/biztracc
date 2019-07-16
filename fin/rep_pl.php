<?php
session_start();
$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$findb = $_SESSION['s_findb'];

$db->query('select * from '.$findb.'.branch order by branchname');
$rows = $db->resultset();
// populate branches list
$branch_options = "<option value=\"\">Select Branch</option>";
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
$bdateh = $bedate;
$bdate = date("d/m/Y", strtotime($bedate));

$db->closeDB();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Profit and Loss Parameters</title>
<link rel="stylesheet" type="text/css" href="../includes/flatpickr_date/dist/flatpickr.min.css">
<script src="../includes/flatpickr_date/dist/flatpickr.js"></script>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<script>

	window.name = "tr_stdtrans";


</script>

</head>

<body>
<form name="form1" method="post" >
<br>
  <table width="900" border="0" align="center" cellpadding="3">
  <tr>
  	<td colspan="5" align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>; font-size:14px;">Profit and Loss Parameters</label></td>
  </tr>
  <tr>
    <td class="boxlabelleft"><input type="radio" name="dates" onclick="displayResult(this.value)" value="ytd" checked>Year to Date</td>
    <td>&nbsp;</td>
    <td class="boxlabelleft">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td class="boxlabelleft"><input type="radio" name="dates" onclick="displayResult(this.value)" value="between">Between Dates</td>
    <td id="bdatecell"><input type="Text" id="bdate" name="bdate" maxlength="25" size="25" value="<?php echo $bdateh; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y"></td>
    <td class="boxlabelleft">and</td>
     <td id="edatecell"><input type="Text" id="edate" name="edate" maxlength="25" size="25" value="<?php echo $edateh; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y"></td>
  </tr>
  <tr>
   	<td class="boxlabelleft">Branches</td>
    <td colspan="3"><select name="branch" id="branch" multiple="multiple">
      <?php echo $branch_options;?>
    </select></td>
  </tr>
  <tr>
  	<td class="boxlabelleft">&nbsp;</td>
    <td colspan="3" class="boxlabelleft">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2"><input type="button" value="Run" name="run"  onClick="pl()" ></td>
  </tr>
  </table>
  
  <script>
  document.getElementById('edate').style.visibility = 'hidden';
  document.getElementById('bdate').style.visibility = 'hidden';
  </script>
  
   <script>
  document.getElementById('edate').style.visibility = 'hidden';
  document.getElementById('bdate').style.visibility = 'hidden';
  </script>
  
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