<?php
session_start();

$admindb = $_SESSION['s_admindb'];
$coyid = $_SESSION['s_coyid'];

require_once('../db.php');
mysql_select_db($admindb) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$subscriber = $subid;

// populate driver drop down
$query = "select uid,concat_ws(' ',ufname,ulname) as fname from users where sub_id = ".$subscriber;
$result = mysql_query($query) or die(mysql_error().$query);
$op_options = "<option value=\"0\">Select Driver</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);

		$selected = '';

	$op_options .= '<option value="'.$uid.'"'.$selected.'>'.$fname.'</option>';
}

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

// populate trucks drop down
$query = "select branch,branchname from branch where branchname like 'Truck%'";
$result = mysql_query($query) or die(mysql_error().$query);
$truck_options = "<option value=\" \">Select Truck</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);

		$selected = '';

	$truck_options .= '<option value="'.$branch.'~'.$branchname.'"'.$selected.'>'.$branchname.'</option>';
}

// populate trailers drop down
$query = "select branch,branchname from branch where branchname like 'Trailer%'";
$result = mysql_query($query) or die(mysql_error().$query);
$trailer_options = "<option value=\" \">Select Trailer</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);

		$selected = '';

	$trailer_options .= '<option value="'.$branch.'~'.$branchname.'"'.$selected.'>'.$branchname.'</option>';
}

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());


