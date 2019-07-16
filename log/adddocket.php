<?php
session_start();

$admindb = $_SESSION['s_admindb'];
$coyid = $_SESSION['s_coyid'];
$coyname = $_SESSION['s_coyname'];

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



$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());

// populate debtors drop down
$query = "select members.lastname,members.firstname,client_company_xref.drno,client_company_xref.drsub from members left join client_company_xref on members.member_id = client_company_xref.client_id where client_company_xref.company_id = ".$coyid." and client_company_xref.drno != 0"; 
$result = mysql_query($query) or die(mysql_error().$query);
$debtors_options = "<option value=\"0\">Select Customer</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);

		$selected = '';

	$debtors_options .= '<option value="'.$drno.'~'.$drsub.'~'.trim($firstname." ".$lastname).'"'.$selected.'>'.trim($firstname." ".$lastname).'</option>';
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
$query = "select uid,route,compartment from routes where uid > 1 order by route,compartment";
$result = mysql_query($query) or die(mysql_error().$query);
$route_options = "<option value=\"0\">Select Route and Compartment</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);

		$selected = '';

	$route_options .= '<option value="'.$uid.'"'.$selected.'>'.$route.'~'.$compartment.'</option>';
}

// populate contractors drop down
$query = "select uid,contractor,crew from contractors order by contractor,crew";
$result = mysql_query($query) or die(mysql_error().$query);
$contractor_options = "<option value=\"0\">Select Contractor</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);

		$selected = '';

	$contractor_options .= '<option value="'.$uid.'"'.$selected.'>'.$contractor.' '.$crew.'</option>';
}

// populate destinations drop down
$query = "select uid,destination from destinations order by destination";
$result = mysql_query($query) or die(mysql_error().$query);
$destination_options = "<option value=\"0\">Select Destination</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);

		$selected = '';

	$destination_options .= '<option value="'.$destination.'"'.$selected.'>'.$destination.'</option>';
}


// populate harvest list
    $arr = array('Clear/Fell','Thinning');
	$harvest_options = "<option value=\" \">Select</option>";
    for($i = 0; $i < count($arr); $i++)	{
				$selected = '';
		$harvest_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
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
<title>Add Docket</title>
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
	var docket = document.getElementById('docket').value;
	var length = document.getElementById('length').value;
	var destination = document.getElementById('destination').value;
	var contractor = document.getElementById('contractor').value;
	var cartage = document.getElementById('cartage').value;
	var truck = document.getElementById('truck').value;
	var gross = document.getElementById('gross').value;
	var tare = document.getElementById('tare').value;
	var net = document.getElementById('net').value;
	var peel = document.getElementById('peel').value;
	var saw = document.getElementById('saw').value;
	var pulp = document.getElementById('pulp').value;
	var other = document.getElementById('other').value;
	var pieces = document.getElementById('pieces').value;
	var species = document.getElementById('lspecies').value;
	var harvest = document.getElementById('lharvest').value;
	var debtor = document.getElementById('debtor').value;
	var route = document.getElementById('route').value;
	
	var ok = "Y";
	if (dt.trim() == "") {
		alert("Please enter a date.");
		ok = "N";
		return false;
	}
	if (docket == "") {
		alert("Please enter a docket number.");
		ok = "N";
		return false;
	}
	if (length == "") {
		alert("Please enter a length.");
		ok = "N";
		return false;
	}
	if (destination == "") {
		alert("Please enter a destination.");
		ok = "N";
		return false;
	}
	if (contractor == "") {
		alert("Please enter a logging contractor.");
		ok = "N";
		return false;
	}
	if (cartage == "") {
		alert("Please enter a cartage contractor.");
		ok = "N";
		return false;
	}
	if (truck == "") {
		alert("Please enter a truck.");
		ok = "N";
		return false;
	}
	if (gross == "") {
		alert("Please enter a gross weight.");
		ok = "N";
		return false;
	}
	if (tare == "") {
		alert("Please enter a tare weight.");
		ok = "N";
		return false;
	}
	if (net == "") {
		alert("Please enter a net weight.");
		ok = "N";
		return false;
	}
	if (peel == "" && saw == "" && pulp == "" && other == "" && pieces == "") {
		alert("Please enter the timber grade.");
		ok = "N";
		return false;
	}
	if (species == "") {
		alert("Please select a species.");
		ok = "N";
		return false;
	}
	if (harvest == " ") {
		alert("Please select harvest type.");
		ok = "N";
		return false;
	}
	if (debtor == 0) {
		alert("Please select a Customer to invoice.");
		ok = "N";
		return false;
	}
	if (route == 0) {
		alert("Please select a route.");
		ok = "N";
		return false;
	}
	
	
	if (ok == "Y") {
		document.getElementById('savebutton').value = "Y";
		document.getElementById('form1').submit();
	}
	
	
}


