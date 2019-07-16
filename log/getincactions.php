<?php
error_reporting (E_ALL ^ E_NOTICE ^ E_WARNING);

session_start();
$incid = $_SESSION['s_incidentid'];

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
$grid->SelectCommand = "select uid,action,bywhom,date_done from incactions where incident_id = ".$incid;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model

// set the primary key - it is serial
$grid->setPrimaryKeyId('uid');

$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getincactions.php');

// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Actions to ensure incident does not happen again",
    "rowNum"=>20,
    "sortname"=>"uid",
    "rowList"=>array(20,50,80),
	"width"=>800,
	"height"=>400
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("action", array("label"=>"Action to be undertaken", "width"=>300));
$grid->setColProperty("bywhom", array("label"=>"Person responsible for action", "width"=>150));
$grid->setColProperty("date_done", array("label"=>"Date completed", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>60));


$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#incactionlist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#incactionlist").getRowData(ids[i]);
			var cl = ids[i];
			be = '<img src="../images/edit.png" title="Edit Action" onclick="javascript:editincaction('+cl+')" ></ids>';
			jQuery("#incactionlist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// enable form editing
$grid->navigator = true;
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#incactionpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add an Action", "onClickButton"=>"js: function(){addincaction();}")
);
$grid->callGridMethod("#incactionlist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#incactionlist','#incactionpager',true, null, null, true,true);
?>



