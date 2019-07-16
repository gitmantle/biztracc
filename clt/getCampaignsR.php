<?php
session_start();
//ini_set('display_errors', true);
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

if(isset($_REQUEST["nm_mask"])) {
	session_start();
	$nm_mask = $_REQUEST['nm_mask'];
} else {
  	$nm_mask = ""; 
}

$cltdb = $_SESSION['s_cltdb'];

//construct where clause 
	$where = "WHERE 1 = 1";
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
	$db->query("insert into ".$cltdb.".".$tcommsfile." (campaign_id,name,startdate,staff,description,goals) values (".$campid.",'".$n."','".$s."','".$a."','".$d."','".$g."')");
	$db->execute();
}

$db->query("select campaign_id from ".$cltdb.".".$tcommsfile);
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	$cid = $campaign_id;
	$db->query("select count(candstatus) as numcomplete from ".$cltdb.".candidates where campaign_id = ".$cid." and candstatus = 'Complete'");
	$rs = $db->single();
	extract($rs);
	$db->query("select count(candstatus) as totcands from ".$cltdb.".candidates where campaign_id = ".$cid);
	$qs = $db->single();
	extract($qs);
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
$grid->setUrl('getCampaignsR.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Campaigns",
    "rowNum"=>17,
    "sortname"=>"name",
    "rowList"=>array(17,30,50),
	"height"=>380,
	"width"=>900
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("campaign_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("name", array("label"=>"Name", "width"=>120));
$grid->setColProperty("startdate", array("label"=>"Start Date", "width"=>80, "formatter"=>"date"));
$grid->setColProperty("staff", array("label"=>"Dealt with by", "width"=>100));
$grid->setColProperty("description", array("label"=>"Description", "width"=>250));
$grid->setColProperty("goals", array("label"=>"Goals", "width"=>250));
$grid->setColProperty("complete", array("label"=>"% Complete", "width"=>85));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>50,"sortable"=>false));


$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#rcamplist").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var rowd = $("#rcamplist").getRowData(ids[i]);
		var cl = ids[i]; 
		be = '<img src="../images/rcamp.png" title="Run Campaign" onclick="javascript:runcampaign('+cl+')" >'; 
		jQuery("#rcamplist").setRowData(ids[i],{act:be}); 
	} 
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

$grid->gSQLMaxRows = 1000;

// enable form editing
$grid->navigator = true;
$grid->setNavOptions('navigator',array('add'=>false,'edit'=>false,'del'=>false));

// Run the script
$grid->renderGrid('#rcamplist','#rcamppager',true, null, null, true,true);


?>

