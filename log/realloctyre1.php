<?php
session_start();

$admindb = $_SESSION['s_admindb'];
$coyid = $_SESSION['s_coyid'];
$tid = $_REQUEST['id'];

require_once('../db.php');
mysql_select_db($admindb) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$subscriber = $subid;

// populate activity list
    $arr = array('Into Stock','Fit to vehicle','Send for retread');
	$activity_options = "<option value=\" \">Select</option>";
    for($i = 0; $i < count($arr); $i++)	{
				$selected = '';
		$activity_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

// populate trucks drop down
$query = "select branch,branchname from branch where (branchname like 'Truck%') or (branchname like 'Trailer%') order by branchname";
$result = mysql_query($query) or die(mysql_error().$query);
$truck_options = "<option value=\" \">Select Vehicle</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
		$selected = '';
	$truck_options .= '<option value="'.$branch.'~'.$branchname.'"'.$selected.'>'.$branchname.'</option>';
}

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());
$query = "select itemid,itemcode,item,serialno,activity,vehicle from tyres where uid = ".$tid;
$result = mysql_query($query) or die(mysql_error().$query);
$row = mysql_fetch_array($result);
extract($row);
$hed = $item." - ".$serialno;
$titemid = $itemid;
$titemcode = $itemcode;
$titem = $item;
$tserialno = $serialno;

date_default_timezone_set($_SESSION['s_timezone']);

$ddate = date("d/m/Y");
$hdate = date("Y-m-d");

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Reallocate Tyre</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>

<script>
	 $(document).ready(function(){
		$('#ddate').datepicker({ dateFormat: "dd/mm/yy", yearRange: "-5:+5", showOn: "button", buttonImage: "../images/calendar.gif", buttonImageOnly: true, altField: "#ddateh", altFormat: "yy-mm-dd"});
	 });

function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

function post() {

	//add validation here if required.
	var act = document.getElementById('activity').value;
	
	var ok = "Y";
	if (act == " ") {
		alert("Please select an activity.");
		ok = "N";
		return false;
	}
	
	if (veh == " " && act == "Fit to vehicle") {
		alert("Please select a vehicle");
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
<form name="form1" id="form1" method="post" >
 <input type="hidden" name="savebutton" id="savebutton" value="N">
 <input type="hidden" name="ddateh" id="ddateh" value="<?php echo $hdate; ?>">
 <table width="490" border="0" align="center" cellspacing="6" bgcolor="<?php echo $bgcolor; ?>">
    <tr>
      <td colspan="2" bgcolor="<?php echo $bghead; ?>" align="center"><label style="color: <?php echo $thfont; ?>"><strong>Reallocate Tyre <?php echo $hed; ?></strong></label></td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Date</label></td>
      <td><input name="ddate" type="text" id="ddate" readonly value="<?php echo $ddate; ?>" ></td>
      </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Activity</label></td>
      <td><select name="activity" id="activity"><?php echo $activity_options;?>
      </select></td>
      </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Vehicle</label></td>
      <td><select name="vehicle" id="vehicle"><?php echo $truck_options;?>
      </select></td>
      </tr>
	<tr>
      <td align="right" colspan="2">
        <input type="button" value="Save" name="save" onClick="post()"  >
      </td>
      </tr>
  	</table>
</form>
	<script>
	document.onkeypress = stopRKey;
    </script> 

<?php

	if($_REQUEST['savebutton'] == "Y") {
		$ddate = $_REQUEST['ddateh'];
		$activity = $_REQUEST['activity'];
		$vehicle = $_REQUEST['vehicle'];

		$qt = "insert into tyres (itemid,itemcode,item,serialno,activity,date,vehicle) values (";
		$qt .= $titemid.",";
		$qt .= "'".$titemcode."',";
		$qt .= "'".$titem."',";
		$qt .= "'".$tserialno."',";
		$qt .= "'".$activity."',";
		$qt .= "'".$ddate."',";
		$qt .= "'".$vehicle."')";

		$r = mysql_query($q) or die(mysql_error().$q);

	  ?>
	  <script>
	  window.open("","tyres").jQuery("#tyrelist").trigger("reloadGrid");
	  this.close();
	  </script>
	  <?php
		
			
	}

?>


</body>
</html>
