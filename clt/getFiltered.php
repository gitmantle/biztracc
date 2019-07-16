<?php
session_start();
$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
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

$cltdb = $_SESSION['s_cltdb'];

$filterfile = "ztmp".$user_id."_tempfilter";
$fltfile = "ztmp".$user_id."_filter";

$db->query("drop table if exists ".$cltdb.".".$filterfile);
$db->execute();
$db->query("drop table if exists ".$cltdb.".".$fltfile);
$db->execute();

$db->query("create table ".$cltdb.".".$filterfile." (uid integer(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, member_id int )  engine myisam");
$db->execute();
$db->query("create table ".$cltdb.".".$fltfile." (uid integer(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, member_id int )  engine myisam");
$db->execute();

if(isset($_REQUEST["association"]) && $_REQUEST["association"] != ' ' ) {
	$ass = 'Y';
	$db->query("select member_id from ".$cltdb.".assoc_xref where association = '".$_REQUEST['association']."'");
	$rows = $db->resultset();
	if (count($rows) > 0) {
		foreach ($rows as $row) {
			extract($row);
			$mem = $member_id;
			$db->query("insert into ".$cltdb.".".$filterfile." (member_id) values (".$mem.")");
			$db->execute();
		}
	}
}

if(isset($_REQUEST["acat"]) && $_REQUEST["acat"] != '' ) {
	$cat = 'Y';
	$db->query("select member_id from ".$cltdb.".acats where category = '".$_REQUEST['acat']."'");
	$rows = $db->resultset();
	if (count($rows) > 0) {
		foreach ($rows as $row) {
			extract($row);
			$mem = $member_id;
			$db->query("insert into ".$cltdb.".".$filterfile." (member_id) values (".$mem.")");
			$db->execute();
		}
	}
}

if(isset($_REQUEST["workflow"]) && $_REQUEST["workflow"] != ' ' ) {
	$ass = 'Y';
	$wfsdays = $_REQUEST['wfsdays'];
	if ($wfsdays == 0) {
		$db->query("select member_id from ".$cltdb.".workflow_xref where process = '".$_REQUEST['workflow']."'");
	} else {
		$db->query("select member_id from ".$cltdb.".workflow_xref where process = '".$_REQUEST['workflow']."' and datediff(curdate(),ddate) >= ".$wfsdays);
	}
	$rows = $db->resultset();
	if (count($rows) > 0) {
		foreach ($rows as $row) {
			extract($row);
			$mem = $member_id;
			$db->query("insert into ".$cltdb.".".$filterfile." (member_id) values (".$mem.")");
			$db->execute();
		}
	}
}

if(isset($_REQUEST["clienttype"]) && $_REQUEST["clienttype"] != ' ' ) {
	$ass = 'Y';
	$db->query("select member_id from ".$cltdb.".clienttype_xref where client_type = '".$_REQUEST['clienttype']."'");
	$rows = $db->resultset();
	if (count($rows) > 0) {
		foreach ($rows as $row) {
			extract($row);
			$mem = $member_id;
			$db->query("insert into ".$cltdb.".".$filterfile." (member_id) values (".$mem.")");
			$db->execute();
		}
	}
}

if(isset($_REQUEST["campaign"]) && $_REQUEST["campaign"] != 0 ) {
	$cmp = 'Y';
	if ($_REQUEST["notcampaign"] == 'N') {
		$db->query("select member_id from ".$cltdb.".candidates where campaign_id = ".$_REQUEST['campaign']);
	} else {
		$db->query("select distinct member_id from ".$cltdb.".candidates where sub_id = ".$subscriber." and campaign_id != ".$_REQUEST['campaign']);
	}
	$rows = $db->resultset();
	if (count($rows) > 0) {
		foreach ($rows as $row) {
			extract($row);
			$mem = $member_id;
			$db->query("insert into ".$cltdb.".".$filterfile." (member_id) values (".$mem.")");
			$db->execute();
		}
	}
}

if(isset($_REQUEST["campaignstage"]) && $_REQUEST["campaignstage"] != ' ' ) {
	$cmp = 'Y';
	if ($_REQUEST["notcampaignstage"] == 'N') {
		$db->query("select member_id from ".$cltdb.".candidates where workflow like '".$_REQUEST['campaignstage']."%'");
	} else {
		$db->query("select member_id from ".$cltdb.".candidates where workflow not like '".$_REQUEST['campaignstage']."%'");
	}
	$rows = $db->resultset();
	if (count($rows) > 0) {
		foreach ($rows as $row) {
			extract($row);
			$mem = $member_id;
			$db->query("insert into ".$cltdb.".".$filterfile." (member_id) values (".$mem.")");
			$db->execute();
		}
	}
}

