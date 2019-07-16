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

$bdate = date("Y-m-d");
$edate = date("Y-m-d");

$db->closeDB();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Day's Takings</title>
<link rel="stylesheet" type="text/css" href="../includes/flatpickr_date/dist/flatpickr.min.css">
<script src="../includes/flatpickr_date/dist/flatpickr.js"></script>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<script>

	window.name = "rep_daytake";

</script>

</head>

<body>
<form name="form1" method="post" >
<br>
  <table width="600" border="0" align="center" cellpadding="3">
  <tr>
  	<td colspan="5" align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>; font-size:14px;">Day's Takings</label></td>
  </tr>
  <tr>
    <td  class="boxlabelleft">For dates from</td>
    <td><input type="Text" id="bdate" name="bdate" maxlength="25" size="25" value="<?php echo $bdate; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y"></a></td>
    <td class="boxlabelleft">to</td>
    <td colspan="2"><input type="Text" id="edate" name="edate" maxlength="25" size="25" value="<?php echo $edateh; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y"></td>
    </tr>
  <tr>
    <td  class="boxlabelleft">Total Cash on Hand</td>
    <td colspan="4"><input type="text" name="tcash" id="tcash" /></td>
    </tr>
  <tr>
   	<td class="boxlabelleft">Float</td>
    <td colspan="4"><input type="text" name="tfloat" id="tfloat" /></td>
    </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="4"><input type="button" value="Run" name="run"  onClick="daytake()" ></td>
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