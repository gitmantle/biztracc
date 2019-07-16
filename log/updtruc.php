<?php
session_start();
require("../db.php");

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select * from params";
$r = mysql_query($q);
$row = mysql_fetch_array($r);
extract($row);


require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>Update RUC Details</title>

<script>

window.name = "rucdetails";

</script>

</head>
<body>
  <table width="960" border="0">
    <tr>
  		<td colspan="5" align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>; font-size:14px;">Update Road User Charge details to facilitate creation of Refund form.</label></td>
    </tr>
    <tr>
      <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Off-road Customer Number</label>
      <input type="text" name="custno" id="custno" value="<?php echo $offroad_custno; ?>" readonly/></td>
      <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Reason Code</label>
        <input type="text" name="reason" id="reason" value="11 - Forestry & Logging" readonly/></td>
      <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">Method used</label>
      <input type="text" name="method" id="method" value="<?php echo $method; ?>" readonly/></td>
      <td class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">GPS type</label>
        <input type="text" name="type" id="type" value="<?php echo $type; ?>" readonly/>
      </td>
      <td class="boxlabelleft"><input type="button" name="edit" id="edit" value="Edit" onclick="editrucparams();"/></td>
    </tr>
    <tr>
      <td colspan="2">Brief description of off-road travel</td>
      <td colspan="2">What records are you able to supply to validate this claim?</td>
      <td align="right">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2"><textarea name="desc" id="desc" cols="50" rows="5"  readonly><?php echo $description; ?></textarea></td>
      <td colspan="2"><textarea name="record" id="record" cols="50" rows="5"  readonly><?php echo $records; ?></textarea></td>
      <td align="right">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="5" ><img src="../includes/calculator/calculator.png" width="16" height="16" alt="Calculators" title="Calculators" onclick="showCalculators();">&nbsp; <img src="../images/todo.gif" width="16" height="16" alt="ToDo" title="ToDo List" onClick="todo()"></td>
    </tr>
  </table>
</body>

</html>