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
$grid->SelectCommand = 'SELECT OrderID, Freight, OrderDate, ShipCity  FROM orders';
// Set output format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('grid.php');
// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>10,
	"toppager"=>true,
    "rowList"=>array(10,20,30),
    "sortname"=>"OrderID"
));
// Change some property of the field(s)
$grid->setColProperty("OrderDate", array(
    "formatter"=>"date",
    "formatoptions"=>array("srcformat"=>"Y-m-d H:i:s","newformat"=>"m/d/Y"),
    "search"=>false
    )
);
// Add the method only to top pager

$grid->callGridMethod("#grid", "navGrid", array('#grid_toppager',
	array("add"=>true),
	array("recreateForm"=>true), // edit - just to demo the diffrenet options
	array( //add
    "width"=>300,
    "height"=>300,
    "reloadAfterSubmit"=>false,
	"recreateForm"=>true,
    "checkOnUpdate"=>true,
    "top"=>"300",
    "left"=>"490",
    "closeAfterAdd"=>true,
	),
	array() // del options
	));

//custom button
$buttonoptions = array("#grid_toppager",
    array("caption"=>"Pdf", "title"=>"Export to Pdf", "onClickButton"=>"js: function(){
		jQuery('#grid').jqGrid('excelExport',{tag:'pdf', url:'grid.php'});}"
	)
);
// Set it to toppager
$grid->callGridMethod("#grid", "navButtonAdd", $buttonoptions);
// Enjoy
$grid->renderGrid('#grid','',true, null, null, true,false);
