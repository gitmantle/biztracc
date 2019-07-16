<?php
session_start();
date_default_timezone_set($_SESSION['s_timezone']);

$crndb = $_SESSION['s_crndb'];
$cltdb = $_SESSION['s_cltdb'];
$coyid = $_SESSION['s_coyid'];

if(isset($_REQUEST['id'])) {
	$id = $_REQUEST['id'];
	$_SESSION['s_jid'] = $id;
} else {
	$id = $_SESSION['s_jid'];
}

$hdate = date("Y-m-d");

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from ".$crndb.".jobs where uid = ".$id);
$row = $db->single();
extract($row);
$op = $operator;
$mc = $machine;
$startdate = substr($starttime,0,10);
$finishdate = substr($endtime,0,10);

if ($startdate == '0000-00-00') {
	$x = explode('-',$hdate);
	$sy = $x[0];
	$sm = $x[1];
	$sd = $x[2];	
} else {
	$x = explode('-',$startdate);
	$sy = $x[0];
	$sm = $x[1];
	$sd = $x[2];	
}


$x = explode('-',$finishdate);
$fy = $x[0];
if ($fy == '0000') {
	$fy = date('Y');
}
$fm = $x[1];
$fd = $x[2];	

// populate start day list
    $arr = array('01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31');
	$sday_options = "<option value=\"00\">00</option>";
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $sd) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$sday_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate start month list
    $arr = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
	$smonth_options = "<option value=\"00\">00</option>";
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $sm) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$smonth_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate start year list
	$syear_options = "<option value=\"0000\">0000</option>";
	for($i = $sy - 2; $i < $sy + 5; $i++)	{
			if ($i == $sy) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}	
		$syear_options .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
	}

// populate finish day list
    $arr = array('01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31');
	$fday_options = "<option value=\"00\">00</option>";
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $fd) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$fday_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate finish month list
    $arr = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
	$fmonth_options = "<option value=\"00\">00</option>";
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $fm) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$fmonth_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate finish year list
	$fyear_options = "<option value=\"0000\">0000</option>";
	for($i = $fy - 2; $i < $fy + 5; $i++)	{
			if ($i == $fy) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}	
		$fyear_options .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
	}

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
	$db->query("select uid,vehicleno,make,regno,color from ".$crndb.".vehicles order by vehicleno");
	$rows = $db->resultset();
	$machine_options = "<option value=\"\">Select Vehicle/Machine</option>";
	foreach ($rows as $row) {
		extract($row);
		$mach = $vehicleno.', '.$make.', '.$regno;
			if ($vehicleno == $mc) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}		
		$machine_options .= '<option value="'.$vehicleno.'~'.$color.'"'.$selected.'>'.$mach.'</option>';
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
<title>Edit a Job</title>

<link href="../includes/css/bootstrap.min.css" rel="stylesheet">
<link href="../includes/css/custom.css" rel="stylesheet">
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="../includes/js/bootstrap.min.js"></script>

<script>


function post() {

	//add validation here if required.
	
	var ok = "Y";
	
	if (ok == "Y") {
		document.getElementById('savebutton').value = "Y";
		document.getElementById('editjob').submit();
	}
}

function cancel() {
	var oldWin = window.open("updtjobs.php","_self").jQuery("#joblist").trigger("reloadGrid");
	oldWin.focus();  // give focus 	  
}

</script>


</head>


<body>

<form id="editjob">
  <input type="hidden" name="savebutton" id="savebutton" value="N">
