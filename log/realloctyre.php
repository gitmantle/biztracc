<?php
session_start();

$admindb = $_SESSION['s_admindb'];
$coyid = $_SESSION['s_coyid'];
$itemid = $_REQUEST['itemid'];
$sno = $_REQUEST['sno'];
$vn = $_SESSION['s_vehicleno'];

require_once('../db.php');
mysql_select_db($admindb) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$subscriber = $subid;


// populate activity list
    $arr = array('Fit to vehicle','Send for retreading','Disposed of');
	$activity_options = "<option value=\" \">Select</option>";
    for($i = 0; $i < count($arr); $i++)	{
		$selected = '';
		$activity_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

$serialtable = 'ztmp'.$user_id.'_vtyres';

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

// populate trucks drop down
$query = "select branch,branchname from branch where (branchname like 'Truck%') or (branchname like 'Trailer%') order by branchname";
$result = mysql_query($query) or die(mysql_error().$query);
$truck_options = "<option value=\" \">Select Vehicle</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	$selected = '';
	$truck_options .= '<option value="'.$branchname.'"'.$selected.'>'.$branchname.'</option>';
}

$query = "select branch from branch where branchname = '".$vn."'";
$result = mysql_query($query) or die(mysql_error().$query);
$row = mysql_fetch_array($result);
extract($row);
$br = $branch;

$query = "select itemcode,item from stkserials where itemcode = '".$itemid."' and serialno = '".$sno."'";
$result = mysql_query($query) or die(mysql_error().$query);
$row = mysql_fetch_array($result);
extract($row);
$hed = $item." - ".$sno;
$titemcode = $itemcode;
$titem = $item;

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
<script language="javascript" type="text/javascript" src="../includes/datetimepick/datetimepicker.js"></script>

<script>

function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

function post() {

	//add validation here if required.
	var act = document.getElementById('activity').value;
	var veh = document.getElementById('vehicle').value;

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

function checkactivity() {
	if (document.getElementById('activity').value == 'Fit to vehicle') {
		document.getElementById('fitvehicle').style.visibility = 'visible';
	} else {
		document.getElementById('fitvehicle').style.visibility = 'hidden';
	}
}

</script>

</head>


<body>
<form name="form1" id="form1" method="post" >
 <input type="hidden" name="savebutton" id="savebutton" value="N">
 <table width="490" border="0" align="center" cellspacing="6" bgcolor="<?php echo $bgcolor; ?>">
    <tr>
      <td colspan="2" bgcolor="<?php echo $bghead; ?>" align="center"><label style="color: <?php echo $thfont; ?>"><strong>Reallocate <?php echo $hed; ?></strong></label></td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Date</label></td>
      <td><input type="Text" id="ddate" name="ddate" maxlength="25" size="25" value="<?php echo $ddate; ?>"><a href="javascript:NewCal('ddate')"><img src="../includes/datetimepick/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a></td>
      </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Activity</label></td>
      <td><select name="activity" id="activity" onchange="checkactivity();"><?php echo $activity_options;?>
      </select></td>
      </tr>
    <tr id="fitvehicle">
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
	document.getElementById('fitvehicle').style.visibility = 'hidden';
    </script> 

<?php

	if($_REQUEST['savebutton'] == "Y") {
		$odt = $_REQUEST['ddate'];
		$t = explode('/',$odt);
		$d = $t[0];
		if (strlen($d) == 1) {
			$d = '0'.$d;
		}
		$m = $t[1];
		if (strlen($m) == 1) {
			$m = '0'.$m;
		}
		$y = $t[2];
		$ddate = $y.'-'.$m.'-'.$d;		
		$activity = $_REQUEST['activity'];
		$vehicle = $_REQUEST['vehicle'];
		if ($activity == 'Fit to vehicle') {
			$act = 'Fit to vehicle '.$vehicle;
		} else {
			$act = $activity;
		}
		$q = "insert into ".$serialtable." (itemcode,item,serialno,ddate,activity,ref_no) values (";
		$q .= "'".$titemcode."','".$titem."','".$sno."','".$ddate."','".$act."','ALOC')";																															
  		$r = mysql_query($q) or die(mysql_error().$q);

		$qt = "insert into stkserials (itemcode,item,serialno,activity,date,branch) values (";
		$qt .= "'".$titemcode."',";
		$qt .= "'".$titem."',";
		$qt .= "'".$sno."',";
		$qt .= "'".$act."',";
		$qt .= "'".$ddate."',";
		$qt .= "'".$br."')";

		$rt = mysql_query($qt) or die(mysql_error().$qt);

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
