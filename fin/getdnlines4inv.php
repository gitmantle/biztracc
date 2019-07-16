<?php
//session_start();



include 'jq-config.php';

$dref = $_SESSION['s_salesorder'];

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
$grid->SelectCommand = "select uid,ref_no,ddate,currency,totvalue,tax from quotes where xref = '".$dref."' and ref_no like 'D_N%' and invref = ''"; 
// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getdnlines4inv.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Uninvoiced Delivery Notes against ".$dref,
    "rowNum"=>12,
    "sortname"=>"ddate",
    "rowList"=>array(12,30,50),
	"multiselect"=>true,
	"height"=>300,
	"width"=>700
	));



// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "editable"=>false, "width"=>20, "hidden"=>true));
$grid->setColProperty("ref_no", array("label"=>"Reference", "width"=>70));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("currency", array("label"=>" ", "width"=>70));
$grid->setColProperty("totvalue", array("label"=>"Value", "width"=>80, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("tax", array("label"=>"Tax", "width"=>60, "align"=>"right","formatter"=>"number"));


$grid->gSQLMaxRows = 4000;

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#dn4invlist','#dn4invpager',true, null, null, true,true);

?>



