<?php
session_start();
date_default_timezone_set($_SESSION['s_timezone']);

$crndb = $_SESSION['s_crndb'];
$findb = $_SESSION['s_findb'];
$hdate = date("Y-m-d");

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
<title>Add a Vehicle</title>
<link rel="stylesheet" type="text/css" href="../includes/flatpickr_date/dist/flatpickr.min.css">
<script src="../includes/flatpickr_date/dist/flatpickr.js"></script>

<link href="../includes/css/bootstrap.min.css" rel="stylesheet">
<link href="../includes/css/custom.css" rel="stylesheet">
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="../includes/js/bootstrap.min.js"></script>

<script>


function post() {

	//add validation here if required.
	var regno = document.getElementById('regno').value;
	var fleetno = document.getElementById('fleetno').value;
	
	var ok = "Y";
	if (regno == "") {
		alert("Please enter a Registration Plate No.");
		ok = "N";
		return false;
	}
	if (fleetno == " ") {
		alert("Please select a Fleet Number.");
		ok = "N";
		return false;
	}
	
	if (ok == "Y") {
		document.getElementById('savebutton').value = "Y";
		document.getElementById('addvehicle').submit();
	}
}

function cancel() {
	  var oldWin =window.open("updtfleet.php","fleet").jQuery("#smlist").trigger("reloadGrid");
	  oldWin.focus();  // give focus 	  
}

</script>


</head>


<body>

<form id="addvehicle">
  <input type="hidden" name="savebutton" id="savebutton" value="N">
<div class="container-fluid">
    <section class="container">
		<div class="container-page">				
			<div class="col-md-12">
				<h3 class="dark-grey">Add a Vehicle</h3>
				
				<div class="form-group col-sm-4">
					<label>Vehicle Registration No.</label>
                </div>
                <div class="form-group col-sm-8">
					<input type="text" name="regno" class="form-control" id="regno" value="">
				</div>
				
				<div class="form-group col-sm-4">
					<label>Make of Vehicle</label>
                </div>
                <div class="form-group col-sm-8">
					<input type="text" name="vmake" class="form-control" id="vmake" value="">
				</div>
								
				<div class="form-group col-sm-4">
					<label>Fleet Number</label>
                </div>
                <div class="form-group col-sm-8">
					<input type="text" name="fleetno" class="form-control" id="fleetno" value="">
				</div>
				
				<div class="form-group col-sm-4">
					<label>Hourly Charge Rate</label>
                </div>
                <div class="form-group col-sm-8">
					<input type="text" name="rate" class="form-control" id="rate" value="">
				</div>
                
				<div class="form-group col-sm-4">
					<label>Next Service due at Kms/Hrs</label>
                </div>
                <div class="form-group col-sm-8">
					<input type="text" name="service" class="form-control" id="service" value="">
				</div>
                
				<div class="form-group col-sm-4">
					<label>Next MOT due (dd/mm/yyyy)</label>
                </div>
                <div class="form-group col-sm-8">
                     <input type="Text" id="ddate" name="ddate" value="<?php echo $hdate; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j,Y">
				</div>
                
                <div class="form-group col-sm-12" >
                	<label>A Branch/Cost Centre will be automatically created in the accounts for this vehicle.</label>
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

 <script>
 	document.getElementById("ddate").flatpickr({
		onChange: function(dateObj, dateStr, instance) {
		}
	});
 </script>

</form>	



<?php

	if($_REQUEST['savebutton'] == "Y") {
		$regno = strtoupper($_REQUEST['regno']);
		$vmake = $_REQUEST['vmake'];
		$fleetno = $_REQUEST['fleetno'];
		$cdate = $_REQUEST['ddate'];
		$service = $_REQUEST['service'];
		$rate = $_REQUEST['rate'];
		
		include_once("../includes/DBClass.php");
		$db = new DBClass();
		
		$findb = $_SESSION['s_findb'];
		$brname = $fleetno;

		$db->query("insert into ".$findb.".branch (branchname) values (:branchname)");		
		$db->bind(':branchname', $brname);
		$db->execute();
		$brno = $db->lastInsertId();
		$branchcode = strval(1000 + $brno);
		
		$db->query("update ".$findb.".branch set branch = :branchcode where uid = :brno");
		$db->bind(':branchcode', $branchcode);
		$db->bind(':brno', $brno);
		$db->execute();
		
		$cc = $branchcode.'~'.$brname;
		
		// get array of system accounts
		$db->query("select grp,account,accountno,sub,blocked,ctrlacc from ".$findb.".glmast where branch = '1000' and system = 'Y'");
		$rows = $db->resultset();
	
		foreach ($rows as $row) {
			extract($row);
			$mname = $account;
			$macno = $accountno;
			$msub = $sub;
			$mblocked = $blocked;
			$mctrlacc = $ctrlacc;
			$mgrp = $grp;
			
			$db->query("insert into ".$findb.".glmast (grp,account,accountno,branch,sub,blocked,system,ctrlacc) values (:grp,:account,:accountno,:branch,:sub,:blocked,:system,:ctrlacc)");
			$db->bind(':grp', $mgrp);
			$db->bind(':account', $mname);
			$db->bind(':accountno', $macno);
			$db->bind(':branch', $branchcode);
			$db->bind(':sub', $msub);
			$db->bind(':blocked', $mblocked);
			$db->bind(':system', 'Y');
			$db->bind(':ctrlacc', $mctrlacc);
			$db->execute();
		}
		
		$db->query("select max(color) as maxcolor from ".$crndb.".vehicles");
		$row = $db->single();
		extract($row);
		if ($maxcolor < 22) {
			$newcolor = $maxcolor + 1;
		} else {
			$newcolor = 1;
		}

		$db->query("insert into ".$crndb.".vehicles (regno,cost_centre,branch,vehicleno,make,cofdate,servicedue,rate,color) values (:regno,:cost_centre,:branch,:vehicleno,:make,:cofdate,:servicedue,:rate,:color)");
		$db->bind(':regno', $regno);
		$db->bind(':cost_centre', $cc);
		$db->bind(':branch', $branchcode);
		$db->bind(':vehicleno', $fleetno);
		$db->bind(':make', $vmake);
		$db->bind(':cofdate', $cdate);
		$db->bind(':servicedue', $service);
		$db->bind(':rate', $rate);
		$db->bind(':color', $newcolor);
		$db->execute();
		
		$db->closeDB();

	  ?>
	  <script>
	  var oldWin =window.open("updtfleet.php","fleet").jQuery("#smlist").trigger("reloadGrid");
	  oldWin.focus();  // give focus 	  
	  </script>
	  <?php
		
			
	}

?>


</body>
</html>
