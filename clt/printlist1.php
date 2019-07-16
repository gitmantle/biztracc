<?php
//error_reporting(0); 
session_start();
$usersession = $_SESSION['usersession'];

$ddate = date("d/m/Y");
$hdate = date("Y-m-d");

$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$userip = $userip;
$userid = $user_id;
$pic = $userid.'pic.jpg';
$subscriber = $sub_id;

$mv = $_REQUEST['mv'];

$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select campaign_id,name from campaigns";
$r = mysql_query($q) or die (mysql_error().$q);
$camp_options = '<option value=" ">Select Campaign</option>';
while ($row = mysql_fetch_array($r)) {
	extract($row);
	$camp_options .= '<option value="'.$campaign_id.'" >'.$name.'</option>';
}

$fltfile = "ztmp".$user_id."_filterlist";
$filterfile = "ztmp".$user_id."_tempfilter";

$query = "drop table if exists ".$fltfile;
$result = mysql_query($query) or die(mysql_error().$query);
$query = "drop table if exists ".$filterfile;
$result = mysql_query($query) or die(mysql_error().$query);

$query = "create table ".$filterfile." (uid integer(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, member_id int )  engine myisam";
$calc = mysql_query($query) or die(mysql_error().$query);
$query = "create table ".$fltfile." (uid integer(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, member_id int, lastname varchar(45) default ' ', firstname varchar(45) default ' ', preferredname varchar(45) default ' ', address varchar(45) default ' ', suburb varchar(45) default ' ', town varchar(45) default ' ', postcode char(4) default ' ', phone varchar(25) default ' ', homephone varchar(25) default ' ', workphone varchar(25) default ' ', cellphone varchar(25) default ' ', email varchar(45) default ' ', staff varchar(45), age int(3) default 0, dob date default '0000-00-00', status varchar(20) default ' ',gender char(6) default ' ')  engine myisam";

$calc = mysql_query($query) or die(mysql_error().$query);



$mem = 'N'; //member
$add = 'N'; //address
$act = 'N'; //notes
$ass = 'N'; //association
$flt = ""; //heading
$cmps = 'N'; //campaign stages
$cmp = 'N'; //campaigns
$ncmp = 'N'; //not campaign
$camp = 'N'; // campaign for $SQL
$com = 'N'; //comms

// get member ids for chosen cover type

if(isset($_REQUEST["association"]) && $_REQUEST["association"] != ' ' ) {
	$ass = 'Y';
	$query = "select member_id from assoc_xref where association = '".$_REQUEST['association']."'";
	$result = mysql_query($query);
	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result)) {
			extract($row);
			$mem = $member_id;
			$q = "insert into ".$filterfile." (member_id) values (".$mem.")";
			$r = mysql_query($q) or die(mysql_error().$q);
		}
	}
}

if(isset($_REQUEST["acat"]) && $_REQUEST["acat"] != '' ) {
	$cat = 'Y';
	$query = "select member_id from acats where category = '".$_REQUEST['acat']."'";
	$result = mysql_query($query);
	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result)) {
			extract($row);
			$mem = $member_id;
			$q = "insert into ".$filterfile." (member_id) values (".$mem.")";
			$r = mysql_query($q) or die(mysql_error().$q);
		}
	}
}

if(isset($_REQUEST["workflow"]) && $_REQUEST["workflow"] != ' ' ) {
	$ass = 'Y';
	$wfsdays = $_REQUEST['wfsdays'];
	if ($wfsdays == 0) {
		$query = "select member_id from workflow_xref where process = '".$_REQUEST['workflow']."'";
	} else {
		$query = "select member_id from workflow_xref where process = '".$_REQUEST['workflow']."' and datediff(curdate(),ddate) >= ".$wfsdays;
	}
	$result = mysql_query($query);
	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result)) {
			extract($row);
			$mem = $member_id;
			$q = "insert into ".$filterfile." (member_id) values (".$mem.")";
			$r = mysql_query($q) or die(mysql_error().$q);
		}
	}
}

