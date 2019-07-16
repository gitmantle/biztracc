<?php
require_once '../../jq-config.php';
// include the jqGrid Class
require_once ABSPATH."php/PHPSuito/jqGrid.php";
// include the driver class
require_once ABSPATH."php/PHPSuito/DBdrivers/jqGridPdo.php";
$inv = jqGridUtils::GetParam("inv");

// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");
// Create the jqGrid instance
$grid = new jqGridRender($conn);
// Write the SQL Query
$grid->SelectCommand = 'SELECT OrderID, RequiredDate, ShipName, ShipCity, Freight FROM orders WHERE OrderID <= ?';
// Set output format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel(null,array($inv));

// Set the url from where we obtain the data
$grid->setUrl('grid.php');
// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>30,
    "rowList"=>array(10,20,30),
    "sortname"=>"OrderID",
	"postData"=>array("inv"=>$inv)
));
// Change some property of the field(s)
$grid->setColProperty("RequiredDate", array(
    "formatter"=>"date",
	"searchoptions"=>array("sopt"=>array('eq')),
	"stype"=>"text",
    "formatoptions"=>array("srcformat"=>"Y-m-d H:i:s","newformat"=>"m.d.Y")
    )
);
$grid->setColProperty("ShipName", array("width"=>"200"));
// Enable filter toolbar searching
$grid->toolbarfilter = true;
$grid->setFilterOptions(array("stringResult"=>false, "searchOperators"=>true));
// we set the select for ship city
$grid->setSelect("ShipCity", "SELECT DISTINCT ShipCity, ShipCity AS CityName FROM orders ORDER BY 2", false, false, true, array(""=>"All"));
$grid->navigator = true;
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false, "search"=>false));

// Enjoy

$grid->renderGrid('#grid','#pager',true, null, array($inv), true,true);