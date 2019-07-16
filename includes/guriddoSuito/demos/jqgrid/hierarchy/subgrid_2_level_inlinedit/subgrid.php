<?php
require_once '../../jq-config.php';
// include the jqGrid Class
require_once ABSPATH."php/PHPSuito/jqGrid.php";
// include the driver class
require_once ABSPATH."php/PHPSuito/DBdrivers/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");
// Get the needed parameters passed from the main grid
// By default we add to postData subgrid and rowid parameters in the main grid
$oper = jqGridUtils::GetParam("oper");
$subtable = jqGridUtils::GetParam("subgrid");
$rowid = jqGridUtils::GetParam("rowid");
//if(!$subtable && !$rowid) die("Missed parameters");
// Create the jqGrid instance
$grid = new jqGridRender($conn);
// Write the SQL Query
$grid->SelectCommand = "SELECT OrderID, ShipName, RequiredDate, ShipCity, Freight, CustomerID FROM orders WHERE CustomerID = ?";
// set the ouput format to json
$grid->dataType = 'json';
$grid->setPrimaryKeyId('OrderID');
// Let the grid create the model
$grid->setColModel(null,array($rowid));
// Set the url from where we obtain the data
$grid->setUrl('subgrid.php');
$onselrow = <<< ONSELROW
function(rowid, selected)
{
	if(rowid && rowid !== lastSelection) {
		$(this).jqGrid('restoreRow', lastSelection);
		$(this).jqGrid('editRow', rowid, true);
		lastSelection = rowid;
	}
}
ONSELROW;
$grid->setGridEvent('onSelectRow', $onselrow);
$grid->table = 'orders';
$grid->setColProperty('OrderID', array(
	"editable"=>false
));
$grid->setColProperty('CustomerID', array(
	"hidden"=>true
));
// Set some grid options
$grid->setGridOptions(array(
    "width"=>540,
    "rowNum"=>10,
    "sortname"=>"OrderID",
    "height"=>110,
    "postData"=>array("subgrid"=>$subtable,"rowid"=>$rowid)));
// Change some property of the field(s)
$grid->setDbTime('Y-m-d H:i:s');
$grid->setUserTime('m/d/Y');
$grid->setDatepicker("RequiredDate");
$grid->setColProperty("RequiredDate", array(
    "formatter"=>"date",
    "formatoptions"=>array("reformatAfterEdit"=>true, "srcformat"=>"Y-m-d H:i:s","newformat"=>"m/d/Y"),
    "search"=>false
    )
);
// Enjoy
$subtable = $subtable."_t";
$pager = $subtable."_p";
$grid->renderGrid($subtable,$pager, true, null, array(&$rowid), true,true);

