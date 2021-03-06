<?php
session_start();
date_default_timezone_set($_SESSION['s_timezone']);

$crndb = $_SESSION['s_crndb'];
if(isset($_REQUEST['id'])) {
	$id = $_REQUEST['id'];
	$_SESSION['s_tid'] = $id;
} else {
	$id = $_SESSION['s_tid'];
}
include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from ".$crndb.".joblines where uid = ".$id);
$row = $db->single();
extract($row);

$xd = explode('-',$ddate);
$y = $xd[0];
$m = $xd[1];
$d = $xd[2];
$op = $operator;
$mc = $machine;

/*
// populate operator list
	$db->query("select operator from ".$crndb.".operators order by operator");
	$rows = $db->resultset();
	$operator_options = "<option value=\"\">Select Operator</option>";
	foreach ($rows as $row) {
		extract($row);
			if ($operator == $op) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}		
		$operator_options .= '<option value="'.$operator.'"'.$selected.'>'.$operator.'</option>';
	}

// populate machine list
	$db->query("select uid,vehicleno,make,regno from ".$crndb.".vehicles order by vehicleno");
	$rows = $db->resultset();
	$machine_options = "<option value=\"\">Select Vehicle/Machine</option>";
	foreach ($rows as $row) {
		extract($row);
		$mach = $vehicleno.', '.$make.', '.$regno;
			if ($machine == $mc) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}		
		$machine_options .= '<option value="'.$vehicleno.'"'.$selected.'>'.$mach.'</option>';
	}
*/

$db->closeDB();


require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="bizTracc">
<meta name="author" content="Murray Russell">
<title>Add a Time</title>

<link href="../includes/css/bootstrap.min.css" rel="stylesheet">
<link href="../includes/css/custom.css" rel="stylesheet">
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="../includes/js/bootstrap.min.js"></script>

<script>

function sttime() {
	var sttime = new Date().toLocaleTimeString('en-US', { hour12: false, hour: "numeric", minute: "numeric"});	
	document.getElementById('starttime').value = sttime;
}

function sptime() {
	var sptime = new Date().toLocaleTimeString('en-US', { hour12: false, hour: "numeric", minute: "numeric"});	
	document.getElementById('stoptime').value = sptime;
}

function checkop(val) {
	if (val != 'Select Operator') {
		document.getElementById('machine').value = document.getElementById('machine').options[0].value;
	}
}

function checkmc(val) {
	if (val != 'Select Vehicle/Machine') {
		document.getElementById('operator').value = document.getElementById('operator').options[0].value;
	}
}

function post() {

	//add validation here if required.
	var starttime = document.getElementById('starttime').value;
	var stoptime = document.getElementById('stoptime').value;
	var operator = document.getElementById('operator').value;
	var machine = document.getElementById('machine').value;
	
	var ok = "Y";
	if (starttime == "" && stoptime == "") {
		alert("Please enter a start time.");
		ok = "N";
		return false;
	}
	if (operator == "" && machine == "") {
		alert("Please select an operator or machine.");
		ok = "N";
		return false;
	}
	
	if (ok == "Y") {
		document.getElementById('savebutton').value = "Y";
		document.getElementById('edittime').submit();
	}
}

function cancel() {
	var oldwin = window.open("times.php","_self").jQuery("#timelist").trigger("reloadGrid");
	oldWin.focus();  // give focus 	  
}

</script>


</head>


<body>

<form id="edittime">
  <input type="hidden" name="savebutton" id="savebutton" value="N">
<div class="container-fluid">
    <section class="container">
		<div class="container-page">				
			<div class="col-md-12">
				<h3 class="dark-grey">Add a Time</h3>
				
				<div class="form-group col-sm-3">
					<label>Job No.</label>
                </div>
                <div class="form-group col-sm-9">
					<input type="text" name="jobno" class="form-control" id="jobno" value="<?php echo $jobno; ?>" readonly>
				</div>

				<div class="form-group col-sm-3">
					<label>Date</label>
                </div>
                
				<div class="form-group col-sm-1">
					<label>Day</label>
                </div>
                <div class="form-group col-sm-2">
					<input type="text" name="d" id="d" readonly value="<?php echo $d; ?>">
				</div>
				
				<div class="form-group col-sm-1">
					<label>Month</label>
                </div>
                <div class="form-group col-sm-2">
					<input type="text" name="m" id="m" readonly value="<?php echo $m; ?>">
				</div>
								
				<div class="form-group col-sm-1">
					<label>Year</label>
                </div>
                <div class="form-group col-sm-2">
					<input type="text" name="y" id="y" readonly value="<?php echo $y; ?>">
				</div>                
				
				<div class="form-group col-sm-3">
					<label>Start time (hh:mm 24 hr)</label>
                </div>
				<div class="form-group col-sm-3">
					<button type="button" class="btn btn-primary" onClick="sttime()">Now</button>
				</div>			
                <div class="form-group col-sm-6">
					<input type="text" name="starttime" class="form-control" id="starttime" value="<?php echo $start; ?>" readonlyh>
				</div>
								
				<div class="form-group col-sm-3">
					<label>Stop time (hh:mm 24 hr)</label>
                </div>
				<div class="form-group col-sm-3">
					<button type="button" class="btn btn-primary" onClick="sptime()">Now</button>
				</div>			
                <div class="form-group col-sm-6">
					<input type="text" name="stoptime" class="form-control" id="stoptime" value="">
				</div>
				
				<div class="form-group col-sm-3">
					<label>Operator</label>
                </div>
                <div class="form-group col-sm-9">
					<input type="text" class="form-control" name="operator" id="operator" value="<?php echo $operator; ?>" readonly>
				</div>
                
				<div class="form-group col-sm-3">
					<label>OR</label>
				</div>
 				<div class="form-group col-sm-9">
					<label>&nbsp;</label>
				</div>
                               
				<div class="form-group col-sm-3">
					<label>Machine</label>
                </div>
                <div class="form-group col-sm-9">
					<input type="text" class="form-control" name="machine" id="machine" value="<?php echo $machine; ?>" readonly>
				</div>
                
				
				<div class="form-group col-sm-6">
				<button type="button" class="btn btn-primary" onClick="post()">Save</button>
				</div>
                
 				<div class="form-group col-sm-6">
				<button type="button" class="btn btn-primary" onClick="cancel()">Cancel</button>
				</div>			
                
                			
				
			</div>
	
				
		</div>
	</section>
</div>

</form>	



<?php

	if($_REQUEST['savebutton'] == "Y") {
		$stoptime = $_REQUEST['stoptime'];
		
		include_once("../includes/DBClass.php");
		$db = new DBClass();
		
		$db->query("update ".$crndb.".joblines set stop = :stop where uid = :uid");
		$db->bind(':stop', $stoptime);
		$db->bind(':uid', $id);
		$db->execute();
		
		$db->closeDB();

	  ?>
	  <script>
	  var oldwin = window.open("times.php","_self").jQuery("#timelist").trigger("reloadGrid");
	  oldWin.focus();  // give focus 	  
	  </script>
	  <?php
		
			
	}

?>


</body>
</html>
