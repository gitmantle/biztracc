<?php
session_start();
ini_set('display_errors', true);

$id = $_SESSION["s_memberid"];

$cltdb = $_SESSION['s_cltdb'];
$admindb = $_SESSION['s_admindb'];

include 'jq-config.php';
// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);

// Create the jqGrid instance
$grid = new jqGridRender($conn);

// the actual query for the grid data
$grid->SelectCommand = "select activities.activities_id,activities.ddate,activities.ttime,substring(activities.activity,1,65) as note,CONCAT_WS(' ',users.ufname,users.ulname) as staffname from ".$cltdb.".activities,".$admindb.".users where activities.staff_id = users.uid and activities.member_id  = ".$id;


// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getactivities.php');

// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>12,
    "sortname"=>"ddate desc, ttime",
	"sortorder"=>"desc",
    "rowList"=>array(12,30,50),
	"multiselect"=>true,
	"height"=>115,
	"width"=>940
    ));

$grid->addCol(array("name"=>"act"),"last");


// Change some property of the field(s)
$grid->setColProperty("activities_id", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("member_id", array("label"=>"Member", "width"=>30, "hidden"=>true));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("ttime", array("label"=>"Time", "width"=>80));
$grid->setColProperty("note", array("label"=>"Note", "width"=>230));
$grid->setColProperty("staffname", array("label"=>"Author", "width"=>120));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>80, "sortable"=>false));


$ldevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#mactivitylist").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var cl = ids[i]; 
		be = '<img src="../images/edit.png" title="View note" onclick="javascript:editactivity('+cl+')" >'; 
		jQuery("#mactivitylist").setRowData(ids[i],{act:be});
	} 
}
LOADCOMPLETE;


$grid->setGridEvent("loadComplete",$ldevent);

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoption1 = array("#mactivitypager2",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a note", "onClickButton"=>"js: function(){addactivity(".$id.");}"),
);
$grid->callGridMethod("#mactivitylist", "navButtonAdd", $buttonoption1); 

$buttonoption2 = array("#mactivitypager2",
    array("buttonicon"=>"ui-icon-print","caption"=>"","position"=>"last","title"=>"Print notes", "onClickButton"=>"js: function(){printnotesm();}")
);
$grid->callGridMethod("#mactivitylist", "navButtonAdd", $buttonoption2); 

// Run the script
$grid->renderGrid('#mactivitylist','#mactivitypager2',true, null, null,true,true,true);


?>




