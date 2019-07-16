<?php
error_reporting (E_ALL ^ E_NOTICE ^ E_WARNING);
session_start();
$id = $_SESSION['s_tablet'];

require_once '../includes/jquery/jq-config.php';
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
$grid->SelectCommand = "select uid,unit,ipadid,truckno,branch from tablets where coyid = ".$id;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model

$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getTablets.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Tablets",
    "rowNum"=>12,
    "sortname"=>"ipadid",
    "rowList"=>array(12,30,50),
	"width"=>590,
	"height"=>300
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("unit", array("label"=>"Unit", "width"=>30));
$grid->setColProperty("ipadid", array("label"=>"Tablet ID", "width"=>50));
$grid->setColProperty("truckno", array("label"=>"Vehicle", "width"=>70));
$grid->setColProperty("branch", array("label"=>"Branch", "width"=>40));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>30));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#tabletlist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#tabletlist").getRowData(ids[i]);
			var cl = ids[i];
			be = '<img src="../images/edit.png" title="Edit Subscriber" onclick="javascript:edittablet('+cl+')" ></ids>';
			jQuery("#tabletlist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// enable form editing
$grid->navigator = true;
$grid->setNavOptions('navigator',array('add'=>false,'edit'=>false,'del'=>false));

// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#tabletpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a Subscriber", "onClickButton"=>"js: function(){addtablet();}")
);
$grid->callGridMethod("#tabletlist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#tabletlist','#tabletpager',true, null, null, true,true);


?>