// populate routes drop down
$query = "select uid,route,public,private from routes";
$result = mysql_query($query) or die(mysql_error().$query);
$route_options = "<option value=\"0~0~0\">Select Route and Compartment</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);

		$selected = '';

	$route_options .= '<option value="'.$route.'~'.$public.'~'.$private.'~'.$uid.'"'.$selected.'>'.$route.' Public '.$public.' Private '.$private.'</option>';
}


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
<title>Add Ferry Trip</title>
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
      <td colspan="4" bgcolor="<?php echo $bghead; ?>" align="center"><label style="color: <?php echo $thfont; ?>"><strong>Add Ferry Trip </strong></label></td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Date</label></td>
      <td colspan="3"  ><input type="Text" id="ddate" name="ddate" maxlength="25" size="25" value="<?php echo $ddate; ?>"><a href="javascript:NewCal('ddate')"><img src="../includes/datetimepick/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a></td>
      </tr>
    <tr>
      <td class="boxlabel">Truck      </td>
      <td colspan="3"><select name="truck" id="truck">
        <?php echo $truck_options;?>
      </select></td>
    <tr>
      <td class="boxlabel">Trailer      </td>
      <td colspan="3"  ><select name="trailer" id="trailer">
        <?php echo $trailer_options;?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabel">Route      </td>
      <td colspan="3"><select name="route" id="route" onchange="checkah(this.value);">
        <?php echo $route_options;?>
      </select></td>
      </tr>
    <tr id="ah">
      <td class="boxlabel">Ad hoc Route</td>
      <td><input type="text" name="ahroute" id="ahroute"></td>
      <td class="boxlabel" >Public
      <input type="text" name="ahpublic" id="ahpublic" size="10" value="0"></td>
      <td class="boxlabel">Private
      <input type="text" name="ahprivate" id="ahprivate" size="10" value="0"></td>
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
	document.getElementById('ah').style.visibility = 'hidden';
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
			if ($_REQUEST['ahpublic'] == 0) {
				$public = 0;
			} else {
				$public = $_REQUEST['ahpublic'];
			}
			if ($_REQUEST['ahprivate'] == 0) {
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

		$q = "insert into ferry (date,truck,trailer,truckbranch,trailerbranch,route,routeid,public,private) values (";
		$q .= '"'.$ddate.'",';
		$q .= '"'.$truck.'",';
		$q .= '"'.$trailer.'",';
		$q .= '"'.$truckbr.'",';
		$q .= '"'.$trailerbr.'",';
		$q .= '"'.$route.'",';
		$q .= $routeid.',';
		$q .= $public.",";
		$q .= $private.")";

		$r = mysql_query($q) or die(mysql_error().$q);
		
		$ferryid = mysql_insert_id();
		
					// update ruckms with milage details for truck
					
					// get the relevant ruc licence number
					$qlic = "select ruclicence from rucs where date_issued >= '".$date."' and date_issued <= '".$date."' and vehicleno = '".$truck."'";
					$rlic = mysql_query($qlic) or die(mysql_error()." ".$qlic);
					$numrecs = mysql_num_rows($rlic);
					if ($numrecs == 1) {
						$row = mysql_fetch_array($rlic);
						extract($row);
						
					} else {
						$qrt = "select ruclicence from rucs where uid=(select max(uid) FROM rucs where vehicleno = '".$truck."')";	
						$rrt = mysql_query($qrt) or die(mysql_error()." ".$qrt);
						$row = mysql_fetch_array($rrt);
						extract($row);
					}
					
					$rlic = $ruclicence;
					
					// get reg number of vehicle
					$qreg = "select regno from vehicles where vehicleno = '".$truck."'";
					$rreg = mysql_query($qreg) or die(mysql_error()." ".$qreg);
					$row = mysql_fetch_array($rreg);
					extract($row);
					$rno = $regno;
					
							
					// get the private milage for this route
					$qpvt = "select sum(routes.private)*2 as dprivate from routes where uid = ".$routeid;
					$rpvt = mysql_query($qpvt) or die(mysql_error()." ".$qpvt);
					$row = mysql_fetch_array($rpvt);
					extract($row);
					$pvtkms = $dprivate;
							
						
						// insert record into ruckms
						$qir = "insert into ruckms (ddate,vehicle,regno,ruclicence,ferry_id,routeid,private) values (";
						$qir .= "'".$ddate."',";
						$qir .= "'".$truck."',";
						$qir .= "'".$rno."',";
						$qir .= "'".$rlic."',";
						$qir .= $ferryid.",";
						$qir .= $routeid.",";
						$qir .= $pvtkms.")";
						$rir = mysql_query($qir) or die(mysql_error()." ".$qir);
				
					// update ruckms with milage details for trailer
					
					// get the relevant ruc licence number
					$qlic = "select ruclicence from rucs where date_issued >= '".$date."' and date_issued <= '".$date."' and vehicleno = '".$trailer."'";
					$rlic = mysql_query($qlic) or die(mysql_error()." ".$qlic);
					$numrecs = mysql_num_rows($rlic);
					if ($numrecs == 1) {
						$row = mysql_fetch_array($rlic);
						extract($row);
						
					} else {
						$qrt = "select ruclicence from rucs where uid=(select max(uid) FROM rucs where vehicleno = '".$trailer."')";	
						$rrt = mysql_query($qrt) or die(mysql_error()." ".$qrt);
						$row = mysql_fetch_array($rrt);
						extract($row);
					}
					
					$rlic = $ruclicence;
					
					// get reg number of vehicle
					$qreg = "select regno from vehicles where vehicleno = '".$trailer."'";
					$rreg = mysql_query($qreg) or die(mysql_error()." ".$qreg);
					$row = mysql_fetch_array($rreg);
					extract($row);
					$rno = $regno;
					
							
					// get the private milage for this route
					$qpvt = "select sum(routes.private) as dprivate from routes where uid = ".$routeid;
					$rpvt = mysql_query($qpvt) or die(mysql_error()." ".$qpvt);
					$row = mysql_fetch_array($rpvt);
					extract($row);
					$pvtkms = $dprivate;
						
						// insert record into ruckms
						$qir = "insert into ruckms (ddate,vehicle,regno,ruclicence,ferry_id,routeid,private) values (";
						$qir .= "'".$ddate."',";
						$qir .= "'".$trailer."',";
						$qir .= "'".$rno."',";
						$qir .= "'".$rlic."',";
						$qir .= $ferryid.",";
						$qir .= $routeid.",";
						$qir .= $pvtkms.")";
						$rir = mysql_query($qir) or die(mysql_error()." ".$qir);

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
