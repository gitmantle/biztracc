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
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");

// Create the jqGrid instance
$grid = new jqGridRender($conn);

$grid->SelectCommand = "select stkmast.itemid,stkgroup.groupname,stkcategory.category,stkmast.itemcode,stkmast.item,stkmast.active,stkmast.onhand,stkmast.uncosted from ".$findb.".stkgroup,".$findb.".stkcategory,".$findb.".stkmast where stkgroup.groupid = stkmast.groupid and stkcategory.catid = stkmast.catid and stkmast.stock = 'Stock'";

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('../fin/getStockQty.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Stock List of stockable items",
    "rowNum"=>7,
    "sortname"=>"item",
    "rowList"=>array(7,30,50),
	"height"=>162,
	"width"=>880
    ));


// Change some property of the field(s)
$grid->setColProperty("itemid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("groupname", array("label"=>"Group", "width"=>100));
$grid->setColProperty("category", array("label"=>"Category", "width"=>100));
$grid->setColProperty("itemcode", array("label"=>"Code", "width"=>100));
$grid->setColProperty("item", array("label"=>"Item", "width"=>100));
$grid->setColProperty("active", array("label"=>"Active", "width"=>40));
$grid->setColProperty("onhand", array("label"=>"On Hand", "width"=>60));
$grid->setColProperty("uncosted", array("label"=>"Uncosted", "width"=>60));


// on select row we should post the member id to second table and trigger it to reload the data
$selectgrp = <<<GRP
function(rowid, selected)
{
    if(rowid != null) {
		var rowd = $("#stklqlist").getRowData(rowid);
		var icode = rowd.itemcode;
        jQuery("#stklocqtylist").jqGrid('setGridParam',{postData:{itid:icode}});
        jQuery("#stklocqtylist").trigger("reloadGrid");
    }
}
GRP;
$grid->setGridEvent('onSelectRow', $selectgrp);

// We should clear the grid data on second grid on sorting, paging, etc.
$cleargrid = <<<CLEAR
function(rowid, selected)
{
   // clear the grid data and footer data
   jQuery("#stklocqtylist").jqGrid('clearGridData',true);
}
CLEAR;

$grid->setGridEvent('onPaging', $cleargrid);
$grid->setGridEvent('onSortCol', $cleargrid);

$grid->gSQLMaxRows = 4000;

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#stklqlist','#stklqpager2',true, null, null, true,true);


?>

