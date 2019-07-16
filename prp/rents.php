<?php
session_start();
date_default_timezone_set($_SESSION['s_timezone']);
$usersession = $_SESSION['usersession'];

$findb = $_SESSION['s_findb'];
$hdate = date("Y-m-d");
$x = explode('-',$hdate);
$y = $x[0];
$m = $x[1];
$d = $x[2];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$trans = 'ztmp'.$user_id.'_trans';

$findb = $_SESSION['s_findb'];

$sql = "drop table if exists ".$findb.".".$trans;
$db->query($sql);
$db->execute();

$sql = "create table ".$findb.".".$trans." ( uid int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, acc2dr int(11) default 0,subdr int(11) default 0,brdr char(4) default '',drindex int(10) default 0,acc2cr int(11) default 0,subcr int(11) default 0,brcr char(4)default '',crindex int(10) default 0,ddate date default '0000-00-00',descript1 varchar(60),reference char(9) default '',refindex int(10) default 0,amount double(16,2) default 0,depdr int(11),depbrdr char(4),depcr int(11),depbrcr char(4),nallocate int(11),tax double(16,2),taxtype char(3),taxpcent double(5,2),applytax char(1),total double(16,2) default 0, done int(11) default 0,type char(1),grn char(10),inv char(10),currency char(3) default '', rate double(7,3) default 1,a2d varchar(45),a2c varchar(45),taxindex int(10),drgst char(1) default 'N', crgst char(1) default 'N',your_ref varchar(30) default '')  engine myisam";
$db->query($sql);
$db->execute();


$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$table = 'ztmp'.$user_id.'_properties';

$sql = "drop table if exists ".$findb.".".$table;
$db->query($sql);
$db->execute();

$sql = "create table ".$findb.".".$table." ( uid int(11)  NOT NULL AUTO_INCREMENT PRIMARY KEY, accountno int(11), branch char(4), sub int(11), account varchar(30))  engine innodb";
$db->query($sql);
$db->execute();

$db->query("select accountno,branch,sub,account from ".$findb.".glmast where accountno = 1 and blocked = 'N' and branch > '1001' order by branch,sub");
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	$db->query("insert into ".$findb.".".$table." (accountno,branch,sub,account) values (:accountno,:branch,:sub,:account)");
	$db->bind(':accountno', $accountno);
	$db->bind(':branch', $branch);
	$db->bind(':sub', $sub);
	$db->bind(':account', $account);
	$db->execute();
}

$db->query("select * from ".$findb.".".$table);
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	$id = $uid;
	if ($account == 'RENT RECEIVED') {
		$db->query("select branchname from ".$findb.".branch where branch = '".$branch."'");
		$row = $db->single();
		extract($row);
		$db->query("update ".$findb.".".$table." set account = '".$branchname."' where uid = ".$id);
		$db->execute();
	}
}

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

//populate property list
	$db->query("select accountno,branch,sub,account from ".$findb.".".$table." order by uid");
	$rows = $db->resultset();
	$property_options = '<option value=" ">Select Property</option>';
	foreach ($rows as $row) {
		extract($row);
		$selected = '';
		$property_options .= '<option value="'.$accountno.'~'.$branch.'~'.$sub.'"'.$selected.'>'.$account.'</option>';
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
<title>Rents</title>

<link href="../includes/css/bootstrap.min.css" rel="stylesheet">
<link href="../includes/css/custom.css" rel="stylesheet">
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="../includes/js/bootstrap.min.js"></script>

<script>


function post() {

	//add validation here if required.
	var property = document.getElementById('property').value;
	var amount = document.getElementById('amount').value;
	
	var ok = "Y";
	if (property == " ") {
		alert("Please select a Property.");
		ok = "N";
		return false;
	}
	if (amount == "0") {
		alert("Please enter an amount.");
		ok = "N";
		return false;
	}
	
	
	if (ok == "Y") {
		
		jQuery.ajaxSetup({async:false});
		
		var ref = 'rec';
		var reference = '';
		$.get("../fin/includes/ajaxGetRef.php", {ref: ref}, function(data){
				var r = data;
				reference = 'REC'+r;
		});	
		var p = document.getElementById('property').value;
		var ps = p.split('~');
	
		var paymethod = "eft";
		var a2dr = 751;
		var s2dr = 0;
		var b2dr = '1000';
		var a2cr = ps[0];
		var b2cr = ps[1];
		var s2cr = ps[2];
		
		var ddate = document.getElementById('y').value + '-' + document.getElementById('m').value + '-' + document.getElementById('d').value;
		var description = document.getElementById('comment').value;
		var taxpcent = 0;
		var tax = 0;
		var taxtype = 'N-T';
		var total = amount;
		var refindex = 0;
		var taxindex = 0;
		var n2dr = 0;
		var n2cr = 0;
		var drgst = 'N';
		var crgst = 'N';
		var fxcode = 'GBP';
		var fxrate = 1;
		var yourref = ' ';
		
		// code to populate relevant variables and post to trmain.
		$.get("../fin/includes/ajaxAddTrans.php", {acc2dr:a2dr, subdr:s2dr, brdr:b2dr, acc2cr:a2cr, subcr:s2cr, brcr:b2cr, ddate:ddate, descript1:description, reference:reference, amount:amount, taxpcent:taxpcent, tax:tax, taxtype:taxtype, total:total, refindex:refindex, taxindex:taxindex, a2d:n2dr, a2c:n2cr, drgst:drgst, crgst:crgst, fxcode:fxcode, fxrate:fxrate, yourref:yourref}, function(data){});
		$.get("../fin/includes/ajaxPostTrans.php", {paymethod:paymethod}, function(data){});
		
		  var oldWin = window.open("index.php");
		  window.open("index.php","_self");
		  this.close();
		  oldWin.focus();  // give focus 	  
		
		jQuery.ajaxSetup({async:true});		
		
	}
}


</script>


</head>


<body>

<form id="rents">
<div class="container-fluid">
    <section class="container">
		<div class="container-page">				
			<div class="col-md-12">
				<h3 class="dark-grey">Rent Received</h3>
				
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
					<label>Rent received for property</label>
                </div>
                <div class="form-group col-sm-8">
					<select class="form-control" name="property" id="property"><?php echo $property_options; ?></select>
				</div>
                
				<div class="form-group col-sm-4">
					<label>Amount received</label>
                </div>
                <div class="form-group col-sm-8">
					<input type="text" name="amount" class="form-control" id="amount" value="0" onFocus=" this.select();" >
				</div>
                
				<div class="form-group col-sm-2">
					<label>Comment</label>
                </div>
                <div class="form-group col-sm-10">
					<input type="text" name="comment" class="form-control" id="comment" value="">
				</div>
                
                
				
				<div class="form-group col-sm-12">
				<button type="button" class="btn btn-primary" onClick="post()">Save</button>
				</div>			
				
			</div>
	
				
		</div>
	</section>
</div>

</form>	


</body>
</html>
