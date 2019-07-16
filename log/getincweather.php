<?php

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
$grid->SelectCommand = "select uid,incweather from incweather";

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model

// set the primary key - it is serial
$grid->setPrimaryKeyId('uid');

$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getincweather.php');

// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Types of Weather",
    "rowNum"=>20,
    "sortname"=>"uid",
    "rowList"=>array(20,50,80),
	"width"=>760,
	"height"=>450
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("incweather", array("label"=>"Type of Weather", "width"=>200));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>60));


$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#incweatherlist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#incweatherlist").getRowData(ids[i]);
			var cl = ids[i];
			be = '<img src="../images/edit.png" title="Edit Weather Type" onclick="javascript:editincweather('+cl+')" ></ids>';
			jQuery("#incweatherlist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// enable form editing
$grid->navigator = true;
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#incweatherpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add Weather Type", "onClickButton"=>"js: function(){addincweather();}")
);
$grid->callGridMethod("#incweatherlist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#incweatherlist','#incweatherpager',true, null, null, true,true);
?>