if(isset($_REQUEST["campaign"]) && $_REQUEST["campaign"] != 0 ) {
	$cmp = 'Y';
	if ($_REQUEST["notcampaign"] == 'N') {
		$query = "select member_id from candidates where campaign_id = ".$_REQUEST['campaign'];
	} else {
		$query = "select distinct member_id from candidates where sub_id = ".$subscriber." and campaign_id != ".$_REQUEST['campaign'];
	}
	$result = mysql_query($query) or die(mysql_error().' '.$query);

	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result)) {
			extract($row);
			$mem = $member_id;
			$q = "insert into ".$filterfile." (member_id) values (".$mem.")";
			$r = mysql_query($q) or die(mysql_error().$q);
		}
	}
}

if(isset($_REQUEST["campaignstage"]) && $_REQUEST["campaignstage"] != ' ' ) {
	$cmp = 'Y';
	if ($_REQUEST["notcampaignstage"] == 'N') {
		$query = "select member_id from candidates where workflow like '".$_REQUEST['campaignstage']."%'";
	} else {
		$query = "select member_id from candidates where workflow not like '".$_REQUEST['campaignstage']."%'";
	}
	$result = mysql_query($query) or die(mysql_error().' '.$query);
	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result)) {
			extract($row);
			$mem = $member_id;
			$q = "insert into ".$filterfile." (member_id) values (".$mem.")";
			$r = mysql_query($q) or die(mysql_error().$q);
		}
	}
}

if(isset($_REQUEST["suburb_mask"]) && $_REQUEST["suburb_mask"] != '' ) {
	$add = 'Y';
	if ($_REQUEST["notsuburb"] == 'N') {
		$query = "select member_id from addresses where suburb =  '".$_REQUEST['suburb_mask']."'";
	} else {
		$query = "select member_id from addresses where suburb != '".$_REQUEST['suburb_mask']."'";
	}
	$result = mysql_query($query) or die(mysql_error().' '.$query);
	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result)) {
			extract($row);
			$mem = $member_id;
			$q = "insert into ".$filterfile." (member_id) values (".$mem.")";
			$r = mysql_query($q) or die(mysql_error().$q);
		}
	}
}

if(isset($_REQUEST["town_mask"]) && $_REQUEST["town_mask"] != '') {
	$add = 'Y';
	if ($_REQUEST["nottown"] == 'N') {
		$query = "select member_id from addresses where town =  '".$_REQUEST['town_mask']."'";
	} else {
		$query = "select member_id from addresses where town != '".$_REQUEST['town_mask']."'";
	}
	$result = mysql_query($query) or die(mysql_error().' '.$query);
	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result)) {
			extract($row);
			$mem = $member_id;
			$q = "insert into ".$filterfile." (member_id) values (".$mem.")";
			$r = mysql_query($q) or die(mysql_error().$q);
		}
	}
}

if(isset($_REQUEST["postcode_mask"]) && $_REQUEST["postcode_mask"] != '' ) {
	$add = 'Y';
	if ($_REQUEST["notpostcode"] == 'N') {
		$query = "select member_id from addresses where postcode =  '".$_REQUEST['postcode_mask']."'";
	} else {
		$query = "select member_id from addresses where postcode != '".$_REQUEST['postcode_mask']."'";
	}
	$result = mysql_query($query) or die(mysql_error().' '.$query);
	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result)) {
			extract($row);
			$mem = $member_id;
			$q = "insert into ".$filterfile." (member_id) values (".$mem.")";
			$r = mysql_query($q) or die(mysql_error().$q);
		}
	}
}

if(isset($_REQUEST["notestring"]) && $_REQUEST["notestring"] != '' ) {
	$act = 'Y';
	$query = "select member_id from activities where locate('".$_REQUEST['notestring']."',activity";
	$result = mysql_query($query) or die(mysql_error().' '.$query);
	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result)) {
			extract($row);
			$mem = $member_id;
			$q = "insert into ".$filterfile." (member_id) values (".$mem.")";
			$r = mysql_query($q) or die(mysql_error().$q);
		}
	}
}





// sort out the rest of the filters


