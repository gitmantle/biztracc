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

$memid = $_SESSION["s_memberid"];

$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = 'select CONCAT_WS(" ",firstname,lastname) as fname from members where member_id = '.$memid;
$r = mysql_query($q) or die(mysql_error().$q);
$row = mysql_fetch_array($r);
extract($row);




// populate process drop down
$query = "select * from workflow order by porder";
$result = mysql_query($query) or die(mysql_error());
$process_options = "<option value=\"0\">Select Workflow stage</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	$process_options .= '<option value="'.$process.'">'.$process.'</option>';
}

$hdate = date("Y-m-d");
$ttime = strftime("%H:%M", time());

?>

<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add Client Type to Member</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<script>

function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

function post() {

	//add validation here if required.
	var wfs = document.getElementById('wfs').value;
	var ok = "Y";
	if (wfs == 0) {
		alert("Please select a workflow stage.");
		ok = "N";
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
    <table width="450" border="0" align="center">
      <tr>
        <td colspan="2"><div align="center" class="style1"><u>Add Workflow Stage</u></div></td>
      </tr>
      <tr>
        <td colspan="2" align="center" ><label style="font-size: 18px;">to:&nbsp;<?php echo $fname; ?></label></td>
      </tr>
      <tr>
        <td class="boxlabel">Workflow Stage</td>
        <td align="left"><select name="wfs" id="wfs">
            <?php echo $process_options;?>
          </select></td>
      </tr>
      <tr>
        <td colspan="2"  align="center" ><input type="submit" value="Add workflow stage to member" name="save"></td>
      </tr>
    </table>
  </form>

<script>document.onkeypress = stopRKey;</script>
</div>
<?php

	if(isset($_POST['save'])) {
			$ct = $_REQUEST['wfs'];
			$q = "insert into workflow_xref (member_id,process,ddate) values (".$memid.",'".$ct."','".$hdate."')";
			$r = mysql_query($q) or die(mysql_error().$q);
			if ($ct == 'Passive') {
				$qm = 'update members set status = "Passive" where member_id = '.$memid;
				$rm = mysql_query($qm) or die(mysql_error().$qm);
			} else {
				$qm = 'update members set status = "In progress" where member_id = '.$memid;
				$rm = mysql_query($qm) or die(mysql_error().$qm);
			}
	
			$query = "insert into audit (ddate,ttime,user_id,uname,member_id,action) values ";
			$query .= "('".$hdate."',";
			$query .= "'".$ttime."',";
			$query .= $user_id.",";
			$query .= '"'.$uname.'",';
			$query .= $memid.",";
			$query .= "' Add workflow stage ".$ct."')";
	
			$result = mysql_query($query) or die(mysql_error().$query);

		echo '<script>';
			echo 'window.open("","editmembers").jQuery("#mworkflowlist").trigger("reloadGrid");';
			if ($ct == 'Passive') {
				echo 'window.open("","editmembers").jQuery("#mstatus").val("Passive");';
			} else {
				echo 'window.open("","editmembers").jQuery("#mstatus").val("In progress");';
			}

		echo 'this.close();';
		echo '</script>';

	}

?>

</body>

</html>

