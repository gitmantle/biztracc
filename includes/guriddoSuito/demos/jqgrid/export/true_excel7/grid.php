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
$grid->SelectCommand = 'SELECT OrderID, OrderDate, CustomerID, ShipName, Freight FROM orders';
// Set output format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('grid.php');
// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>10,
    "rowList"=>array(10,20,30),
    "sortname"=>"OrderID",
	"caption"=>"Excel export"
));
// Change some property of the field(s)
$grid->setColProperty("OrderDate", array(
    "formatter"=>"date",
    "formatoptions"=>array("srcformat"=>"Y-m-d H:i:s","newformat"=>"m/d/Y"),
    "search"=>false
    )
);
$grid->setColProperty("ShipName", array("label"=>"Shipper Name","width"=>"200"));
$grid->setSelect('CustomerID', "SELECT CustomerID, CompanyName FROM customers");
// Enable navigator
$grid->navigator = true;
// Enable excel export
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// Set different Excel options (all available)
// a PHP excel lib should be preset 
$grid->setExcelOptions(array(
	"file_type"=>"Excel2007", //Excel2007,Excel5,xml
	"file"=>"report.xlsx",
	"start_cell" => "A1",
	"creator"=>"jqGrid",
	"author"=>"jqGrid",
	"title"=>"jqGrid Excel",
	"subject"=>"Office 2007 XLSX Document",
	"description"=>"Document created with Guriddo",
	"keywords"=>"Guriddo, jqGrid, Excel",
	"font"=>"Arial",
	"font_size"=>11,
	"header_title"=>"Report created with jqGrid",
	"protect" => false,
	"password"=>"Guriddo",
	"path_to_phpexcel_class"=>"External/phpexcel/PHPExcel.php"
));
$grid->exportfile = 'Report.xls';
// Enjoy
$grid->renderGrid('#grid','#pager',true, null, null, true,true);

