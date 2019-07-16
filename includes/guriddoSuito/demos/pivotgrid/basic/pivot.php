<?php
require_once '../jq-config.php';
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
	"sortname" => "CategoryName",
    "rowList"=>array(10,20,50),
));
// Grid xDimension settings
$pivot->setxDimension(array(
	array("dataName"=>"CategoryName", "width"=>150)
));

// Members
$pivot->setaggregates(array(
	array(
		"member"=>'Price',
		"aggregator"=>'sum',
		"width"=>80,
		"label"=>'Sum Price',
		"formatter"=>'number',
		"align"=>'right'
	)
));

$pivot->renderPivot("#grid","#pager", true, null, true, true);
