<?php
session_start();
date_default_timezone_set($_SESSION['s_timezone']);

$crndb = $_SESSION['s_crndb'];

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
$grid->SelectCommand = "select uid,operator,address,phone,mobile,email,rate from ".$crndb.".operators";

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model

// set the primary key - it is serial
$grid->setPrimaryKeyId('uid');

$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getoperators.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Drivers/Operators",
    "rowNum"=>20,
    "sortname"=>"operator",
    "rowList"=>array(20,50,80),
	"width"=>960,
	"height"=>450
    ));


// Change some property of the field(s)
$grid->addCol(array("name"=>"act"),"last");
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("operator", array("label"=>"Driver/Operator", "width"=>70));
$grid->setColProperty("address", array("label"=>"Address", "width"=>70));
$grid->setColProperty("phone", array("label"=>"Phone", "width"=>70));
$grid->setColProperty("mobile", array("label"=>"Mobile", "width"=>80));
$grid->setColProperty("email", array("label"=>"Email", "width"=>70));
$grid->setColProperty("rate", array("label"=>"Hourly Rate", "width"=>50, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#oplist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#oplist").getRowData(ids[i]);
			var cl = ids[i];
			be = '<img src="../images/edit.png" title="Edit Driver/Operator" onclick="javascript:editoperator('+cl+')" ></ids>';
			jQuery("#oplist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// enable form editing
$grid->navigator = true;
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#oppager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add Driver/Operator", "onClickButton"=>"js: function(){addoperator();}")
);
$grid->callGridMethod("#oplist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#oplist','#oppager',true, null, null, true,true);
?>



