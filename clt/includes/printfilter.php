<?php
error_reporting(E_ALL ^ E_NOTICE); 

$mv = $_SESSION['s_mv'];

$cltdb = $_SESSION['s_cltdb'];
$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$dbp = new DBClass();

$dbp->query("select * from sessions where session = :vusersession");
$dbp->bind(':vusersession', $usersession);
$row = $dbp->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];
$usergroup = $row['usergroup'];

$mem = 'N'; //member
$add = 'N'; //address
$act = 'N'; //notes
$ass = 'N'; //associations
$cmp = 'N'; //campaigns
$com = 'N'; //comms
$cat = "N"; //accounting category

$fltfile = "ztmp".$user_id."_filterlist";
$filterfile = "ztmp".$user_id."_tempfilter";

$dbp->query("drop table if exists ".$cltdb.".".$filterfile);
$dbp->execute();
$dbp->query("drop table if exists ".$cltdb.".".$fltfile);
$dbp->execute();

$dbp->query("create table ".$cltdb.".".$filterfile." (uid integer(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, member_id int )  engine myisam");
$dbp->execute();
$dbp->query("create table ".$cltdb.".".$fltfile." (uid integer(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, member_id int, lastname varchar(45) default ' ', firstname varchar(45) default ' ', preferredname varchar(45) default ' ', address varchar(45) default ' ', suburb varchar(45) default ' ', town varchar(45) default ' ', postcode char(4) default ' ', phone varchar(25) default ' ', homephone varchar(25) default ' ', workphone varchar(25) default ' ', cellphone varchar(25) default ' ', email varchar(45) default ' ', staff varchar(45), age int(3) default 0, dob date default '0000-00-00', status varchar(20) default ' ', gender char(6) default ' ', selected char(1) default 'N')  engine myisam");
$dbp->execute();

// get member ids for chosen cover type

if(isset($_REQUEST["association"]) && $_REQUEST["association"] != ' ' ) {
	$ass = 'Y';
	$dbp->query("select member_id from ".$cltdb.".assoc_xref where association = '".$_REQUEST['association']."'");
	$rows = $dbp->resultset();
	if (count($rows) > 0) {
		foreach ($rows as $row) {
			extract($row);
			$mem = $member_id;
			$dbp->query("insert into ".$cltdb.".".$filterfile." (member_id) values (".$mem.")");
			$dbp->execute();
		}
	}
}

if(isset($_REQUEST["acat"]) && $_REQUEST["acat"] != '' ) {
	$cat = 'Y';
	$dbp->query("select member_id from ".$cltdb.".acats where category = '".$_REQUEST['acat']."'");
	$rows = $dbp->resultset();
	if (count($rows) > 0) {
		foreach ($rows as $row) {
			extract($row);
			$mem = $member_id;
			$dbp->query("insert into ".$cltdb.".".$filterfile." (member_id) values (".$mem.")");
			$dbp->execute();
		}
	}
}

if(isset($_REQUEST["workflow"]) && $_REQUEST["workflow"] != ' ' ) {
	$ass = 'Y';
	$wfsdays = $_REQUEST['wfsdays'];
	if ($wfsdays == 0) {
		$dbp->query("select member_id from ".$cltdb.".workflow_xref where process = '".$_REQUEST['workflow']."'");
	} else {
		$dbp->query("select member_id from ".$cltdb.".workflow_xref where process = '".$_REQUEST['workflow']."' and datediff(curdate(),ddate) >= ".$wfsdays);
	}
	$rows = $dbp->resultset();
	if (count($rows) > 0) {
		foreach ($rows as $row) {
			extract($row);
			$mem = $member_id;
			$dbp->query("insert into ".$cltdb.".".$filterfile." (member_id) values (".$mem.")");
			$dbp->execute();
		}
	}
}

if(isset($_REQUEST["campaign"]) && $_REQUEST["campaign"] != 0 ) {
	$cmp = 'Y';
	if ($_REQUEST["notcampaign"] == 'N') {
		$dbp->query("select member_id from ".$cltdb.".candidates where campaign_id = ".$_REQUEST['campaign']);
	} else {
		$dbp->query("select distinct member_id from ".$cltdb.".candidates where sub_id = ".$subscriber." and campaign_id != ".$_REQUEST['campaign']);
	}
	$rows = $dbp->resultset();
	if (count($rows) > 0) {
		foreach ($rows as $row) {
			$mem = $member_id;
			$dbp->query("insert into ".$cltdb.".".$filterfile." (member_id) values (".$mem.")");
			$dbp->execute();
		}
	}
}

