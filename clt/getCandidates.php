<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];
date_default_timezone_set($_SESSION['s_timezone']);

$cltdb = $_SESSION['s_cltdb'];

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

$db->closeDB();

$campid = $_SESSION['s_campid'];
$campname = $_SESSION['s_campname'];

if (isset($_REQUEST['campid'])) {
	$campid = $_REQUEST['campid'];
}


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
$grid->SelectCommand = "select member_id,candidate_id,lastname,firstname,preferred,staff,suburb,workflow,candstatus from ".$cltdb.".candidates where campaign_id = ".$campid;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getCandidates.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
	"caption"=>"Candidates for Campaign - ".$campname,						
    "rowNum"=>6,
    "sortname"=>"lastname",
    "rowList"=>array(6,30,50),
	"height"=>135,
	"width"=>1000
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("member_id", array("label"=>"Member", "width"=>20, "hidden"=>true));
$grid->setColProperty("candidate_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("lastname", array("label"=>"Last Name", "width"=>170));
$grid->setColProperty("firstname", array("label"=>"First Name", "width"=>150));
$grid->setColProperty("preferred", array("label"=>"Preferred", "width"=>150));
$grid->setColProperty("staff", array("label"=>"Dealt with by", "width"=>150));
$grid->setColProperty("suburb", array("label"=>"Suburb", "width"=>150));
$grid->setColProperty("workflow", array("label"=>"Campaign Stage", "width"=>195));
$grid->setColProperty("candstatus", array("label"=>"Status", "width"=>70));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>70));


$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#candlist").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var cl = ids[i]; 
		var rowdata = $("#candlist").getRowData(cl);
		var cand_id = rowdata.candidate_id;
		var ug = $usergroup;
		if (ug > 18) {
			be = '<img src="../images/play.png" title="Work with Candidate" onclick="javascript:candidate('+cl+','+cand_id+')" >'; 
			de = '<img src="../images/delete.png" title="Delete Candidate" onclick="javascript:delcand('+cl+','+cand_id+')" >'; 
			jQuery("#candlist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+de}); 
		} else {
			be = '<img src="../images/play.png" title="Work with Candidate" onclick="javascript:candidate('+cl+','+cand_id+')" >'; 
			jQuery("#candlist").setRowData(ids[i],{act:be}); 
		}
	} 
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

$dcevent = <<<DBLCLICK
function(rowid) {
	var rowdata = $("#candlist").getRowData(rowid);
	var memberid = rowdata.member_id;
	editmem(memberid);
}
DBLCLICK;

$grid->setGridEvent('ondblClickRow',$dcevent);


$grid->gSQLMaxRows = 1000;

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function
//$buttonoptions = array("#candpager",
//    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a Candidate", "onClickButton"=>"js: function(){addcand();}")
//);
//$grid->callGridMethod("#candlist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#candlist','#candpager',true, null, null, true,true);


?>

