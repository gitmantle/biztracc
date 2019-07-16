<?php
session_start();

error_reporting(E_ALL ^ E_NOTICE);

$id = $_SESSION['s_campid'];
$cltdb = $_SESSION['s_cltdb'];


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
$grid->SelectCommand = "SELECT campdoc_id,campaign_id,ddate,doc,subject,staff from ".$cltdb.".campaign_docs where campaign_id = ".$id;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getcampdocs.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
	"caption"=>"Campaign Documents",						
    "rowNum"=>12,
    "sortname"=>"ddate",
    "rowList"=>array(12,30,50),
	"height"=>250,
	"width"=>800
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("campdoc_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("campaign_id", array("label"=>"Campaign", "width"=>20, "hidden"=>true));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>80, "formatter"=>"date"));
$grid->setColProperty("doc", array("label"=>"Document", "width"=>200));
$grid->setColProperty("subject", array("label"=>"Subject", "width"=>200));
$grid->setColProperty("staff", array("label"=>"Staff Member", "width"=>150));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>80, "sortable"=>false));


$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#doclist").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var cl = ids[i]; 
		var rowdata = $("#doclist").getRowData(cl);
		var d = "'"+rowdata.campaign_id+"__"+rowdata.doc+"'";
		be = '<img src="../images/edit.png" title="View" onclick="javascript:viewdoc('+d+')" ></ids>'; 
		se = '<img src="../images/delete.png" title="Delete" onclick="javascript:deldoc('+cl+','+d+')" />'; 
		jQuery("#doclist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;'+se}) 
	} 
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#docpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a Document", "onClickButton"=>"js: function(){addcampdoc();}")
);
$grid->callGridMethod("#doclist", "navButtonAdd", $buttonoptions); 



// Run the script
$grid->renderGrid('#doclist','#docpager',true, null, null, true,true);

?>




