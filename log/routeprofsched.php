<?php
session_start();

date_default_timezone_set($_SESSION['s_timezone']);

$fromdate = $_REQUEST['bdate'];
$todate = $_REQUEST['edate'];

$_SESSION['s_fromdate'] = $fromdate;
$_SESSION['s_todate'] = $todate;
$_SESSION['s_tbheading'] = ' from '.$fromdate.' to '.$todate;

$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$tbfile = 'ztmp'.$user_id.'_rprof';

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

$query = "drop table if exists ".$tbfile;
$result = mysql_query($query) or die(mysql_error());

$query = "create table ".$tbfile." (docket_id int(11) default 0, docket_no int(11) default 0, ddate date default '0000-00-00',route varchar(50) default '',cpt char(6) default '',vehicle varchar(15) default '', valuekm decimal(16,2) default 0)  engine myisam";
$calc = mysql_query($query) or die(mysql_error());

$heading = '';

// populate profitability table
// between dates 
	
$query = "insert into ".$tbfile." select dockets.docket_id, dockets.docket_no, dockets.ddate, routes.route, dockets.cpt, dockets.truck, (dockets.amount/(routes.public+routes.private+routes.positioning)) as valukm from dockets,routes where dockets.routeid = routes.uid and dockets.ddate >= '".$fromdate."' and dockets.ddate <= '".$todate."' and dockets.truck <> ''";
$result = mysql_query($query) or die(mysql_error().' '.$query);

$query = "insert into ".$tbfile." select dockets.docket_id, dockets.docket_no, dockets.ddate, routes.route, dockets.cpt, dockets.trailer, (dockets.amount/(routes.public+routes.private+routes.positioning)) as valukm from dockets,routes where dockets.routeid = routes.uid and dockets.ddate >= '".$fromdate."' and dockets.ddate <= '".$todate."' and dockets.trailer <> ''";
$result = mysql_query($query) or die(mysql_error().' '.$query);


$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");




?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title>Routes Profitability Report</title>

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
	        <td><?php include "getrouteprofgrid.php"; ?></td>
        </tr>
	</table>		

</body>
</html>