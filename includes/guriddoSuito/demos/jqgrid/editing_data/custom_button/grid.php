<?php
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
$grid->SelectCommand = 'SELECT OrderID, CustomerID, Freight, OrderDate, ShipCity FROM orders';
// set the ouput format to json
$grid->table = 'orders';
$grid->dataType = 'json';
$grid->setPrimaryKeyId('OrderID');
$grid->serialKey = true;
// this enables returning the result after insert in case of serialKey
$grid->getLastInsert = true;
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('grid.php');
// Set some grid options
$grid->setGridOptions(array(
	"rowNum"=>10,
	"rowList"=>array(10,20,30), 
	"sortname"=>"OrderID",
	"height"=>150
));
// Change some property of the field(s)
$grid->setColProperty("OrderDate", array(
    "formatter"=>"date",
    "formatoptions"=>array("srcformat"=>"Y-m-d H:i:s","newformat"=>"m/d/Y"),
    "search"=>false,
    )
);

$grid->setUserTime('m/d/Y');
$grid->setUserDate('m/d/Y');

$grid->setColProperty("OrderID",array("editable"=>false, "key"=>true));
$grid->setSelect('CustomerID', "SELECT CustomerID, CompanyName FROM customers");
// Enable toolbar searching
$grid->navigator = true;
$grid->setNavOptions('navigator', array("del"=>false,"excel"=>true,"search"=>false,"refresh"=>false));
$grid->setNavOptions('edit', array(
	"height"=>'auto',
	"dataheight"=>"auto", 
	"width"=>"auto", 
	"reloadAfterSubmit"=>false,
// definition of custom button in edit form
	"buttons" => array(
		array(
			'side' => "right",
			'text' => "Custom",
			'position' => "first",
			'click' => 'js:function( form, params, event) {
				alert("Custom action in edit form");
			}'
		)
	)
));
$grid->setNavOptions('add', array("height"=>'auto',"dataheight"=>"auto", "width"=>"auto", "reloadAfterSubmit"=>false));

$after = <<< AFTER
function( data, postdata, oper)
{
	// when grid -> getLastInsert = true;  the key is send like keyName#Value
	var retdata = data.responseText.split('#');
		//console.log(retdata);
		//console.log(postdata);
	if( retdata[0] && retdata[1] != null )
	{
		// make the selection
		setTimeout(function(){ $('#grid').jqGrid('setSelection', retdata[1]) }, 200);
		// return the key so that it will be set in grid
		return[true,'',retdata[1]];
	}
	return [false, 'Error during add'];
}
AFTER;

$grid->setNavEvent('add', 'afterSubmit', $after);
// Enjoy
$grid->renderGrid('#grid','#pager',true, null, null, true,true);
$conn = null;
?>
