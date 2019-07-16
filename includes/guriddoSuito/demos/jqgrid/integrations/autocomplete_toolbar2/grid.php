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
$grid->SelectCommand = 'SELECT OrderID, OrderDate, CustomerID, Freight, ShipName FROM orders';
// set the ouput format to json
$grid->dataType = 'json';
$grid->table ="orders";
$grid->setPrimaryKeyId("OrderID");
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('grid.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Autocomplete in tollbar search",
    "rowNum"=>10,
    "sortname"=>"OrderID",
    "hoverrows"=>true,
    "rowList"=>array(10,20,50),
    ));
// Change some property of the field(s)
$grid->setColProperty("OrderID", array("label"=>"ID", "width"=>60));
$grid->setColProperty("OrderDate", array(
    "formatter"=>"date",
    "formatoptions"=>array("srcformat"=>"Y-m-d H:i:s","newformat"=>"m/d/Y")
    )
);

// Search on select autocomplete
$autocmp = <<< ENTER
// ShipName is the field, gs_ShipName is the searchinput
$("#gs_ShipName").on("autocompleteselect", function (event, ui) {

	// Rebind the default action from grid
	$(event.target).trigger('change');
	
	// trigger the serch on select
	var grid = $("#grid")[0];
	grid.triggerToolbar();
	}
);
ENTER;
$grid->setJSCode($autocmp);
// set autocomplete. Serch for name and ID, but select a ID
// set it only for editing and not on serch
$grid->setAutocomplete("ShipName", false,"SELECT DISTINCT ShipName FROM orders WHERE ShipName LIKE ? ORDER BY ShipName", null, false, true );
$grid->datearray = array('OrderDate');
// Enjoy
$grid->navigator = true;
$grid->toolbarfilter = true;
$grid->setNavOptions('navigator', array("search"=>false, "excel"=>false,"add"=>false, "edit"=>false, "del"=>false));
$grid->renderGrid('#grid','#pager',true, null, null, true,true);

