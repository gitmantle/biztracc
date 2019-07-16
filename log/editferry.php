<?php
session_start();

$admindb = $_SESSION['s_admindb'];

require_once('../db.php');
mysql_select_db($admindb) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$subscriber = $subid;

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

$rtid = $_REQUEST['uid'];
$q = "select * from ferry where uid = ".$rtid;
$r = mysql_query($q);
$row = mysql_fetch_array($r);
extract($row);
$id = $uid;
$trbr = $truckbranch;
$tlbr = $trailerbranch;
$rid = $routeid;
if ($routeid == 1) {
	$ahrt = $route;
	$apub = $public;
	$aprv = $private;
} else {
	$ahrt = "";
	$apub = "0.00";
	$aprv = "0.00";
}

$q = "select claimed from ruckms where ferry_id = ".$id;
$r = mysql_query($q) or die(mysql_error().$q);
$row = mysql_fetch_array($r);
extract($row);
$claim = $claimed;

if ($claim == 'Yes') {
	$q = "select route from routes where uid = ".$rid;
	$r = mysql_query($q);
	$row = mysql_fetch_array($r);
	extract($row);
	$rt = $route;
}



// populate routes drop down
$query = "select uid,route,public,private from routes";
$result = mysql_query($query) or die(mysql_error().$query);
$route_options = "<option value=\"0~0~0\">Select Route</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($uid == $rid) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$route_options .= '<option value="'.$route.'~'.$public.'~'.$private.'~'.$uid.'"'.$selected.'>'.$route.' Public '.$public.' Private '.$private.'</option>';
}


$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

// populate trucks drop down
$query = "select branch,branchname from branch where branchname like 'Truck%'";
$result = mysql_query($query) or die(mysql_error().$query);
$truck_options = "<option value=\" \">Select Truck</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($branch == $trbr) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$truck_options .= '<option value="'.$branch.'~'.$branchname.'"'.$selected.'>'.$branchname.'</option>';
}

// populate trailers drop down
$query = "select branch,branchname from branch where branchname like 'Trailer%'";
$result = mysql_query($query) or die(mysql_error().$query);
$trailer_options = "<option value=\" \">Select Trailer</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($branch == $tlbr) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$trailer_options .= '<option value="'.$branch.'~'.$branchname.'"'.$selected.'>'.$branchname.'</option>';
}


$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());


date_default_timezone_set($_SESSION['s_timezone']);

$dt = split('-',$date);
$y = $dt[0];
$m = $dt[1];
$d = $dt[2];
$fdt = mktime(0,0,0,$m,$d,$y);
$ddate = date("d/m/Y",$fdt);
$hdate = date("Y-m-d",$fdt);

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Ferry Trip</title>
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

function checkah(ah) {
	if (ah == 'Ad Hoc~0~0~1') {
		document.getElementById('ah').style.visibility = 'visible';
	} else {
		document.getElementById('ah').style.visibility = 'hidden';
		document.getElementById('ahroute').value = "";
		document.getElementById('ahpublic').value = 0;
		document.getElementById('ahprivate').value = 0;
	}
}

function SQLdate(dt) {
	var sdt = dt.split('/');
	var d = sdt[0];
	var m = sdt[1];
	var y = sdt[2];
	if (m.length < 2) m = "0" + m;
	if (d.length < 2) d = "0" + d;
	
	var SQLFormatted = "" + y +"-"+ m +"-"+ d;	
	
	return SQLFormatted;
	
}

function post() {

	//add validation here if required.
	var dt = SQLdate(document.getElementById('ddate').value);
	var truck = document.getElementById('truck').value;
	var route = document.getElementById('route').value;
	
	var ok = "Y";
	if (dt == "") {
		alert("Please enter a date.");
		ok = "N";
		return false;
	}
	if (truck == "") {
		alert("Please enter a truck.");
		ok = "N";
		return false;
	}
	if (route == '0~0~0') {
		alert("Please select a route.");
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
 <table width="690" border="0" align="center" cellspacing="6" bgcolor="<?php echo $bgcolor; ?>">
    <tr>
      <td colspan="4" bgcolor="<?php echo $bghead; ?>" align="center"><label style="color: <?php echo $thfont; ?>"><strong>Edit Ferry Trip </strong></label></td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Date</label></td>
      <td colspan="3"  ><input type="Text" id="ddate" name="ddate" maxlength="25" size="25" value="<?php echo $ddate; ?>"><a href="javascript:NewCal('ddate')"><img src="../includes/datetimepick/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a></td>
      </tr>
    <tr>
      <td class="boxlabel">Truck</td>
      <td colspan="3"><select name="truck" id="truck">
        <?php echo $truck_options;?>
      </select></td>
    <tr>
      <td class="boxlabel">Trailer</td>
      <td colspan="3"  ><select name="trailer" id="trailer">
        <?php echo $trailer_options;?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabel">Route</td>
      <td colspan="3" ><?php 
			if ($claim == 'No') {
        		echo '<select name="route" id="route">'.$route_options.'</select>';
			} else {
				echo '<input name="route" type="text" id="route" readonly value="'.$rt.'" >';
			}
      	?>
      </td>
      </tr>
    <tr id="ah">
      <td class="boxlabel">Ad hoc Route</td>
      <td><input type="text" name="ahroute" id="ahroute" value="<?php echo $ahrt; ?>"></td>
      <td class="boxlabel" >Public
      <input type="text" name="ahpublic" id="ahpublic" size="10" value="<?php echo $apub; ?>"></td>
      <td class="boxlabel">Private
      <input type="text" name="ahprivate" id="ahprivate" size="10" value="<?php echo $aprv; ?>"></td>
    </tr>
	<tr>
      <td align="right" colspan="4">
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
		$trk = split('~',$_REQUEST['truck']);
		$truckbr = $trk[0];
		$truck = $trk[1];
		$trl = split('~',$_REQUEST['trailer']);
		$trailerbr = $trl[0];
		$trailer = $trl[1];
		$rt = $_REQUEST['route'];
		if ($rt == 'Ad Hoc~0~0~1') {
			$route = $_REQUEST['ahroute'];
			if ($_REQUEST['ahpublic'] == '0') {
				$public = 0;
			} else {
				$public = $_REQUEST['ahpublic'];
			}
			if ($_REQUEST['ahprivate'] == '0') {
				$private = 0;
			} else {
				$private = $_REQUEST['ahprivate'];
			}
			$routeid = 1;
		} else {
			$rtx = split('~',$rt);
			$route = $rtx[0];
			$public = $rtx[1];
			$private = $rtx[2];
			$routeid = $rtx[3];
		}
		if ($claim == 'Yes') {
			$routeid = $rid;
		}

		$q = "update ferry set ";
		$q .= 'date = "'.$ddate.'",';
		$q .= 'truck = "'.$truck.'",';
		$q .= 'trailer = "'.$trailer.'",';
		$q .= 'truckbranch = "'.$truckbr.'",';
		$q .= 'trailerbranch = "'.$trailerbr.'",';
		$q .= 'route = "'.$route.'",';
		$q .= 'routeid = '.$routeid.',';
		$q .= 'public = '.$public.",";
		$q .= 'private = '.$private;
		$q .= ' where uid = '.$rtid;

		$r = mysql_query($q) or die(mysql_error().$q);

	  ?>
	  <script>
	  window.open("","ferry").jQuery("#ferrylist").trigger("reloadGrid");
	  this.close();
	  </script>
	  <?php
		
			
	}

?>


</body>
</html>
