<?php
ini_set('display_errors', 1);
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
$grid->SelectCommand = 'SELECT CONCAT(a.OrderID,a.ProductID) as Unc, a.OrderID, b.ProductName, a.Quantity, a.UnitPrice, a.Quantity*a.UnitPrice as TotalLine FROM order_details a, products b WHERE a.ProductID=b.ProductID';
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
    "height"=>'200',
    "grouping"=>true,
    "groupingView"=>array(
    	"groupField" => array('OrderID'),
    	"groupColumnShow" => array(true),
    	"groupText" =>array('<b>{0} - {1} product(s)</b> '),
    	"groupDataSorted" => true,
    	"groupSummary" => array(true)
    ) 
    ));
// Change some property of the field(s)
$grid->setColProperty("Unc", array("hidden"=>true));
$grid->setColProperty("OrderID", array("label"=>"OrderID", "width"=>60));
// Add a avg and summary properties
$grid->setColProperty("UnitPrice", array("width"=>100,"align"=>"right"));
$grid->setColProperty("TotalLine", array("width"=>100,"align"=>"right", "summaryType"=>"avg", "summaryTpl"=>"AVG: {0}" , "summaryRound"=>2, "formatter"=>"number"));
$grid->setColProperty("Quantity", array("width"=>100,"align"=>"right", "summaryType"=>"sum", "summaryTpl"=>"{0}"));
$grid->navigator = true;
$grid->setNavOptions('navigator', array("pdf"=>true));
// Enjoy
$grid->renderGrid('#grid','#pager',true, null, null, true,true);

