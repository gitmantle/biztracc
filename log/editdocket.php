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

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

date_default_timezone_set($_SESSION['s_timezone']);

$docid = $_REQUEST['uid'];
$q = "select * from dockets where docket_id = ".$docid;
$r = mysql_query($q);
$row = mysql_fetch_array($r);
extract($row);
$sid = $sub_id;
$odebtor = $debtor;
$otruckbr = $truckbranch;
$otrailerbr = $trailerbranch;
$hvst = $harvest;
$op = $operator;
$fid = $forest;
$rid = $routeid;
$contr = $contractor;
$crw = $crew;
$dest = $destination;
$docno = $docket_no;

$dt = split('-',$ddate);
$y = $dt[0];
$m = $dt[1];
$d = $dt[2];
$fdt = mktime(0,0,0,$m,$d,$y);
$ddate = date("d/m/Y",$fdt);
$hdate = date("Y-m-d",$fdt);

$q = "select claimed from ruckms where docket_no = ".$docno;
$r = mysql_query($q) or die(mysql_error().$q);
$numrows = mysql_num_rows($r);
if ($numrows == 0) { 
	$claim = 'No';			  
} else {
	$row = mysql_fetch_array($r);
	extract($row);
	$claim = $claimed;
}

if ($claim == 'Yes') {
	$q = "select route from routes where uid = ".$rid;
	$r = mysql_query($q);
	$row = mysql_fetch_array($r);
	extract($row);
	$rt = $route;
}

// populate routes drop down
$query = "select uid,route,compartment from routes where uid > 1 order by route,compartment";
$result = mysql_query($query) or die(mysql_error().$query);
$route_options = "<option value=\"0\">Select Route</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($uid == $rid) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$route_options .= '<option value="'.$uid.'"'.$selected.'>'.$route.'~'.$compartment.'</option>';
}

// populate contractors drop down
$query = "select uid,contractor,crew from contractors order by contractor,crew";
$result = mysql_query($query) or die(mysql_error().$query);
$contractor_options = "<option value=\"0\">Select Contractor</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($contractor == $contr && $crew == $crw) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$contractor_options .= '<option value="'.$uid.'"'.$selected.'>'.$contractor.' '.$crew.'</option>';
}

// populate destinations drop down
$query = "select uid,destination from destinations order by destination";
$result = mysql_query($query) or die(mysql_error().$query);
$destination_options = "<option value=\"0\">Select Destination</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($destination == $dest) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$destination_options .= '<option value="'.$destination.'"'.$selected.'>'.$destination.'</option>';
}


$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());

// populate debtors drop down
$query = "select members.lastname,members.firstname,client_company_xref.drno,client_company_xref.drsub from members left join client_company_xref on members.member_id = client_company_xref.client_id where client_company_xref.company_id = ".$coyid." and client_company_xref.drno != 0"; 
$result = mysql_query($query) or die(mysql_error().$query);
$debtors_options = "<option value=\"0\">Select Customer</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($odebtor == $drno.'~'.$drsub) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
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
	if ($otruckbr == $branch) {
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
	if ($otrailerbr == $branch) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$trailer_options .= '<option value="'.$branch.'~'.$branchname.'"'.$selected.'>'.$branchname.'</option>';
}

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());


// populate species list
    $arr = array('P.RAD', 'EUC', 'OTHER');
	$species_options = "";
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $species) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$species_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}


// populate harvest list
    $arr = array('Clear/Fell','Thinning');
	$harvest_options = "<option value=\" \">Select</option>";
    for($i = 0; $i < count($arr); $i++)	{
			if ($arr[$i] == $hvst) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}	
		$harvest_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

$admindb = $_SESSION['s_admindb'];
mysql_select_db($admindb) or die(mysql_error());

// populate driver drop down
$query = "select uid,concat_ws(' ',ufname,ulname) as fname from users where sub_id = ".$subscriber;
$result = mysql_query($query) or die(mysql_error().$query);
$op_options = "<option value=\"0\">Select Driver</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
		if ($uid == $op) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
	$op_options .= '<option value="'.$uid.'"'.$selected.'>'.$fname.'</option>';
}

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());




