<?php
session_start();

date_default_timezone_set($_SESSION['s_timezone']);

$fromdate = $_REQUEST['bdate'];
$todate = $_REQUEST['edate'];
$branchcode = substr($_REQUEST['branch'], 0, strlen($_REQUEST['branch'])-1); 	
$brcodes = "";
$br = explode(",",$branchcode);
foreach ($br as $value) {
	$brcodes .= "'".$value."~";
}
$branchcode = substr($brcodes,0,strlen($brcodes)-1);
$branchcode = str_replace('~',chr(39).',',$branchcode).chr(39);
$brs = $branchcode;


$_SESSION['s_fromdate'] = $fromdate;
$_SESSION['s_todate'] = $todate;
$_SESSION['s_tbheading'] = ' from '.$fromdate.' to '.$todate.' for '.$brs;
$_SESSION['s_cost_centres'] = $brs;

$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");




?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title>Travel Log Report</title>

<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<link rel="stylesheet" type="text/css" media="screen" href="../includes/jquery/themes/ui.multiselect.css"/> 

<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>


<script type="text/javascript" src="js/prc.js"></script>


<script type="text/javascript">

window.name = 'profgrid';

</script>
</head>
<body>
    <table align="center">
        <tr>
	        <td><?php include "gettravel.php"; ?></td>
        </tr>
	</table>		

</body>
</html>