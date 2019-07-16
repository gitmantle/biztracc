<?php
session_start();
$usersession = $_SESSION['usersession'];

$ddate = date("d/m/Y");
$hdate = date("Y-m-d");

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
$grid->SelectCommand = "SELECT acccat_id,acccat from ".$cltdb.".acccats";

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getacccats.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Accounting Categories",
    "rowNum"=>12,
    "sortname"=>"acccat",
    "rowList"=>array(12,30,50)
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("acccat_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("acccat", array("label"=>"Accounting Categories", "width"=>370));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>70));

$grid->setGridOptions(array("width"=>500,"height"=>280));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#acccatlist").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var cl = ids[i]; 
		be = '<img src="../images/edit.png" title="Edit" onclick="javascript:editacccat('+cl+')" ></ids>'; 
		jQuery("#acccatlist").setRowData(ids[i],{act:be}) 
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
$buttonoptions = array("#acccatpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a client type", "onClickButton"=>"js: function(){addacccat();}")
);
$grid->callGridMethod("#acccatlist", "navButtonAdd", $buttonoptions); 



// Run the script
$grid->renderGrid('#acccatlist','#acccatpager',true, null, null, true,true);


?>




