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
$pivot = new jqPivotGrid($conn);
// Write the SQL Query
$pivot->SelectCommand = "SELECT c.CategoryName, b.ProductName, e.Country, SUM( a.Quantity * a.UnitPrice ) AS Price, SUM(a.Quantity) AS Quantity 
FROM order_details a, products b, categories c, orders d, customers e
WHERE a.ProductID = b.ProductID
AND b.CategoryID = c.CategoryID
AND a.OrderID = d.OrderID
AND d.CustomerID = e.CustomerID
AND (
e.Country = 'UK'
OR e.Country = 'USA' 
OR e.Country = 'Germany'
)
GROUP BY a.ProductID, e.Country";

// Set the url from where we obtain the data
$pivot->setData('pivot.php');

// Grid creation options
$pivot->setGridOptions(array(
	"rowNum"=>10,
	"shrinkToFit"=>false,
	"height"=>200,
	"sortname" => "CategoryName",
    "rowList"=>array(10,20,50),
	"caption"=>"Many members"
));
// Grid xDimension settings
$pivot->setxDimension(array(
	array("dataName"=>"CategoryName", "width"=>100),
	array("dataName" => "ProductName")
));

// Grid yDimension settings
$pivot->setyDimension(array(
	array("dataName" => "Country")
));


// Members
$pivot->setaggregates(array(
	array(
		"member"=>'Price',
		"aggregator"=>'sum',
		"width"=>80,
		"label"=>'Sum',
		"formatter"=>'number',
		"align"=>'right',
		// the summary type set the sum function of the groups
		"summaryType"=>'sum'
	),
	array(
		"member"=>'Quantity',
		"aggregator"=>'sum',
		"width"=>80,
		"label"=>'Qty',
		"formatter"=>'number',
		"align"=>'right',
		"summaryType"=>'sum'		
	)
));
// Set the pivot options
$pivot->setPivotOptions(array(
	"rowTotals" => true, 
	"colTotals" => true
));


// Enable navigator
$pivot->navigator = true;
// Enable excel export
$pivot->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false, "search"=>false, "refresh"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function

$buttonoptions = array("#pager",
    array("caption"=>"Csv", "title"=>"Local Export to CSV", "onClickButton"=>"js: function(){
		jQuery('#grid').jqGrid('exportToCsv');}"
	)
);
$pivot->callGridMethod("#grid", "navButtonAdd", $buttonoptions,500);
$buttonoptions = array("#pager",
    array("caption"=>"Excel", "title"=>"Local Export to Escel", "onClickButton"=>"js: function(){
		jQuery('#grid').jqGrid('exportToExcel');}"
	)
);
$pivot->callGridMethod("#grid", "navButtonAdd", $buttonoptions,500);
$buttonoptions = array("#pager",
    array("caption"=>"Pdf", "title"=>"Local Export to PDF", "onClickButton"=>"js: function(){
		jQuery('#grid').jqGrid('exportToPdf',{orientation:'landscape'});}"
	)
);
$pivot->callGridMethod("#grid", "navButtonAdd", $buttonoptions,500);



$pivot->renderPivot("#grid","#pager", true, null, true, true);
