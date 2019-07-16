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
$grid->SelectCommand = "select hcode,uid,heading from ".$findb.".assetheadings where heading != 'SPARE'";

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getfagroups.php');


// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>12,
    "sortname"=>"uid",
    "rowList"=>array(12,30,50),
	"height"=>280,
	"width"=>290
    ));


// Change some property of the field(s)
$grid->setColProperty("hcode", array("label"=>"Code", "width"=>25));
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("heading", array("label"=>"Asset Group", "width"=>100));

// on select row we should post the member id to second table and trigger it to reload the data
$selectgrp = <<<GRP
function(rowid, selected)
{
    if(rowid != null) {
        jQuery("#faacclist").jqGrid('setGridParam',{postData:{grp:rowid}});
        jQuery("#faacclist").trigger("reloadGrid");
    }
}
GRP;
$grid->setGridEvent('onSelectRow', $selectgrp);

// We should clear the grid data on second grid on sorting, paging, etc.
$cleargrid = <<<CLEAR
function(rowid, selected)
{
   // clear the grid data and footer data
   jQuery("#facodelist").jqGrid('clearGridData',true);
}
CLEAR;

$grid->setGridEvent('onPaging', $cleargrid);
$grid->setGridEvent('onSortCol', $cleargrid);

// Enable navigator
$grid->navigator = false;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#facodelist','#facodepager',true, null, null,true,true,true);

?>




