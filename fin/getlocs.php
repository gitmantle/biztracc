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


if(isset ($_REQUEST["grp"])) {
    $id = jqGridUtils::Strip($_REQUEST["grp"]);
} else {
    $id = '';
}
$_SESSION['s_stkgroup'] = $id;

// Create the jqGrid instance
$grid = new jqGridRender($conn);

// the actual query for the grid data
$grid->SelectCommand = "select uid,location,branch from ".$findb.".stklocs";

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getlocs.php');



// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>12,
    "sortname"=>"location",
    "rowList"=>array(12,50,100),
	"height"=>280,
	"width"=>400
    ));

$grid->addCol(array("name"=>"act"),"last");


// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("location", array("label"=>"Location", "width"=>150));
$grid->setColProperty("branch", array("label"=>"Branch", "width"=>70));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>70));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#stkloclist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#stkloclist").getRowData(ids[i]);
			var cl = ids[i];
			be = '<img src="../images/edit.png" title="Edit stock location" onclick="javascript:editloc('+cl+')" ></ids>';
			jQuery("#stkloclist").setRowData(ids[i],{act:be}); 
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
$buttonoptions = array("#stklocpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a stock location.", "onClickButton"=>"js: function(){addloc();}")
);
$grid->callGridMethod("#stkloclist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#stkloclist','#stklocpager',true, null, null,true,true,true);



?>




