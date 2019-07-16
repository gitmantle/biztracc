<?php
error_reporting(E_ALL ^ E_NOTICE);
session_start();
$sid = $_SESSION['s_subscriber'];

require_once '../includes/jquery/jq-config.php';
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
$grid->SelectCommand = "SELECT users.uid,users.ufname,users.ulname,users.uadmin from users where users.sub_id =  ".$sid;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getusers.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Users",
    "rowNum"=>12,
    "sortname"=>"ufname",
    "rowList"=>array(12,30,50),
	"width"=>600,
	"height"=>280
    ));

$grid->addCol(array("name"=>"act"),"first");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("ulname", array("label"=>"Last Name", "width"=>150));
$grid->setColProperty("ufname", array("label"=>"First name", "width"=>150));
$grid->setColProperty("uadmin", array("label"=>"Administrator", "width"=>150));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>70));


$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#stafflist2").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var cl = ids[i]; 
		be = '<img src="../images/edit.png" title="Edit" onclick="javascript:edituser('+cl+')" ></ids>'; 
		jQuery("#stafflist2").setRowData(ids[i],{act:be}) 
	} 
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#stafflist2','#staffpager2',true, null, null, true,true);


?>




