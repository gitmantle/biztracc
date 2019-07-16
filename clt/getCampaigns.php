<?php
session_start();
//error_reporting(0);
$usersession = $_SESSION['usersession'];

$ddate = date("d/m/Y");
$hdate = date("Y-m-d");

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

$cltdb = $_SESSION['s_cltdb'];

if(isset($_REQUEST["nm_mask"])) {
	$nm_mask = $_REQUEST['nm_mask'];
} else {
  	$nm_mask = ""; 
}

//construct where clause 
$where = " where 1 = 1";
if($nm_mask!='') {
	$where.= " AND name LIKE '".$nm_mask."%'"; 
}

$tcommsfile = "ztmp".$user_id."_campfile";

$db->query("drop table if exists ".$cltdb.".".$tcommsfile);
$db->execute();
$db->query("create table ".$cltdb.".".$tcommsfile." (campaign_id int(11) default 0,name varchar(45) default '',startdate date,staff varchar(45) default '',description text, goals text, complete decimal(5,2) default 0)  engine myisam");
$db->execute();

$db->query("select campaign_id as campid,name as n,startdate as s,staff as a,description as d,goals as g from ".$cltdb.".campaigns ".$where);
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	$db->query('insert into '.$cltdb.'.'.$tcommsfile.' (campaign_id,name,startdate,staff,description,goals) values ('.$campid.',"'.$n.'","'.$s.'","'.$a.'","'.$d.'","'.$g.'")');
	$db->execute();
}

$db->query("select campaign_id from ".$cltdb.".".$tcommsfile);
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	$cid = $campaign_id;
	$db->query("select count(candstatus) as numcomplete from ".$cltdb.".candidates where campaign_id = ".$cid." and candstatus = 'Complete'");
	$row = $db->single();
	extract($row);
	$db->query("select count(candstatus) as totcands from ".$cltdb.".candidates where campaign_id = ".$cid);
	$row = $db->single();
	extract($row);
	if ($totcands == 0) {
		$compcent = 0;
	} else {
		$compcent = $numcomplete/$totcands*100;
	}
	$db->query("update ".$cltdb.".".$tcommsfile." set complete = ".$compcent." where campaign_id = ".$cid);
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
$grid->SelectCommand = "select campaign_id,name,startdate,staff,description,goals,complete from ".$cltdb.".".$tcommsfile;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getCampaigns.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Campaigns",
    "rowNum"=>12,
    "sortname"=>"name",
    "rowList"=>array(12,30,50),
	"height"=>300,
	"width"=>970
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("campaign_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("name", array("label"=>"Name", "width"=>120));
$grid->setColProperty("startdate", array("label"=>"Start Date", "width"=>80, "formatter"=>"date"));
$grid->setColProperty("staff", array("label"=>"Represented by", "width"=>90));
$grid->setColProperty("description", array("label"=>"Description", "width"=>250));
$grid->setColProperty("goals", array("label"=>"Goals", "width"=>250));
$grid->setColProperty("complete", array("label"=>"% Complete", "width"=>75));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>125));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#campaignlist").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var rowd = $("#campaignlist").getRowData(ids[i]);
		var cl = ids[i]; 
		var ug = $usergroup;
		if (ug >= 18) {
		  be = '<img src="../images/edit.png" title="Edit" onclick="javascript:editcampaign('+cl+')" >'; 
		  pe = '<img src="../images/people.png" title="Administer Candidates" onclick="javascript:admincand('+cl+')" >'; 
		  dc = '<img src="../images/docs.png" title="Update Documents" onclick="javascript:docs('+cl+')" >'; 
		  ct = '<img src="../images/costs.png" title="Update Costs" onclick="javascript:costs('+cl+')" >'; 
		  se = '<img src="../images/delete.png" title="Delete" onclick="javascript:delcampaign('+cl+')" >'; 
		  jQuery("#campaignlist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;'+pe+'&nbsp;&nbsp;&nbsp;'+dc+'&nbsp;&nbsp;&nbsp;'+ct+'&nbsp;&nbsp;&nbsp;'+se}); 
		} else {
		  be = '<img src="../images/edit.png" title="Edit" onclick="javascript:editcampaign('+cl+')" >'; 
		  dc = '<img src="../images/docs.png" title="Update Documents" onclick="javascript:docs('+cl+')" >'; 
		  ct = '<img src="../images/costs.png" title="Update Costs" onclick="javascript:costs('+cl+')" >'; 
		  se = '<img src="../images/delete.png" title="Delete" onclick="javascript:delcampaign('+cl+')" >'; 
		  jQuery("#campaignlist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;'+dc+'&nbsp;&nbsp;&nbsp;'+ct+'&nbsp;&nbsp;&nbsp;'+se}); 
			
		}
	} 
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

$grid->gSQLMaxRows = 1000;

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#campaignpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a Campaign", "onClickButton"=>"js: function(){addcampaign();}")
);
$grid->callGridMethod("#campaignlist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#campaignlist','#campaignpager',true, null, null, true,true);

?>

