<?php
session_start();
ini_set('display_errors', true);

$id = $_SESSION["s_memberid"];

$cltdb = $_SESSION['s_cltdb'];

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
$grid->SelectCommand = "select email_id,email_date,email_time,email_from,email_subject,email_message from ".$cltdb.".emails where member_id = ".$id; 

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getemails.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "rowNum"=>12,
    "sortname"=>"email_date",
	"sortorder"=>"desc",
    "rowList"=>array(12,30,50),
	"height"=>115,
	"width"=>540
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("email_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("email_date", array("label"=>"Date", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("email_time", array("label"=>"Time", "width"=>80));
$grid->setColProperty("email_from", array("label"=>"From", "width"=>120));
$grid->setColProperty("email_subject", array("label"=>"Subject", "width"=>150));
$grid->setColProperty("email_message", array("label"=>"Message", "width"=>50, "hidden"=>true));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>40));

$ldevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#memaillist").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var cl = ids[i]; 
		se = '<img src="../images/delete.png" title="Delete" onclick="javascript:delemail('+cl+')" />'; 
		jQuery("#memaillist").setRowData(ids[i],{act:se}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$ldevent);

// on select row we should post the messge to text box
$selectemail = <<<EMAIL
function(rowid, selected)
{
	var rowdata = $("#memaillist").getRowData(rowid);
	var content = rowdata.email_message;
	mdisplayitem(content);
}
EMAIL;
$grid->setGridEvent('onSelectRow', $selectemail);

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));


// Run the script
$grid->renderGrid('#memaillist','#memailpager',true, null, null, true,true);

?>




