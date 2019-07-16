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

$grid->SelectCommand = 'SELECT OrderID, OrderDate, CustomerID, Freight, ShipName FROM orders WHERE OrderId < 10400';
// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model from SQL query
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('grid.php');
// Set alternate background using altRows property
$grid->setGridOptions(array(
    "rowNum"=>10,
    "sortname"=>"OrderID",
    "rowList"=>array(10,20,50),
	"loadonce"=>true,
	"rowTotal"=>-1,
    "height"=>'auto',
    "grouping"=>true,
    "groupingView"=>array(
    	"groupField" => array('CustomerID'),
    	"groupColumnShow" => array(true),
    	"groupText" =>array('<b>{0}</b>'),
    	"groupDataSorted" => true,
    	"groupSummary" => array(true)
    ) 
    ));
// Change some property of the field(s)
$grid->setColProperty("OrderID", array("label"=>"ID", "width"=>60));
$grid->setColProperty("OrderDate", array(
    "formatter"=>"date",
    "formatoptions"=>array("srcformat"=>"Y-m-d H:i:s","newformat"=>"m/d/Y")
    )
);
// Add a summary property to the Freight Column
$grid->setColProperty("Freight", array("summaryType"=>"sum", "summaryTpl"=>"Sum: {0}", "formatter"=>"number"));
// Enable navigator
$grid->navigator = true;
// Enable excel export
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false, "search"=>false, "refresh"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#pager",
    array("caption"=>"Csv", "title"=>"Local Export to CSV", "onClickButton"=>"js: function(){
		jQuery('#grid').jqGrid('exportToCsv');}"
	)
);
$grid->callGridMethod("#grid", "navButtonAdd", $buttonoptions);
$buttonoptions = array("#pager",
    array("caption"=>"Excel", "title"=>"Local Export to Escel", "onClickButton"=>"js: function(){
		jQuery('#grid').jqGrid('exportToExcel');}"
	)
);
$grid->callGridMethod("#grid", "navButtonAdd", $buttonoptions);
$buttonoptions = array("#pager",
    array("caption"=>"Pdf", "title"=>"Local Export to PDF", "onClickButton"=>"js: function(){
		jQuery('#grid').jqGrid('exportToPdf');}"
	)
);
$grid->callGridMethod("#grid", "navButtonAdd", $buttonoptions);

// Enjoy
$grid->renderGrid('#grid','#pager',true, null, null, true,true);

