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
$grid1 = new jqGridRender($conn);
// Write the SQL Query
$grid1->SelectCommand = 'SELECT OrderID, OrderDate, CustomerID, ShipName, Freight FROM orders WHERE OrderID>=50000';
// set the ouput format to json
$grid1->dataType = 'json';
// Let the grid create the model
$grid1->setColModel();
// Set the url from where we obtain the data
$grid1->setUrl('grid2.php');
// Set in grid options scroll to 1
$grid1->setGridOptions(array("rowNum"=>100,"sortname"=>"OrderID","height"=>200));
// Change some property of the field(s)
$grid1->setColProperty("OrderID", array("width"=>80));
$grid1->setColProperty("OrderDate", array(
    "formatter"=>"date",
    "formatoptions"=>array("srcformat"=>"Y-m-d H:i:s","newformat"=>"m/d/Y")
    )
);

$dragdrop = <<< DRAG
jQuery("#grid").jqGrid('gridDnD',{
	connectWith:'#grid2',
	beforedrop: function(ev, ui, row) {
		jQuery.ajax({
			url: 'dragdrop.php',
			data : row,
			async: false,
			success : function( response )
			{
				alert(response);
				// we can cancel drag and drop if we have to set
				// ui.helper.dropped =  false;
			}
		})
	}
});
DRAG;

$grid1->setJSCode($dragdrop);
// Enjoy
$grid1->renderGrid('#grid2','#pager2',true, null, null, true,true);

