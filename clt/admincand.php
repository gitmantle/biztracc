<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];
$admindb = $_SESSION['s_admindb'];
date_default_timezone_set($_SESSION['s_timezone']);

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

$campid = $_REQUEST['uid'];
$_SESSION['s_campid'] = $campid;

// populate  staff drop down
$db->query("select * from users where sub_id = ".$subid." order by ulname");
$rows = $db->resultsetNum();
$staff_options = '<option value="0">Select User</option>';
foreach ($rows as $row) {
	extract($row);
	$staff_options .= '<option value="'.$row[2].' '.$row[3].'">'.$row[2].' '.$row[3].'</option>';
}

$cltdb = $_SESSION['s_cltdb'];

$db->query('select name as cname from '.$cltdb.'.campaigns where campaign_id = '.$campid);
$row = $db->single();
extract($row);
$_SESSION['s_campname'] = $cname;
$campaign = $cname;
$mid = 0;
$firstcandid = 0;

$db->query("select * from ".$cltdb.".candidates where candidates.campaign_id = ".$campid." order by lastname");
$rows = $db->resultset();
$firstmemid = 0;
$firstcandid = 0;
if (count($rows) > 0) {
  foreach ($rows as $row) {
	extract($row);
	$mid = $member_id;
	if ($firstmemid == 0) {
		$firstmemid = $mid;
	}
	$cid = $candidate_id;
	if ($firstcandid == 0) {
		$firstcandid = $cid;
	}
  }
}
$_SESSION['s_mid'] = $mid;

// populate campaign options
$db->query("select campaign_id,name from ".$cltdb.".campaigns");
$rows = $db->resultset();
$camp_options = '<option value=" ">Select Campaign</option>';
foreach ($rows as $row) {
	extract($row);
	$camp_options .= '<option value="'.$campaign_id.'" >'.$name.'</option>';
}

date_default_timezone_set($_SESSION['s_timezone']);

$cdate = date("d/m/Y");
$edate = date("d/m/Y");
$ttime = strftime("%H:%M", time());
$filterfile = "ztmp".$user_id."_adcamp";

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

$db->closeDB();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Administer Candidates</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-1.8.4.custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script>

window.name = "admincands";

var filterfile = "<?php echo $filterfile; ?>";

function addcandidates() {
  var camp_id = document.getElementById('campid').value;
  var x = 0, y = 0; // default values	
  x = window.screenX +5;
  y = window.screenY +265;	
	window.open('add2camp.php?filterfile='+filterfile+'&camp_id='+camp_id,'a2c','toolbar=0,scrollbars=1,height=10,width=10,resizable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editmem(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	jQuery.ajaxSetup({async:false});
	$.get("../ajax/ajaxUpdtMember.php", {memberid: uid}, function(data){
	});
	jQuery.ajaxSetup({async:true});
	editmem2();
}

function editmem2() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editmember.php','edmem','toolbar=0,scrollbars=1,height=470,width=970,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function delcand(memid,candid) {
	  if (confirm("Are you sure you want to delete this candidate from this campaign?")) {
		$.get("includes/ajaxdelcand.php", {tid: candid}, function(data){$("#adcandlist").trigger("reloadGrid")});
	  }
}

function delmem(memid,candid) {
	 if (confirm("Are you ABSOLUTELY sure you want to delete this member FROM THE SYSTEM?")) {
			$.get("includes/ajaxdelmember.php", {tid: memid}, function(data){
				$.get("includes/ajaxdelcand.php", {tid: candid}, function(data){$("#adcandlist").trigger("reloadGrid")});
			});
	  }
}

var advisor; 
var stage; 
var status; 


function getParams() {
	advisor = jQuery("#ladvisor").val(); 
	stage = jQuery("#lstage").val(); 
	status = jQuery("#lstatus").val(); 


}

function filter() {
	getParams();
	jQuery("#adcandlist").setGridParam({url:"getAdminCandidates.php?advisor="+advisor+"&stage="+stage+"&status="+status,page:1}).trigger("reloadGrid"); 
}

function freset() {
	document.getElementById('ladvisor').value = "";
	document.getElementById('lstage').value = "";
	document.getElementById('lstatus').value = "";
	jQuery("#adcandlist").setGridParam({url:"getAdminCandidates.php",page:1}).trigger("reloadGrid"); 
}


</script>
</head>
<body>
<table width="1010" border="0">
	<tr>
    	<td class="boxlabel"><select name="ladvisor" id="ladvisor">
        <?php echo $staff_options; ?>
  	  </select></td>
        <td class="boxlabel"><select name="lstage" id="lstage" >
          <option value="">Select Stage</option>
          <option value="Not Available">Not Available</option>
          <option value="Callback">Callback</option>
          <option value="Not Interested">Not Interested</option>
          <option value="Appointment">Appointment</option>
          <option value="Advisor Callback">Staff to Callback</option>
          <option value="Advisor Email">Advisor Email</option>
          <option value="See Notes">See Notes</option>
        </select></td>
    	<td class="boxlabel"><select name="lstatus" id="lstatus">
    	  <option value="">Select Status</option>
    	  <option value="Available">Available</option>
    	  <option value="Complete">Complete</option>
        <?php echo $status_options; ?>
  	  </select></td>
     	<td class="boxlabel"><input type="button" name="breset" id="breset" value="Reset" onclick="freset()">
   	    <input type="button" name="bfilter" id="bfilter" value="Filter" onclick="filter()"></td>
  <tr>
  <tr>
    <td colspan="4"><?php include "getAdminCandidates.php" ?></td>
  </tr>
  <tr>
    <td colspan="2" class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Add as Candidates to &nbsp;</label></td>
    <td><select name="campid" id="campid">
        <?php echo $camp_options;?>
      </select></td>
    <td align="left"><input type="button" name="btncamp" id="btncamp" value="Add" onclick="addcandidates();" /></td>
  </tr>
</table>
</body>
</html>
