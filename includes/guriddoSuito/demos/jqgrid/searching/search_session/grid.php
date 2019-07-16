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
$grid->SelectCommand = 'SELECT OrderID, OrderDate, CustomerID, ShipName, Freight FROM orders';
// Set output format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('grid.php');
if(!isset($_SESSION)) {
	session_start();
}
if( jqGridUtils::GetParam('_search') == 'true') {
	$searchstring = jqGridUtils::GetParam("filters");
	$searchobject = jqGridUtils::decode($searchstring);
	if (isset($searchobject['rules']) && count($searchobject['rules'])>0 ) {
		foreach($searchobject['rules'] as $key=>$val) {
			if(isset($val['field'])) {
				$field = $val['field'];
				$_SESSION[$field]= $val['data'];
			}				
		}
	}
}
$myorder = isset($_SESSION['OrderID'])? $_SESSION['OrderID'] : "10255";



// initialsearch 
$sarr = <<< FFF
{ "groupOp":"AND",
	"rules":[
	  {"field":"OrderID","op":"lt","data":"$myorder"}
	 ]
}
FFF;
// other fields can be added here with
//{"field":"Freight","op":"gt","data":"0"}

// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>10,
    "rowList"=>array(10,20,30),
    "sortname"=>"OrderID",
	// set the initila search upon loading
	"search"=>true, 
	// setr criteria
	"postData"=>array( "filters"=> $sarr )
));
//toolbarfilter sttings
$grid->setColProperty("OrderID", array(
	// the value set in filters is 10255
    "searchoptions"=>array("defaultValue"=>"$myorder", "sopt"=>array("lt"))
    )
);


// Change some property of the field(s)
$grid->setColProperty("OrderDate", array(
    "formatter"=>"date",
    "formatoptions"=>array("srcformat"=>"Y-m-d H:i:s","newformat"=>"m/d/Y")
    )
);
$grid->setColProperty("ShipName", array("width"=>"200"));
// Enable navigator
$grid->navigator = true;
$grid->toolbarfilter = true;
// Enable search
// By default we have multiple search enabled
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// In order to enable the more complex search we should set multipleGroup option
// Also we need show query roo
$grid->setNavOptions('search', array(
	"multipleGroup"=>true,
	"showQuery"=>true
));
// Enjoy
$grid->renderGrid('#grid','#pager',true, null, null, true,true);
$conn = null;
?>
