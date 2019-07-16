<?php
//session_start();



include 'jq-config.php';

$dref = $_SESSION['s_delnote'];

// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");

// Create the jqGrid instance
$grid = new jqGridRender($conn);

// enable debugging
//$grid->debug = true;

// Write the SQL Query
// We suppose that mytable exists in your database
$grid->SelectCommand = "select uid,itemcode,item,unit,quantity from quotelines where ref_no = '".$dref."'"; 


// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getdnlines.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Delivery Note Lines - ".$dref,
    "rowNum"=>12,
    "sortname"=>"item",
    "rowList"=>array(12,30,50),
	"height"=>300,
	"width"=>900
	));



// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "editable"=>false, "width"=>20, "hidden"=>true));
$grid->setColProperty("itemcode", array("label"=>"Code", "width"=>70));
$grid->setColProperty("item", array("label"=>"Item", "width"=>120));
$grid->setColProperty("unit", array("label"=>"Unit", "width"=>50));
$grid->setColProperty("quantity", array("label"=>"Quantity", "width"=>70, "align"=>"right","formatter"=>"number"));


$grid->gSQLMaxRows = 4000;

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#dnlinelist','#dnlinepager',true, null, null, true,true);

?>



