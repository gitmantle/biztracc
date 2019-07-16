<?php
session_start();
date_default_timezone_set($_SESSION['s_timezone']);

$jobid = $_SESSION['s_jobid'];
$crndb = $_SESSION['s_crndb'];
$findb = $_SESSION['s_findb'];
$hdate = date("Y-m-d");
$x = explode('-',$hdate);
$y = $x[0];
$m = $x[1];
$d = $x[2];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select jobno from ".$crndb.".jobs where uid = ".$jobid);
$row = $db->single();
extract($row);

// populate day list
    $arr = array('01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31');
	$mday_options = "<option value=\"00\">00</option>";
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $d) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$mday_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate month list
    $arr = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
	$mmonth_options = "<option value=\"00\">00</option>";
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $m) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$mmonth_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate year list
	$myear_options = "<option value=\"0000\">0000</option>";
	for($i = $y - 2; $i < $y + 1; $i++)	{
			if ($i == $y) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}	
		$myear_options .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
	}

// populate operator list
	$db->query("select operator from ".$crndb.".operators order by operator");
	$rows = $db->resultset();
	// populate location list
	$operator_options = "<option value=\"\">Select Operator</option>";
	foreach ($rows as $row) {
		extract($row);
		$operator_options .= "<option value=\"".$operator."\">".$operator."</option>";
	}

// populate machine list
	$db->query("select uid,vehicleno,make,regno from ".$crndb.".vehicles order by vehicleno");
	$rows = $db->resultset();
	// populate location list
	$machine_options = "<option value=\"\">Select Vehicle/Machine</option>";
	foreach ($rows as $row) {
		extract($row);
		$mach = $vehicleno.', '.$make.', '.$regno;
		$machine_options .= "<option value=\"".$vehicleno."\">".$mach."</option>";
	}


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
		document.getElementById('addtime').submit();
	}
}

function cancel() {
	var oldwin = window.open("times.php","_self").jQuery("#timelist").trigger("reloadGrid");
	oldWin.focus();  // give focus 	  
}

</script>


</head>


<body>

<form id="addtime">
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
					<select  name="d" id="d"><?php echo $mday_options; ?></select>
				</div>
				
				<div class="form-group col-sm-1">
					<label>Month</label>
                </div>
                <div class="form-group col-sm-2">
					<select name="m" id="m"><?php echo $mmonth_options; ?></select>
				</div>
								
				<div class="form-group col-sm-1">
					<label>Year</label>
                </div>
                <div class="form-group col-sm-2">
					<select  name="y" id="y"><?php echo $myear_options; ?></select>
				</div>                
				
				<div class="form-group col-sm-4">
					<label>Start time (hh:mm 24 hr)</label>
                </div>
				<div class="form-group col-sm-2">
					<button type="button" class="btn btn-primary" onClick="sttime()">Now</button>
				</div>			
                <div class="form-group col-sm-6">
					<input type="text" name="starttime" class="form-control" id="starttime" value="">
				</div>
								
				<div class="form-group col-sm-4">
					<label>Stop time (hh:mm 24 hr)</label>
                </div>
				<div class="form-group col-sm-2">
					<button type="button" class="btn btn-primary" onClick="sptime()">Now</button>
				</div>			
                <div class="form-group col-sm-6">
					<input type="text" name="stoptime" class="form-control" id="stoptime" value="">
				</div>
				
				<div class="form-group col-sm-3">
					<label>Operator</label>
                </div>
                <div class="form-group col-sm-9">
					<select class="form-control" name="operator" id="operator" onChange="checkop(this.value)"><?php echo $operator_options; ?></select>
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
					<select class="form-control" name="machine" id="machine" onChange="checkmc(this.value)"><?php echo $machine_options; ?></select>
				</div>
                
				
				<div class="form-group col-sm-6">
				<button type="button" class="btn btn-primary" onClick="post()">Save</button>
                
 				<div class="form-group col-sm-6">
				<button type="button" class="btn btn-primary" onClick="cancel()">Cancel</button>
				</div>			
               
				</div>			
				
			</div>
	
				
		</div>
	</section>
</div>

</form>	



<?php

	if($_REQUEST['savebutton'] == "Y") {
		$jobno = $_REQUEST['jobno'];
		$cdate = $_REQUEST['y'].'-'.$_REQUEST['m'].'-'.$_REQUEST['d'];
		$starttime = $_REQUEST['starttime'];
		$stoptime = $_REQUEST['stoptime'];
		$operator = $_REQUEST['operator'];
		
		include_once("../includes/DBClass.php");
		$db = new DBClass();
		
		$db->query("insert into ".$crndb.".joblines (jobno,ddate,start,stop,operator,machine) values (:jobno,:ddate,:start,:stop,:operator,:machine)");
		$db->bind(':jobno', $jobno);
		$db->bind(':ddate', $cdate);
		$db->bind(':start', $starttime);
		$db->bind(':stop', $stoptime);
		$db->bind(':operator', $operator);
		$db->bind(':machine', $machine);
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
