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
$grid->SelectCommand = 'SELECT OrderID, RequiredDate, ShipName, ShipCity, Freight FROM orders';
// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('grid.php');
// Set some grid options
$grid->setGridOptions(array("rowNum"=>10,"rowList"=>array(10,20,130),"sortname"=>"OrderID"));
// Enable footerdata an tell the grid to obtain it from the request
$grid->setGridOptions(array("footerrow"=>true,"userDataOnFooter"=>true));
// Change some property of the field(s)
$grid->setColProperty("RequiredDate", array("formatter"=>"date","formatoptions"=>array("srcformat"=>"Y-m-d H:i:s","newformat"=>"m/d/Y")));
// At end call footerData to put total  label
$grid->callGridMethod('#grid', 'footerData', array("set",array("OrderID"=>"Total with very very big text:")));
// Set which parameter to be sumarized
$summaryrows = array("Freight"=>array("Freight"=>"SUM"));
$code = <<<MYCODE
setTimeout(function(){
// set the text in the first column
// remove the formated style
$('tr.footrow','.ui-jqgrid-ftable').removeClass('footrow-ltr');
var styles= {'border-right-width': "1px", "border-right-color": "inherit", "border-right-style": "solid"};
// add the style only to a cells with sums
$('tr.footrow td:gt(2)').each(function(){ 
	$(this).css( styles );
});
// make the long text visible in the first cell
$('tr.footrow td:eq(0)').each(function(){
	$(this).css( 'overflow','visible');
});
},50);		
MYCODE;
$grid->setJSCode($code);
$grid->renderGrid('#grid','#pager',true,$summaryrows , null, true,true);

