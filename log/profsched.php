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

$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$tbfile = 'ztmp'.$user_id.'_prof';

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

$query = "drop table if exists ".$tbfile;
$result = mysql_query($query) or die(mysql_error());

$query = "create table ".$tbfile." (truckno varchar(25), branch char(4) default '', income decimal(16,2) NOT NULL default 0, cost decimal(16,2) NOT NULL default 0, pl decimal(16,2) default 0)  engine myisam";
$calc = mysql_query($query) or die(mysql_error());

$heading = '';

// populate profitability table
// between dates 
	
$query = "insert into ".$tbfile." select branch.branchname, branch.branch, sum(trmain.credit), sum(trmain.debit), sum(trmain.credit) - sum(trmain.debit) from branch, trmain where branch.branch = trmain.branch and branch.branchname like 'Tr%' and branch.branch in (".$brs.") and trmain.accountno < 201 and trmain.reference not like 'GRN%' and ddate >= '".$fromdate."' and ddate <= '".$todate."' group by branch.branch";
$result = mysql_query($query) or die(mysql_error().' '.$query);
		
/*
$_SESSION['s_finheading'] = $heading;
$_SESSION['s_pdftable'] = $tbfile;
$_SESSION['s_fintemplate'] = 'tbtemplate';
$_SESSION['s_daterange'] = $fromdate.'~'.$todate;
*/
$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");




?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title>Profitability Report</title>

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
	        <td><?php include "getprofgrid.php"; ?></td>
        </tr>
	</table>		

</body>
</html>