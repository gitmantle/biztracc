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
$grid->SelectCommand = "select uid,jobno,ddate,start,stop,operator,machine from ".$crndb.".joblines";

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model

// set the primary key - it is serial
$grid->setPrimaryKeyId('uid');

$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('gettimes.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Operator/Machine Times",
    "rowNum"=>20,
    "sortname"=>"jobno",
	"sortorder" => 'desc',
    "rowList"=>array(20,50,80),
	"width"=>960,
	"height"=>450
    ));


// Change some property of the field(s)
$grid->addCol(array("name"=>"act"),"last");
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("jobno", array("label"=>"Job No.", "width"=>70));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("start", array("label"=>"Start", "width"=>80));
$grid->setColProperty("stop", array("label"=>"Stop", "width"=>80));
$grid->setColProperty("operator", array("label"=>"Operator", "width"=>170));
$grid->setColProperty("machine", array("label"=>"Machine", "width"=>170));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#timelist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#timelist").getRowData(ids[i]);
			var cl = ids[i];
			var stp = rowd.stop;
			if (stp == '00:00:00') {
				be = '<img src="../images/edit.png" title="Set Stop Time" onclick="javascript:edittime('+cl+')" ></ids>';
			} else {
				be = '&nbsp;&nbsp';
			}
			jQuery("#timelist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// enable form editing
$grid->navigator = true;
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#timepager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a Time", "onClickButton"=>"js: function(){addtime();}")
);
$grid->callGridMethod("#timelist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#timelist','#timepager',true, null, null, true,true);
?>