if(isset($_REQUEST["industry"]) && $_REQUEST["industry"] != 0 ) {
	if ($_REQUEST["notindustry"] == 'N') {
		$industry_mask = " and industry_id = '".$_REQUEST['industry']."'";
	} else {
		$industry_mask = " and industry_id != '".$_REQUEST['industry']."'";
	}
	$add = 'Y';
} else {
  	$industry_mask = "";  
}

if(isset($_REQUEST["status_mask"]) && $_REQUEST["status_mask"] != ' ' ) {
	if ($_REQUEST["notstatus"] == 'N') {
		$status_mask = " and members.status = '".$_REQUEST['status_mask']."'";
	} else {
		$status_mask = " and members.status != '".$_REQUEST['status_mask']."'";
	}
	$mem = 'Y';
} else {
  	$status_mask = ""; 
}

if(isset($_REQUEST["staff"]) && $_REQUEST["staff"] != 0 ) {
	$staff_mask = " and staff != '".$_REQUEST['staff']."'";
	$mem = 'Y';
} else {
  	$staff_mask = ""; 
}


if(isset($_REQUEST["dto"]) && $_REQUEST["dto"] != 0 ) {
	if ($_REQUEST["notage"] == 'N') {
		$agerange_mask = " and (year( dob ) >= (year( now( ) ) -".$_REQUEST["dto"].") and year( dob ) <= (year( now( ) ) -".$_REQUEST["dfrom"]."))";
	} else {
		$agerange_mask = " and (year( dob ) < (year( now( ) ) -".$_REQUEST["dto"].") or year( dob ) > (year( now( ) ) -".$_REQUEST["dfrom"]."))";
	}
	$mem = 'Y';
} else {
  	$agerange_mask = "";  
}


if(isset($_REQUEST["occupation"]) && $_REQUEST["occupation"] != '' ) {
	if ($_REQUEST["notoccupation"] == 'N') {
		$occupation_mask = " and occupation = '".$_REQUEST['occupation']."'";
	} else {
		$occupation_mask = " and occupation ! = '".$_REQUEST['occupation']."'";
	}
	$mem = 'Y';
} else {
  	$occupation_mask = "";  
}

if(isset($_REQUEST["position"]) && $_REQUEST["position"] != '' ) {
	if ($_REQUEST["notposition"] == 'N') {
		$position_mask = " and position = '".$_REQUEST['position']."'";
	} else {
		$position_mask = " and position ! = '".$_REQUEST['position']."'";
	}
	$mem = 'Y';
} else {
  	$position_mask = "";  
}

if(isset($_REQUEST["gender"]) && $_REQUEST["gender"] != ' ' ) {
	$gender_mask = " and gender = '".$_REQUEST['gender']."'";
	$mem = 'Y';
} else {
  	$gender_mask = "";  
}

if(isset($_REQUEST["birthmonth"]) && $_REQUEST["birthmonth"] != '00' ) {
	$bm = $_REQUEST['birthmonth'];
	$birth_mask = " and substring(dob,6,2) = ".$bm;
	$mem = 'Y';
} else {
  	$birth_mask = "";  
}

if((isset($_REQUEST["sdate"]) && $_REQUEST["sdate"] != '') && (isset($_REQUEST["edate"]) && $_REQUEST["edate"] != '') ) {
	if(isset($_REQUEST["sdate"]) && $_REQUEST["sdate"] != '0000-00-00' ) {
		$sdate_mask = " and commenced >= '".$_REQUEST['sdate']."'";
		$mem = 'Y';
	} else {
		$sdate_mask = "";  
	}
	if(isset($_REQUEST["edate"]) && $_REQUEST["edate"] != '0000-00-00' ) {
		$edate_mask = " and commenced <= '".$_REQUEST['edate']."'";
		$mem = 'Y';
	} else {
		$edate_mask = "";  
	}
}

if(isset($_REQUEST["recchecked"]) && $_REQUEST["recchecked"] == 'Y' ) {
	$checked_mask = " and checked = 'Yes'";
	$mem = 'Y';
} else {
  	$checked_mask = "";  
}

