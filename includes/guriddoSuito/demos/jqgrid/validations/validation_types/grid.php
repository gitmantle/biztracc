<?php
date_default_timezone_set('Europe/London');
require_once '../../jq-config.php';
// include the jqGrid Class
require_once ABSPATH."php/PHPSuito/jqGrid.php";
// include the driver class
require_once ABSPATH."php/PHPSuito/DBdrivers/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
$conn->query("SET NAMES 'utf8'");

// Create the jqGrid instance
$grid = new jqGridRender($conn);
// Write the SQL Query
$grid->SelectCommand = 'SELECT OrderID, CustomerID, Freight, OrderDate, ShipCity, ShipAddress, ShipVia, "support@guriddo.net" as ShipMail, "http://guriddo.net" as ShipUrl FROM orders';
// set the ouput format to json
$grid->table = 'orders';
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
	"height"=>150)
);
// Change some property of the field(s)
$grid->setColProperty("OrderDate", array(
    "formatter"=>"date",
    "formatoptions"=>array("srcformat"=>"Y-m-d H:i:s","newformat"=>"m/d/Y"),
    "search"=>true,
    "editable"=>true
    )
);
$grid->setColProperty("OrderID",array("editable"=>false));
//$grid->setColProperty("ShipCity",array("editable"=>false));
$grid->setSelect('CustomerID', "SELECT CustomerID, CompanyName FROM customers");
// Enable toolbar searching
$grid->navigator = true;
$grid->setNavOptions('navigator', array("del"=>false,"excel"=>false,"search"=>false,"refresh"=>false));
$grid->setNavOptions('edit', array("width"=>400, "height"=>'auto',"dataheight"=>"auto"));
$grid->setNavOptions('add', array("width"=>400, "height"=>'auto',"dataheight"=>"auto"));
$grid->setUserTime('m/d/Y');
$grid->datearray = array('Orderdate');

// Enable server validation
$grid->serverValidate = true;

// Build server validations array
$validations = array(
    'ShipCity' => array("text"=>true, "required"=>true),
    'ShipAddress' => array("text"=>true, "required"=>true),
	'Freight'=>array("number"=>true,"minValue"=>10,"maxValue"=>50),
	'ShipVia'=>array("integer"=>true, "minValue"=>1, "maxValue"=>3, "required"=>true),
	'ShipMail'=>array("email"=>true),
	'ShipUrl'=>array("url"=>true),
	'OrderDate'=>array("date"=>true, "format"=>'m/d/Y', "minValue"=>"12/31/2001") // set the user input format
);

//set the rules
$grid->setValidationRules($validations);

// Enjoy
$grid->renderGrid('#grid','#pager',true, null, null, true,true);

