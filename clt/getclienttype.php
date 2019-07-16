<?php
session_start();

$id = $_SESSION["s_memberid"];

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
$grid->SelectCommand = "select clienttype_xref_id, client_type from ".$cltdb.".clienttype_xref where member_id = ".$id; 

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getclienttype.php');

// Set some grid options
$grid->setGridOptions(array(
	"caption"=>"Client Type",
	"rowNum"=>12,
    "sortname"=>"client_type",
	"sortorder"=>"asc",
    "rowList"=>array(12,30,50),
	"height"=>95,
	"width"=>300
    ));

$grid->addCol(array("name"=>"act"),"last");


// Change some property of the field(s)
$grid->setColProperty("clienttype_xref_id", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("client_type", array("label"=>"Client Type", "width"=>180));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));


$ldevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#mctlist").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var cl = ids[i]; 
		de = '<img src="../images/delete.png" title="Delete" onclick="javascript:delctmember('+cl+')" >'; 
		jQuery("#mctlist").setRowData(ids[i],{act:de});
	} 
}
LOADCOMPLETE;


$grid->setGridEvent("loadComplete",$ldevent);

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoption1 = array("#mctpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a client type", "onClickButton"=>"js: function(){addct2member(".$id.");}"),
);
$grid->callGridMethod("#mctlist", "navButtonAdd", $buttonoption1); 

// Run the script
$grid->renderGrid('#mctlist','#mctpager',true, null, null,true,true,true);

?>




