<?php
session_start();
error_reporting (E_ALL ^ E_NOTICE);
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
$grid->SelectCommand = "select invhead.uid,invhead.ref_no,invhead.ddate,invhead.client from ".$findb.".invhead where (invhead.totvalue = 0) and invhead.transtype = 'GRN'";

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getUncostGRNs.php');


// Set some grid options
$grid->setGridOptions(array(
    "caption"=>"Uncosted GRNs",
    "rowNum"=>3,
    "sortname"=>"ref_no",
    "rowList"=>array(3,20,100),
	"height"=>72,
	"width"=>900
    ));


// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("ref_no", array("label"=>"Reference", "width"=>80));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("client", array("label"=>"Supplier", "width"=>150));

// on select row we should post the member id to second table and trigger it to reload the data
$selectgrp = <<<GRP
function(rowid, selected)
{
    if(rowid != null) {
		var rowd = $("#uncostlist").getRowData(rowid);
		var icode = rowd.ref_no;
        jQuery("#UncostGRNitems").jqGrid('setGridParam',{postData:{itid:icode}});
        jQuery("#UncostGRNitems").trigger("reloadGrid");
    }
}
GRP;
$grid->setGridEvent('onSelectRow', $selectgrp);

// We should clear the grid data on second grid on sorting, paging, etc.
$cleargrid = <<<CLEAR
function(rowid, selected)
{
   // clear the grid data and footer data
   jQuery("#UncostGRNitems").jqGrid('clearGridData',true);
}
CLEAR;

$grid->setGridEvent('onPaging', $cleargrid);
$grid->setGridEvent('onSortCol', $cleargrid);


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#uncostlist','#uncostpager',true, null, null,true,true,true);
$conn = null;

?>