if(isset($_REQUEST["notchecked"]) && $_REQUEST["notchecked"] == 'Y' ) {
	$notchecked_mask = " and checked != 'Yes'";
	$mem = 'Y';
} else {
  	$notchecked_mask = "";  
}

//construct where clause 

$where = " where 1 = 1"; 

$where .= $suburb_mask.$town_mask.$postcode_mask.$status_mask.$staff_mask.$agerange_mask.$occupation_mask.$position_mask.$checked_mask.$gender_mask.$birth_mask.$notchecked_mask.$note_mask.$sdate_mask.$edate_mask; 


//construct the select statement

if ($where != " where 1 = 1") {
	$sel = "select distinct members.member_id from members";
	
	$SQL = $sel.$where; 
	$result = mysql_query( $SQL ) or die("Couldn t execute query.".mysql_error()." - ".$SQL);
	while ($row = mysql_fetch_array($result)) {
		extract($row);
		$mem = $member_id;
		$q = "insert into ".$filterfile." (member_id) values (".$mem.")";
		$r = mysql_query($q) or die(mysql_error().$q);
	}
}

$query = "select distinct member_id from ".$filterfile;
$result = mysql_query($query);
if (mysql_num_rows($result) > 0) {
	while ($row = mysql_fetch_array($result)) {
		extract($row);
		$mem = $member_id;
		$q = "insert into ".$fltfile." (member_id) values (".$mem.")";
		$r = mysql_query($q) or die(mysql_error().$q);
	}
}

$heading = "Filtered list ";

// the filtered data

$query = "drop table if exists ".$filterfile;
$result = mysql_query($query) or die(mysql_error());

$query = "select member_id from ".$fltfile;
$result = mysql_query($query) or die(mysql_error().$query);
while ($row = mysql_fetch_array($result)) {
	extract($row);
	$mem = $member_id;
	$fquery = "select member_id as m, lastname as l, firstname as f, preferredname as p, dob as d, gender as g, status as s, staff as ma from members where member_id = ".$mem;
	$fresult = mysql_query($fquery) or die(mysql_error().$fquery);
	$frow = mysql_fetch_array($fresult);
	extract($frow);
	if ($d == "0000-00-00") {
		$age = 0;
	} else {
		$age = floor((time() - strtotime($d))/31556926);
	}

	$q = "update ".$fltfile." set lastname = '".mysql_real_escape_string($l)."',firstname = '".mysql_real_escape_string ($f)."',preferredname = '".mysql_real_escape_string ($p)."',staff = '".mysql_real_escape_string ($ma)."',age = ".$age.",dob = '".$d."',gender = '".$g."',status = '".$s."' where member_id = ".$mem;
	$r = mysql_query($q) or die(mysql_error().$q);
}

$query = "select member_id from ".$fltfile;
$result = mysql_query($query) or die(mysql_error().$query);
while ($row1 = mysql_fetch_array($result)) {
	extract($row1);
	$mem = $member_id;
	if ($mv == 'm') {
		$aq = "select street_no as st,ad1 as a1,ad2 as a2,suburb as s,town as t,postcode as p from addresses where member_id = ".$mem." and preferredp = 'Y'";
	} else {
		$aq = "select street_no as st,ad1 as a1,ad2 as a2,suburb as s,town as t,postcode as p from addresses where member_id = ".$mem." and preferredv = 'Y'";
	}
	$ar = mysql_query($aq) or die(mysql_error().$aq);
	if (mysql_num_rows($ar) > 0) {
		$arow = mysql_fetch_array($ar);
		extract($arow);
		$addr = $st.", ".$a1.", ".$a2;
		$q = "update ".$fltfile." set address = '".mysql_real_escape_string ($addr)."', suburb = '".mysql_real_escape_string ($s)."', town = '".mysql_real_escape_string ($t)."', postcode = '".$p."' where member_id = ".$mem;
		$r = mysql_query($q) or die(mysql_error().$q);
	} else {
		$aq = "select distinct street_no as st,ad1 as a1,ad2 as a2,suburb as s,town as t,postcode as p from addresses where member_id = ".$mem;
		$ar = mysql_query($aq) or die(mysql_error().$aq);
		if (mysql_num_rows($ar) > 0) {
			$arow = mysql_fetch_array($ar);
			extract($arow);
			$addr = $st.", ".$a1.", ".$a2;
			$q = "update ".$fltfile." set address = '".mysql_real_escape_string ($addr)."', suburb = '".mysql_real_escape_string ($s)."', town = '".mysql_real_escape_string ($t)."', postcode = '".$p."' where member_id = ".$mem;
			$r = mysql_query($q) or die(mysql_error().$q);
		}
	}
}


