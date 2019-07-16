<?php
session_start();
date_default_timezone_set($_SESSION['s_timezone']);

$crndb = $_SESSION['s_crndb'];
$findb = $_SESSION['s_findb'];

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

<link href="../includes/css/bootstrap.min.css" rel="stylesheet">
<link href="../includes/css/custom.css" rel="stylesheet">
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="../includes/js/bootstrap.min.js"></script>

<script>


function post() {

	//add validation here if required.
	var op = document.getElementById('operator').value;
	
	var ok = "Y";
	if (op == "") {
		alert("Please enter a Driver/Operator name");
		ok = "N";
		return false;
	}
	
	if (ok == "Y") {
		document.getElementById('savebutton').value = "Y";
		document.getElementById('addoperator').submit();
	}
}

function cancel() {
	var oldWin = window.open("updtoperators.php","operators").jQuery("#oplist").trigger("reloadGrid");
	oldWin.focus();  // give focus 	  
}

</script>


</head>


<body>

<form id="addoperator">
  <input type="hidden" name="savebutton" id="savebutton" value="N">
<div class="container-fluid">
    <section class="container">
		<div class="container-page">				
			<div class="col-md-12">
				<h3 class="dark-grey">Add Driver/Operator</h3>
				
				<div class="form-group col-sm-4">
					<label>Name</label>
                </div>
                <div class="form-group col-sm-8">
					<input type="text" name="operator" class="form-control" id="operator" value="">
				</div>
				
				<div class="form-group col-sm-4">
					<label>Address</label>
                </div>
                <div class="form-group col-sm-8">
					<input type="text" name="ad" class="form-control" id="ad" value="">
				</div>
								
				<div class="form-group col-sm-4">
					<label>Hourly Charge Rate</label>
                </div>
                <div class="form-group col-sm-8">
					<input type="text" name="rate" class="form-control" id="rate" value="">
				</div>
                
				<div class="form-group col-sm-4">
					<label>Phone</label>
                </div>
                <div class="form-group col-sm-8">
					<input type="text" name="phone" class="form-control" id="phone" value="">
				</div>
				
				<div class="form-group col-sm-4">
					<label>Mobile</label>
                </div>
                <div class="form-group col-sm-8">
					<input type="text" name="mob" class="form-control" id="mob" value="">
				</div>
                
				<div class="form-group col-sm-4">
					<label>Email</label>
                </div>
                <div class="form-group col-sm-8">
					<input type="text" name="email" class="form-control" id="email" value="">
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
		$operator = $_REQUEST['operator'];
		$address = $_REQUEST['ad'];
		$phone = $_REQUEST['phone'];
		$mobile = $_REQUEST['mob'];
		$email = $_REQUEST['email'];
		$rate = $_REQUEST['rate'];
		
		include_once("../includes/DBClass.php");
		$db = new DBClass();

		$db->query("insert into ".$crndb.".operators (operator,address,phone,mobile,email,rate) values (:operator,:address,:phone,:mobile,:email,:rate)");
		$db->bind(':operator', $operator);
		$db->bind(':address', $address);
		$db->bind(':phone', $phone);
		$db->bind(':mobile', $mobile);
		$db->bind(':email', $email);
		$db->bind(':rate', $rate);
		$db->execute();
		
		$db->closeDB();

	  ?>
	  <script>
	  var oldWin = window.open("updtoperators.php","operators").jQuery("#oplist").trigger("reloadGrid");
	  oldWin.focus();  // give focus 	  
	  </script>
	  <?php
		
			
	}

?>


</body>
</html>
