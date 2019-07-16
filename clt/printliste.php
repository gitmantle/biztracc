<?php
//error_reporting(0); 
session_start();
$usersession = $_SESSION['usersession'];

$ddate = date("d/m/Y");
$hdate = date("Y-m-d");

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];
$usergroup = $row['usergroup'];

$userip = $row['userip'];
$userid = $user_id;
$pic = $userid.'pic.jpg';

$mv = 'm';
$_SESSION['s_mv'] = $mv;

require_once("includes/printfilter.php");
$ddate = date("d/m/Y");

$db->closeDB();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Filtered List</title>

<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<link rel="stylesheet" type="text/css" media="screen" href="../includes/jquery/themes/ui.multiselect.css" /> 

<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-1.8.4.custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>

<script type="text/javascript">
window.name = "filteredlist";
var filterfile = "<?php echo $filterfile; ?>";

function viewmem(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editmember.php?uid='+uid,'vmem','toolbar=0,scrollbars=1,height=780,width=1140,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function list2email() {
  var x = 0, y = 0; // default values	
  x = window.screenX +5;
  y = window.screenY +265;	
  window.open('emailbulk.php?filterfile='+filterfile,'liste','toolbar=0,scrollbars=1,height=600,width=950,resizable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function emailmerge() {
  var x = 0, y = 0; // default values	
  x = window.screenX +5;
  y = window.screenY +265;	
  window.open('emailmerge.php?filterfile='+filterfile,'listm','toolbar=0,scrollbars=1,height=600,width=950,resizable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function trash() {
	var aleads = jQuery("#memliste").getGridParam('selarrrow');	
	var num = aleads.length;	
	var astring = aleads.toString();
	if (num > 0) {
		$.get("includes/ajaxDelFilter.php", {astring: astring, from: 'e' }, function(data){$("#memliste").trigger("reloadGrid")});		
	}
}

</script>

</head>

<body>
	<table align="left" border="0" cellspacing="0" cellpadding="0">
    <tr>
    <td align="center"><label style="color: <?php echo $tdfont; ?>; font-size: 14px;"><?php echo $heading; ?></label></td>
    </tr>
        <tr>
            <td>
                <?php include "getFilteredListe.php" ?>
            </td>
        </tr>
    <tr>
    <td> 
      <input name="bemail" type="button" value="Send Bulk Email" onclick="list2email()" />
      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
      <input name="bemail" type="button" value="Mailmerge to Bulk Email" onclick="emailmerge()" />
    </td>
    </tr>
    </table>

</body>

</html>