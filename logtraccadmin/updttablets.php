<?php
session_start();

$_SESSION['s_tablet'] = $_REQUEST['cid'];

$theme = "cupertino";

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<title>Update Tablets</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />

<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-1.8.4.custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>

<script type="text/javascript">

	window.name = "spupdttablets";
	
 
  function edittablet() {
  var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +265;
	 window.open('edittablet.php','edtab','toolbar=0,scrollbars=1,height=500,width=650,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
  }
  
	
  function addtablet() {
  var x = 0, y = 0; // default values	
  x = window.screenX +5;
  y = window.screenY +265;
	  
	  window.open('addtablet.php','adtab','toolbar=0,scrollbars=1,height=500,width=650,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
  }
						 
</script>

</head>
<body>
    <table align="center">
        <tr>
	        <td><?php include "getTablets.php"; ?></td>
        </tr>
	</table>		



</body>
</html>