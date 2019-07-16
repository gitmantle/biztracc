<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];
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

// populate staff drop down
$db->query("select * from users where sub_id = ".$subscriber." order by ulname");
$rows = $db->resultset();
$staff_options = "<option value=\"0\">Select User</option>";
foreach ($rows as $row) {
	extract($row);
	if ($ufname." ".$ulname == $sname) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$staff_options .= '<option value="'.$ufname.' '.$ulname.'"'.$selected.'>'.$ufname.' '.$ulname.'</option>';
}


$cltdb = $_SESSION['s_cltdb'];

$db->query("select name as cname from ".$cltdb.".campaigns where campaign_id = ".$campid);
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
if ($db->rowcount() > 0) {
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


date_default_timezone_set($_SESSION['s_timezone']);

$cdate = date("d/m/Y");
$edate = date("d/m/Y");
$edateh = date("Y-m-d");
$ttime = strftime("%H:%M", time());

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

$db->closeDB();

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Run a Campaign</title>
<link rel="stylesheet" type="text/css" href="../includes/flatpickr_date/dist/flatpickr.min.css">
<script src="../includes/flatpickr_date/dist/flatpickr.js"></script>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />

<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-1.8.4.custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/dr.tabs.js"></script>

<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script type="text/javascript" src="../includes/ui.tabs.closable.js"></script>

<script>

window.name = "campcandidates";

var candid = <?php echo $firstcandid; ?>;
var memid = <?php echo $firstmemid; ?>;
var emdate;

function candidate(m,c) {
	candid = c;
	memid = m;
	document.getElementById('tanotes').value = '';
	document.getElementById('btnsave').style.visibility = 'hidden';
	$.get("popcandidate.php", {memid: memid, candid: candid}, function(data){
		var dt = jQuery.trim(data);
		if (dt == 'B') {
			alert('This record is already in use');
			return false;
		}
		if (dt == 'C') {
			alert('This record has been completed');
			return false;
		}
		var dat = dt.split('#');														   
		var memname = dat[0];
		var age = dat[1];
		document.getElementById('mmember').value = memname;
		document.getElementById('mage').value = age;
		document.getElementById('maction').selectedIndex = 0;
	
		timeoutHnd = setTimeout(gridReload1,500); 
		timeoutHnd = setTimeout(gridReload2,500); 
		timeoutHnd = setTimeout(gridReload3,500); 
	});
	timeoutHnd = setTimeout(gridReload0,500); 

}

function delcand(memid,candid) {
	var campid = <?php echo $campid; ?>;
	if (confirm("Are you sure you want to delete this candidate")) {
	  window.open('delcand.php?uid='+candid+'&campid='+campid,'delcd','toolbar=0,scrollbars=1,height=10,width=10');
	}
}

function gridReload0() {
	var cid = <?php echo $campid; ?>;
	var pageno = jQuery('#candlist').getGridParam('page');
	jQuery("#candlist").setGridParam({url:"getCandidates.php?campid="+cid}).trigger("reloadGrid"); 
}

function gridReload1() {
	jQuery("#ccommslist").setGridParam({url:"getCandComm.php?id="+memid}).trigger("reloadGrid"); 
}

function gridReload2() {
	jQuery("#cadlist").setGridParam({url:"getCandAd.php?id="+memid}).trigger("reloadGrid"); 
}

function gridReload3() {
	jQuery("#cnotelist").setGridParam({url:"getcnotes.php?id="+memid}).trigger("reloadGrid"); 
}


function closewin() {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
		window.open('closecand.php?','cwin','toolbar=0,scrollbars=1,height=10,width=10,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addcand() {
		var x = 0, y = 0; // default values	
		var campid = <?php echo $campid; ?>;
		x = window.screenX +5;
		y = window.screenY +100;
		window.open('addcand.php?campid='+campid,'adcan','toolbar=0,scrollbars=1,height=300,width=700,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addcomm() {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
		window.open('addcomm.php?uid='+memid+'&clientid='+memid+'&from=c','adcom','toolbar=0,scrollbars=1,height=200,width=700,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editcomm(uid) {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
		window.open('editcomm.php?uid='+uid+'&from=c','edcom','toolbar=0,scrollbars=1,height=200,width=700,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addad() {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
		window.open('addaddress.php?uid='+memid+'&clientid='+memid+'&from=c','addad','toolbar=0,scrollbars=1,height=400,width=950,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editad(uid) {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
		window.open('editaddress.php?uid='+uid+'&from=c','eddad','toolbar=0,scrollbars=1,height=400,width=950,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addnote() {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
		window.open('addactivity.php?uid='+memid+'&from=c'+'&view=c','adact','toolbar=0,scrollbars=1,height=600,width=1000,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}


function viewpol(polid) {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
		window.open('campolicy.php?polid='+polid,'vpol','toolbar=0,scrollbars=1,height=550,width=950,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function displayitem(cnt) {
	document.getElementById('tanotes').value = '';
	if (cnt != '') {
		document.getElementById('tanotes').value = cnt;
	}
}

function editmem2() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	edmembers = window.open('editmember.php?&cfrom=C','edmem','toolbar=0,scrollbars=1,height=820,width=1140,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editmem(uid) {
	var ugroup = <?php echo $usergroup; ?>;
	if (ugroup > 10) {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +200;
		jQuery.ajaxSetup({async:false});
		$.get("includes/ajaxUpdtSession.php", {memberid: uid}, function(data){
		});
		jQuery.ajaxSetup({async:true});
		//var t=setTimeout("editmem2()",5000);	
		editmem2();
	}
}

function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

function post() {
	var act = document.getElementById('maction').value;
	//var ac = act.split('#');
	//var action = ac[0];
	if (act == '') {
		alert('You have not made any change to Camapign Stage to save');
		return false;
	}
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +100;
	document.getElementById('btnsave').style.visibility = 'hidden';
	
	window.open('postcand.php?memid='+memid+'&candid='+candid+'&action='+act+'&edate='+emdate,'pcand','toolbar=0,scrollbars=1,height=10,width=10,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function changestage(stage) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +100;
	
	if (stage == 'Not Available' || stage == 'Not Interested') {
		document.getElementById('btnsave').style.visibility = 'visible';
	}
	
	if (stage == 'Callback') {
		document.getElementById('dt').style.visibility = 'visible';
		document.getElementById('tm').style.visibility = 'visible';
		document.getElementById('cdate').style.visibility = 'visible';
		document.getElementById('ttime').style.visibility = 'visible';
		document.getElementById('btncallback').style.visibility = 'visible';
		
	}
	if (stage == 'Advisor Callback' || stage == 'Advisor Email' || stage == 'Appointment' || stage == 'See Notes') {
		var adv = document.getElementById('staffname').value;
		var mem = document.getElementById('mmember').value;
		document.getElementById('email').style.visibility = 'visible';
		document.getElementById('taemail').style.visibility = 'hidden';		
		
	}
	
}

function addcallback() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +100;
	var action = 'Callback';
	var dt = document.getElementById('cdate').value;
	var tm = document.getElementById('ttime').value;
	document.getElementById('dt').style.visibility = 'hidden';
	document.getElementById('tm').style.visibility = 'hidden';
	document.getElementById('cdate').style.visibility = 'hidden';
	document.getElementById('ttime').style.visibility = 'hidden';
	document.getElementById('btncallback').style.visibility = 'hidden';
	document.getElementById('maction').value = '';
	window.open('postcand.php?candid='+candid+'&action='+action+'&ddate='+dt+'&memid='+memid+'&ttime='+tm+'&edate='+emdate,'pcand','toolbar=0,scrollbars=1,height=10,width=10,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addem() {
	var adv = document.getElementById('staffname').value;
	var em = document.getElementById('taemail').value;
	if (adv == 0) {
		alert('Please select an advisor');
		return false;
	}
	if (em == "") {
		alert('Please enter message');
		return false;
	}
	var action = document.getElementById('maction').value;
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +100;
	var ad = adv.split('#');
	var advisor = ad[1];
	var caller = <?php echo $user_id; ?>;
	document.getElementById('email').style.visibility = 'hidden';
	document.getElementById('taemail').style.visibility = 'hidden';
	document.getElementById('baddem').style.visibility = 'hidden';
	document.getElementById('taemail').value = '';
	window.open('postcand.php?candid='+candid+'&action='+action+'&email='+em+'&advisor='+advisor+'&memid='+memid+"&caller="+caller+'&edate='+emdate,'pcand','toolbar=0,scrollbars=1,height=10,width=10,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

function changeadv() {
	var adv = document.getElementById('staffname').value;
	var action = document.getElementById('maction').value;
	var ad = adv.split('#');
	var advisor = ad[0];
	var mem = document.getElementById('mmember').value;
	var dt = document.getElementById('cdate').value;
	emdate = dt;
	if (action == 'Advisor Callback') {
		document.getElementById('taemail').value = advisor+' to call back '+mem+' on '+dt+' about ';
	}
	if (action == 'Advisor Email') {
		document.getElementById('taemail').value = advisor+' to email '+mem+' on '+dt+' about ';
	}
	if (action == 'Appointment') {
		document.getElementById('taemail').value = advisor+' has appointment with '+mem+' on '+dt+' about ';
	}
	if (action == 'See Notes') {
		document.getElementById('taemail').value = advisor+' please see notes on '+mem+' added on '+dt+' about ';
	}
	
	document.getElementById('taemail').style.visibility = 'visible';
	document.getElementById('baddem').style.visibility = 'visible';
}

function changecdate(dt) {
	emdate = dt;
	var action = document.getElementById('maction').value;
	var mem = document.getElementById('mmember').value;
	var adv = document.getElementById('staffname').value;
	var ad = adv.split('#');
	var advisor = ad[0];
	if (action == 'Advisor Callback') {
		document.getElementById('taemail').value = advisor+' to call back '+mem+' on '+dt+' about ';
	}
	if (action == 'Advisor Email') {
		document.getElementById('taemail').value = advisor+' to email '+mem+' on '+dt+' about ';
	}
	if (action == 'Appointment') {
		document.getElementById('taemail').value = advisor+' has appointment with '+mem+' on '+dt+' about ';
	}
	if (action == 'See Notes') {
		document.getElementById('taemail').value = advisor+' please see notes on '+mem+' added on '+dt+' about ';
	}
	
	document.getElementById('taemail').style.visibility = 'visible';
	document.getElementById('baddem').style.visibility = 'visible';
}

function exit() {
	var x = confirm('You are about to exit without saving this record. Is that what you want to do?');
	if (x == true) {
		this.close();
	} else {
		return false;
	}
}

function closeem() {
	document.getElementById('email').style.visibility = 'hidden';
	document.getElementById('taemail').value = '';
	document.getElementById('taemail').style.visibility = 'hidden';
	document.getElementById('baddem').style.visibility = 'hidden';
}

function viewdocs() {
	var uid = '<?php echo $campid; ?>';
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('vcampdocs.php?uid='+uid,'cdc','toolbar=0,scrollbars=1,height=400,width=900,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}



</script>


</head>

<body onbeforeunload="closewin();">
<form name="campcand" id="campcand" method="post" action="">

<div id="bwin">
<div id="cand" style="position:absolute;visibility:visible;top:1px;left:1px;height:220px;width:1010px;background-color:#E6E6E6;border-width:thin thin thin thin; border-color:blue; border-style:solid;">
	<table>
    <tr>
	     <td><?php include "getCandidates.php" ?></td>
     </tr>
     </table>
</div>
<div id="mem" style="position:absolute;visibility:visible;top:221px;left:1px;height:30px;width:1010px;background-color:#E6E6E6;border-width:thin thin thin thin; border-color:blue; border-style:solid;">
    <table width="980" border="0">
      <tr>
        <td class="boxlabel">Member</td>
        <td align="left"><input name="mmember" type="text" id="mmember" size="50" maxlength="50" /></td>
        <td class="boxlabel">Age</td>
        <td align="left"><input name="mage" type="text" id="mage" size="3" maxlength="5" /></td>
      </tr>
    </table>
</div>
<div id="poli" style="position:absolute;visibility:visible;top:251px;left:1px;height:60px;width:1010px;background-color:#E6E6E6;border-width:thin thin thin thin; border-color:blue; border-style:solid;">
  <table width="980" border="0">
      <tr>
        <td colspan="2" class="boxlabel">Campaign Stage</td>
        <td align="left"><select name="maction" id="maction" onchange="changestage(this.value);">
          <option value="">Select Stage</option>
          <option value="Not Available">Not Available</option>
          <option value="Callback">Callback</option>
          <option value="Not Interested">Not Interested</option>
          <option value="Appointment">Appointment</option>
          <option value="Advisor Callback">Advisor Callback</option>
          <option value="Advisor Email">Advisor Email</option>
          <option value="See Notes">See Notes</option>
        </select></td>
        <td width="218" align="left" id="dt">Date 
          <input name="cdate" type="text" id="cdate"  size="15" maxlength="15" value="<?php echo $cdate; ?>" /></td>
        <td width="218" align="left" id="tm">Time 
          <input name="ttime" type="text" id="ttime" size="6" maxlength="5" value="<?php echo $ttime; ?>"/>
        </td>
       	<td>
          <input type="button" name="btncallback" id="btncallback" value="Save Callback" onclick="addcallback();" />
        </td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td width="93" align="center" bgcolor="#FF0000"><input type="button" name="btnsave" id="btnsave" value="Save Stage" onclick="post();" /></td>
        <td width="56" align="center">&nbsp;</td>
        <td width="226" align="left"><input type="button" name="btnexit" id="btnexit" value="Exit Campaign" onclick="exit();" /></td>
        <td colspan="2">&nbsp;</td>
        <td><input type="button" name="btnview" id="btnview" value="View Campaign Documents" onclick="viewdocs()"/></td>
        <td>&nbsp;</td>
      </tr>
  </table>
</div>

<div id="ph" style="position:absolute;visibility:visible;top:311px;left:1px;height:140px;width:400px;background-color:#E6E6E6;border-width:thin thin thin thin; border-color:blue; border-style:solid;">
	<table>
    <tr>
	     <td><?php include "getCandComm.php" ?></td>
     </tr>
     </table>
</div>
<div id="ad" style="position:absolute;visibility:visible;top:311px;left:401px;height:140px;width:610px;background-color:#E6E6E6;border-width:thin thin thin thin; border-color:blue; border-style:solid;">
	<table>
    <tr>
	     <td><?php include "getCandAd.php" ?></td>
     </tr>
     </table>
</div>

<div id="notg" style="position:absolute;visibility:visible;top:451px;left:1px;height:147px;width:505px;background-color:#E6E6E6;border-width:thin thin thin thin; border-color:blue; border-style:solid;">
    <table>
    <tr>
	     <td><?php include "getcnotes.php" ?></td>
     </tr>
    </table>	
</div>

<div id="notb" style="position:absolute;visibility:visible;top:451px;left:506px;height:147px;width:505px;background-color:#E6E6E6;border-width:thin thin thin thin; border-color:blue; border-style:solid;">
 <textarea name="tanotes" id="tanotes" cols="58" rows="7" readonly></textarea>
</div>


<div id="email" style="position:absolute;visibility:hidden;top:260px;left:350px;height:200px;width:435px;background-color:#E6E6E6;border-width:thin thin thin thin; border-color:#0F0; border-style:solid;">
   Email
    <table width="440" cellpadding="0" cellspacing="0">
    <tr>
         <td align="left"><select name="staffname" id="staffname"><?php echo $staff_options;?></select></td>
        <td align="left">Act on &nbsp;<input type="Text" id="edate" name="edate" maxlength="25" size="25" value="<?php echo $edateh; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y"></td>
        <td><input name="bgo" type="button" id="bgo" value="Go" onclick="changeadv();"></td>
   </tr>
    <tr>
	    <td colspan="3" align="left"><textarea name="taemail" id="taemail" cols="50" rows="5" ></textarea></td>
    </tr>
    </table> 
    <table cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td><input name="baddem" type="button" id="baddem" value="Save Email" onclick="addem();"></td>
            <td><input name="bcloseem" type="button" id="bcloseem" value="Close" onclick="closeem();"></td>
        </tr>
    </table>	
</div>

	<script>//document.onkeypress = stopRKey();</script>
    <script>
		document.getElementById('dt').style.visibility = 'hidden';
		document.getElementById('tm').style.visibility = 'hidden';
		document.getElementById('cdate').style.visibility = 'hidden';
		document.getElementById('ttime').style.visibility = 'hidden';
		document.getElementById('btncallback').style.visibility = 'hidden';
		document.getElementById('btnsave').style.visibility = 'hidden';
		document.getElementById('baddem').style.visibility = 'hidden';

		var mid = <?php echo $firstmemid; ?>;
		var cid = <?php echo $firstcandid; ?>;
		$.get("popfcandidate.php", {memid: mid, candid: cid}, function(data){
			var dat = data.split('#');														   
			var memname = dat[0];
			var age = dat[1];
			document.getElementById('mmember').value = memname;
			document.getElementById('mage').value = age;
		});
		
		timeoutHnd = setTimeout(gridReload1,500) 
		timeoutHnd = setTimeout(gridReload2,500) 
		timeoutHnd = setTimeout(gridReload3,500) 

	</script>
 <script>
 	document.getElementById("edate").flatpickr({
		onChange: function(dateObj, dateStr, instance) {
		}
	});
 </script>
</div>

</form>

</body>
</html>