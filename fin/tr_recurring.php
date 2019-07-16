<?php
session_start();
$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db_trd = new DBClass();

$db_trd->query("select * from sessions where session = :vusersession");
$db_trd->bind(':vusersession', $usersession);
$row = $db_trd->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$findb = $_SESSION['s_findb'];

$db_trd->query("select z_rt1,z_rt2,z_rt3,z_rt4,z_rt5,z_rt6 from ".$findb.".globals");
$row = $db_trd->single();
extract($row);

$db_trd->closeDB();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Recurring Transactions</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<script>

	window.name = "tr_recurring";

</script>

</head>

<body>
<form name="form1" method="post" >
<br>
  <table width="900" border="0" align="center" cellpadding="3">
  <tr>
  	<td colspan="2" align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>; font-size:14px;">Select Recurring Transaction File to Process</label></td>
  </tr>
  <tr>
    <td class="boxlabelleft"><input type="radio" name="rtfile" value="rt1" checked><?php echo $z_rt1; ?></td>
    <td>&nbsp;</td>
    </tr>
  <tr>
    <td class="boxlabelleft"><input type="radio" name="rtfile" value="rt2" checked><?php echo $z_rt2; ?></td>
    <td>&nbsp;</td>
    </tr>
  <tr>
    <td class="boxlabelleft"><input type="radio" name="rtfile" value="rt3" checked><?php echo $z_rt3; ?></td>
    <td>&nbsp;</td>
    </tr>
  <tr>
    <td class="boxlabelleft"><input type="radio" name="rtfile" value="rt4" checked><?php echo $z_rt4; ?></td>
    <td>&nbsp;</td>
    </tr>
  <tr>
    <td class="boxlabelleft"><input type="radio" name="rtfile" value="rt5" checked><?php echo $z_rt5; ?></td>
    <td>&nbsp;</td>
    </tr>
  <tr>
    <td class="boxlabelleft"><input type="radio" name="rtfile" value="rt6" checked><?php echo $z_rt6; ?></td>
    <td>&nbsp;</td>
    </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="right"><input type="button" value="Run" name="run"  onClick="recurring()" ></td>
    </tr>
  </table>
</form>
</body>
</html>