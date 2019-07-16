<?php
session_start();
date_default_timezone_set($_SESSION['s_timezone']);

$crndb = $_SESSION['s_crndb'];
$cltdb = $_SESSION['s_cltdb'];
$coyid = $_SESSION['s_coyid'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select m.member_id,m.firstname,m.lastname,x.drno,x.drsub from ".$cltdb.".members m left join ".$cltdb.".client_company_xref x on m.member_id = x.client_id where x.company_id = ".$coyid." and x.drno != 0 order by lastname");
$rows = $db->resultset();
// populate location list
$client_options = "<option value=\"\">Select Client</option>";
foreach ($rows as $row) {
	extract($row);
	$clt = trim($firstname.' '.$lastname);
	$client_options .= "<option value=\"".$drno.'~'.$drsub.'~'.$clt.'~'.$member_id."\">".$clt."</option>";
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
<title>Add a Job</title>

<link href="../includes/css/bootstrap.min.css" rel="stylesheet">
<link href="../includes/css/custom.css" rel="stylesheet">
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="../includes/js/bootstrap.min.js"></script>

<script>


function post() {

	//add validation here if required.
	var jobno = document.getElementById('jobno').value;
	var client = document.getElementById('client').value;
	var cname = document.getElementById('cname').value;
	var ad1 = document.getElementById('ad1').value;
	var pcode = document.getElementById('pcode').value;
	var phone = document.getElementById('phone').value;
	var mobile = document.getElementById('mobile').value;
	var email = document.getElementById('email').value;
	
	var ok = "Y";
	if (jobno == "") {
		alert("Please enter a Job No.");
		ok = "N";
		return false;
	}
	if (client == "" && cname == "") {
		alert("Please select or create a client.");
		ok = "N";
		return false;
	}
	
	if (client == "" && (ad1 == "" && pcode == "" && phone == "" && mobile == "" && email == "")) {
		alert("Please enter some contact details.");
		ok = "N";
		return false;
	}
	
	if (ok == "Y") {
		document.getElementById('savebutton').value = "Y";
		document.getElementById('addclient').submit();
	}
}

function cancel() {
	var oldWin = window.open("updtjobs.php","_self").jQuery("#joblist").trigger("reloadGrid");
	oldWin.focus();  // give focus 	  
}

</script>


</head>


<body>

<form id="addclient">
  <input type="hidden" name="savebutton" id="savebutton" value="N">
<div class="container-fluid">
    <section class="container">
		<div class="container-page">				
			<div class="col-md-12">
				<h3 class="dark-grey">Add a Job</h3>
				
				<div class="form-group col-sm-4">
					<label>Job No.</label>
                </div>
                <div class="form-group col-sm-8">
					<input type="text" name="jobno" class="form-control" id="jobno" value="">
				</div>
				
				<div class="form-group col-sm-4">
					<label>Location</label>
                </div>
                <div class="form-group col-sm-8">
					<input type="text" name="location" class="form-control" id="location" value="">
				</div>
                
				<div class="form-group col-sm-4">
					<label>Comment</label>
                </div>
                <div class="form-group col-sm-8">
					<textarea class="form-control" rows="3" id="comment" name="comment"></textarea>
				</div>
				
				<div class="form-group col-sm-4">
					<label>Select existing Client</label>
                </div>
                <div class="form-group col-sm-8">
					<select class="form-control" name="client" id="client"><?php echo $client_options; ?></select>
				</div>
                
 				<div class="col-sm-12">
               		<h4>OR - add a new Client</h4>
                </div>
								
				<div class="form-group col-sm-4">
					<label>Name</label>
                </div>
                <div class="form-group col-sm-8">
					<input type="text" name="cname" class="form-control" id="cname" value="">
				</div>
			
				<div class="form-group col-sm-4">
					<label>Address line 1</label>
                </div>
                <div class="form-group col-sm-8">
					<input type="text" name="ad1" class="form-control" id="ad1" value="">
				</div>
			
				<div class="form-group col-sm-4">
					<label>Address line 2</label>
                </div>
                <div class="form-group col-sm-8">
					<input type="text" name="ad2" class="form-control" id="ad2" value="">
				</div>
			
				<div class="form-group col-sm-4">
					<label>Town</label>
                </div>
                <div class="form-group col-sm-8">
					<input type="text" name="town" class="form-control" id="town" value="">
				</div>
			
				<div class="form-group col-sm-4">
					<label>Post Code</label>
                </div>
                <div class="form-group col-sm-8">
					<input type="text" name="pcode" class="form-control" id="pcode" value="">
				</div>
			
				<div class="form-group col-sm-4">
					<label>Phone</label>
                </div>
                <div class="form-group col-sm-2 text-right">
                	<label>Area Code</label>
                </div>
                <div class="form-group col-sm-2">
					<input type="text" name="acode" class="form-control" id="acode" value="">
				</div>
                <div class="form-group col-sm-2 text-right">
                	<label>Number</label>
                </div>
                <div class="form-group col-sm-2">
					<input type="text" name="phone" class="form-control" id="phone" value="">
				</div>
				
				<div class="form-group col-sm-4">
					<label>Mobile</label>
                </div>
                <div class="form-group col-sm-8">
					<input type="text" name="mobile" class="form-control" id="mobile" value="">
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
		$jobno = strtoupper($_REQUEST['jobno']);
		$clt = explode('~',$_REQUEST['client']);
		$client_acno = $clt[0];
		$client_sub = $clt[1];
		$client = $clt[2];
		$memberno = $clt[3];
		$location = $_REQUEST['location'];
		$cdate = date('Y-m-d');
		$cname = $_REQUEST['cname'];
		$ad1 = $_REQUEST['ad1'];
		$ad2 = $_REQUEST['ad2'];
		$town = $_REQUEST['town'];
		$pcode = $_REQUEST['pcode'];
		$acode = $_REQUEST['acode'];
		$phone = $_REQUEST['phone'];
		$mobile = $_REQUEST['mobile'];
		$email = $_REQUEST['email'];
		$comment = $_REQUEST['comment'];
		
		
		include_once("../includes/DBClass.php");
		$db = new DBClass();
		
		// if new client, add to members, addresses, comms and cross reference record as debtor of this company
		if ($_REQUEST['client'] == '') {
			$db->query("insert into ".$cltdb.".members (lastname) values (:lastname)");
			$db->bind(':lastname', $cname);
			$db->execute();
			$memberno = $db->lastInsertId();
			$client = $cname;
			$client_acno = 30000000 + $memberno;
			$client_sub = 0;
			$ad = trim($ad1.$ad2.$town.$pcode);
			
			if ($ad != '') {
				$db->query("insert into ".$cltdb.".addresses (member_id,location,address_type_id,ad1,ad2,town,postcode,billing) values (:member_id,:location,:address_type_id,:ad1,:ad2,:town,:postcode,:billing)");
				$db->bind(':member_id',$memberno);
				$db->bind(':location', 'Street');
				$db->bind(':address_type_id', 2);
				$db->bind(':ad1', $ad1);
				$db->bind(':ad2', $ad2);
				$db->bind(':town', $town);
				$db->bind(':postcode', $pcode);
				$db->bind(':billing', 'Y');
				$db->execute();
			}
			
			if ($phone != '') {
				$c2 = str_replace(" ","",$phone);
				$db->query("insert into ".$cltdb.".comms (member_id,comms_type_id,area_code,comm,comm2) values (:member_id,:comms_type_id,:area_code,:comm,:comm2)");
				$db->bind(':member_id', $memberno);
				$db->bind(':comms_type_id', 2);
				$db->bind(':area_code', $acode);
				$db->bind(':comm', $phone);
				$db->bind(':comm2', $c2);
				$db->execute();
				
			}
			
			if ($mobile != '') {
				$c2 = str_replace(" ","",$mobile);
				$db->query("insert into ".$cltdb.".comms (member_id,comms_type_id,area_code,comm,comm2) values (:member_id,:comms_type_id,:area_code,:comm,:comm2)");
				$db->bind(':member_id', $memberno);
				$db->bind(':comms_type_id', 3);
				$db->bind(':area_code', $acode);
				$db->bind(':comm', $mobile);
				$db->bind(':comm2', $c2);
				$db->execute();
				
			}	
			
			if ($email != '') {
				$db->query("insert into ".$cltdb.".comms (member_id,comms_type_id,comm) values (:member_id,:comms_type_id,:comm)");
				$db->bind(':member_id', $memberno);
				$db->bind(':comms_type_id', 4);
				$db->bind(':comm', $email);
				$db->execute();
				
			}			
		
			// create cross reference record as debtor of this company
			$lname = str_replace(' ',"",$client);
	
			$db->query("insert into ".$cltdb.".client_company_xref (client_id,company_id,drno,sortcode,member) values (:client_id,:company_id,:drno,:sortcode,:member)");
			$db->bind(':client_id', $memberno);
			$db->bind(':company_id', $coyid);
			$db->bind(':drno', $client_acno);
			$db->bind(':sortcode', $lname.$dracno.'-0');
			$db->bind(':member', $cname);		
			$db->execute();	
		
		}
		
		$dt = date('Y-m-d');

		
		// add record to jobs
		$db->query("insert into ".$crndb.".jobs (jobno,date_created,state,client,client_accno,client_sub,location,comment) values (:jobno,:date_created,:state,:client,:client_accno,:client_sub,:location,:comment)");
		$db->bind(':jobno', $jobno);
		$db->bind(':date_created', $dt);
		$db->bind(':state', 'Open');
		$db->bind(':client', $client);
		$db->bind(':client_accno', $client_acno);
		$db->bind(':client_sub', $client_sub);
		$db->bind(':location', $location);
		$db->bind(':comment', $comment);
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
