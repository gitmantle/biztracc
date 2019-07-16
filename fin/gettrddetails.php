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

if(isset ($_REQUEST["cid"])) {
    $id = jqGridUtils::Strip($_REQUEST["cid"]);
} else {
    $id = "";
}

// Create the jqGrid instance
$grid = new jqGridRender($conn);

// the actual query for the grid data
$grid->SelectCommand = "select uid,itemcode,item,price,unit,quantity,tax,value,ref_no from ".$findb.".invtrans where ref_no = '".$id."'";

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('gettrddetails.php');

// Set some grid options
$grid->setGridOptions(array(
    "caption"=>"Line Items",
    "rowNum"=>7,
    "sortname"=>"itemcode",
    "rowList"=>array(7,50,100),
	"height"=>150,
	"width"=>750
    ));


// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("itemcode", array("label"=>"Item Code", "width"=>90));
$grid->setColProperty("item", array("label"=>"Item", "width"=>150));
$grid->setColProperty("price", array("label"=>"Price", "width"=>90, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("unit", array("label"=>"Unit", "width"=>70));
$grid->setColProperty("quantity", array("label"=>"Quantity", "width"=>70));
$grid->setColProperty("tax", array("label"=>"Tax", "width"=>70, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("value", array("label"=>"Value", "width"=>90, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("ref_no", array("label"=>"Ref", "width"=>50, "hidden"=>true));


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#trddetlist','#trddetpager',true, null, null,true,true,true);
?>




