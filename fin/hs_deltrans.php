<?php
session_start();
$usersession = $_SESSION['usersession'];

date_default_timezone_set($_SESSION['s_timezone']);

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Delete a Transaction</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<script type="text/javascript">

	window.name = "deltrans";

</script>

</head>

<body>
<form name="form1" method="post" >
<br>
  <table width="880" border="0" align="center" cellpadding="3">
  <tr>
  	<td colspan="2" align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>; font-size:14px;">Delete a Transaction</label></td>
  </tr>
  <tr>
    <td colspan="2" class="boxlabelleft">You may not delete transactions that have a component that has gone through a bank account and been reconciled, or a transaction that has had a payment allocated against it.</td>
    </tr>
  <tr>
    <td class="boxlabelleft">Enter the reference of the transaction you wish to delete</td>
    <td class="boxlabelleft"><input type="text" name="tref" id="tref" /></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="right"><input type="button" value="Delete Transaction" name="run"  onClick="deltran()" ></td>
  </tr>
  </table>
  

  
</form>
</body>
</html>