if(isset($_REQUEST["campaignstage"]) && $_REQUEST["campaignstage"] != ' ' ) {
	$cmp = 'Y';
	if ($_REQUEST["notcampaignstage"] == 'N') {
		$dbp->query("select member_id from ".$cltdb.".candidates where workflow like '".$_REQUEST['campaignstage']."%'");
	} else {
		$dbp->query("select member_id from ".$cltdb.".candidates where workflow not like '".$_REQUEST['campaignstage']."%'");
	}
	$rows = $dbp->resultset();
	if (count($rows) > 0) {
		foreach ($rows as $row) {
			extract($row);
			$mem = $member_id;
			$dbp->query("insert into ".$cltdb.".".$filterfile." (member_id) values (".$mem.")");
			$dbp->execute();
		}
	}
}

if(isset($_REQUEST["suburb_mask"]) && $_REQUEST["suburb_mask"] != '' ) {
	$add = 'Y';
	if ($_REQUEST["notsuburb"] == 'N') {
		$dbp->query("select member_id from ".$cltdb.".addresses where suburb =  '".$_REQUEST['suburb_mask']."'");
	} else {
		$dbp->query("select member_id from ".$cltdb.".addresses where suburb != '".$_REQUEST['suburb_mask']."'");
	}
	$rows = $dbp->resultset();
	if (count($rows) > 0) {
		foreach ($rows as $row) {
			extract($row);
			$mem = $member_id;
			$dbp->query("insert into ".$cltdb.".".$filterfile." (member_id) values (".$mem.")");
			$dbp->execute();
		}
	}
}

if(isset($_REQUEST["town_mask"]) && $_REQUEST["town_mask"] != '') {
	$add = 'Y';
	if ($_REQUEST["nottown"] == 'N') {
		$dbp->query("select member_id from ".$cltdb.".addresses where town =  '".$_REQUEST['town_mask']."'");
	} else {
		$dbp->query("select member_id from ".$cltdb.".addresses where town != '".$_REQUEST['town_mask']."'");
	}
	$rows = $dbp->resultset();
	if (count($rows) > 0) {
		foreach ($rows as $row) {
			extract($row);
			$mem = $member_id;
			$dbp->query("insert into ".$cltdb.".".$filterfile." (member_id) values (".$mem.")");
			$dbp->execute();
		}
	}
}

if(isset($_REQUEST["postcode_mask"]) && $_REQUEST["postcode_mask"] != '' ) {
	$add = 'Y';
	if ($_REQUEST["notpostcode"] == 'N') {
		$dbp->query("select member_id from ".$cltdb.".addresses where postcode =  '".$_REQUEST['postcode_mask']."'");
	} else {
		$dbp->query("select member_id from ".$cltdb.".addresses where postcode != '".$_REQUEST['postcode_mask']."'");
	}
	$rows = $dbp->resultset();
	if (count($rows) > 0) {
		foreach ($rows as $row) {
			$mem = $member_id;
			$dbp->query("insert into ".$cltdb.".".$filterfile." (member_id) values (".$mem.")");
			$dbp->execute();
		}
	}
}

