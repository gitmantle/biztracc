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
$cluid = $_SESSION["s_memberid"];

$cltdb = $_SESSION['s_cltdb'];

// populate comm types drop down
$db->query("select * from ".$cltdb.".comms_type");
$rows = $db->resultset();
$commtype_options = "<option value=\"0\">Select Communication Type</option>";
foreach ($rows as $row) {
	extract($row);
	$commtype_options .= "<option value=\"".$comms_type_id."\">".$comm_type."</option>";
}

// check to see if client already has a billing address
$db->query("select billing,email from ".$cltdb.".client_company_xref where client_id = ".$cluid);
$row = $db->single();
extract($row);
if ($billing > 0 || $email > 0) {
	$billadd = 'Y';
} else {
	$billadd = 'N';
}

$db->closeDB();

?>
<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add Communication</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script>
function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

function c_type() {
	var ct = document.getElementById('comm_type').value;
	if (ct == 4) {
		document.getElementById('billaddress').style.visibility = "visible";
		document.getElementById('ccode').style.visibility = "hidden";
		document.getElementById('acode').style.visibility = "hidden";
	} else {
		document.getElementById('billaddress').style.visibility = "hidden";
		document.getElementById('ccode').style.visibility = "visible";
		document.getElementById('acode').style.visibility = "visible";
	}
}

function post() {
	//add validation here if required.
	var comm = document.getElementById('comm').value;
	var commtype = document.getElementById('comm_type').value;
	var ok = "Y";
	if (comm == "") {
		alert("Please enter details etc.");
		ok = "N";
		return false;
	}
	if (commtype == "0") {
		alert("Please select a communication type.");
		ok = "N"
		return false;
	}

	if (ok == "Y") {
		document.getElementById('savebutton').value = "Y";
		document.getElementById('form1').submit();
	}
}

</script>
<style type="text/css">
<!--
.style1 {
	font-size: large
}
-->
</style>
</head>
<body>
<div id="mwin">
<form name="form1" id="form1" method="post" >
  <input type="hidden" name="savebutton" id="savebutton" value="N">
  <table id="acom" width="600" border="0" align="center">
    <tr>
      <td colspan="3"><div align="center" class="style1"><u>Add Communication Item </u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">Type</td>
      <td colspan="2"><select name="comm_type" id="comm_type" onchange="c_type();">
          <?php echo $commtype_options; ?>
        </select>
        <span class="compulsory">*</span></td>
    </tr>
    <tr id="ccode">
      <td class="boxlabel">Country Code</td>
      <td colspan="2"><input name="country_code" type="text" id="country_code"  size="15" maxlength="15" ></td>
    </tr>
    <tr id="acode">
      <td class="boxlabel">Area Code</td>
      <td colspan="2"><input name="area_code" type="text" id="area_code"  size="15" maxlength="10"></td>
    </tr>
    <tr>
      <td class="boxlabel">Number/Details</td>
      <td colspan="2"><input name="comm" type="text" id="comm"  size="50" maxlength="75">
        <span class="compulsory">*</span></td>
    </tr>
    <tr id="billaddress">
      <td class="boxlabel">Is this the Billing Address</td>
      <td colspan="2"><select name="billadd" id="billadd">
          <option value="N">No</option>
          <option value="Y">Yes</option>
        </select></td>
    </tr>
    <tr>
      <td class="boxlabel">Preferred</td>
      <td align="left"><select name="pref" id="pref">
          <option value="N">No</option>
          <option value="Y">Yes</option>
        </select></td>
      <td align="right"><input type="button" value="Save" name="save" onclick="post()"></td>
    </tr>
  </table>
</form>
</div>

  <script>
	document.onkeypress = stopRKey;
	document.getElementById('ccode').style.visibility = "hidden";
	document.getElementById('acode').style.visibility = "hidden";
  </script>

<?php


	if(isset($_REQUEST['savebutton']) && $_REQUEST['savebutton'] == "Y") {
		
		include_once("../includes/cltadmin.php");
		$oCm = new cltadmin;	

		$oCm->comms_type_id = $_REQUEST['comm_type'];
		$oCm->client_id = $cluid;
		$oCm->uid = $cluid;
		$oCm->country_code = $_REQUEST['country_code'];
		$oCm->area_code = $_REQUEST['area_code'];
		$oCm->comm = $_REQUEST['comm'];
		$oCm->preferred = $_REQUEST['pref'];
		$oCm->staff_id = $user_id;
		$oCm->ebilladd = $_REQUEST['billadd'];

		$commid = $oCm->AddComm();

	  $hdate = date('Y-m-d');
	  $ttime = strftime("%H:%M", time());

		include_once("../includes/DBClass.php");
		$dba = new DBClass();
		
		$dba->query("insert into ".$cltdb.".audit (ddate,ttime,user_id,uname,member_id,comms_id,action) values (:ddate,:ttime,:user_id,:uname,:member_id,:comms_id,:action)");
		$dba->bind(':ddate', $hdate);
		$dba->bind(':ttime', $ttime);
		$dba->bind(':user_id', $user_id);
		$dba->bind(':uname', $sname);
		$dba->bind(':member_id', $cluid);
		$dba->bind(':comms_id', $commid);
		$dba->bind(':action', 'Add Communication');
		
		$dba->execute();
		$dba->closeDB();

?>
<script>
		window.open("","editmembers").jQuery("#mcommslist").trigger("reloadGrid");
		this.close();
</script>
<?php

	}

?>
</body>
</html>
