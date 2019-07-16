<?php
session_start();

date_default_timezone_set($_SESSION['s_timezone']);

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Recalculate Balances</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<script type="text/javascript">

	window.name = "recalcbals";

</script>

</head>

<body>
<form name="form1" method="post" >
<br>
  <table width="880" border="0" align="center" cellpadding="3">
  <tr>
  	<td colspan="2" align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>; font-size:14px;">Recalculate Balances</label></td>
  </tr>
  <tr>
    <td colspan="2" class="boxlabelleft">Running this utility will recalculate all General Ledger, Debtor, Creditor and Stock balances from the transactions held in the transaction table. It is prudent to take a backup of your financial and client data before running this.</td>
    </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="right"><input type="button" value="Run Recalculate Balances Utility" name="run"  onClick="recalcbals()" ></td>
  </tr>
  </table>
  

  
</form>
</body>
</html>