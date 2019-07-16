<?php
session_start();
//ini_set('display_errors', true);

date_default_timezone_set($_SESSION['s_timezone']);

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Choose a Campaign</title>

<script type="text/javascript">

window.name = "runcampaign";


</script>
</head>
<body>
 <table width="250" border="0">
   <tr>
     <th class="boxlabel" align="right"><label style="color: <?php echo $tdfont; ?>">Campaign</label></th>
     <th align="left"><input type="text" id="csearchlastname" size="20" onkeypress="doSearch1c()" /></th>
     <th><input type="button" name="bunsearch" id="bunsearch" value="Reset" onclick="sresetr()" /></th>
   </tr>
   <tr>
     <td colspan="3"><?php include "getCampaignsR.php" ?></td>
   </tr>
	<tr>
	<td colspan="3"><img src="../includes/calculator/calculator.png" width="16" height="16" alt="Calculators" title="Calculators" onclick="showCalculators();"></td>
	</tr>
	</table>		



</body>
</html>