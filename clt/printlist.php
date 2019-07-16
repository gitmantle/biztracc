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

$mv = $_REQUEST['mv'];
$_SESSION['s_mv'] = $mv;

require_once("includes/printfilter.php");

$cltdb = $_SESSION['s_cltdb'];

$fltfile = "ztmp".$user_id."_filterlist";

$db->query("select campaign_id,name from ".$cltdb.".campaigns");
$rows = $db->resultset();
$camp_options = '<option value=" ">Select Campaign</option>';
foreach ($rows as $row) {
	extract($row);
	$camp_options .= '<option value="'.$campaign_id.'" >'.$name.'</option>';
}

$ddate = date("d/m/Y");

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

$db->closeDB();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Filtered List</title>
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript">

window.name = "filteredlist";

var filterfile = "<?php echo $fltfile; ?>";

function viewmem(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editmember.php?uid='+uid,'vmem','toolbar=0,scrollbars=1,height=780,width=1140,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

  function list2pdf() {
  var heading = '<?php echo $heading; ?>';
  var x = 0, y = 0; // default values	
  x = window.screenX +5;
  y = window.screenY +265;	
	  window.open('list2pdf.php?filterfile='+filterfile+'&heading='+heading,'listpdf'+filterfile,'toolbar=0,scrollbars=1,height=500,width=1020,resizable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
  }

  function xl7() {
  var heading = '<?php echo $heading; ?>';
  var x = 0, y = 0; // default values	
  x = window.screenX +5;
  y = window.screenY +265;	
	  window.open('xl_maillist.php?filterfile='+filterfile+'&heading='+heading,'listxl'+filterfile+'&gen='+7,'toolbar=0,scrollbars=1,height=500,width=1020,resizable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
  }

function trash() {
	var aleads = jQuery("#memlistf").getGridParam('selarrrow');	
	var num = aleads.length;	
	var astring = aleads.toString();
	if (num > 0) {
		$.get("includes/ajaxDelFilter.php", {astring: astring, from: 'p'}, function(data){$("#memlistf").trigger("reloadGrid")});		
	}
}

function xl3() {
  var heading = '<?php echo $heading; ?>';
  var x = 0, y = 0; // default values	
  x = window.screenX +5;
  y = window.screenY +265;	
	  window.open('xl_maillist.php?filterfile='+filterfile+'&heading='+heading,'listxl'+filterfile+'&gen='+3,'toolbar=0,scrollbars=1,height=500,width=1020,resizable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addcandidates() {
  var camp_id = document.getElementById('campid').value;
  if (camp_id == 0) {
	alert('Please choose a campaign first');
	return false;
  }

  var x = 0, y = 0; // default values	
  x = window.screenX +5;
  y = window.screenY +265;	
	window.open('add2camp.php?filterfile='+filterfile+'&camp_id='+camp_id,'a2c','toolbar=0,scrollbars=1,height=10,width=10,resizable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addconsol() {
	 if (confirm("Are you sure you want to add these members to the consolidated list?")) {
		$.get("includes/ajaxaddconsol.php");																	
	  }
}

function viewconsol() {
  var x = 0, y = 0; // default values	
  x = window.screenX +5;
  y = window.screenY +165;	
	consolidated_list = window.open("viewconsolidated.php","conlist","toolbar=0,scrollbars=1,height=540,width=1010,resizable=0,left="+x+",screenX="+x+",top="+y+",screenY="+y);
}


function updtsel(selr) {
	var camp_id = document.getElementById('campid').value;
	if (camp_id == 0) {
	  alert('Please choose a campaign first');
	  return false;
	} else {
		$.get("includes/ajaxupdtsel.php", {selrows: selr, camp_id: camp_id}, function(data){
			$("#memlistf").trigger("reloadGrid")
		});
	}
}



</script>
</head>
<body>
<table align="left" border="0" cellspacing="0" cellpadding="0" bgcolor="<?php echo $bgcolor; ?>">
  <tr>
    <td colspan="6" align="left"><label style="color: <?php echo $tdfont; ?>; font-size: 14px;"><?php echo $heading; ?></label></td>
    <td align="left">&nbsp;</td>
  </tr>
  <tr>
    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Add as Candidates to &nbsp;</label></td>
    <td><select name="campid" id="campid">
        <?php echo $camp_options;?>
      </select></td>
    <td><input type="button" name="btncamp" id="btncamp" value="Add"/></td>
    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Send to ... </label></td>
    <td><input name="bpdf" type="button" value="PDF" onclick="list2pdf()" /></td>
    <td><input type="button" name="bconsolidate2" id="bconsolidate2" value="Add to Consolidated List" onclick="addconsol()"/>
    <input type="button" name="bconsolidate" id="bconsolidate" value="View Consolidated List" onclick="viewconsol();"/></td>
  </tr>
  <tr>
    <td colspan="7"><?php include "getFilteredList.php" ?></td>
  </tr>
</table>
</body>
</html>