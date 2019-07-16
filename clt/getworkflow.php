<?php
session_start();

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
// Write the SQL Query

// the actual query for the grid data
$grid->SelectCommand = "SELECT process_id,process,porder from ".$cltdb.".workflow";

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getworkflow.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Work Flow Stages",
    "rowNum"=>12,
    "sortname"=>"porder",
    "rowList"=>array(12,30,50)
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("process_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("process", array("label"=>"Workflow Stage", "width"=>370));
$grid->setColProperty("porder", array("label"=>"Order", "width"=>70));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>70));

$grid->setGridOptions(array("width"=>500,"height"=>280));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#flowlist").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var cl = ids[i]; 
		be = '<img src="../images/edit.png" title="Edit" onclick="javascript:editworkflow('+cl+')" ></ids>'; 
		se = '<img src="../images/delete.png" title="Delete" onclick="javascript:delworkflow('+cl+')" />'; 
		jQuery("#flowlist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+se}) 
	} 
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#wfpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a workflow stage", "onClickButton"=>"js: function(){addworkflow();}")
);
$grid->callGridMethod("#flowlist", "navButtonAdd", $buttonoptions); 



// Run the script
$grid->renderGrid('#flowlist','#wfpager',true, null, null, true,true);


?>




