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

$cmuid = $_REQUEST['uid'];

$db->query("select * from ".$cltdb.".comms where comms_id = ".$cmuid);
$row = $db->single();
extract($row);
$commtype = $comms_type_id;
$memid = $member_id;

// populate comms type drop down
$db->query("select * from ".$cltdb.".comms_type");
$rows = $db->resultset();
$commtype_options = "<option value=\"\">Select Communication Type</option>";
foreach ($rows as $row) {
	extract($row);
	if ($comms_type_id == $commtype) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$commtype_options .= "<option value=\"".$comms_type_id."\" ".$selected.">".$comm_type."</option>";
}

$db->closeDB();

?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Comunications</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script src="../includes/jquery.js" type="text/javascript"></script>

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
	} else {
		document.getElementById('billaddress').style.visibility = "hidden";
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
<form name="form1" method="post" >
  <table width="600" border="0" align="center">
    <tr>
      <td colspan="3"><div align="center" class="style1"><u>Edit Communication Item </u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">Type</td>
      <td colspan="2"><select name="comm_type" id="comm_type" onchange="c_type();">
	  	<?php echo $commtype_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabel"><div align="right">Country Code</div></td>
      <td colspan="2"><input name="country_code" type="text" id="country_code"  size="15" maxlength="15" value="<?php echo $country_code; ?>"></td>
     </tr>
    <tr>
      <td class="boxlabel"><div align="right">Area Code</div></td>
      <td colspan="2"><input name="area_code" type="text" id="area_code"  size="15" maxlength="10" value="<?php echo $area_code; ?>"></td>
     </tr>
    <tr>
      <td class="boxlabel"><div align="right">Number/Detail</div></td>
      <td colspan="2"><input name="comm" type="text" id="commb"  size="50" maxlength="75" value="<?php echo $comm; ?>"></td>
    </tr>
    <tr id="billaddress">
      <td class="boxlabel">Is this the Billing Address</td>
      <td colspan="2"><select name="billadd" id="billadd">
        <?php
		if ($billing == 'Y') {
			echo '<option value="N">No</option>';
			echo '<option value="Y" selected>Yes</option>';
		} else {
			echo '<option value="N" selected>No</option>';
			echo '<option value="Y">Yes</option>';
		}
		?>    
       </select></td>
    </tr>
	 
	<tr>
	  <td class="boxlabel">Preferred</td>
	  <td align="left"><select name="pref" id="pref">
	    <?php
		if ($preferred == 'Y') {
			echo '<option value="N">No</option>';
			echo '<option value="Y" selected>Yes</option>';
		} else {
			echo '<option value="N" selected>No</option>';
			echo '<option value="Y">Yes</option>';
		}
		?>
	    </select></td>
	  <td align="right"><input type="submit" value="Save" name="save" ></td>
	  </tr>
  </table>
</form>
</div>
	<script>document.onkeypress = stopRKey;</script> 

<?php

	if(isset($_POST['save'])) {

		if ($_REQUEST['comm'] == '') {
			echo '<script>';
			echo 'alert("Please enter a communication item.")';
			echo '</script>';	
		} else {			

				include_once("../includes/cltadmin.php");
				$oCm = new cltadmin;	
				
				$oCm->uid = $cmuid;
				$oCm->client_id = $cluid;
				$oCm->comms_type_id = $_REQUEST['comm_type'];
				$oCm->country_code = $_REQUEST['country_code'];
				$oCm->area_code = $_REQUEST['area_code'];
				$oCm->comm = $_REQUEST['comm'];
				$oCm->preferred = $_REQUEST['pref'];
				$oCm->ebilladd = $_REQUEST['billadd'];
	
				$oCm->EditComm();
				
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
				$dba->bind(':comms_id', $cmuid);
				$dba->bind(':action', 'Edit Communication');
				
				$dba->execute();
				$dba->closeDB();
	
				?>
				<script>
			    window.open("","editmembers").jQuery("#mcommslist").trigger("reloadGrid");
				this.close();
				</script>
				<?php
		
			
		}
	}

?>

</body>
</html>
