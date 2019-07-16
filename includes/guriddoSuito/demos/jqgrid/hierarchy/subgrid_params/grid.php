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

// Create the jqGrid instance
$grid = new jqGridRender($conn);
// Write the SQL Query
$grid->SelectCommand = 'SELECT OrderID, OrderDate, ShipAddress, CustomerID, Freight, ShipName FROM orders';
// set the ouput format to json
$grid->dataType = 'xml';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('grid.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"This is custom Caption",
    "rowNum"=>10,
    "sortname"=>"OrderID",
    "hoverrows"=>true,
    "rowList"=>array(10,20,50),
	"autowidth"=>false,
	"width"=>700,
    "footerrow"=>false,
    "hoverrows"=>true,
    "userDataOnFooter"=>true,
    "grouping"=>true,
    "groupingView"=>array(
        "groupField" => array('CustomerID'),
        "groupColumnShow" => array(true),
        "groupText" =>array('<b> {0}  Count: {1} </b>'),
        "groupDataSorted" => true,
        "groupSummary" => array(true)
	)    	
));
// Change some property of the field(s)
$grid->setColProperty("OrderID", array("label"=>"ID", "width"=>90));
//$grid->setColProperty("ShipAddress", array("hidden"=>true));
$grid->setColProperty("OrderDate", array(
    "formatter"=>"date",
    "formatoptions"=>array("srcformat"=>"Y-m-d H:i:s","newformat"=>"m/d/Y")
    )
);
$grid->setColProperty("Freight", array("hidden"=>false));
$summaryrows = array("Freight"=>array("Freight"=>"SUM"));
$grid->callGridMethod('#grid', 'footerData', array("set",array("CustomerID"=>"Total:")));

$grid->setSubGridGrid("subgrid.php", array("CustomerID","OrderDate", "OrderID"));

// Enable navigator
$grid->navigator = true;
// Enable only editing
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// Enjoy
$grid->renderGrid('#grid','#pager',true, $summaryrows, null, true,true);