<div class="container-fluid">
    <section class="container">
		<div class="container-page">				
			<div class="col-md-12">
				<h3 class="dark-grey">Edit a Job</h3>
				
				<div class="form-group col-sm-4">
					<label>Job No.</label>
                </div>
                <div class="form-group col-sm-8">
					<input type="text" name="jobno" class="form-control" id="jobno" value="<?php echo $jobno; ?>" readonly>
				</div>
				
				<div class="form-group col-sm-4">
					<label>Location</label>
                </div>
                <div class="form-group col-sm-8">
					<input type="text" name="location" class="form-control" id="location" value="<?php echo $location; ?>">
				</div>
                
				<div class="form-group col-sm-4">
					<label>Comment</label>
                </div>
                <div class="form-group col-sm-8">
					<textarea class="form-control" rows="3" id="comment" name="comment"><?php echo $comment; ?></textarea>
				</div>
				
				<div class="form-group col-sm-4">
					<label>Client</label>
                </div>
                <div class="form-group col-sm-8">
					<input type="text" name="client" class="form-control" id="client" value="<?php echo $client; ?>" readonly>
				</div>
                
				<div class="form-group col-sm-4">
					<label>Date Created</label>
                </div>
                <div class="form-group col-sm-8">
					<input type="text" name="ddate" class="form-control" id="ddate" value="<?php echo $date_created; ?>" readonly>
				</div>
			
				<div class="form-group col-sm-4">
					<label>Operator/Driver</label>
                </div>
                <div class="form-group col-sm-8">
					<select class="form-control" name="operator" id="operator" ><?php echo $operator_options; ?></select>
				</div>
			
				<div class="form-group col-sm-4">
					<label>Machine/Vehicle</label>
                </div>
                <div class="form-group col-sm-8">
					<select class="form-control" name="machine" id="machine" ><?php echo $machine_options; ?></select>
				</div>
                
 				<div class="form-group col-sm-3">
					<label>Start Date</label>
                </div>
                
				<div class="form-group col-sm-1">
					<label>Day</label>
                </div>
                <div class="form-group col-sm-2">
					<select  name="sd" id="sd"><?php echo $sday_options; ?></select>
				</div>
				
				<div class="form-group col-sm-1">
					<label>Month</label>
                </div>
                <div class="form-group col-sm-2">
					<select name="sm" id="sm"><?php echo $smonth_options; ?></select>
				</div>
								
				<div class="form-group col-sm-1">
					<label>Year</label>
                </div>
                <div class="form-group col-sm-2">
					<select  name="sy" id="sy"><?php echo $syear_options; ?></select>
				</div>                    
                
				<div class="form-group col-sm-3">
					<label>Finish Date</label>
                </div>
                
				<div class="form-group col-sm-1">
					<label>Day</label>
                </div>
                <div class="form-group col-sm-2">
					<select  name="fd" id="fd"><?php echo $fday_options; ?></select>
				</div>
				
				<div class="form-group col-sm-1">
					<label>Month</label>
                </div>
                <div class="form-group col-sm-2">
					<select name="fm" id="fm"><?php echo $fmonth_options; ?></select>
				</div>
								
				<div class="form-group col-sm-1">
					<label>Year</label>
                </div>
                <div class="form-group col-sm-2">
					<select  name="fy" id="fy"><?php echo $fyear_options; ?></select>
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
		
		$startdate = $_REQUEST['sy'].'-'.$_REQUEST['sm'].'-'.$_REQUEST['sd'];
		$finishdate = $_REQUEST['fy'].'-'.$_REQUEST['fm'].'-'.$_REQUEST['fd'];
		$operator = $_REQUEST['operator'];
		$m = explode('~',$_REQUEST['machine']);
		$machine = $m[0];
		$color = $m[1];
		$location = $_REQUEST['location'];
		$subject = $jobno.' - '.$machine.' - '.$operator.' - '.$location;
		$description = $jobno.' - '.$machine.' - '.$operator.' - '.$location;
		$comment = $_REQUEST['comment'];
		
		include_once("../includes/DBClass.php");
		$db = new DBClass();
		
		$db->query("update ".$crndb.".jobs set location = :location, starttime = :startdate, endtime = :finishdate, operator = :operator, machine = :machine, subject = :subject, description = :description, isalldayevent = :isalldayevent, color = :color, comment = :comment where uid = :uid");
		$db->bind(':location', $location);
		$db->bind(':startdate', $startdate);
		$db->bind(':finishdate', $finishdate);
		$db->bind(':operator', $operator);
		$db->bind(':machine', $machine);
		$db->bind(':subject', $subject);
		$db->bind(':description', $description);
		$db->bind(':isalldayevent', 1);
		$db->bind(':color', $color);
		$db->bind(':comment', $comment);
		$db->bind(':uid', $id);
		$db->execute();
		
		$db->closeDB();

	  ?>
	  <script>
		var oldWin = window.open("updtjobs.php","_self").jQuery("#joblist").trigger("reloadGrid");
		oldWin.focus();  // give focus 	  
	  </script>
	  <?php
		
			
	}

?>


</body>
</html>