if(isset($_REQUEST["suburb_mask"]) && $_REQUEST["suburb_mask"] != '' ) {
	$add = 'Y';
	if ($_REQUEST["notsuburb"] == 'N') {
		$db->query("select member_id from ".$cltdb.".addresses where suburb =  '".$_REQUEST['suburb_mask']."'");
	} else {
		$db->query("select member_id from ".$cltdb.".addresses where suburb != '".$_REQUEST['suburb_mask']."'");
	}
	$rows = $db->resultset();
	if (count($rows) > 0) {
		foreach ($rows as $row) {
			extract($row);
			$mem = $member_id;
			$db->query("insert into ".$cltdb.".".$filterfile." (member_id) values (".$mem.")");
			$db->execute();
		}
	}
}

if(isset($_REQUEST["town_mask"]) && $_REQUEST["town_mask"] != '') {
	$add = 'Y';
	if ($_REQUEST["nottown"] == 'N') {
		$db->query("select member_id from ".$cltdb.".addresses where town =  '".$_REQUEST['town_mask']."'");
	} else {
		$db->query("select member_id from ".$cltdb.".addresses where town != '".$_REQUEST['town_mask']."'");
	}
	$rows = $db->resultset();
	if (count($rows) > 0) {
		foreach ($rows as $row) {
			extract($row);
			$mem = $member_id;
			$db->query("insert into ".$cltdb.".".$filterfile." (member_id) values (".$mem.")");
			$db->execute();
		}
	}
}

if(isset($_REQUEST["postcode_mask"]) && $_REQUEST["postcode_mask"] != '' ) {
	$add = 'Y';
	if ($_REQUEST["notpostcode"] == 'N') {
		$db->query("select member_id from ".$cltdb.".addresses where postcode =  '".$_REQUEST['postcode_mask']."'");
	} else {
		$db->query("select member_id from ".$cltdb.".addresses where postcode != '".$_REQUEST['postcode_mask']."'");
	}
	$rows = $db->resultset();
	if (count($rows) > 0) {
		foreach ($rows as $row) {
			extract($row);
			$mem = $member_id;
			$db->query("insert into ".$cltdb.".".$filterfile." (member_id) values (".$mem.")");
			$db->execute();
		}
	}
}

if(isset($_REQUEST["notestring"]) && $_REQUEST["notestring"] != '' ) {
	$act = 'Y';
	$db->query("select member_id from ".$cltdb.".activities where locate('".$_REQUEST['notestring']."',activity");
	$rows = $db->resultset();
	if (count($rows) > 0) {
		foreach ($rows as $row) {
			extract($row);
			$mem = $member_id;
			$db->query("insert into ".$cltdb.".".$filterfile." (member_id) values (".$mem.")");
			$db->execute();
		}
	}
}

if ($cat == 'Y' || $ass == 'Y' || $cmp == 'Y' || $add == 'Y' || $act == 'Y') {
	$db->query("select distinct member_id from ".$cltdb.".".$filterfile);
	$rows = $db->resultset();
	if (count($rows) > 0) {
		foreach ($rows as $row) {
			extract($row);
			$mem = $member_id;
			$db->query("insert into ".$cltdb.".".$fltfile." (member_id) values (".$mem.")");
			$db->execute();
		}
	}
}

$db->query("drop table if exists ".$cltdb.".".$filterfile);
$db->execute();

// sort out the rest of the filters

if(isset($_REQUEST["industry"]) && $_REQUEST["industry"] != 0 ) {
	if ($_REQUEST["notindustry"] == 'N') {
		$industry_mask = " and industry_id = ".$_REQUEST['industry'];
	} else {
		$industry_mask = " and industry_id != ".$_REQUEST['industry'];
	}
	$mem = 'Y';
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

if(isset($_REQUEST["staff"]) && $_REQUEST["staff"] != '0' ) {
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
	$birth_mask = " and substring(dob,6,2) = '".$bm."'";
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

$where = " WHERE 1 = 1"; 

// check if $fltfile is empty. If so do not include reference to it
	$flt = 'Y';
	$db->query("select count( uid ) as tot from ".$cltdb.".".$fltfile);
	$row = $db->single();
	extract($row);	
	if ($tot == 0) {
		$flt = 'N';
	}

if (($cat == 'Y' || $ass == 'Y' || $cmp == 'Y' || $add == 'Y' || $act == 'Y') && $flt == 'Y') {
	$where .= " and members.member_id = ".$cltdb.".".$fltfile.".member_id";
}

/*
echo 'w1 '.$where.' ** ';

echo ' status '.$status_mask.' - ';
echo ' staff '.$staff_mask.' - ';
echo ' agerange '.$agerange_mask.' - ';
echo ' industry '.$industry_mask.' - ';
echo ' occupation '.$occupation_mask.' - ';
echo ' checked '.$checked_mask.' - ';
echo ' gender '.$gender_mask.' - ';
echo ' birth '.$birth_mask.' - ';
echo ' notchecked '.$notchecked_mask.' - ';
echo ' sdate '.$sdate_mask.' - ';
echo ' edate '.$edate_mask.' - ';
*/


$where .= $status_mask.$staff_mask.$agerange_mask.$industry_mask.$occupation_mask.$position_mask.$checked_mask.$gender_mask.$birth_mask.$notchecked_mask.$sdate_mask.$edate_mask; 

//construct the select statement

$sel = "select members.member_id,members.firstname,members.lastname,members.preferredname,members.status,members.dob,members.checked from ".$cltdb.".members";

if (($cat == 'Y' || $ass == 'Y' || $cmp == 'Y' || $add == 'Y' || $act == 'Y') && $flt == 'Y') {
	$sel .= ",".$cltdb.".".$fltfile;
}

$db->closeDB();

include 'jq-config.php';
//require_once "jq-config.php"
// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");


// Create the jqGrid instance
$grid = new jqGridRender($conn);

// Write the SQL Query
// the actual query for the grid data
$grid->SelectCommand = $sel.$where;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel();

// Set the url from where we obtain the data
$grid->setUrl('getFiltered.php');

// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Members and Addresses",
    "rowNum"=>12,
    "sortname"=>"lastname",
    "rowList"=>array(12,30,50),
	"height"=>280,
	"width"=>560
    ));

