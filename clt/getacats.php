<?php
session_start();
$usersession = $_SESSION['usersession'];

$ddate = date("d/m/Y");
$hdate = date("Y-m-d");
$cluid = $_SESSION['s_memberid'];

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
$grid->SelectCommand = "SELECT uid,category from ".$cltdb.".acats where member_id = ".$cluid;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getacats.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "rowNum"=>5,
    "sortname"=>"category",
    "rowList"=>array(5,12,50),
	"height"=>115,
	"width"=>600
	
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("category", array("label"=>"Accounting Categories", "width"=>370));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>50,"sortable"=>false));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#acatlist").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var cl = ids[i]; 
		se = '<img src="../images/delete.png" title="Delete" onclick="javascript:delacat('+cl+')" >'; 
		jQuery("#acatlist").setRowData(ids[i],{act:se}) 
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
$buttonoptions = array("#acatpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add an account category", "onClickButton"=>"js: function(){addacat();}")
);
$grid->callGridMethod("#acatlist", "navButtonAdd", $buttonoptions); 



// Run the script
$grid->renderGrid('#acatlist','#acatpager',true, null, null, true,true);


?>