function calcnet() {
	var gr = document.getElementById('gross').value;
	var ta = document.getElementById('tare').value;
	var nt = parseFloat(gr - ta);
	ntr = Math.round(nt*1000)/1000;
	ntf = ntr.toFixed(3);
	document.getElementById('net').value = ntf;
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
 <table width="790" border="0" align="center" cellspacing="6" bgcolor="<?php echo $bgcolor; ?>">
    <tr>
      <td colspan="6" bgcolor="<?php echo $bghead; ?>" align="center"><label style="color: <?php echo $thfont; ?>"><strong>Add Docket </strong></label></td>
    </tr>
    <tr>
      <td colspan="2" class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Date</label>
        <input type="Text" id="ddate" name="ddate" maxlength="25" size="25" value="<?php echo $ddate; ?>"><a href="javascript:NewCal('ddate')"><img src="../includes/datetimepick/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a></td>
      <td colspan="3" class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Docket No</label>
        <input name="docket" type="text" id="docket" size="15" ></td>
      <td><select name="loperator" id="loperator">
      	<?php echo $op_options; ?>
      </select></td>
      </tr>
    <tr>
      <td colspan="4" class="boxlabel">Route
        <select name="route" id="route">
          <?php echo $route_options;?>
      </select></td>
      <td colspan="2" class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Stand</label>
        <input name="skid" type="text" id="skid" size="5" maxlength="5" ></td>
      </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Length</label>
        <input name="length" type="text" id="length" size="5" maxlength="5"></td>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Peel</label>
        <input name="peel" type="text" id="peel" size="5" maxlength="5">
        </td>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Saw</label>
        <input name="saw" type="text" id="saw" size="5" maxlength="5"></td>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Pulp</label>
        <input name="pulp" type="text" id="pulp" size="5" maxlength="5"></td>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Other</label>
        <input name="other" type="text" id="other" size="5" maxlength="5"></td>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Pieces</label>
        <input name="pieces" type="text" id="pieces" size="5" maxlength="5"></td>
    </tr>
    <tr>
      <td colspan="2" class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Species</label>
        <select name="lspecies" id="lspecies">
          <option value="">Select</option>
          <option value="P.RAD">P.RAD</option>
          <option value="EUC">EUC</option>
          <option value="OTHER">OTHER</option>
        </select></td>
      <td colspan="4" class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Harvest</label>
        <select name="lharvest" id="lharvest"><?php echo $harvest_options;?>
      </select></td>
      </tr>
    <tr>
      <td colspan="2" class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Logging Contractor</label>
        <select name="contractor" id="contractor">
          <?php echo $contractor_options;?>
      </select></td>
      <td colspan="4" class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Destination</label>
        <select name="destination" id="destination"><?php echo $destination_options;?>
      </select></td>
      </tr>
    <tr>
      <td colspan="2" class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Cartage Contractor</label>
        <input name="cartage" type="text" id="cartage" value="<?php echo $coyname; ?>" readonly></td>
      <td colspan="2" class="boxlabel">Truck
        <select name="truck" id="truck"><?php echo $truck_options;?>
      </select></td>
      <td colspan="2" class="boxlabel">Trailer
        <select name="trailer" id="trailer"><?php echo $trailer_options;?>
      </select></td>
      </tr>
    <tr>
      <td colspan="2" class="boxlabel">&nbsp;</td>
      <td colspan="4" class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Gross</label>
      <input name="gross" type="text" id="gross"></td>
      </tr>
    <tr>
      <td colspan="2" class="boxlabel">Invoice Customer
        <select name="debtor" id="debtor"><?php echo $debtors_options;?>
      </select></td>
      <td colspan="4" class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Tare&nbsp;&nbsp;</label>
      <input name="tare" type="text" id="tare" onBlur="calcnet();"></td>
      </tr>
    <tr>
      <td class="boxlabel">&nbsp;</td>
      <td class="boxlabel">&nbsp;</td>
      <td colspan="4" class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Net&nbsp;&nbsp;&nbsp;</label>
      <input name="net" type="text" id="net"></td>
      </tr>
	<tr>
      <td align="right" colspan="6">
        <input type="button" value="Save" name="save" onClick="post()"  >
      </td>
      </tr>
  	</table>
</form>
	<script>
	document.onkeypress = stopRKey;
    var admin = '<?php echo $admin; ?>'; 
	if (admin == 'Y') {
		document.getElementById('loperator').style.visibility = 'visible';
	} else {
		document.getElementById('loperator').style.visibility = 'hidden';
	}
    </script> 

<?php

	if($_REQUEST['savebutton'] == "Y") {
		$dt = explode('/',$_REQUEST['ddate']);
		$d = $dt[0];
		$m = $dt[1];
		$y = $dt[2];
		$ddate = $y.'-'.$m.'-'.$d;
		$docket = $_REQUEST['docket'];
		$skid = $_REQUEST['skid'];
		$length = $_REQUEST['length'];
		$peel = $_REQUEST['peel'];
		$saw = $_REQUEST['saw'];
		$pulp = $_REQUEST['pulp'];
		$other = $_REQUEST['other'];
		$pieces = $_REQUEST['pieces'];
		$species = $_REQUEST['lspecies'];
		$harvest = $_REQUEST['lharvest'];
		$destination = strtoupper($_REQUEST['destination']);
		$ctr = $_REQUEST['contractor'];
		$qf = "select contractor,crew from contractors where uid = ".$ctr;
		$rf = mysql_query($qf);
		$row = mysql_fetch_array($rf) or die(mysql_error());
		extract($row);
		$contr = $contractor;
		$crw = $crew;
		$cartage = $_REQUEST['cartage'];
		$trk = split('~',$_REQUEST['truck']);
		$truckbr = $trk[0];
		$truck = $trk[1];
		$trl = split('~',$_REQUEST['trailer']);
		$trailerbr = $trl[0];
		$trailer = $trl[1];
		$gross = $_REQUEST['gross'];
		$tare = $_REQUEST['tare'];
		$net = $_REQUEST['net'];
		$driver = $_REQUEST['loperator'];
		$route = $_REQUEST['route'];
		$qf = "select forest,compartment from routes where uid = ".$route;
		$rf = mysql_query($qf);
		$row = mysql_fetch_array($rf) or die(mysql_error());
		extract($row);
		$frt = $forest;
		$cpt = $compartment;
		$amt = $_REQUEST['amt'];
		$dbt = $_REQUEST['debtor'];
		$d = explode('~',$dbt);
		$debtor = $d[0].'~'.$d[1];
		$customer = $d[2];
		
		// calculate the $ amount
		$qr = "select rate from routes where uid = ".$route;
		$rr = mysql_query($qr) or die (mysql_error());
		$row = mysql_fetch_array($rr);
		extract($row);
		$amt = $net * $rate;

		$q = "insert into dockets (ddate,docket_no,forest,cpt,skid,length,peel,saw,pulp,other,pieces,species,harvest,customer,destination,contractor,crew,cartage,truck,trailer,truckbranch,trailerbranch,gross,tare,net,routeid,debtor,amount,operator) values (";
		$q .= '"'.$ddate.'",';
		$q .= '"'.$docket.'",';
		$q .= '"'.$frt.'",';
		$q .= '"'.$cpt.'",';
		$q .= '"'.$skid.'",';
		$q .= $length.',';
		$q .= '"'.$peel.'",';
		$q .= '"'.$saw.'",';
		$q .= '"'.$pulp.'",';
		$q .= '"'.$other.'",';
		$q .= '"'.$pieces.'",';
		$q .= '"'.$species.'",';
		$q .= '"'.$harvest.'",';
		$q .= '"'.$customer.'",';
		$q .= '"'.$destination.'",';
		$q .= '"'.$contr.'",';
		$q .= '"'.$crw.'",';
		$q .= '"'.$cartage.'",';
		$q .= '"'.$truck.'",';
		$q .= '"'.$trailer.'",';
		$q .= '"'.$truckbr.'",';
		$q .= '"'.$trailerbr.'",';
		$q .= '"'.$gross.'",';
		$q .= '"'.$tare.'",';
		$q .= '"'.$net.'",';
		$q .= $route.',';
		$q .= '"'.$debtor.'",';
		$q .= $amt.",";
		$q .= $driver.")";

		$r = mysql_query($q) or die(mysql_error().$q);
		
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
					$qpvt = "select sum(routes.private)*2 as dprivate from routes where uid = ".$route;
					$rpvt = mysql_query($qpvt) or die(mysql_error()." ".$qpvt);
					$row = mysql_fetch_array($rpvt);
					extract($row);
					$pvtkms = $dprivate;
							
					// insert record into ruckms
					$qir = "insert into ruckms (ddate,vehicle,regno,ruclicence,docket_no,routeid,private) values (";
					$qir .= "'".$date."',";
					$qir .= "'".$truck."',";
					$qir .= "'".$rno."',";
					$qir .= "'".$rlic."',";
					$qir .= $docket.",";
					$qir .= $route.",";
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
					$qpvt = "select sum(routes.private) as dprivate from routes where uid = ".$route;
					$rpvt = mysql_query($qpvt) or die(mysql_error()." ".$qpvt);
					$row = mysql_fetch_array($rpvt);
					extract($row);
					$pvtkms = $dprivate;
							
					// insert record into ruckms
					$qir = "insert into ruckms (ddate,vehicle,regno,ruclicence,docket_no,routeid,private) values (";
					$qir .= "'".$date."',";
					$qir .= "'".$trailer."',";
					$qir .= "'".$rno."',";
					$qir .= "'".$rlic."',";
					$qir .= $docket.",";
					$qir .= $route.",";
					$qir .= $pvtkms.")";
					$rir = mysql_query($qir) or die(mysql_error()." ".$qir);

	  ?>
	  <script>
	  window.open("","dockets").jQuery("#docketlist").trigger("reloadGrid");
	  this.close();
	  </script>
	  <?php
		
			
	}

?>


</body>
</html>
