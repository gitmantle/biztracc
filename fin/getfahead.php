<?php
session_start();
//ini_set('display_errors', true);

$findb = $_SESSION['s_findb'];

include 'jq-config.php';

// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");
// Get the needed parameters passed from the main grid

// Create the jqGrid instance
$grid = new jqGridRender($conn);

// the actual query for the grid data
$grid->SelectCommand = "select uid,heading from ".$findb.".assetheadings";

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getfahead.php');



// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>10,
    "sortname"=>"uid",
    "rowList"=>array(10),
	"height"=>280,
	"width"=>400
    ));

$grid->addCol(array("name"=>"act"),"last");


// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("heading", array("label"=>"Location", "width"=>150));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>70));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#faheadlist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#faheadlist").getRowData(ids[i]);
			var cl = ids[i];
			if (cl > 6) {
				be = '<img src="../images/edit.png" title="Edit Fixed Asset Heading" onclick="javascript:fa_editfahead('+cl+')" ></ids>';
			} else {
				be = "&nbsp;&nbsp;&nbsp;";	
			}
			jQuery("#faheadlist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);
// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#faheadlist','#faheadpager',true, null, null,true,true,true);



?>




