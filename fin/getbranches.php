<?php
session_start();
ini_set('display_errors', true);

$findb = $_SESSION['s_findb'];

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
$grid->SelectCommand = "select uid,branch,branchname from ".$findb.".branch";

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getbranches.php');


// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>12,
    "sortname"=>"uid",
    "rowList"=>array(12,30,50),
	"height"=>280,
	"width"=>290
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("branch", array("label"=>"Branch", "width"=>70));
$grid->setColProperty("branchname", array("label"=>"Branch Name", "width"=>150));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#branchlist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#branchlist").getRowData(ids[i]);
			var cl = ids[i];
			be = '<img src="../images/edit.png" title="Edit Branch" onclick="javascript:editbranch('+cl+')" ></ids>';
			jQuery("#branchlist").setRowData(ids[i],{act:be}); 
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
$buttonoptions = array("#branchpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a Branch.", "onClickButton"=>"js: function(){addbranch();}")
);
$grid->callGridMethod("#branchlist", "navButtonAdd", $buttonoptions); 


// Run the script
$grid->renderGrid('#branchlist','#branchpager',true, null, null,true,true,true);

?>




