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
$grid->SelectCommand = 'SELECT OrderID, RequiredDate, ShipName, ShipCity, Freight FROM orders';
// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('grid.php');
// Set some grid options
$grid->setGridOptions(array(
	"rowNum"=>10,
	"rowList"=>array(10,20,130),
	"sortname"=>"OrderID",
	"height"=>200,
	// enable colMenu
	"colMenu"=>true
	)
);
$grid->setColProperty("OrderID", array(
	// colMenu for particular column
	"colmenu"=>true,
	// set which actions to perform
	"coloptions"=> array("sorting"=>true, "columns"=> true, "filtering"=>true, "seraching"=>true, "grouping"=>false, "freeze" => true)
));

// Enable footerdata an tell the grid to obtain it from the request
$grid->setGridOptions(array("footerrow"=>true,"userDataOnFooter"=>true));
// Change some property of the field(s)
$grid->setColProperty("RequiredDate", array("formatter"=>"date","formatoptions"=>array("srcformat"=>"Y-m-d H:i:s","newformat"=>"m/d/Y")));
// At end call footerData to put total  label
$grid->callGridMethod('#grid', 'footerData', array("set",array("ShipCity"=>"Total:")));

// add custom coll menu to all columns
$grid->callGridMethod('#grid', 'colMenuAdd',  array( 'all', array(
	"id" => "myid",
	"title" => 'My menu',
	"funcname" => 'js:function(colname){ alert(colname);}'
)	
));

// Set which parameter to be sumarized
$summaryrows = array("Freight"=>array("Freight"=>"SUM"));
$grid->renderGrid('#grid','#pager',true,$summaryrows , null, true,true);

