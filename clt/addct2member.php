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

$subscriber = $subid;
$cluid = $_SESSION['s_memberid'];

$cltdb = $_SESSION['s_cltdb'];

$db->query('select CONCAT_WS(" ",firstname,lastname) as fname from '.$cltdb.'.members where member_id = '.$cluid);
$row = $db->single();
extract($row);

// populate client types drop down
$db->query("select * from ".$cltdb.".client_types");
$rows = $db->resultset();
$ctype_options = '<option value=" ">Select Client Type</option>';
foreach ($rows as $row) {
	extract($row);
	$ctype_options .= '<option value="'.$client_type.'" >'.$client_type.'</option>';
}

$db->closeDB();

?>
<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add Client Type to Member</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script>

function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

function post() {
	//add validation here if required.
	var ct = document.getElementById('ctype').value;
	var ok = "Y";
	if (ct == "") {
		alert("Please enter client type.");
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
<div id="swin">

<form name="form1" id="form1" method="post" >
  <input type="hidden" name="savebutton" id="savebutton" value="N">
  <table width="300" border="0" align="center">
    <tr>
      <td colspan="2"><div align="center" class="style1"><u>Add Client Type</u></div></td>
    </tr>
    <tr>
      <td colspan="2" align="center" ><label style="font-size: 18px;">to:&nbsp;<?php echo $fname; ?></label></td>
    </tr>
    <tr>
      <td class="boxlabel">Client type</td>
      <td align="left"><select name="ctype" id="ctype">
          <?php echo $ctype_options;?>
        </select></td>
    </tr>
    <tr>
      <td colspan="2"  align="center" ><input type="submit" value="Add client type to member" name="save"></td>
    </tr>
  </table>
</form>

</div>

<script>document.onkeypress = stopRKey;</script>
<?php

	if(isset($_POST['save'])) {
		$ok = 'Y';
		if ($_REQUEST['ctype'] == ' ') {
			echo '<script>';
			echo 'alert("Please select a client type.")';
			echo '</script>';	
			$ok = 'N';
		}

		if ($ok == 'Y') {	
		
			include_once("../includes/DBClass.php");
			$db = new DBClass();

			$ct = $_REQUEST['ctype'];
			$db->query("insert into ".$cltdb.".clienttype_xref (member_id,client_type,sub_id) values (:member_id,:client_type,:sub_id)");
			$db->bind(':member_id', $cluid);
			$db->bind(':client_type', $ct);
			$db->bind(':sub_id', $subscriber);
			
			$db->execute();
			$db->closeDB();

			echo '<script>';
			echo 'window.open("","editmembers").jQuery("#mctlist").trigger("reloadGrid");';
			echo 'this.close();';
			echo '</script>';
		}
	}

?>
</body>
</html>
