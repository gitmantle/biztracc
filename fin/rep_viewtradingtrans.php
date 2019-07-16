<?php
session_start();

date_default_timezone_set($_SESSION['s_timezone']);

$refno = $_REQUEST['rf'];
$_SESSION['s_tradingref'] = $refno;

$usersession = $_SESSION['usersession'];


$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");




?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title>View Trading Transactions</title>

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

window.name = 'rep_viewtradingtrans';

function editdesc(rf) {
    var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
    window.open('tr_editdesc.php?uid='+rf,'eddesc','toolbar=0,scrollbars=1,height=170,width=800,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

</script>
</head>
<body>
    <table align="center">
        <tr>
	        <td><?php include "getviewtradingtrans.php"; ?></td>
        </tr>
	</table>		

</body>
</html>