<?php
session_start();


require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>Workshops</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script>

window.name = "workshops";

function addworkshop() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addworkshop.php','adws','toolbar=0,scrollbars=1,height=200,width=700,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editworkshop(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editworkshop.php?uid='+uid,'edws','toolbar=0,scrollbars=1,height=200,width=700,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function updtwsstaff(uid) {
	var x = 0, y = 0; // default values	
 	x = window.screenX +5;
  	y = window.screenY +265;
	jQuery.ajaxSetup({async:false});
	$.get("includes/ajaxupdtwsstaff.php", {uid: uid}, function(data){
	});
	jQuery.ajaxSetup({async:true});
	updtwsstaff2();
}
	
function updtwsstaff2() {
	var x = 0, y = 0; // default values	
 	x = window.screenX +5;
  	y = window.screenY +265;
		
		window.open('wsstaff.php','edwss','toolbar=0,scrollbars=1,height=450,width=850,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

</script>

</head>
<body>
  <table width="960" border="0">
    <tr>
    <tr>
      <td><?php include "getworkshops.php"; ?></td>
    </tr>
    <tr>
      <td ><img src="../includes/calculator/calculator.png" width="16" height="16" alt="Calculators" title="Calculators" onclick="showCalculators();">&nbsp; <img src="../images/todo.gif" width="16" height="16" alt="ToDo" title="ToDo List" onClick="todo()"></td>
    </tr>
  </table>
</body>

</html>