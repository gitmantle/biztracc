<?php
session_start();

date_default_timezone_set($_SESSION['s_timezone']);

$fromdate = $_REQUEST['bdate'];
$todate = $_REQUEST['edate'];
$driverids = substr($_REQUEST['driver'], 0, strlen($_REQUEST['driver'])-1); 	
$drvids = "";
$dr = explode(",",$driverids);
foreach ($dr as $value) {
	$drvids .= "'".$value."~";
}
$driverids = substr($drvids,0,strlen($drvids)-1);
$driverids = str_replace('~',chr(39).',',$driverids).chr(39);
$drs = $driverids;

$_SESSION['s_fromdate'] = $fromdate;
$_SESSION['s_todate'] = $todate;
$_SESSION['s_tbheading'] = ' from '.$fromdate.' to '.$todate.' for '.$drs;
$_SESSION['s_drivers'] = $drs;

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

<title>Driver Log Report</title>

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

window.name = 'dloggrid';

</script>
</head>
<body>
    <table align="center">
        <tr>
	        <td><?php include "getdriverlog.php"; ?></td>
        </tr>
	</table>		

</body>
</html>