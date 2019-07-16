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
$cmuid = $_REQUEST['uid'];

$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());

$query = "select * from comms where comms_id = ".$cmuid;
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$commtype = $comms_type_id;
$memid = $member_id;

// populate comms type drop down
$query = "select * from comms_type";
$result = mysql_query($query) or die(mysql_error());
$commtype_options = "<option value=\"\">Select Communication Type</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($comms_type_id == $commtype) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$commtype_options .= "<option value=\"".$comms_type_id."\" ".$selected.">".$comm_type."</option>";
}

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
      <td class="boxlabel"><div align="right">Type</td>
      <td colspan="2"><select name="comm_type" id="comm_type">
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
      <td colspan="2"><input name="comm" type="text" id="commb"  size="75" maxlength="75" value="<?php echo $comm; ?>"></td>
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
				$oCm->comms_type_id = $_REQUEST['comm_type'];
				$oCm->country_code = $_REQUEST['country_code'];
				$oCm->area_code = $_REQUEST['area_code'];
				$oCm->comm = $_REQUEST['comm'];
				$oCm->preferred = $_REQUEST['pref'];
	
				$oCm->EditComm();
				
			  $hdate = date('Y-m-d');
			  $ttime = strftime("%H:%M", mktime());
	
			  $query = "insert into audit (ddate,ttime,user_id,uname,member_id,comms_id,action) values ";
			  $query .= "('".$hdate."',";
			  $query .= "'".$ttime."',";
			  $query .= $user_id.",";
			  $query .= '"'.$uname.'",';
			  $query .= $memid.",";
			  $query .= $cmuid.",";
			  $query .= "'Edit Communication')";
			  
			  $result = mysql_query($query) or die(mysql_error().$query);
					
	
				?>
				<script>
			    window.open("","editsuppliers").jQuery("#mcommslist").trigger("reloadGrid");
				this.close();
				</script>
				<?php
		
			
		}
	}

?>

</body>
</html>
