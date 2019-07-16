<?php
ini_set("diplay_errors",1);
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
$grid->SelectCommand = 'SELECT a.OrderID, a.RequiredDate, a.ShipName, a.ShipCity, a.Freight FROM orders a';
// Set output format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('grid.php');
//$grid->debug = true;
// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>10,
    "rowList"=>array(10,20,30),
	"footerrow" => true,
	"userDataOnFooter" => true
    //"sortname"=>"Ordera"
));
// Change some property of the field(s)
$grid->setColProperty("RequiredDate", array(
    "formatter"=>"date",
    "formatoptions"=>array("srcformat"=>"Y-m-d H:i:s","newformat"=>"m/d/Y")
    )
);
$grid->setDatepicker('RequiredDate');
$grid->setColProperty("OrderID", array("searchoptions"=>array("sopt"=>array("bt","eq","ne","le","ge","nu"))));
$grid->setColProperty("ShipName", array("width"=>"200", "searchoptions"=>array("sopt"=>array("eq","ne","le","ge","nu","bw"))));
// Enable filter toolbar searching
$grid->toolbarfilter = true;
$grid->setFilterOptions(array(
	"stringResult"=> true,
    "searchOperators"=>true
));
// we set the select for ship city
$grid->setSelect("ShipCity", "SELECT DISTINCT ShipCity, ShipCity AS CityName FROM orders ORDER BY 2", false, false, true, array(""=>"All"));
$grid->navigator = true;
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false, "search"=>false));
$summaryrows = array("Freight" => array("Freight" => "SUM"));
// Enjoy
$grid->renderGrid('#grid','#pager',true, $summaryrows, null, true,true);

