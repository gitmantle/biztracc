<?php
session_start();
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
$grid->SelectCommand = "SELECT costs_id,campaign_id,item,cost from ".$cltdb.".campaign_costs where campaign_id = ".$id;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getcampcosts.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
	"caption"=>"Campaign Costs",						
    "rowNum"=>12,
    "sortname"=>"item",
    "rowList"=>array(12,30,50),
	"height"=>250,
	"width"=>500
    ));

// Enable footerdata an tell the grid to obtain it from the request
$grid->setGridOptions(array("footerrow"=>true,"userDataOnFooter"=>true));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("costs_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("campaign_id", array("label"=>"Campaign", "width"=>20, "hidden"=>true));
$grid->setColProperty("item", array("label"=>"Item", "width"=>200));
$grid->setColProperty("cost", array("label"=>"Cost", "width"=>100, "align"=>"right", "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));

// At end call footerData to put total  label
$grid->callGridMethod('#costlist', 'footerData', array("set",array("item"=>"Total:")));
// Set which parameter to be sumarized
$summaryrows = array("cost"=>array("cost"=>"SUM"));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#costlist").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var cl = ids[i]; 
		var rowdata = $("#costlist").getRowData(cl);
		var camp = rowdata.campaign_id;
		be = '<img src="../images/edit.png" title="Edit" onclick="javascript:editcampcost('+cl+','+camp+')" ></ids>'; 
		se = '<img src="../images/delete.png" title="Delete" onclick="javascript:delcampcost('+cl+','+camp+')" />'; 
		jQuery("#costlist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;'+se}) 
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
$buttonoptions = array("#costpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a Campaign", "onClickButton"=>"js: function(){addcampcost();}")
);
$grid->callGridMethod("#costlist", "navButtonAdd", $buttonoptions); 


// Run the script
$grid->renderGrid('#costlist','#costpager',true, $summaryrows, null, true,true);

?>




