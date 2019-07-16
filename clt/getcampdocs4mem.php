<?php
session_start();

include 'jq-config.php';
// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);

// Get the needed parameters passed from the main grid
if(isset ($_REQUEST["cid"])) {
    $id = jqGridUtils::Strip($_REQUEST["cid"]);
} else {
    $id = 0;
}

// Create the jqGrid instance
$grid = new jqGridRender($conn);

// the actual query for the grid data
$grid->SelectCommand = "SELECT campdoc_id,campaign_id,ddate,subject,doc from campaign_docs where campaign_id = ".$id;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getcampdocs4mem.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
	"caption"=>"Campaign Documents",						
    "rowNum"=>12,
    "sortname"=>"ddate",
    "rowList"=>array(12,30,50),
	"height"=>100,
	"width"=>280
    ));

$grid->addCol(array("name"=>"act"),"first");

// Change some property of the field(s)
$grid->setColProperty("campdoc_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("campaign_id", array("label"=>"CID", "width"=>20, "hidden"=>true));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>80, "formatter"=>"date"));
$grid->setColProperty("subject", array("label"=>"Subject", "width"=>200));
$grid->setColProperty("doc", array("label"=>"Doc", "width"=>20, "hidden"=>true));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>80, "sortable"=>false));



$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#doclist2").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var cl = ids[i]; 
		var rowdata = $("#doclist2").getRowData(cl);
		var d = "'"+rowdata.campaign_id+"__"+rowdata.doc+"'";
		be = '<img src="../images/edit.png" title="View" onclick="javascript:viewdoc('+d+')" ></ids>'; 
		jQuery("#doclist2").setRowData(ids[i],{act:be}) 
	} 
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#doclist2','#docpager2',true, null, null, true,true);

?>




