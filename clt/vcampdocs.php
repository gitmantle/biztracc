<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];
$admindb = $_SESSION['s_admindb'];
date_default_timezone_set($_SESSION['s_timezone']);

require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$campid = $_REQUEST['uid'];
$_SESSION['s_campid'] = $campid;

$query = "select * from campaigns where campaign_id = ".$campid;
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>View Campaign Documents</title>

<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />

<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-1.8.4.custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>

<script type="text/javascript">

window.name = 'updtcampdocs';

	function viewdoc(d) {
		var readfile = "../documents/campaign/"+d;
			var x = 0, y = 0; // default values	
			x = window.screenX +5;
			y = window.screenY +100;
		window.open(readfile,'cdoc','toolbar=0,scrollbars=1,height=500,width=1000,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	}


</script>
</head>
<body>
    <table>
    <tr><td align="center" ><label style="font-size: 14px;">View Campaign Documents for <?php echo $name; ?></label></td></tr>
    <tr>
    <td><?php include "getvcampdocs.php" ?></td>
    </tr>
	</table>		

</body>
</html>