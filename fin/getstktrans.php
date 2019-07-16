<?php
error_reporting (E_ALL ^ E_NOTICE);

include 'jq-config.php';

// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);

// Create the jqGrid instance
$grid = new jqGridRender($conn);


// the actual query for the grid data
$grid->SelectCommand = "SELECT uid,itemcode,ddate,increase,decrease,ref_no,amount from stktrans";

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getstktrans.php');


// Set some grid options
$grid->setGridOptions(array(
    "caption"=>"Stock Movements",
    "rowNum"=>20,
    "sortname"=>"ddate",
    "rowList"=>array(50,100,200),
	"height"=>200,
	"width"=>900
    ));



// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("itemcode", array("label"=>"Stock code", "width"=>80));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("incease", array("label"=>"Increase", "width"=>80));
$grid->setColProperty("decrease", array("label"=>"Decrease", "width"=>80));
$grid->setColProperty("ref_no", array("label"=>"Reference", "width"=>75));
$grid->setColProperty("amount", array("label"=>"Amount", "width"=>25));


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));


// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#stkmovepager",
    array("buttonicon"=>"ui-icon-newwin","caption"=>"","position"=>"last","title"=>"Output to Excel.", "onClickButton"=>"js: function(){stk2xl();}")
);
$grid->callGridMethod("#stkmovelist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#stkmovelist','#stkmovepager',true, null, null,true,true,true);
$conn = null;

?>




