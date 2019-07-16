<?php
session_start();
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
$grid->SelectCommand = "SELECT stkmast.itemid,stkmast.itemcode,stkmast.item,stkgroup.groupname,stkcategory.category,stkmast.onhand,stkmast.uncosted from ".$findb.".stkmast,".$findb.".stkgroup,".$findb.".stkcategory where stkmast.groupid = stkgroup.groupid and stkmast.catid = stkcategory.catid and stkmast.active = 'Yes' and stkmast.stock = 'Stock'";

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getStockadjlist.php');


// Set some grid options
$grid->setGridOptions(array(
    "caption"=>"Stock Items",
    "rowNum"=>7,
    "sortname"=>"item",
    "rowList"=>array(7,100,200),
	"height"=>162,
	"width"=>900
    ));



// Change some property of the field(s)
$grid->setColProperty("itemid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("itemcode", array("label"=>"Stock Code", "width"=>50));
$grid->setColProperty("item", array("label"=>"Description", "width"=>230));
$grid->setColProperty("groupname", array("label"=>"Group", "width"=>75));
$grid->setColProperty("category", array("label"=>"Category", "width"=>75));
$grid->setColProperty("onhand", array("label"=>"On Hand", "width"=>40));
$grid->setColProperty("uncosted", array("label"=>"Uncosted", "width"=>40));

// on select row we should post the member id to second table and trigger it to reload the data
$selectgrp = <<<GRP
function(rowid, selected)
{
    if(rowid != null) {
		var rowd = $("#stkadjlist").getRowData(rowid);
		var icode = rowd.itemcode;
        jQuery("#stklqlist").jqGrid('setGridParam',{postData:{itid:icode}});
        jQuery("#stklqlist").trigger("reloadGrid");
    }
}
GRP;
$grid->setGridEvent('onSelectRow', $selectgrp);

// We should clear the grid data on second grid on sorting, paging, etc.
$cleargrid = <<<CLEAR
function(rowid, selected)
{
   // clear the grid data and footer data
   jQuery("#stklqlist").jqGrid('clearGridData',true);
}
CLEAR;

$grid->setGridEvent('onPaging', $cleargrid);
$grid->setGridEvent('onSortCol', $cleargrid);

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));


// Run the script
$grid->renderGrid('#stkadjlist','#stkadjpager',true, null, null,true,true,true);
$conn = null;

?>




