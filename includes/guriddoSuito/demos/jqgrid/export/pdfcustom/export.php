<?php
require_once '../../jq-config.php';
// include the jqGrid Class
require_once ABSPATH."php/jqGrid.php";
// include the driver class
require_once ABSPATH."php/jqGridPdo.php";
// LOAD lang file
require_once(ABSPATH.'/php/tcpdf/config/lang/eng.php');
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);

// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");
// Create the jqGrid instance
$grid = new jqGridrender($conn);
// Write the SQL Query
$grid->SelectCommand = 'SELECT OrderID, OrderDate, CustomerID, ShipName, Freight FROM orders';
$grid->setColModel();
// we want to export additinal data when excel
$grid->renderGrid('#grid','#pager',true, null, null, true,true);
?>
