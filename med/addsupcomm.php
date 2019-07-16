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

$cluid = $_SESSION["s_memberid"];

$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());

// populate comm types drop down
$query = "select * from comms_type";
$result = mysql_query($query) or die(mysql_error());
$commtype_options = "<option value=\"0\">Select Communication Type</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	$commtype_options .= "<option value=\"".$comms_type_id."\">".$comm_type."</option>";
}


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
.style1 {font-size: large}
-->
</style>
</head>


<body>
<div id="mwin">
<form name="form1" id="form1" method="post" >
 <input type="hidden" name="savebutton" id="savebutton" value="N">
  <table width="600" border="0" align="center">
    <tr>
      <td colspan="3"><div align="center" class="style1"><u>Add Communication Item </u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">Type</td>
      <td colspan="2"><select name="comm_type" id="comm_type">
	  	<?php echo $commtype_options; ?>
      </select> 
        <span class="compulsory">*</span></td>
    </tr>
    <tr>
      <td class="boxlabel">Country Code</td>
      <td colspan="2"><input name="country_code" type="text" id="country_code"  size="15" maxlength="15" ></td>
     </tr>
    <tr>
      <td class="boxlabel">Area Code</td>
      <td colspan="2"><input name="area_code" type="text" id="area_code"  size="15" maxlength="10"></td>
     </tr>
    <tr>
      <td class="boxlabel">Number/Details</td>
      <td colspan="2"><input name="comm" type="text" id="comm"  size="60" maxlength="75"> 
          <span class="compulsory">*</span>	</td>
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
	<script>document.onkeypress = stopRKey;</script> 
<?php

	if($_REQUEST['savebutton'] == "Y") {
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

		$commid = $oCm->AddComm();

	  $hdate = date('Y-m-d');
	  $ttime = strftime("%H:%M", mktime());

	  $query = "insert into audit (ddate,ttime,user_id,uname,member_id,comms_id,action) values ";
	  $query .= "('".$hdate."',";
	  $query .= "'".$ttime."',";
	  $query .= $user_id.",";
      $query .= '"'.$uname.'",';
	  $query .= $cluid.",";

	  $query .= $commid.",";
	  $query .= "'Add Communication')";

	  $result = mysql_query($query) or die(mysql_error().$query);

		?>

		<script>
		window.open("","editsuppliers").jQuery("#mcommslist").trigger("reloadGrid");
		this.close();
		</script>

		<?php

	}

?>

</body>
</html>

