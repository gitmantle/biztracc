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
$grid->SelectCommand = "select uid,tax,description,taxpcent,defgst,defn_t from ".$findb.".taxtypes";


// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getgst.php');

// enable debugging
//$grid->debug = true;

// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>10,
    "sortname"=>"uid",
    "rowList"=>array(10,20,50),
	"height"=>140,
	"width"=>600
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("tax", array("label"=>"Tax", "width"=>25));
$grid->setColProperty("description", array("label"=>"Description", "width"=>150));
$grid->setColProperty("taxpcent", array("label"=>"Percentage", "width"=>60, "align"=>"right"));
$grid->setColProperty("defgst", array("label"=>"Default Tax", "width"=>100));
$grid->setColProperty("defn_t", array("label"=>"Default No Tax", "width"=>100));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#gstlist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#gstlist").getRowData(ids[i]);
			var cl = ids[i];
			be = '<img src="../images/edit.png" title="Edit Tax" onclick="javascript:editgst('+cl+')" ></ids>';
			jQuery("#gstlist").setRowData(ids[i],{act:be}); 
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
$buttonoptions = array("#gstpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add Tax Type.", "onClickButton"=>"js: function(){addgst();}")
);
$grid->callGridMethod("#gstlist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#gstlist','#gstpager',true, null, null,true,true,true);

?>




