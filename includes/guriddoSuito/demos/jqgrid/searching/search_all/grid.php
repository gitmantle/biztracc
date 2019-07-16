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

$grid->SelectCommand = 'SELECT CustomerID, CompanyName, Phone, PostalCode, City FROM customers';
// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('grid.php');
// Set some grid options
$grid->setGridOptions(array(
	"rowNum"=>10,
	"rowTotal" => -1,
	"rowList"=>array(10,20,30),
	"sortname"=>"CustomerID",
	"loadonce" =>true,
	"height"=>150)
);
$code = <<<CODE
	var timer;
	$("#search_cells").on("keyup", function() {
		var self = this;
		if(timer) { clearTimeout(timer); }
		timer = setTimeout(function(){
			//timer = null;
			$("#grid").jqGrid('filterInput', self.value);
		},0);
	});
CODE;
$grid->setJSCode($code);
// Enable navigator searching
$grid->navigator = true;
// Set which buttons should be visible
$grid->setNavOptions('navigator',array("add"=>false,"edit"=>false,"del"=>false,"view"=>false,"excel"=>false));
// Enjoy
$grid->renderGrid('#grid','#pager',true, null, null, true,true);