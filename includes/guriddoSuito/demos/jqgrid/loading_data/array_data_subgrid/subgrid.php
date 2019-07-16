<?php
require_once '../../jq-config.php';
// include the jqGrid Class
require_once ABSPATH."php/jqGrid.php";
// include the driver class
require_once ABSPATH."php/jqGridArray.php";
// Connection to the server
//$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
//$conn->query("SET NAMES utf8");
// Get the needed parameters passed from the main grid
// By default we add to postData subgrid and rowid parameters in the main grid
$subtable = jqGridUtils::GetParam("subgrid");
$rowid = (integer)jqGridUtils::GetParam("rowid");

if(!$subtable && !$rowid) { die("Missed parameters"); }
// Create the jqGrid 
$conn = new jqGridArray();
// Create the jqGrid instance
$grid = new jqGridRender($conn);
// Create a random array data
for ($i = 0; $i < 4; $i++)
{
	$datasubgrid[$i]['idgrid']	= (integer)$rowid;
	$datasubgrid[$i]['num'] = (integer)($i+1);
	$datasubgrid[$i]['foosubgrid']	= md5(rand(0, 10000));
	$datasubgrid[$i]['barsubgrid']	= 'bar'.($i+1);
}

// Write the SQL Query
$grid->SelectCommand = "SELECT idgrid, num, foosubgrid, barsubgrid FROM datasubgrid WHERE idgrid = ?";
// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel(null,array($rowid));
// Set the url from where we obtain the data
$grid->setUrl('subgrid.php');
//$grid->debug = true;
// Set some grid options
$grid->setGridOptions(array(
    "width"=>540,
    "rowNum"=>10,
    "sortname"=>"idgrid",
    "height"=>110,
    "postData"=>array("subgrid"=>$subtable,"rowid"=>$rowid)));
// Change some property of the field(s)
$grid->navigator = true;
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// Enjoy
$subtable = $subtable."_t";
$pager = $subtable."_p";
$grid->renderGrid($subtable,$pager, true, null, array($rowid), true,true);

