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
$subtable = jqGridUtils::Strip($_REQUEST["subgrid"]);
$rowid = jqGridUtils::Strip($_REQUEST["rowid"]);

$orderid = jqGridUtils::GetParam("OrderID");
$cust = jqGridUtils::GetParam("CustomerID");
$date = jqGridUtils::GetParam("OrderDate");

if(!$subtable && !$rowid) die("Missed parameters");
// Create the jqGrid instance
$grid = new jqGridRender($conn);
// Write the SQL Query
$grid->SelectCommand = "SELECT a.OrderID, a.ProductID, a.Quantity, a.UnitPrice, b.OrderDate, b.CustomerID FROM order_details a, orders b WHERE a.OrderID = b.OrderID AND a.OrderID=? AND b.CustomerID = ?";
// set the ouput format to json
$grid->dataType = 'xml';
// Let the grid create the model
$grid->setColModel(null, array($orderid, $cust));
// Set the url from where we obtain the data
$grid->setUrl('subgrid.php');
// Set some grid options
$grid->setGridOptions(array(
    "width"=>480,
    "rowNum"=>10,
    "sortname"=>"OrderID",
    "height"=>'auto',
    "postData"=>array("subgrid"=>$subtable,"rowid"=>$rowid,"CustomerID"=>$cust, "OrderDate"=>$date, "OrderID"=>$orderid ))
);
// Log of the parameters
$load = <<< LOAD
function()
{
	$("#cust").html('CustomerID: $cust');
	$("#date").html('OrderDate: $date');
}
LOAD;
$grid->setGridEvent('loadComplete', $load);
// Change some property of the field(s)
$grid->navigator = true;
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// Enjoy
$subtable = $subtable."_t";
$pager = $subtable."_p";
$grid->renderGrid($subtable,$pager, true, null, array($orderid, $cust), true,true);

