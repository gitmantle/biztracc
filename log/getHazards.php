<?php
session_start();
$rid = $_SESSION['s_route'];

require_once('../db.php');

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

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
$grid->SelectCommand = "select uid,ddate,hazard,risk,strategy from site_hazards where routeid = ".$rid;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model

// set the primary key - it is serial
$grid->setPrimaryKeyId('uid');

$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getHazards.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Site Hazards",
    "rowNum"=>10,
    "sortname"=>"ddate",
    "rowList"=>array(10,50,80),
	"width"=>660,
	"height"=>230
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("hazard", array("label"=>"Hazard", "width"=>250));
$grid->setColProperty("risk", array("label"=>"Risk", "width"=>90));
$grid->setColProperty("strategy", array("label"=>"Strategy", "width"=>90));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>60));


$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#hazardlist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#hazardlist").getRowData(ids[i]);
			var cl = ids[i];
			be = '<img src="../images/edit.png" title="Edit hazard" onclick="javascript:edithazard('+cl+')" ></ids>';
			jQuery("#hazardlist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// enable form editing
$grid->navigator = true;
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#hazardpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a hazard", "onClickButton"=>"js: function(){addhazard();}")
);
$grid->callGridMethod("#hazardlist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#hazardlist','#hazardpager',true, null, null, true,true);
?>



