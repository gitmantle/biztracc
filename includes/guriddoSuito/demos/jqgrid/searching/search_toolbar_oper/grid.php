<?php
require_once '../../jq-config.php';
// include the jqGrid Class
require_once ABSPATH."php/PHPSuito/jqGrid.php";
// include the driver class
require_once ABSPATH."php/PHPSuito/DBdrivers/jqGridPdo.php";
// include the datepicker
//require_once ABSPATH."php/jqCalendar.php";

// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");
// Create the jqGrid instance
$grid = new jqGridRender($conn);
// Write the SQL Query
$grid->SelectCommand = 'SELECT OrderID, RequiredDate, ShipName, ShipCity, Freight FROM orders';
// Set output format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('grid.php');
// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>10,
    "rowList"=>array(10,20,30),
    "sortname"=>"OrderID"
	//"rowTotal"=>-1,
	//"loadonce"=>true
));

// Change some property of the field(s)
$grid->setColProperty("OrderID", array(
	"searchoptions"=>array("sopt"=>array("eq","ne","le","lt","ge","gt")),
    )
);


$grid->setColProperty("RequiredDate", array(
	"datefmt"=>'d.m.Y',
    "formatter"=>"date",
	"searchoptions"=>array("sopt"=>array("eq","ne","le","lt","ge","gt")),
    "formatoptions"=>array("srcformat"=>"Y-m-d H:i:s","newformat"=>"d.m.Y")
    )
);
$grid->setColProperty("ShipName", array("width"=>"200", "searchoptions"=>array("sopt"=>array('cn'))));

$grid->setColProperty("ShipCity", array( "searchoptions"=>array("sopt"=>array("eq","ne"))));
$grid->setColProperty("Freight", array("searchoptions"=>array("sopt"=>array("eq","gt","lt"))));

// Set the dates
$grid->setUserDate('d.m.Y');
$grid->setUserTime('d.m.Y');

$grid->setDbDate('Y-m-d');
$grid->setDbTime('Y-m-d');

// Set the datepicker
$grid->setDatepicker( "RequiredDate" );
// In this case no need to set a mm/dd/yyyy - it is get autoamatically from setUserDate


//and finaly set it to the grid
$grid->datearray= array( "RequiredDate" );
// Enable filter toolbar searching
$grid->toolbarfilter = true;
// Enable operation search
$grid->setFilterOptions(array("searchOperators"=>true));

// we set the select for ship city
$grid->setSelect("ShipCity", "SELECT DISTINCT ShipCity, ShipCity AS CityName FROM orders ORDER BY 2", false, false, true, array(""=>"All"));
$grid->navigator = true;
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false, "search"=>false));

//Trigger toolbar with custom button
$search = <<<SEARCH
jQuery("#searchtoolbar").click(function(){
	jQuery('#grid')[0].triggerToolbar();
	return false;
});
SEARCH;
$grid->setJSCode($search);
// Enjoy
$grid->renderGrid('#grid','#pager',true, null, null, true,true);

