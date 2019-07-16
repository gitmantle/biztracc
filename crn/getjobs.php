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
$grid->SelectCommand = "select uid,jobno,date_created,client,location,state from ".$crndb.".jobs";

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model

// set the primary key - it is serial
$grid->setPrimaryKeyId('uid');

$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getjobs.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Administer Jobs",
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
$grid->setColProperty("date_created", array("label"=>"Created", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("client", array("label"=>"Client", "width"=>170));
$grid->setColProperty("location", array("label"=>"Location", "width"=>170));
$grid->setColProperty("state", array("label"=>"State", "width"=>80));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#joblist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#joblist").getRowData(ids[i]);
			var cl = ids[i];
			be = '<img src="../images/edit.png" title="Edit Job" onclick="javascript:editjob('+cl+')" ></ids>';
			se = '<img src="../images/clockicon.png" title="Operator and Machine times" onclick="javascript:times('+cl+')" ></ids>';
			jQuery("#joblist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+se}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// enable form editing
$grid->navigator = true;
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#jobpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a Job", "onClickButton"=>"js: function(){addjob();}")
);
$grid->callGridMethod("#joblist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#joblist','#jobpager',true, null, null, true,true);
?>



