<?php
session_start();

$fromdate = $_REQUEST['bdateh'];
$todate = $_REQUEST['edateh'];
$ob = $_REQUEST['ob'];

$_SESSION['s_fromdate'] = $fromdate;
$_SESSION['s_todate'] = $todate;
$_SESSION['s_brcons'] = 'n';
$_SESSION['s_subcons'] = 'n';
$_SESSION['s_sob'] = $ob;

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");


?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title>Detail 1 DR Account</title>

<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<link rel="stylesheet" type="text/css" media="screen" href="../includes/jquery/themes/ui.multiselect.css"/> 

<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>


<script type="text/javascript" src="js/fin.js"></script>


<script type="text/javascript">

window.name = 'drgrid';

function dr12pdf() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('rep_dr12pdf.php','cr1pdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

function dr12xl() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('rep_dr12excel.php','dr1xl','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

</script>
</head>
<body>
    <table align="center">
        <tr>
	        <td><?php include "get1dr.php"; ?></td>
        </tr>
	</table>		

</body>
</html>


