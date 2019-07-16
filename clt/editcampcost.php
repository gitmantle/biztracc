<?php
session_start();
error_reporting(E_ERROR | E_WARNING | E_PARSE);

$usersession = $_SESSION['usersession'];

$costid = $_REQUEST['uid'];
$campid = $_REQUEST['campid'];
$cltdb = $_SESSION['s_cltdb'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];


$db->query("select * from ".$cltdb.".campaigns where campaign_id = ".$campid);
$row = $db->single();
extract($row);

$db->query("select * from ".$cltdb.".campaign_costs where costs_id = ".$costid);
$row = $db->single();
extract($row);

$db->closeDB();

?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Campaign Cost</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<script>
function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

function post() {

	//add validation here if required.
	var nm = document.getElementById('item').value;
	var ok = "Y";
	if (nm == "") {
		alert("Please enter an item.");
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
.style1 {font-size: large}
-->
</style>
</head>


<body>
<div id="mwin">
<form name="form1" id="form1" method="post" >
 <input type="hidden" name="savebutton" id="savebutton" value="N">
  <table width="570" border="0" align="center">
    <tr>
      <td colspan="2"><div align="center" class="style1"><u>Edit Campaign Cost for <?php echo $name; ?></u></div></td>
    </tr>
    <tr>
      <td width="106" class="boxlabel"><div align="right">Item</div></td>
      <td><input name="item" type="text" id="item"  size="45" maxlength="45" value="<?php echo $item; ?>"></td>
      </tr>
    <tr>
      <td width="106" class="boxlabel"><div align="right">Cost</div></td>
      <td><input name="cost" type="text" id="cost" value="<?php echo $cost; ?>" ></td>
      </tr>
	<tr>
      <td>&nbsp;</td>
      <td align="right"><input type="button" value="Save" name="save" onclick="post();"></td>
      </tr>
  </table>
</form>
</div>
	<script>document.onkeypress = stopRKey;</script> 

<?php

	if($_REQUEST['savebutton'] == "Y") {

		include_once("../includes/cltadmin.php");
		$oAct = new cltadmin;	
		
		$oAct->campitem = $_REQUEST['item'];
		$oAct->campcost = $_REQUEST['cost'];
		$oAct->sub_id = $sub_id;
		$oAct->uid = $costid;

		$actid = $oAct->EditCampCost();


	  ?>
	  <script>
	  window.open("","updtcampcosts").jQuery("#costlist").trigger("reloadGrid");
	  this.close();
	  </script>
	  <?php
		
			
	}

?>


</body>
</html>
