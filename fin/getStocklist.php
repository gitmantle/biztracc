<?php
session_start();

$grp = $_SESSION['s_stkgrp'];
$cat = $_SESSION['s_stkcat'];
$coyidno = $_SESSION['s_coyid'];

$findb = $_SESSION['s_findb'];

$where = '';
if ($grp > 0 && $cat > 0) {
	$where .= "where stkgroup.groupid = stkmast.groupid and stkcategory.catid = stkmast.catid and stkmast.groupid = ".$grp." and stkmast.catid = ".$cat;
}
if ($grp == '*' && $cat > 0) {
	$where .= "where stkgroup.groupid = stkmast.groupid and stkcategory.catid = stkmast.catid and stkmast.catid = ".$cat;
}
if ($grp >0 && $cat == '*') {
	$where .= "where stkgroup.groupid = stkmast.groupid and stkcategory.catid = stkmast.catid and stkmast.groupid = ".$grp;
}
if ($grp == '*' && $cat == '*') {
	$where .= "where stkgroup.groupid = stkmast.groupid and stkcategory.catid = stkmast.catid";
}

$where = $where." and stkmast.stock = 'Stock'";

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

$grid->SelectCommand = "select stkmast.itemid,stkgroup.groupname,stkcategory.category,stkmast.itemcode,stkmast.item,stkmast.active,stkmast.onhand,stkmast.uncosted from ".$findb.".stkgroup,".$findb.".stkcategory,".$findb.".stkmast ".$where;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getStocklist.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Stock List",
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
		var rowd = $("#stkgrplist").getRowData(rowid);
		var icode = rowd.itemcode;
        jQuery("#stkmovelist").jqGrid('setGridParam',{postData:{itid:icode}});
        jQuery("#stkmovelist").trigger("reloadGrid");
    }
}
GRP;
$grid->setGridEvent('onSelectRow', $selectgrp);

// We should clear the grid data on second grid on sorting, paging, etc.
$cleargrid = <<<CLEAR
function(rowid, selected)
{
   // clear the grid data and footer data
   jQuery("#stkmovelist").jqGrid('clearGridData',true);
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
$grid->renderGrid('#stkgrplist','#stkgrppager2',true, null, null, true,true);


?>