if(isset($_REQUEST["notestring"]) && $_REQUEST["notestring"] != '' ) {
	$act = 'Y';
	$dbp->query("select member_id from ".$cltdb.".activities where locate('".$_REQUEST['notestring']."',activity");
	$rows = $dbp->resultset();
	if (count($rows) > 0) {
		foreach ($rows as $row) {
			$mem = $member_id;
			$dbp->query("insert into ".$cltdb.".".$filterfile." (member_id) values (".$mem.")");
			$dbp->execute();
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


if(isset($_REQUEST["occupation_mask"]) && $_REQUEST["occupation_mask"] != '' ) {
	if ($_REQUEST["notoccupation"] == 'N') {
		$occupation_mask = " and occupation = '".$_REQUEST['occupation_mask']."'";
	} else {
		$occupation_mask = " and occupation ! = '".$_REQUEST['occupation_mask']."'";
	}
	$mem = 'Y';
} else {
  	$occupation_mask = "";  
}

if(isset($_REQUEST["position_mask"]) && $_REQUEST["position_mask"] != '' ) {
	if ($_REQUEST["notposition"] == 'N') {
		$position_mask = " and position = '".$_REQUEST['position_mask']."'";
	} else {
		$position_mask = " and position ! = '".$_REQUEST['position_mask']."'";
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
} else {
	$sdate_mask = "";
	$edate_mask = "";
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

$where .= $status_mask.$staff_mask.$agerange_mask.$occupation_mask.$position_mask.$checked_mask.$gender_mask.$birth_mask.$notchecked_mask.$sdate_mask.$edate_mask; 

//construct the select statement

if ($where != " where 1 = 1") {
	$sel = "select distinct members.member_id from ".$cltdb.".members";
	$SQL = $sel.$where; 
	$dbp->query($SQL);
	$rowsf = $dbp->resultset();
	foreach ($rowsf as $rowf) {
		extract($rowf);
		$mem = $member_id;
		$dbp->query("insert into ".$cltdb.".".$filterfile." (member_id) values (".$mem.")");
		$dbp->execute();
	}
}

$dbp->query("select distinct member_id from ".$cltdb.".".$filterfile);
$rows = $dbp->resultset();
if (count($rows) > 0) {
	foreach ($rows as $row) {
		extract($row);
		$mem = $member_id;
		$dbp->query("insert into ".$cltdb.".".$fltfile." (member_id) values (".$mem.")");
		$dbp->execute();
	}
}

$heading = "Filtered list ";

// the filtered data


$dbp->query("select member_id from ".$cltdb.".".$fltfile);
$rows = $dbp->resultset();
foreach ($rows as $row) {
	extract($row);
	$mem = $member_id;
	$dbp->query("select member_id as m, lastname as l, firstname as f, preferredname as p, dob as d, gender as g, status as s, staff as ma from ".$cltdb.".members where member_id = ".$mem);
	$frow = $dbp->single();
	extract($frow);
	if ($d == "0000-00-00") {
		$age = 0;
	} else {
		$age = floor((time() - strtotime($d))/31556926);
	}

	$dbp->query("update ".$cltdb.".".$fltfile." set lastname = :lastname, firstname = :firstname, preferredname = :preferredname, staff = :staff, age = :age, dob = :dob, gender = :gender, status = :status where member_id = :member_id");
	$dbp->bind(':lastname', $l);
	$dbp->bind(':firstname', $f);
	$dbp->bind(':preferredname', $p);
	$dbp->bind(':staff', $ma);
	$dbp->bind(':age', $age);
	$dbp->bind(':dob', $d);
	$dbp->bind(':gender', $g);
	$dbp->bind(':status', $s);
	$dbp->bind(':member_id', $mem);

	$dbp->execute();
}

$dbp->query("select member_id from ".$cltdb.".".$fltfile);
$rows = $dbp->resultset();
foreach ($rows as $row) {
	extract($row);
	$mem = $member_id;
	if ($mv == 'm') {
		$dbp->query("select street_no as st,ad1 as a1,ad2 as a2,suburb as s,town as t,postcode as p from ".$cltdb.".addresses where member_id = ".$mem." and preferredp = 'Y'");
	} else {
		$dbp->query("select street_no as st,ad1 as a1,ad2 as a2,suburb as s,town as t,postcode as p from ".$cltdb.".addresses where member_id = ".$mem." and preferredv = 'Y'");
	}
	$ar = $dbp->single();
	if (!empty($ar)) {
		extract($ar);
		$addr = $st.", ".$a1.", ".$a2;
		$dbp->query("update ".$cltdb.".".$fltfile." set address = :address, suburb = :suburb, town = :town, postcode = :postcode where member_id = :member_id");
		$dbp->bind(':address', $addr);
		$dbp->bind(':suburb', $s);
		$dbp->bind(':town', $t);
		$dbp->bind(':postcode', $p);
		$dbp->bind(':member_id', $mem);

		$dbp->execute();
	} else {
		$dbp->query("select distinct street_no as st,ad1 as a1,ad2 as a2,suburb as s,town as t,postcode as p from ".$cltdb.".addresses where member_id = ".$mem);
		$ar = $dbp->single();
		if (!empty($ar)) {
			extract($ar);
			$addr = $st.", ".$a1.", ".$a2;
			$dbp->query("update ".$cltdb.".".$fltfile." set address = :address, suburb = :suburb, town = :town, postcode = :postcode where member_id = :member_id");
			$dbp->bind(':address', $addr);
			$dbp->bind(':suburb', $s);
			$dbp->bind(':town', $t);
			$dbp->bind(':postcode', $p);
			$dbp->bind(':member_id', $mem);

			$dbp->execute();
		}
	}
}


$dbp->query("select member_id from ".$cltdb.".".$fltfile);
$rows = $dbp->resultset();
foreach ($rows as $row) {
	extract($row);
	$mem = $member_id;
	$dbp->query("select area_code,comm from ".$cltdb.".comms where member_id = ".$mem." and preferred = 'Y'");
	$ar = $dbp->single();
	if (!empty($ar)) {
		extract($ar);
		$ph = $area_code." ".$comm;
		$dbp->query("update ".$cltdb.".".$fltfile." set phone = :phone where member_id = :member_id");
		$dbp->bind(':phone', $ph);
		$dbp->bind(':member_id', $mem);

		$dbp->execute();
	} else {
		$dbp->query("select distinct area_code,comm from ".$cltdb.".comms where member_id = ".$mem." and comms_type_id < 4");
		$ar = $dbp->single();
		if (!empty($ar)) {
			extract($ar);
			$ph = $area_code." ".$comm;
			$dbp->query("update ".$cltdb.".".$fltfile." set phone = :phone where member_id = :member_id");
			$dbp->bind(':phone', $ph);
			$dbp->bind(':member_id', $mem);

			$dbp->execute();
		}
	}
}

// populate home, work and cell phone numbers
$dbp->query("select member_id from ".$cltdb.".".$fltfile);
$rows = $dbp->resultset();
foreach ($rows as $row) {
	extract($row);
	$mem = $member_id;
	$dbp->query("select distinct area_code,comm from ".$cltdb.".comms where member_id = ".$mem." and comms_type_id = 1");
	$ar = $dbp->single();
	if (!empty($ar)) {
		extract($ar);
		$ph = $area_code." ".$comm;
		$dbp->query("update ".$cltdb.".".$fltfile." set homephone = :homephone where member_id = :member_id");
		$dbp->bind(':homephone', $ph);
		$dbp->bind(':member_id', $mem);
		
		$dbp->execute();
	}
	$dbp->query("select distinct area_code,comm from ".$cltdb.".comms where member_id = ".$mem." and comms_type_id = 2");
	$ar = $dbp->single();
	if (!empty($ar)) {
		extract($ar);
		$ph = $area_code." ".$comm;
		$dbp->query("update ".$cltdb.".".$fltfile." set workphone = :workphone where member_id = :member_id");
		$dbp->bind(':workphone', $ph);
		$dbp->bind(':member_id', $mem);
		
		$dbp->execute();
	}
	$dbp->query("select distinct area_code,comm from ".$cltdb.".comms where member_id = ".$mem." and comms_type_id = 3");
	$ar = $dbp->single();
	if (!empty($ar)) {
		extract($ar);
		$ph = $area_code." ".$comm;
		$dbp->query("update ".$cltdb.".".$fltfile." set cellphone = :cellphone where member_id = :member_id");
		$dbp->bind(':cellphone', $ph);
		$dbp->bind(':member_id', $mem);
		
		$dbp->execute();
	}
	$dbp->query("select distinct comm from ".$cltdb.".comms where member_id = ".$mem." and comms_type_id = 4");
	$ar = $dbp->single();
	if (!empty($ar)) {
		extract($ar);
		$ph = $area_code." ".$comm;
		$dbp->query("update ".$cltdb.".".$fltfile." set email = :email where member_id = :member_id");
		$dbp->bind(':email', $ph);
		$dbp->bind(':member_id', $mem);
		
		$dbp->execute();
	}
}

$dbp->query("drop table if exists ".$cltdb.".".$filterfile);
$dbp->execute();

$dbp->closeDB();

?>