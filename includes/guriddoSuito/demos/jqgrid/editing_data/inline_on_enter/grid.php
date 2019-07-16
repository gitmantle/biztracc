<?php
ini_set("display_errors","1");
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
$grid->SelectCommand = 'SELECT OrderID, CustomerID, OrderDate, Freight, ShipName FROM orders';
// set the ouput format to json
$grid->dataType = 'json';
$grid->table ="orders";
$grid->setPrimaryKeyId("OrderID");
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('grid.php');
$grid->cacheCount = true;
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"This is custom Caption",
    "rowNum"=>10,
    "sortname"=>"OrderID",
    "hoverrows"=>true,
    "rowList"=>array(10,20,50),
	"postData"=>array("grid_recs"=>776)
    ));
// Change some property of the field(s)
$grid->setColProperty("OrderID", array("label"=>"ID", "width"=>60, "editable"=>false));
$grid->setColProperty("OrderDate", array(
    "formatter"=>"date",
    "formatoptions"=>array("reformatAfterEdit"=>true, "srcformat"=>"Y-m-d H:i:s","newformat"=>"m/d/Y")
    )
);
$grid->setAutocomplete("CustomerID",false,"SELECT CustomerID, CompanyName FROM customers WHERE CompanyName LIKE ? ORDER BY CompanyName",null,true,true);
$grid->setDatepicker("OrderDate",array("buttonOnly"=>false));
$grid->datearray = array('OrderDate');
// Enjoy
$grid->navigator = false;
$onselrow = <<< ONSELROW
function(id, selected)
{
    if (id && id !== lastSelection) {
		var grid = $("#grid");
        grid.jqGrid('restoreRow',lastSelection);
        grid.jqGrid('editRow',id, {
			keys: true,
			onEnter : function(rowid, options, event) {
				if (confirm("Save the row with ID: "+rowid) === true) {
					$(this).jqGrid("saveRow", rowid, options );
				}
			}
		});
	}
    lastSelection = id;		
}
ONSELROW;
$grid->setGridEvent('onSelectRow', $onselrow);
$grid->renderGrid('#grid','#pager',true, null, null, true,true);
