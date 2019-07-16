<?php

//ini_set('display_errors', true);
session_start();
$usersession = $_SESSION['usersession'];

$ddate = date("d/m/Y");
$hdate = date("Y-m-d");

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

// Create the jqGrid instance
$grid = new jqGridRender($conn);
// Write the SQL Query
// We suppose that mytable exists in your database
$grid->SelectCommand = "SELECT uid,descript,currency,rate,symbol,def_forex from ".$findb.".forex";

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getforex.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Foreign Exchange Rates",
    "rowNum"=>12,
    "sortname"=>"descript",
    "rowList"=>array(12,30,50),
	"height"=>270,
	"width"=>700
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("descript", array("label"=>"Currency", "width"=>100));
$grid->setColProperty("currency", array("label"=>"Code", "width"=>70));
$grid->setColProperty("rate", array("label"=>"Exchange Rate", "width"=>100, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("symbol", array("label"=>"Symbol", "width"=>70));
$grid->setColProperty("def_forex", array("label"=>"Local Currency", "width"=>70));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>70));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#forexlist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#forexlist").getRowData(ids[i]);
			var cl = ids[i];
			be = '<img src="../images/edit.png" title="Edit Forex" onclick="javascript:editforex('+cl+')" ></ids>';
			jQuery("#forexlist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

$grid->gSQLMaxRows = 4000;

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#forexpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add forex rate", "onClickButton"=>"js: function(){addforex();}")
);
$grid->callGridMethod("#forexlist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#forexlist','#forexpager',true, null, null, true,true);


?>