require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Docket</title>
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

function calcnet() {
	var gross = document.getElementById('gross').value;
	var tare = document.getElementById('tare').value;
	var nt = parseFloat(gross) - parseFloat(tare);
	document.getElementById('net').value = nt;
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
      <td colspan="6" bgcolor="<?php echo $bghead; ?>" align="center"><label style="color: <?php echo $thfont; ?>"><strong>Edit Docket</strong></label></td>
    </tr>
    <tr>
      <td colspan="2" class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Date</label>
        <input type="Text" id="ddate" name="ddate" maxlength="25" size="25" value="<?php echo $ddate; ?>"><a href="javascript:NewCal('ddate')"><img src="../includes/datetimepick/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a></td>
      <td colspan="2" class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Docket No</label>
        <input name="docket" type="text" id="docket" value="<?php echo $docket_no; ?>"></td>
      <td colspan="2" class="boxlabel"><select name="loperator" id="loperator">
        <?php echo $op_options; ?>
      </select></td>
      </tr>
    <tr>
      <td colspan="4" class="boxlabel">Route
      	<?php 
			if ($claim == 'No') {
        		echo '<select name="route" id="route">'.$route_options.'</select>';
			} else {
				echo '<input name="route" type="text" id="route" readonly value="'.$rt.'" >';
			}
      	?>
      </td>
      <td colspan="2" class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Stand</label>
        <input name="skid" type="text" id="skid" size="5" maxlength="5" value="<?php echo $skid; ?>" ></td>
      </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Length</label>
        <input name="length" type="text" id="length" size="5" maxlength="5" value="<?php echo $length; ?>"></td>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Peel</label>
        <input name="peel" type="text" id="peel" size="5" maxlength="5" value="<?php echo $peel; ?>">
        </td>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Saw</label>
        <input name="saw" type="text" id="saw" size="5" maxlength="5" value="<?php echo $saw; ?>"></td>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Pulp</label>
        <input name="pulp" type="text" id="pulp" size="5" maxlength="5" value="<?php echo $pulp; ?>"></td>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Other</label>
        <input name="other" type="text" id="other" size="5" maxlength="5" value="<?php echo $other; ?>"></td>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Pieces</label>
        <input name="pieces" type="text" id="pieces" size="5" maxlength="5" value="<?php echo $pieces; ?>"></td>
    </tr>
    <tr>
      <td colspan="2" class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Species</label>
        <select name="lspecies" id="lspecies">
			<?php echo $species_options; ?>
        </select></td>
      <td colspan="4" class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Harvest</label>
        <select name="lharvest" id="lharvest">
			<?php echo $harvest_options; ?>
        </select></td>
    <tr>
      <td colspan="2" class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Logging Contractor</label>
        <select name="contractor" id="contractor">
          <?php echo $contractor_options;?>
      </select></td>
      <td colspan="4" class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Destination</label>
        <select name="destination" id="destination"><?php echo $destination_options;?></select></td>
      </tr>
    <tr>
      <td colspan="2" class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Cartage Contractor</label>
        <input name="cartage" type="text" id="cartage" value="<?php echo $cartage; ?>" readonly></td>
      <td colspan="2" class="boxlabel">Truck
        <select name="truck" id="truck"><?php echo $truck_options;?>
      </select></td>
      <td colspan="2" class="boxlabel">Trailer
        <select name="trailer" id="trailer"><?php echo $trailer_options;?>
      </select></td>
    <tr>
      <td colspan="2" class="boxlabel">&nbsp;</td>
      <td colspan="4" class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Gross</label>
      <input name="gross" type="text" id="gross" value="<?php echo $gross; ?>"></td>
      </tr>
    <tr>
      <td colspan="2" class="boxlabel">Invoice Customer
        <select name="debtor" id="debtor"><?php echo $debtors_options;?>
      </select></td>
      <td colspan="4" class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Tare&nbsp;&nbsp;</label>
        <input name="tare" type="text" id="tare" value="<?php echo $tare; ?>"></td>
      </tr>
    <tr>
      <td class="boxlabel">Invoice
      <input type="text" name="inv" id="inv" size="10" value="<?php echo $invoice; ?>" readonly></td>
      <td class="boxlabel">$
      <input type="text" name="amt" id="amt" size="10" value="<?php echo $amount; ?>">&nbsp; excl. GST</td>
      <td colspan="4" class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Net&nbsp;&nbsp;&nbsp;</label>
        <input name="net" type="text" id="net" value="<?php echo $net; ?>" onFocus="calcnet()"></td>
      </tr>
	<tr>
      <td align="right" colspan="6">
        <input type="button" value="Save" name="save" onClick="post()"  >
      </td>
      </tr>
  </table>
</form>
	<script>document.onkeypress = stopRKey;</script> 

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
		$destination = $_REQUEST['destination'];
		$ctr = $_REQUEST['contractor'];
		$qf = "select contractor,crew from contractors where uid = ".$ctr;
		$rf = mysql_query($qf);
		$row = mysql_fetch_array($rf) or die(mysql_error());
		extract($row);
		$contr = $contractor;
		$crw = $crew;
		$crew = $_REQUEST['crew'];
		$cartage = $_REQUEST['cartage'];
		$trk = explode('~',$_REQUEST['truck']);
		$truckbr = $trk[0];
		$truck = $trk[1];
		$trl = explode('~',$_REQUEST['trailer']);
		$trailerbr = $trl[0];
		$trailer = $trl[1];
		$gross = $_REQUEST['gross'];
		$tare = $_REQUEST['tare'];
		$net = $_REQUEST['net'];
		if ($claim == 'No') {
			$route = $_REQUEST['route'];
		} else {
			$route = $rid;
		}
		$qf = "select forest,compartment from routes where uid = ".$route;
		$rf = mysql_query($qf);
		$row = mysql_fetch_array($rf) or die(mysql_error());
		extract($row);
		$frt = $forest;
		$cpt = $compartment;
		if ($_REQUEST['amt'] == '') {
			$amt = 0;
		} else {
			$amt = $_REQUEST['amt'];
		}
		$driver = $_REQUEST['loperator'];
		$dbt = $_REQUEST['debtor'];
		$d = explode('~',$dbt);
		$debtor = $d[0].'~'.$d[1];
		$customer = $d[2];

		$q = "update dockets set "; 
		$q .= "ddate = '".$ddate."',";
		$q .= "docket_no = '".$docket."',";
		$q .= "forest = '".$frt."',";
		$q .= "cpt = '".$cpt."',";
		$q .= "skid = '".$skid."',";
		$q .= "length = ".$length.',';
		$q .= "peel = '".$peel."',";
		$q .= "saw = '".$saw."',";
		$q .= "pulp = '".$pulp."',";
		$q .= "other = '".$other."',";
		$q .= "pieces = '".$pieces."',";
		$q .= "species = '".$species."',";
		$q .= "harvest = '".$harvest."',";
		$q .= "customer = '".$customer."',";
		$q .= "destination = '".$destination."',";
		$q .= "contractor = '".$contr."',";
		$q .= "crew = '".$crw."',";
		$q .= "cartage = '".$cartage."',";
		$q .= "truck = '".$truck."',";
		$q .= "trailer = '".$trailer."',";
		$q .= "truckbranch = '".$truckbr."',";
		$q .= "trailerbranch = '".$trailerbr."',";
		$q .= "gross = '".$gross."',";
		$q .= "tare = '".$tare."',";
		$q .= "net = '".$net."',";
		$q .= "routeid = ".$route.",";
		$q .= "amount = ".$amt.",";
		$q .= "operator = ".$driver.",";
		$q .= "debtor = '".$debtor."'";
		$q .= " where docket_id = ".$docid;
		
		$r = mysql_query($q) or die(mysql_error().$q);

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
