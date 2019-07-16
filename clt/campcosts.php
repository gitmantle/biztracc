<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];
date_default_timezone_set($_SESSION['s_timezone']);

$cltdb = $_SESSION['s_cltdb'];
$campid = $_REQUEST['uid'];
$_SESSION['s_campid'] = $campid;

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

$db->query("select sum(cost) as tot from ".$cltdb.".campaign_costs where campaign_id = ".$campid);
$row = $db->single();
extract($row);

$db->closeDB();

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Update Campaign Costs</title>

<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />

<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-1.8.4.custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>

<script type="text/javascript">

window.name = 'updtcampcosts';

	function editcampcost(uid,campid) {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +375;
		window.open('editcampcost.php?uid='+uid+'&campid='+campid,'edccost','toolbar=0,scrollbars=1,height=150,width=650,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	}  				

	function addcampcost(campid) {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +375;
		window.open('addcampcost.php?campid='+campid,'addccost','toolbar=0,scrollbars=1,height=150,width=650,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	}  				

	function delcampcost(uid) {
	  if (confirm("Are you sure you want to delete this cost")) {
				$.get("includes/ajaxdelcampcost.php", {tid: uid}, function(data){$("#costlist").trigger("reloadGrid")});
		  }
	}


</script>
</head>
<body>
    <table>
    <tr>
	    <td align="center" colspan="3"><label style="font-size: 14px;">Update Campaign Costs for <?php echo $name; ?></label></td></tr>
    <tr>
	    <td colspan="2"><?php include "getcampcosts.php" ?></td>
    </tr>
	</table>		


</body>
</html>