$grid->addCol(array("name"=>"act"));

// Change some property of the field(s)
$grid->setColProperty("member_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>70));
$grid->setColProperty("checked", array("label"=>"Checked", "width"=>20, "hidden"=>true));
$grid->setColProperty("lastname", array("label"=>"Last Name", "width"=>210));
$grid->setColProperty("firstname", array("label"=>"First Name", "width"=>100));
$grid->setColProperty("preferredname", array("label"=>"Preferred", "width"=>75));
$grid->setColProperty("dob", array("label"=>"Age", "width"=>30));

// point to Age column to use the function named getAge
$grid->setColProperty('dob', array('formatter'=>'js:getAge'));

$getAge = <<<GETAGE
function getAge (cellValue, options, rowdata) 
{
	var dt = cellValue.split('-');
	var by = dt[0];
	var bm = dt[1];
	var bd = dt[2]; 
	var d = new Date();
	var curr_day = d.getDate();
	var curr_month = d.getMonth()+1;
	var curr_year = d.getFullYear();
	var birthYear = parseInt(by);
	var birthMonth = parseInt(bm);
	var birthDay = parseInt(bd);
	var age = curr_year - birthYear;

	if (curr_month < birthMonth || (curr_month == birthMonth && curr_day < birthDay)) {
		age = age - 1;
	}

	if(by == '0000') {
		age = 0;
	}
	return age;
}

GETAGE;
$grid->setJSCode($getAge); 

$grid->setSubGrid("getAdClientNS.php",
        array('Type', 'Addr_1', 'Addr_2', 'Suburb', 'Town'),
        array(50,120,110,110,90),
        array('left','left','left','left','left'));

// on select row we should post the member id to second table and trigger it to reload the data
$selectcomms = <<<COMMS

function(rowid, selected)
{
    if(rowid != null) {
        jQuery("#commslist").jqGrid('setGridParam',{postData:{memid:rowid}});
        jQuery("#commslist").trigger("reloadGrid");
    }
}

COMMS;
$grid->setGridEvent('onSelectRow', $selectcomms);

// We should clear the grid data on second grid on sorting, paging, etc.
$cleargrid = <<<CLEAR

function(rowid, selected)
{
   // clear the grid data and footer data
   jQuery("#commslist").jqGrid('clearGridData',true);
}
CLEAR;

$grid->setGridEvent('onPaging', $cleargrid);
$grid->setGridEvent('onSortCol', $cleargrid);

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#memlist2").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#memlist2").getRowData(ids[i]);
			var chk = rowd.checked;
			var cl = ids[i];
			be = '<img src="../images/edit.png" title="Edit Member" onclick="javascript:editmem('+cl+')" ></ids>';
			if (chk == 'Yes') {
				ck = '<img src="../images/tick.png" title="Checked">'; 
			} else {
				ck = '<img src="../images/close.png" title="Not Checked">'; 
			}
			se = '<img src="../images/delete.png" title="Delete Member" onclick="javascript:delmem('+cl+')" >'; 
			jQuery("#memlist2").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp'+ck+'&nbsp;&nbsp;&nbsp;'+se}); 
	}
}

LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

//$grid->gSQLMaxRows = 2000;

// enable form editing
$grid->navigator = true;
$grid->setNavOptions('navigator',array('add'=>false,'edit'=>false,'del'=>false,'excel'=>false));

// Run the script
$grid->renderGrid('#memlist2','#mempager2',true, null, null, true,true);


?>



