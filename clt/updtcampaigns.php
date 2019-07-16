<?php
session_start();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Update Campaigns</title>

<script type="text/javascript">

window.name = "updtcampaigns";

</script>
</head>
<body>
<form name="updtmem" id="updtmem" method="post" action="">
 <input type="hidden" name="ddateh" id="ddateh" value="0000-00-00">
 <table width="950" border="0">
   <tr>
     <th class="boxlabel" align="right"><label style="color: <?php echo $tdfont; ?>">Campaign</label></th>
     <th align="left"><input type="text" id="clastname" size="20" onkeypress="doSearchc1()" /></th>
     <th><input type="button" name="bunsearch" id="bunsearch" value="Reset" onclick="sresetc()" /></th>
   </tr>
   <tr>
     <td colspan="3"><?php include "getCampaigns.php" ?></td>
   </tr>
   <tr>
     <td><img src="../includes/calculator/calculator.png" width="16" height="16" alt="Calculators" title="Calculators" onclick="showCalculators();" /></td>
     <td>&nbsp;</td>
     <td>&nbsp;</td>
   </tr>
 </table>
</form>

</body>
</html>