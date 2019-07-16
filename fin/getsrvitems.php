<?php
session_start();
$findb = $_SESSION['s_findb'];

include '../fin/jq-config.php';

// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);

// Create the jqGrid instance
$grid = new jqGridRender($conn);


// the actual query for the grid data
$grid->SelectCommand = "SELECT stkmast.itemid,stkmast.itemcode,stkmast.item,stkcategory.category,stkmast.active from ".$findb.".stkmast,".$findb.".stkgroup,".$findb.".stkcategory where stkmast.groupid = stkgroup.groupid and stkmast.catid = stkcategory.catid and stkmast.stock = 'Service'";

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('../fin/getsrvitems.php');


// Set some grid options
$grid->setGridOptions(array(
    "caption"=>"Service Items",
    "rowNum"=>20,
    "sortname"=>"item",
    "rowList"=>array(20,100,200),
	"height"=>450,
	"width"=>900
    ));


$grid->addCol(array("name"=>"act"),"last");


// Change some property of the field(s)
$grid->setColProperty("itemid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("itemcode", array("label"=>"Stock Code", "width"=>50));
$grid->setColProperty("item", array("label"=>"Description", "width"=>150));
$grid->setColProperty("category", array("label"=>"Category", "width"=>75));
$grid->setColProperty("active", array("label"=>"Active", "width"=>28));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>30));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#srvlist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#srvlist").getRowData(ids[i]);
			var cl = ids[i];
			be = '<img src="../images/edit.png" title="Edit Stock Item" onclick="javascript:editsrv('+cl+')" ></ids>';
			jQuery("#srvlist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#srvpager",
    array("buttonicon"=>"ui-icon-newwin","caption"=>"","position"=>"last","title"=>"Output to Excel.", "onClickButton"=>"js: function(){srv2xl();}")
);
$grid->callGridMethod("#srvlist", "navButtonAdd", $buttonoptions); 

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#srvpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a Stock Item.", "onClickButton"=>"js: function(){addsrvitem();}")
);
$grid->callGridMethod("#srvlist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#srvlist','#srvpager',true, null, null,true,true,true);
$conn = null;

?>




