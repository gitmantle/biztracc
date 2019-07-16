<?php
session_start();
$usersession = $_SESSION['usersession'];

$ddate = date("d/m/Y");
$hdate = date("Y-m-d");

$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$subscriber = $subid;

$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());

include 'jq-config.php';
// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);

// Create the jqGrid instance
$grid = new jqGridRender($conn);
// Write the SQL Query

// the actual query for the grid data
$grid->SelectCommand = "SELECT status_id,status from status";

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getclientstatus.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Client Status Categories",
    "rowNum"=>12,
    "sortname"=>"status_id",
    "rowList"=>array(12,30,50)
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("status_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("status", array("label"=>"Client Status Category", "width"=>370));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>70));

$grid->setGridOptions(array("width"=>500,"height"=>280));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#clientstatuslist").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var cl = ids[i]; 
		be = '<img src="../images/edit.png" title="Edit" onclick="javascript:editclientstatus('+cl+')" ></ids>'; 
		jQuery("#clientstatuslist").setRowData(ids[i],{act:be}) 
	} 
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#clientstatuspager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a client status category", "onClickButton"=>"js: function(){addclientstatus();}")
);
$grid->callGridMethod("#clientstatuslist", "navButtonAdd", $buttonoptions); 



// Run the script
$grid->renderGrid('#clientstatuslist','#clientstatuspager',true, null, null, true,true);


?>




