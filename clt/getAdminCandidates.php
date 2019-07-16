<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];
date_default_timezone_set($_SESSION['s_timezone']);

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

$campid = $_SESSION['s_campid'];
$campname = $_SESSION['s_campname'];

$cltdb = $_SESSION['s_cltdb'];

if (isset($_REQUEST['campid'])) {
	$campid = $_REQUEST['campid'];
}

if(isset($_REQUEST["advisor"]) && $_REQUEST["advisor"] != '' ) {
	$advisor_mask = " and staff = '".$_REQUEST['advisor']."'";
	
	$x = 'Y';
	
} else {
  	$advisor_mask = "";  
}
if(isset($_REQUEST["stage"]) && $_REQUEST["stage"] != '' ) {
	$stage_mask = " and workflow = '".$_REQUEST['stage']."'";
} else {
  	$stage_mask = "";  
}
if(isset($_REQUEST["status"]) && $_REQUEST["status"] != '' ) {
	$status_mask = " and candstatus = '".$_REQUEST['status']."'";
} else {
  	$status_mask = "";  
}

$where = " where campaign_id = ".$campid;
$where .= $advisor_mask.$stage_mask.$status_mask; 

$filterfile = "ztmp".$user_id."_adcamp";

$db->query("drop table if exists ".$cltdb.".".$filterfile);
$db->execute();

$db->query("create table ".$cltdb.".".$filterfile." (uid integer(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, member_id int, candidate_id int(11) default 0,lastname varchar(45) default '',firstname varchar(45) default '', preferredname varchar(45) default '', staff varchar(45) default '', suburb varchar(45) default '', workflow varchar(50) default '',status varchar(45) default '')  engine myisam");
$db->execute();

$db->query("select member_id as mid,candidate_id as cid,lastname as lname,firstname as fname,preferred as pname,staff as adv,suburb as sb,workflow as wf,candstatus as cs from ".$cltdb.".candidates ".$where);
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	$db->query('insert into '.$cltdb.'.'.$filterfile.'(member_id,candidate_id,lastname,firstname,preferredname,staff,suburb,workflow,status) values ('.$mid.','.$cid.',"'.$lname.'","'.$fname.'","'.$pname.'","'.$adv.'","'.$sb.'","'.$wf.'","'.$cs.'")');
	$db->execute();
}

$db->closeDB();

include 'jq-config.php';
// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);

// Create the jqGrid instance
$grid = new jqGridRender($conn);
// the actual query for the grid data
$grid->SelectCommand = "select member_id,candidate_id,lastname,firstname,preferredname,staff,suburb,workflow,status from ".$cltdb.".".$filterfile;

// Set the table to where you add the data
//$grid->table = $filterfile; 

//$grid->setPrimaryKeyId('member_id');

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getAdminCandidates.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
	"caption"=>"Candidates for Campaign - ".$campname,						
    "rowNum"=>15,
    "sortname"=>"lastname",
    "rowList"=>array(15,30,50),
	"height"=>350,
	"width"=>1000
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("member_id", array("label"=>"Member", "width"=>20, "hidden"=>true));
$grid->setColProperty("candidate_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("lastname", array("label"=>"Last Name", "width"=>170));
$grid->setColProperty("firstname", array("label"=>"First Name", "width"=>150));
$grid->setColProperty("preferredname", array("label"=>"Preferred", "width"=>150));
$grid->setColProperty("staff", array("label"=>"Staff", "width"=>150));
$grid->setColProperty("suburb", array("label"=>"Suburb", "width"=>150));
$grid->setColProperty("workflow", array("label"=>"Campaign Stage", "width"=>195));
$grid->setColProperty("status", array("label"=>"Status", "width"=>70));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>90));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#adcandlist").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var cl = ids[i]; 
		var rowdata = $("#adcandlist").getRowData(cl);
		var cand_id = rowdata.candidate_id;
		var ug = $usergroup;
		if (ug > 18) {
			be = '<img src="../images/edit.png" title="Edit Member" onclick="javascript:editmem('+cl+')" ></ids>';
			de = '<img src="../images/delete.png" title="Delete Candidate from this Campaign" onclick="javascript:delcand('+cl+','+cand_id+')" >'; 
			sd = '<img src="../images/sysdelete.png" title="Completely Delete Candidate from System" onclick="javascript:delmem('+cl+','+cand_id+')" >'; 
			jQuery("#adcandlist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;&nbsp;'+de+'&nbsp;&nbsp;&nbsp;&nbsp;'+sd}); 
		} else {
			be = '<img src="../images/play.png" title="Work with Candidate" onclick="javascript:candidate('+cl+','+cand_id+')" >'; 
			jQuery("#adcandlist").setRowData(ids[i],{act:be}); 
		}
	} 
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);


$dcevent = <<<DBLCLICK
function(rowid) {
	var rowdata = $("#adcandlist").getRowData(rowid);
	var memberid = rowdata.member_id;
	editmem(memberid);
}
DBLCLICK;

$grid->setGridEvent('ondblClickRow',$dcevent);


$grid->gSQLMaxRows = 1000;

// enable form editing
$grid->navigator = true;
$grid->setNavOptions('navigator',array('add'=>false,'edit'=>false,'del'=>false));

// Run the script
$grid->renderGrid('#adcandlist','#adcandpager',true, null, null, true,true);

?>