$query = "select member_id from ".$fltfile;
$result = mysql_query($query) or die(mysql_error().$query);
while ($row = mysql_fetch_array($result)) {
	extract($row);
	$mem = $member_id;
	$aq = "select area_code,comm from comms where member_id = ".$mem." and preferred = 'Y'";
	$ar = mysql_query($aq) or die(mysql_error().$aq);
	if (mysql_num_rows($ar) > 0) {
		$arow = mysql_fetch_array($ar);
		extract($arow);
		$ph = mysql_real_escape_string ($area_code." ".$comm);
		$q = "update ".$filterfile." set phone = '".$ph."' where member_id = ".$mem;
		$r = mysql_query($q) or die(mysql_error().$q);
	} else {
		$aq = "select distinct area_code,comm from comms where member_id = ".$mem." and comms_type_id < 4";
		$ar = mysql_query($aq) or die(mysql_error().$aq);
		if (mysql_num_rows($ar) > 0) {
			$arow = mysql_fetch_array($ar);
			extract($arow);
			$ph = mysql_real_escape_string ($area_code." ".$comm);
			$q = "update ".$fltfile." set phone = '".$ph."' where member_id = ".$mem;
			$r = mysql_query($q) or die(mysql_error().$q);
		}
	}
}

// populate home, work and cell phone numbers
$query = "select member_id from ".$fltfile;
$result = mysql_query($query) or die(mysql_error().$query);
while ($row = mysql_fetch_array($result)) {
	extract($row);
	$mem = $member_id;
	$aq = "select distinct area_code,comm from comms where member_id = ".$mem." and comms_type_id = 1";
	$ar = mysql_query($aq) or die(mysql_error().$aq);
	if (mysql_num_rows($ar) > 0) {
		$arow = mysql_fetch_array($ar);
		extract($arow);
		$ph = mysql_real_escape_string ($area_code." ".$comm);
		$q = "update ".$fltfile." set homephone = '".$ph."' where member_id = ".$mem;
		$r = mysql_query($q) or die(mysql_error().$q);
	}
	$aq = "select distinct area_code,comm from comms where member_id = ".$mem." and comms_type_id = 2";
	$ar = mysql_query($aq) or die(mysql_error().$aq);
	if (mysql_num_rows($ar) > 0) {
		$arow = mysql_fetch_array($ar);
		extract($arow);
		$ph = mysql_real_escape_string ($area_code." ".$comm);
		$q = "update ".$fltfile." set workphone = '".$ph."' where member_id = ".$mem;
		$r = mysql_query($q) or die(mysql_error().$q);
	}
	$aq = "select distinct area_code,comm from comms where member_id = ".$mem." and comms_type_id = 3";
	$ar = mysql_query($aq) or die(mysql_error().$aq);
	if (mysql_num_rows($ar) > 0) {
		$arow = mysql_fetch_array($ar);
		extract($arow);
		$ph = mysql_real_escape_string ($area_code." ".$comm);
		$q = "update ".$fltfile." set cellphone = '".$ph."' where member_id = ".$mem;
		$r = mysql_query($q) or die(mysql_error().$q);
	}
	$aq = "select distinct comm from comms where member_id = ".$mem." and comms_type_id = 4";
	$ar = mysql_query($aq) or die(mysql_error().$aq);
	if (mysql_num_rows($ar) > 0) {
		$arow = mysql_fetch_array($ar);
		extract($arow);
		$ph = mysql_real_escape_string ($comm);
		$q = "update ".$fltfile." set email = '".$ph."' where member_id = ".$mem;
		$r = mysql_query($q) or die(mysql_error().$q);
	}
}

