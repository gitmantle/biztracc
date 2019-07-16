<?php
session_start();

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Stock List</title>

<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<link rel="stylesheet" type="text/css" media="screen" href="../includes/jquery/themes/ui.multiselect.css"/> 

<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>

<script type="text/javascript">

window.name = "crlist";

function showCalculators() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +100;
	window.open('../includes/calculators.php','calc','toolbar=0,scrollbars=1,height=500,width=950,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function todo() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +275;
	window.open('../includes/updttodo.php','todo','toolbar=0,scrollbars=1,height=550,width=1000,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

</script>
</head>
<body>
  <table width="850" border="0" align="center">
    <tr>
      <td><?php include "getStocklist.php"; ?></td>
    </tr>
    <tr>
      <td><?php include "getStockmove.php"; ?></td>
    </tr>
    <tr>
      <td ><img src="../includes/calculator/calculator.png" width="16" height="16" alt="Calculators" title="Calculators" onclick="showCalculators();">&nbsp; <img src="../images/todo.gif" width="16" height="16" alt="ToDo" title="ToDo List" onClick="todo()"></td>
    </tr>
  </table>
</body>
</html>