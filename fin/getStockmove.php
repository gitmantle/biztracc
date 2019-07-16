<?php
session_start();
//ini_set('display_errors', true);

$findb = $_SESSION['s_findb'];

include 'jq-config.php';

// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");
// Get the needed parameters passed from the main grid

if(isset ($_REQUEST["itid"])) {
    $id = jqGridUtils::Strip($_REQUEST["itid"]);
} else {
    $id = '';
}

// Create the jqGrid instance
$grid = new jqGridRender($conn);

// the actual query for the grid data
$grid->SelectCommand = "select uid,ddate,increase,decrease,locid,ref_no from ".$findb.".stktrans where itemcode = '".$id."'";

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getStockmove.php');

// Set some grid options
$grid->setGridOptions(array(
	"caption"=>"Stock Movement",						
    "rowNum"=>7,
    "sortname"=>"ddate",
	"sortorder"=>"desc",
    "rowList"=>array(7,50,100),
	"height"=>182,
	"width"=>880
    ));

// Enable footerdata an tell the grid to obtain it from the request
$grid->setGridOptions(array("footerrow"=>true,"userDataOnFooter"=>true));

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>100, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("increase", array("label"=>"Increase", "width"=>70));
$grid->setColProperty("decrease", array("label"=>"Decrease", "width"=>50));
$grid->setColProperty("locid", array("label"=>"Location", "width"=>50));
$grid->setColProperty("ref_no", array("label"=>"Reference", "width"=>50));

// At end call footerData to put total  label
$grid->callGridMethod('#stkmovelist', 'footerData', array("set",array("ddate"=>"Balances:")));
// Set which parameter to be sumarized
$summaryrows = array("increase"=>array("increase"=>"SUM"),"decrease"=>array("decrease"=>"SUM"));


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#stkmovelist','#stkmovepager',true, $summaryrows, null,true,true,true);
?>