$ddate = date("d/m/Y");

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Filtered List</title>
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript">

window.name = "filteredlist";

var filterfile = "<?php echo $fltfile; ?>";

function viewmem(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editmember.php?uid='+uid,'vmem','toolbar=0,scrollbars=1,height=780,width=1140,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}


  function list2pdf() {
  var heading = '<?php echo $heading; ?>';
  var x = 0, y = 0; // default values	
  x = window.screenX +5;
  y = window.screenY +265;	
	  window.open('list2pdf.php?filterfile='+filterfile+'&heading='+heading,'listpdf'+filterfile,'toolbar=0,scrollbars=1,height=500,width=1020,resizable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
  }


  function xl7() {
  var heading = '<?php echo $heading; ?>';
  var x = 0, y = 0; // default values	
  x = window.screenX +5;
  y = window.screenY +265;	
	  window.open('xl_maillist.php?filterfile='+filterfile+'&heading='+heading,'listxl'+filterfile+'&gen='+7,'toolbar=0,scrollbars=1,height=500,width=1020,resizable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
  }

function trash() {
	var aleads = jQuery("#memlistf").getGridParam('selarrrow');	
	var num = aleads.length;	
	var astring = aleads.toString();
	if (num > 0) {
		$.get("includes/ajaxDelFilter.php", {astring: astring, from: 'p'}, function(data){$("#memlistf").trigger("reloadGrid")});		
	}
}


function xl3() {
  var heading = '<?php echo $heading; ?>';
  var x = 0, y = 0; // default values	
  x = window.screenX +5;
  y = window.screenY +265;	
	  window.open('xl_maillist.php?filterfile='+filterfile+'&heading='+heading,'listxl'+filterfile+'&gen='+3,'toolbar=0,scrollbars=1,height=500,width=1020,resizable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addcandidates() {
  var camp_id = document.getElementById('campid').value;
  if (camp_id == 0) {
	alert('Please choose a campaign first');
	return false;
  }

  var x = 0, y = 0; // default values	
  x = window.screenX +5;
  y = window.screenY +265;	
	window.open('add2camp.php?filterfile='+filterfile+'&camp_id='+camp_id,'a2c','toolbar=0,scrollbars=1,height=10,width=10,resizable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}



function addconsol() {
	 if (confirm("Are you sure you want to add these members to the consolidated list?")) {
		$.get("includes/ajaxaddconsol.php");																	
	  }
}



function viewconsol() {
  var x = 0, y = 0; // default values	
  x = window.screenX +5;
  y = window.screenY +165;	
	consolidated_list = window.open("viewconsolidated.php","conlist","toolbar=0,scrollbars=1,height=540,width=1010,resizable=0,left="+x+",screenX="+x+",top="+y+",screenY="+y);
}


</script>
</head>
<body>
<table align="left" border="0" cellspacing="0" cellpadding="0" bgcolor="<?php echo $bgcolor; ?>">
  <tr>
    <td colspan="6" align="left"><label style="color: <?php echo $tdfont; ?>; font-size: 14px;"><?php echo $heading; ?></label></td>
    <td align="left"><input type="button" name="bconsolidate" id="bconsolidate" value="Add to Consolidated List" onclick="addconsol()"/>
      &nbsp;
      <input type="button" name="bconsolidate" id="bconsolidate" value="View Consolidated List" onclick="viewconsol();"/></td>
  </tr>
  <tr>
    <td colspan="7"><?php include "getFilteredList.php" ?></td>
  </tr>
  <tr>
    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Add as Candidates to &nbsp;</label></td>
    <td><select name="campid" id="campid">
        <?php echo $camp_options;?>
      </select></td>
    <td><input type="button" name="btncamp" id="btncamp" value="Add" onclick="addcandidates();" /></td>
    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
    <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Send to ... </label></td>
    <td><input name="bpdf" type="button" value="PDF" onclick="list2pdf()" /></td>
    <td>&nbsp;</td>
  </tr>
</table>
</body>
</html>