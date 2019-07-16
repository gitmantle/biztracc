<?php

//ini_set('display_errors', true);
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
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");

// Create the jqGrid instance
$grid = new jqGridRender($conn);
// Write the SQL Query
// We suppose that mytable exists in your database
$grid->SelectCommand = "SELECT uid,nature from ".$cltdb.".complaint_nature";

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getcomplaint_nature.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Natures of Complaint",
    "rowNum"=>12,
    "sortname"=>"nature",
    "rowList"=>array(12,30,50),
	"height"=>270,
	"width"=>500
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("nature", array("label"=>"Nature", "width"=>210));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>70));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#naturelist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#naturelist").getRowData(ids[i]);
			var cl = ids[i];
			be = '<img src="../images/edit.png" title="Edit Nature" onclick="javascript:editnat('+cl+')" ></ids>';
			jQuery("#naturelist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

$grid->gSQLMaxRows = 4000;

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#naturepager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a nature", "onClickButton"=>"js: function(){addnat();}")
);
$grid->callGridMethod("#naturelist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#naturelist','#naturepager',true, null, null, true,true);


?>




